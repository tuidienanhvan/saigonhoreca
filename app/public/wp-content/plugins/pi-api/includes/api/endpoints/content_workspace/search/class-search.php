<?php
/**
 * Pi API Search Endpoint
 *
 * GET /pi/v1/search?q=<query>&types=posts,pages,leads,categories,media
 *
 * Returns up to 5 results per type. Powers the Command Palette / GlobalSearch
 * top-bar dropdown in pi-admin-webapp.
 *
 * Implementation notes:
 *  - WP_Query for posts/pages, get_terms for categories, attachment query
 *    for media, direct $wpdb for leads (custom table pi_leads).
 *  - Results are intentionally lean: { id, title, url, snippet }.
 *  - On empty/short query (<2 chars) we return zeros without hitting DB.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Search {

    const MIN_QUERY_LEN = 2;
    const PER_TYPE_LIMIT = 5;

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/search', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'search'],
            'permission_callback' => $admin,
            'args'                => [
                'q' => [
                    'description' => 'Search query (Vietnamese or English).',
                    'type'        => 'string',
                    'required'    => false,
                    'default'     => '',
                ],
                'types' => [
                    'description' => 'Comma-separated list of types to query.',
                    'type'        => 'string',
                    'required'    => false,
                    'default'     => 'posts,pages,leads,categories,media',
                ],
            ],
        ]);
    }

    public static function search(\WP_REST_Request $request) {
        $q = trim((string) $request->get_param('q'));
        $types = array_filter(array_map('trim', explode(',', (string) $request->get_param('types'))));

        $payload = [
            'query'      => $q,
            'posts'      => [],
            'pages'      => [],
            'leads'      => [],
            'categories' => [],
            'media'      => [],
            'total'      => 0,
        ];

        if (mb_strlen($q) < self::MIN_QUERY_LEN) {
            return new \WP_REST_Response($payload, 200);
        }

        if (in_array('posts', $types, true)) {
            $payload['posts'] = self::query_posts($q, 'post');
        }
        if (in_array('pages', $types, true)) {
            $payload['pages'] = self::query_posts($q, 'page');
        }
        if (in_array('categories', $types, true)) {
            $payload['categories'] = self::query_categories($q);
        }
        if (in_array('media', $types, true)) {
            $payload['media'] = self::query_media($q);
        }
        if (in_array('leads', $types, true)) {
            $payload['leads'] = self::query_leads($q);
        }

        $payload['total'] =
            count($payload['posts']) +
            count($payload['pages']) +
            count($payload['leads']) +
            count($payload['categories']) +
            count($payload['media']);

        return new \WP_REST_Response($payload, 200);
    }

    private static function query_posts($q, $post_type) {
        $query = new \WP_Query([
            'post_type'      => $post_type,
            'post_status'    => ['publish', 'draft', 'pending'],
            'posts_per_page' => self::PER_TYPE_LIMIT,
            's'              => $q,
            'no_found_rows'  => true,
        ]);

        $results = [];
        foreach ($query->posts as $p) {
            $results[] = [
                'id'      => (int) $p->ID,
                'title'   => $p->post_title ?: '(untitled)',
                'url'     => get_permalink($p),
                'editUrl' => "/edit-post/{$p->ID}",
                'status'  => $p->post_status,
                'snippet' => wp_trim_words(wp_strip_all_tags($p->post_content), 18, '…'),
            ];
        }
        return $results;
    }

    private static function query_categories($q) {
        $terms = get_terms([
            'taxonomy'   => 'category',
            'hide_empty' => false,
            'number'     => self::PER_TYPE_LIMIT,
            'name__like' => $q,
        ]);
        if (is_wp_error($terms)) return [];

        $results = [];
        foreach ($terms as $t) {
            $results[] = [
                'id'    => (int) $t->term_id,
                'title' => $t->name,
                'url'   => get_term_link($t),
                'count' => (int) $t->count,
                'slug'  => $t->slug,
            ];
        }
        return $results;
    }

    private static function query_media($q) {
        $query = new \WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => self::PER_TYPE_LIMIT,
            's'              => $q,
            'no_found_rows'  => true,
        ]);

        $results = [];
        foreach ($query->posts as $p) {
            $results[] = [
                'id'    => (int) $p->ID,
                'title' => $p->post_title,
                'url'   => wp_get_attachment_url($p->ID),
                'mime'  => $p->post_mime_type,
                'thumb' => wp_get_attachment_image_url($p->ID, 'thumbnail'),
            ];
        }
        return $results;
    }

    private static function query_leads($q) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_leads';

        // Bail if leads table doesn't exist (plugin not active).
        if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
            return [];
        }

        $like = '%' . $wpdb->esc_like($q) . '%';
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, name, email, phone, status, created_at
                 FROM {$table}
                 WHERE name LIKE %s OR email LIKE %s OR phone LIKE %s OR message LIKE %s
                 ORDER BY created_at DESC
                 LIMIT %d",
                $like, $like, $like, $like, self::PER_TYPE_LIMIT
            ),
            ARRAY_A
        );

        if (!$rows) return [];
        return array_map(function ($r) {
            return [
                'id'        => (int) $r['id'],
                'title'     => $r['name'] ?: $r['email'] ?: '(no name)',
                'email'     => $r['email'],
                'phone'     => $r['phone'],
                'status'    => $r['status'],
                'createdAt' => $r['created_at'],
            ];
        }, $rows);
    }
}
