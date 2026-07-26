<?php
/**
 * Pi API SEO Sitemap Endpoints
 *
 * Routes:
 *   GET  /pi/v1/seo/sitemaps/settings
 *   POST /pi/v1/seo/sitemaps/settings
 *   POST /pi/v1/seo/sitemaps/rebuild
 *   GET  /pi/v1/seo/sitemaps/index
 *   GET  /pi/v1/seo/sitemaps/ping?engine=google|bing
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoSitemaps {

    const OPT_POST_TYPES       = 'sgh_seo_sitemap_include_post_types';
    const OPT_TAXONOMIES       = 'sgh_seo_sitemap_include_taxonomies';
    const OPT_EXCLUDE_IDS      = 'sgh_seo_sitemap_exclude_ids';
    const OPT_INCLUDE_IMAGES   = 'sgh_seo_sitemap_include_images';
    const OPT_MAX_URLS_PER_FILE = 'sgh_seo_sitemap_max_urls_per_file';
    const OPT_LAST_REBUILD     = 'sgh_seo_sitemap_last_rebuild';

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/sitemaps/settings', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_settings'],    'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'update_settings'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/sitemaps/rebuild', [
            ['methods' => 'POST', 'callback' => [self::class, 'rebuild'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/sitemaps/index', [
            ['methods' => 'GET', 'callback' => [self::class, 'index'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/sitemaps/ping', [
            ['methods' => 'GET', 'callback' => [self::class, 'ping'], 'permission_callback' => $admin],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // SETTINGS
    // ──────────────────────────────────────────────────────────────────────
    public static function get_settings() {
        $defaults_pt = ['post', 'page'];
        $defaults_tx = ['category'];

        $all_pts = get_post_types(['public' => true], 'objects');
        $all_txs = get_taxonomies(['public' => true], 'objects');

        $pt_choices = [];
        foreach ($all_pts as $pt) {
            if (in_array($pt->name, ['attachment'], true)) continue;
            $pt_choices[] = ['slug' => $pt->name, 'label' => $pt->labels->name ?? $pt->name];
        }
        $tx_choices = [];
        foreach ($all_txs as $tx) {
            $tx_choices[] = ['slug' => $tx->name, 'label' => $tx->labels->name ?? $tx->name];
        }

        return new \WP_REST_Response([
            'include_post_types'   => (array) get_option(self::OPT_POST_TYPES, $defaults_pt),
            'include_taxonomies'   => (array) get_option(self::OPT_TAXONOMIES, $defaults_tx),
            'exclude_ids'          => (array) get_option(self::OPT_EXCLUDE_IDS, []),
            'include_images'       => (bool)  get_option(self::OPT_INCLUDE_IMAGES, true),
            'max_urls_per_file'    => (int)   get_option(self::OPT_MAX_URLS_PER_FILE, 1000),
            'last_rebuild'         => (string) get_option(self::OPT_LAST_REBUILD, ''),
            'available_post_types' => $pt_choices,
            'available_taxonomies' => $tx_choices,
        ], 200);
    }

    public static function update_settings(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }

        if (isset($body['include_post_types']) && is_array($body['include_post_types'])) {
            update_option(self::OPT_POST_TYPES, array_values(array_filter(array_map('sanitize_key', $body['include_post_types']))));
        }
        if (isset($body['include_taxonomies']) && is_array($body['include_taxonomies'])) {
            update_option(self::OPT_TAXONOMIES, array_values(array_filter(array_map('sanitize_key', $body['include_taxonomies']))));
        }
        if (isset($body['exclude_ids']) && is_array($body['exclude_ids'])) {
            update_option(self::OPT_EXCLUDE_IDS, array_values(array_filter(array_map('intval', $body['exclude_ids']))));
        }
        if (array_key_exists('include_images', $body)) {
            update_option(self::OPT_INCLUDE_IMAGES, (bool) $body['include_images']);
        }
        if (isset($body['max_urls_per_file'])) {
            update_option(self::OPT_MAX_URLS_PER_FILE, max(50, min(50000, (int) $body['max_urls_per_file'])));
        }

        return new \WP_REST_Response(['success' => true, 'settings' => self::get_settings()->get_data()], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // REBUILD
    // ──────────────────────────────────────────────────────────────────────
    public static function rebuild() {
        // Flush rewrite rules so dynamic sitemap endpoints are re-bound.
        flush_rewrite_rules(false);

        // Clear any transient cache kept by plugin v1/v2.
        delete_transient('sh_sitemap_xml');
        delete_transient('pi_seo_sitemap_xml');
        delete_transient('pi_seo_sitemap_index');

        // Invoke plugin's cache-flush helpers if they exist.
        if (class_exists('\\Pi\\SEO\\Sitemap') && method_exists('\\Pi\\SEO\\Sitemap', 'flushCache')) {
            try { \Pi\SEO\Sitemap::flushCache(); } catch (\Throwable $e) {}
        }

        // Trigger WP Core's native sitemap provider to refresh too.
        do_action('pi_seo_sitemap_rebuild');

        $now = current_time('mysql');
        update_option(self::OPT_LAST_REBUILD, $now);

        return new \WP_REST_Response([
            'success'      => true,
            'last_rebuild' => $now,
            'message'      => 'Đã xây lại sitemap',
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────────────────
    public static function index() {
        $post_types = (array) get_option(self::OPT_POST_TYPES, ['post', 'page']);
        $taxonomies = (array) get_option(self::OPT_TAXONOMIES, ['category']);
        $exclude    = (array) get_option(self::OPT_EXCLUDE_IDS, []);

        $entries = [];

        // 1. Master index
        $entries[] = [
            'label'       => 'Sitemap Index',
            'name'        => 'sitemap.xml',
            'url'         => home_url('/sitemap.xml'),
            'entry_count' => null,
            'kind'        => 'index',
        ];

        // 2. Per post-type sitemaps
        foreach ($post_types as $pt) {
            $pt = sanitize_key($pt);
            if (!post_type_exists($pt)) continue;
            $query_args = [
                'post_type'      => $pt,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'no_found_rows'  => false,
                'post__not_in'   => $exclude,
            ];
            $q = new \WP_Query($query_args);
            $entries[] = [
                'label'       => 'Post type: ' . $pt,
                'name'        => $pt . '-sitemap.xml',
                'url'         => home_url('/wp-sitemap-posts-' . $pt . '-1.xml'),
                'entry_count' => (int) $q->found_posts,
                'kind'        => 'post_type',
                'slug'        => $pt,
            ];
        }

        // 3. Per-taxonomy sitemaps
        foreach ($taxonomies as $tx) {
            $tx = sanitize_key($tx);
            if (!taxonomy_exists($tx)) continue;
            $count = (int) wp_count_terms(['taxonomy' => $tx, 'hide_empty' => false]);
            $entries[] = [
                'label'       => 'Taxonomy: ' . $tx,
                'name'        => $tx . '-sitemap.xml',
                'url'         => home_url('/wp-sitemap-taxonomies-' . $tx . '-1.xml'),
                'entry_count' => $count,
                'kind'        => 'taxonomy',
                'slug'        => $tx,
            ];
        }

        // 4. Users
        $entries[] = [
            'label'       => 'Users',
            'name'        => 'users-sitemap.xml',
            'url'         => home_url('/wp-sitemap-users-1.xml'),
            'entry_count' => (int) count_users()['total_users'],
            'kind'        => 'users',
        ];

        return new \WP_REST_Response([
            'entries'      => $entries,
            'last_rebuild' => (string) get_option(self::OPT_LAST_REBUILD, ''),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // PING
    // ──────────────────────────────────────────────────────────────────────
    public static function ping(\WP_REST_Request $request) {
        $engine = strtolower((string) ($request->get_param('engine') ?: 'google'));
        $sitemap_url = home_url('/sitemap.xml');

        $endpoints = [
            'google' => 'https://www.google.com/ping?sitemap=' . urlencode($sitemap_url),
            'bing'   => 'https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url),
        ];

        if (!isset($endpoints[$engine])) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Engine không hỗ trợ (google | bing)'], 400);
        }

        $response = wp_remote_get($endpoints[$engine], [
            'timeout'  => 10,
            'blocking' => true,
            'headers'  => ['User-Agent' => 'Pi-SEO-Sitemap-Ping/1.0'],
        ]);

        if (is_wp_error($response)) {
            return new \WP_REST_Response([
                'success' => false,
                'engine'  => $engine,
                'message' => $response->get_error_message(),
            ], 500);
        }

        $code = wp_remote_retrieve_response_code($response);
        $ok   = ($code >= 200 && $code < 400);

        return new \WP_REST_Response([
            'success' => $ok,
            'engine'  => $engine,
            'code'    => (int) $code,
            'url'     => $endpoints[$engine],
            'sitemap' => $sitemap_url,
            'message' => $ok ? 'Ping thành công (' . $code . ')' : 'Ping thất bại (' . $code . ')',
        ], $ok ? 200 : 502);
    }
}
