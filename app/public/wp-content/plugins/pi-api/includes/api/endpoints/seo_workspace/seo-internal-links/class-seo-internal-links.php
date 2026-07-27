<?php
/**
 * Pi API SEO Internal Links — suggestion engine for cross-post linking
 *
 * Storage:
 *   pi_seo_internal_links_state   — per-post suggestion cache + decisions
 *
 * Stub heuristic: build a vocabulary of post titles, then for each post body
 * find substrings that match other posts' titles and emit them as suggestions.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoInternalLinks {

    const OPT_STATE = 'pi_seo_internal_links_state';

    /* ───────────────────────────── ROUTES ───────────────────────────── */

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/internal-links/scan', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'scan'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/internal-links/suggestions/(?P<post_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_suggestions'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/internal-links/apply', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'apply'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/internal-links/stats', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'stats'],
            'permission_callback' => $admin,
        ]);
    }

    /* ───────────────────────────── HELPERS ───────────────────────────── */

    private static function read_state() {
        $s = get_option(self::OPT_STATE, []);
        return is_array($s) ? $s : [];
    }

    private static function write_state(array $s) {
        update_option(self::OPT_STATE, $s, false);
    }

    /**
     * Build suggestions for a single post against a corpus.
     * @return array<int,array<string,mixed>>
     */
    private static function build_suggestions_for_post($post, $corpus) {
        if (!$post) return [];
        $content = wp_strip_all_tags((string) $post->post_content);
        if ($content === '') return [];
        $low = mb_strtolower($content);

        $out  = [];
        $sid  = 1;
        foreach ($corpus as $candidate) {
            if ((int) $candidate->ID === (int) $post->ID) continue;
            $title = trim((string) $candidate->post_title);
            if (mb_strlen($title) < 4) continue;
            $needle = mb_strtolower($title);
            $pos = mb_strpos($low, $needle);
            if ($pos === false) continue;

            $start   = max(0, $pos - 60);
            $context = mb_substr($content, $start, mb_strlen($title) + 120);

            $out[] = [
                'id'           => 's-' . $post->ID . '-' . $sid++,
                'anchor'       => $title,
                'target_id'    => (int) $candidate->ID,
                'target_title' => $title,
                'target_url'   => get_permalink($candidate),
                'context'      => $context,
                'position'     => (int) $pos,
                'status'       => 'pending',
            ];
            if (count($out) >= 5) break;
        }
        return $out;
    }

    private static function get_corpus($limit = 50) {
        $q = new \WP_Query([
            'post_type'      => ['post', 'page'],
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);
        return $q->posts ?: [];
    }

    /* ───────────────────────────── ENDPOINTS ───────────────────────────── */

    public static function scan(\WP_REST_Request $req) {
        $post_id = (int) $req->get_param('post_id');
        $state = self::read_state();
        $corpus = self::get_corpus(80);
        $now = current_time('mysql');

        if ($post_id > 0) {
            $post = get_post($post_id);
            if (!$post) {
                return new \WP_Error('not_found', 'Post not found', ['status' => 404]);
            }
            $suggestions = self::build_suggestions_for_post($post, $corpus);
            $state[(string) $post_id] = [
                'last_scanned' => $now,
                'suggestions'  => $suggestions,
                'decisions'    => $state[(string) $post_id]['decisions'] ?? [],
            ];
            self::write_state($state);
            return [
                'ok'           => true,
                'post_id'      => $post_id,
                'count'        => count($suggestions),
                'last_scanned' => $now,
            ];
        }

        // Whole site scan
        $scanned = 0;
        foreach ($corpus as $p) {
            $sugg = self::build_suggestions_for_post($p, $corpus);
            $state[(string) $p->ID] = [
                'last_scanned' => $now,
                'suggestions'  => $sugg,
                'decisions'    => $state[(string) $p->ID]['decisions'] ?? [],
            ];
            $scanned++;
        }
        self::write_state($state);

        return [
            'ok'      => true,
            'scanned' => $scanned,
            'time'    => $now,
        ];
    }

    public static function get_suggestions(\WP_REST_Request $req) {
        $post_id = (int) $req['post_id'];
        $post = get_post($post_id);
        if (!$post) {
            return new \WP_Error('not_found', 'Post not found', ['status' => 404]);
        }
        $state = self::read_state();
        $entry = $state[(string) $post_id] ?? null;

        if (!$entry) {
            // Lazy-scan on first access
            $corpus = self::get_corpus(80);
            $sugg = self::build_suggestions_for_post($post, $corpus);
            $entry = [
                'last_scanned' => current_time('mysql'),
                'suggestions'  => $sugg,
                'decisions'    => [],
            ];
            $state[(string) $post_id] = $entry;
            self::write_state($state);
        }

        $decisions = $entry['decisions'] ?? [];
        $suggestions = array_map(function ($s) use ($decisions) {
            $s['status'] = $decisions[$s['id']] ?? 'pending';
            return $s;
        }, $entry['suggestions']);

        return [
            'post_id'      => $post_id,
            'post_title'   => $post->post_title,
            'last_scanned' => $entry['last_scanned'] ?? null,
            'suggestions'  => $suggestions,
        ];
    }

    public static function apply(\WP_REST_Request $req) {
        $body = $req->get_json_params() ?: [];
        $post_id = (int) ($body['post_id'] ?? 0);
        $sid     = sanitize_text_field((string) ($body['suggestion_id'] ?? ''));
        $action  = (string) ($body['action'] ?? 'accept');
        if ($post_id <= 0 || $sid === '') {
            return new \WP_Error('bad_request', 'post_id and suggestion_id required', ['status' => 400]);
        }
        $action = $action === 'reject' ? 'rejected' : 'accepted';

        $state = self::read_state();
        $entry = $state[(string) $post_id] ?? ['suggestions' => [], 'decisions' => []];
        $entry['decisions'][$sid] = $action;
        $state[(string) $post_id] = $entry;
        self::write_state($state);

        return ['ok' => true, 'post_id' => $post_id, 'suggestion_id' => $sid, 'status' => $action];
    }

    public static function stats() {
        $state = self::read_state();
        $totalPosts = 0;
        $totalSugg = 0;
        $accepted = 0;
        $rejected = 0;
        $pending = 0;
        foreach ($state as $entry) {
            $totalPosts++;
            $sl = $entry['suggestions'] ?? [];
            $totalSugg += count($sl);
            $dec = $entry['decisions'] ?? [];
            foreach ($sl as $s) {
                $st = $dec[$s['id']] ?? 'pending';
                if ($st === 'accepted') $accepted++;
                elseif ($st === 'rejected') $rejected++;
                else $pending++;
            }
        }

        // Posts list with counts
        $posts = [];
        $corpus = self::get_corpus(80);
        foreach ($corpus as $p) {
            $entry = $state[(string) $p->ID] ?? null;
            $count = $entry ? count($entry['suggestions'] ?? []) : 0;
            $posts[] = [
                'id'           => (int) $p->ID,
                'title'        => $p->post_title,
                'type'         => $p->post_type,
                'count'        => $count,
                'last_scanned' => $entry['last_scanned'] ?? null,
            ];
        }

        return [
            'total_posts'     => $totalPosts,
            'total_suggestions' => $totalSugg,
            'accepted'        => $accepted,
            'rejected'        => $rejected,
            'pending'         => $pending,
            'posts'           => $posts,
        ];
    }
}
