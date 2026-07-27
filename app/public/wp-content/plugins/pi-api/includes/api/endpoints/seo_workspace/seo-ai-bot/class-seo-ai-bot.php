<?php
/**
 * Pi API SEO AI Bot Endpoints
 *
 * Provides queue/status/apply endpoints for an AI-driven SEO meta generator.
 * Real provider routing lives in pi-backend (pi_seo/routers/seo_bot.py).
 * On each status() poll, processes up to 3 posts via pi-backend
 * POST /v1/seo/bot/generate (real AI: OpenAI/Anthropic/Groq).
 * Falls back to mock suggestions if backend is unreachable.
 *
 * Storage: option `pi_seo_ai_bot_tasks` (capped to 20 most recent tasks).
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoAiBot {

    const OPTION_KEY = 'pi_seo_ai_bot_tasks';
    const MAX_TASKS  = 20;

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/ai-bot/suggest', [
            'methods'  => 'POST',
            'callback' => [self::class, 'suggest'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/ai-bot/status/(?P<task_id>[A-Za-z0-9_-]+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'status'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/ai-bot/apply', [
            'methods'  => 'POST',
            'callback' => [self::class, 'apply'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/ai-bot/apply-bulk', [
            'methods'  => 'POST',
            'callback' => [self::class, 'apply_bulk'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/ai-bot/recent', [
            'methods'  => 'GET',
            'callback' => [self::class, 'recent'],
            'permission_callback' => $admin,
        ]);
    }

    /* ─── Storage helpers ─────────────────────────────────────────── */

    private static function load_tasks() {
        $tasks = get_option(self::OPTION_KEY, []);
        return is_array($tasks) ? $tasks : [];
    }

    private static function save_tasks($tasks) {
        // Keep most recent N (newest first).
        if (count($tasks) > self::MAX_TASKS) {
            $tasks = array_slice($tasks, 0, self::MAX_TASKS);
        }
        update_option(self::OPTION_KEY, $tasks, false);
    }

    private static function find_task($task_id) {
        $tasks = self::load_tasks();
        foreach ($tasks as $t) {
            if (($t['task_id'] ?? '') === $task_id) return $t;
        }
        return null;
    }

    private static function update_task($task_id, $updates) {
        $tasks = self::load_tasks();
        foreach ($tasks as &$t) {
            if (($t['task_id'] ?? '') === $task_id) {
                $t = array_merge($t, $updates);
                self::save_tasks($tasks);
                return $t;
            }
        }
        return null;
    }

    /* ─── Real pi-backend suggestion call ────────────────────────── */

    /**
     * Call pi-backend POST /v1/seo/bot/generate for a single post.
     * Returns ['title'=>..., 'description'=>..., '_meta'=>[...]] on success
     * or ['_error'=>'...'] on failure.
     */
    private static function real_suggestions_for($post_id, $action, $tone, $language) {
        $post = get_post($post_id);
        if (!$post) {
            return ['_error' => 'Post not found'];
        }

        $license_key = \PiApi\Settings::getLicenseKey();
        if ($license_key === '') {
            return ['_error' => 'No license key configured'];
        }

        $site_url    = home_url('/');
        $post_title  = get_the_title($post_id);
        $raw_content = wp_strip_all_tags($post->post_content);
        // Backend schema (SeoBotGenerateRequest): excerpt <=2000, content_snippet <=4000.
        $excerpt         = mb_substr(wp_trim_words($raw_content, 100), 0, 2000);
        $content_snippet = mb_substr($raw_content, 0, 4000);

        // Backend language enum is "vi"|"en"|"auto" — coerce anything else to "auto".
        $lang = in_array($language, ['vi', 'en', 'auto'], true) ? $language : 'auto';
        // Backend tone enum: professional|casual|friendly|authoritative|playful.
        $tone_safe = in_array(
            $tone,
            ['professional', 'casual', 'friendly', 'authoritative', 'playful'],
            true
        ) ? $tone : 'professional';

        $payload = [
            'site_url'        => $site_url,
            'post_id'         => (int) $post_id,
            'post_title'      => $post_title ?: ('Post #' . $post_id),
            'focus_keyword'   => '',
            'excerpt'         => $excerpt,
            'content_snippet' => $content_snippet,
            'tone'            => $tone_safe,
            'audience'        => 'general',
            'language'        => $lang,
            'variants'        => 1,
        ];

        $response = \PiApi\BackendClient::seoGenerate($payload, $license_key);

        if (empty($response['success'])) {
            $error_msg = $response['error'] ?? 'Backend request failed';
            $http_code = $response['status'] ?? 0;
            return [
                '_error' => $error_msg,
                '_meta'  => ['http_status' => $http_code],
            ];
        }

        $data    = $response['data'] ?? [];
        $variant = $data['variants'][0] ?? [];
        $out     = [];

        if ($action === 'meta_title' || $action === 'both') {
            $out['title'] = $variant['title'] ?? '';
        }
        if ($action === 'meta_description' || $action === 'both') {
            $out['description'] = $variant['description'] ?? '';
        }
        $out['_meta'] = [
            'tokens_used' => $data['tokens_used'] ?? 0,
            'model'       => $data['model'] ?? '',
            'source'      => 'pi-backend',
        ];
        return $out;
    }

    /**
     * Try real backend first, fall back to mock on failure.
     */
    private static function get_suggestions($post_id, $action, $tone, $language) {
        $result = self::real_suggestions_for($post_id, $action, $tone, $language);

        if (isset($result['_error'])) {
            error_log('[pi-api seo-ai-bot] backend failed for post ' . $post_id . ': ' . $result['_error']);
            $result = self::mock_suggestions_for($post_id, $action, $tone, $language);
            $result['_fallback'] = true;
            $result['_meta'] = ['source' => 'mock-fallback'];
        }

        return $result;
    }

    /* ─── Mock suggestion fallback (kept for graceful degradation) ─ */

    /** @deprecated Retained as fallback when pi-backend is unreachable. */
    private static function mock_suggestions_for($post_id, $action, $tone, $language) {
        $title  = get_the_title($post_id) ?: ('Bài viết #' . $post_id);
        $brand  = wp_strip_all_tags(get_bloginfo('name')) ?: 'Trang web mẫu';
        $tonemap = [
            'professional' => 'chuyên nghiệp',
            'friendly'     => 'thân thiện',
            'persuasive'   => 'thuyết phục',
        ];
        $tone_label = $tonemap[$tone] ?? 'chuyên nghiệp';
        $is_en      = ($language === 'en');

        $mock_title = $is_en
            ? sprintf('%s | %s', $title, $brand)
            : sprintf('%s | %s', $title, $brand);

        $mock_desc = $is_en
            ? sprintf('A %s overview of %s — read more on %s.', $tone_label, $title, $brand)
            : sprintf('Mô tả ngắn gọn (%s) về "%s" — tìm hiểu thêm tại %s.', $tone_label, $title, $brand);

        $out = [];
        if ($action === 'meta_title' || $action === 'both') {
            $out['title'] = $mock_title;
        }
        if ($action === 'meta_description' || $action === 'both') {
            $out['description'] = $mock_desc;
        }
        return $out;
    }

    /* ─── Endpoints ───────────────────────────────────────────────── */

    public static function suggest(\WP_REST_Request $request) {
        $params   = $request->get_json_params();
        $post_ids = array_values(array_unique(array_map('intval', (array) ($params['post_ids'] ?? []))));
        $action   = in_array($params['action'] ?? '', ['meta_title', 'meta_description', 'both'], true)
            ? $params['action'] : 'both';
        $tone     = in_array($params['tone'] ?? '', ['professional', 'friendly', 'persuasive'], true)
            ? $params['tone'] : 'professional';
        $language = in_array($params['language'] ?? '', ['vi', 'en'], true)
            ? $params['language'] : 'vi';

        if (empty($post_ids)) {
            return new \WP_REST_Response(['message' => 'post_ids is required'], 400);
        }

        $task_id = 'aibot_' . wp_generate_uuid4();
        $task = [
            'task_id'   => $task_id,
            'status'    => 'queued',
            'progress'  => 0,
            'action'    => $action,
            'tone'      => $tone,
            'language'  => $language,
            'post_ids'  => $post_ids,
            'results'   => [],
            'created_at'=> time(),
            'updated_at'=> time(),
        ];

        $tasks = self::load_tasks();
        array_unshift($tasks, $task);
        self::save_tasks($tasks);

        return new \WP_REST_Response([
            'task_id' => $task_id,
            'status'  => 'queued',
        ], 200);
    }

    public static function status(\WP_REST_Request $request) {
        $task_id = (string) $request->get_param('task_id');
        $task    = self::find_task($task_id);

        if (!$task) {
            return new \WP_REST_Response(['message' => 'Task not found'], 404);
        }

        // Real progression: each poll processes up to BATCH_SIZE unprocessed
        // posts via pi-backend, updating results incrementally until all done.
        if ($task['status'] !== 'complete' && $task['status'] !== 'failed') {
            $total       = max(1, count($task['post_ids']));
            $results     = $task['results'] ?? [];
            $done_ids    = array_column($results, 'post_id');
            $remaining   = array_values(array_diff($task['post_ids'], $done_ids));
            $batch_size  = min(3, count($remaining)); // Process up to 3 per poll

            for ($i = 0; $i < $batch_size; $i++) {
                $pid = $remaining[$i];
                $suggestions = self::get_suggestions(
                    $pid,
                    $task['action'],
                    $task['tone'],
                    $task['language']
                );
                $results[] = [
                    'post_id'     => $pid,
                    'post_title'  => get_the_title($pid) ?: ('Bài #' . $pid),
                    'suggestions' => $suggestions,
                ];
            }

            $processed     = count($results);
            $next_progress = (int) floor(($processed / $total) * 100);
            $next_status   = $processed >= $total ? 'complete' : 'running';

            $task = self::update_task($task_id, [
                'status'    => $next_status,
                'progress'  => $next_progress,
                'results'   => $results,
                'updated_at'=> time(),
            ]);
        }

        return new \WP_REST_Response([
            'task_id'  => $task['task_id'],
            'status'   => $task['status'],
            'progress' => (int) $task['progress'],
            'action'   => $task['action'],
            'tone'     => $task['tone'],
            'language' => $task['language'],
            'results'  => $task['results'] ?? [],
        ], 200);
    }

    public static function apply(\WP_REST_Request $request) {
        $params      = $request->get_json_params();
        $post_id     = (int) ($params['post_id'] ?? 0);
        $suggestions = (array) ($params['suggestions'] ?? []);

        if ($post_id <= 0) {
            return new \WP_REST_Response(['message' => 'post_id is required'], 400);
        }

        $applied = self::apply_one($post_id, $suggestions);
        if (!$applied['ok']) {
            return new \WP_REST_Response(['message' => $applied['message']], 404);
        }

        return new \WP_REST_Response([
            'success' => true,
            'post_id' => $post_id,
            'applied' => $applied['applied'],
        ], 200);
    }

    public static function apply_bulk(\WP_REST_Request $request) {
        $params       = $request->get_json_params();
        $applications = (array) ($params['applications'] ?? []);

        $results = [];
        foreach ($applications as $app) {
            $pid = (int) ($app['post_id'] ?? 0);
            $sug = (array) ($app['suggestions'] ?? []);
            if ($pid <= 0) {
                $results[] = ['post_id' => $pid, 'ok' => false, 'message' => 'invalid post_id'];
                continue;
            }
            $r = self::apply_one($pid, $sug);
            $results[] = [
                'post_id' => $pid,
                'ok'      => $r['ok'],
                'message' => $r['message'] ?? null,
                'applied' => $r['applied'] ?? null,
            ];
        }

        return new \WP_REST_Response(['results' => $results], 200);
    }

    private static function apply_one($post_id, $suggestions) {
        if (!get_post($post_id)) {
            return ['ok' => false, 'message' => 'post not found'];
        }
        $applied = [];
        if (isset($suggestions['title']) && $suggestions['title'] !== '') {
            update_post_meta($post_id, '_pi_seo_title', sanitize_text_field($suggestions['title']));
            $applied[] = 'title';
        }
        if (isset($suggestions['description']) && $suggestions['description'] !== '') {
            update_post_meta($post_id, '_pi_seo_description', sanitize_textarea_field($suggestions['description']));
            $applied[] = 'description';
        }
        return ['ok' => true, 'applied' => $applied];
    }

    public static function recent() {
        $tasks = self::load_tasks();
        // Slim payload: drop full results to keep the listing light.
        $slim = array_map(function ($t) {
            return [
                'task_id'   => $t['task_id']   ?? '',
                'status'    => $t['status']    ?? 'queued',
                'progress'  => (int) ($t['progress'] ?? 0),
                'action'    => $t['action']    ?? 'both',
                'tone'      => $t['tone']      ?? 'professional',
                'language'  => $t['language']  ?? 'vi',
                'post_count'=> count($t['post_ids'] ?? []),
                'created_at'=> $t['created_at']?? null,
                'updated_at'=> $t['updated_at']?? null,
            ];
        }, $tasks);
        return new \WP_REST_Response(['tasks' => $slim], 200);
    }
}
