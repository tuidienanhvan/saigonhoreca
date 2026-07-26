<?php
/**
 * Pi API SEO Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Seo {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/audit', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_audit'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/seo/settings', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_settings'],    'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'update_settings'], 'permission_callback' => $admin],
        ]);
    }

    public static function get_audit(\WP_REST_Request $request) {
        $type = $request->get_param('type') ?: 'post';
        $page = max(1, (int) ($request->get_param('page') ?: 1));
        
        $query = new \WP_Query([
            'post_type' => $type, 'post_status' => 'publish',
            'posts_per_page' => 50, 'paged' => $page,
        ]);

        $items = [];
        foreach ($query->posts as $p) {
            $items[] = [
                'id'    => $p->ID,
                'title' => $p->post_title,
                'score' => (int) get_post_meta($p->ID, '_pi_seo_score', true),
            ];
        }

        return new \WP_REST_Response(['items' => $items, 'total' => (int) $query->found_posts], 200);
    }

    public static function get_settings() {
        return new \WP_REST_Response([
            'ga4_id' => get_option('sgh_ga4_id', ''),
            'gtm_id' => get_option('sgh_gtm_id', ''),
            'fb_app_id' => get_option('sgh_fb_app_id', ''),
        ], 200);
    }

    public static function update_settings(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (isset($p['ga4_id'])) update_option('sgh_ga4_id', sanitize_text_field($p['ga4_id']));
        if (isset($p['gtm_id'])) update_option('sgh_gtm_id', sanitize_text_field($p['gtm_id']));
        
        return new \WP_REST_Response(['success' => true], 200);
    }
}
