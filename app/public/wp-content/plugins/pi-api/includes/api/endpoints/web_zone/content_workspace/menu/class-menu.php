<?php
/**
 * Pi API Menu Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Menu {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/menu/audit', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_audit'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/menu/sync', [
            'methods'  => 'POST',
            'callback' => [self::class, 'sync'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_audit() {
        if (!class_exists('\\PiDashboardV2\\SiteStructure')) return new \WP_REST_Response(['message' => 'SiteStructure not available'], 400);

        $structure = \PiDashboardV2\SiteStructure::getMenu();
        $menu_obj  = wp_get_nav_menu_object('Primary Menu');

        return new \WP_REST_Response([
            'definition' => $structure,
            'actual_id'  => $menu_obj ? $menu_obj->term_id : null,
        ], 200);
    }

    public static function sync() {
        if (!class_exists('\\PiDashboardV2\\SiteSync')) return new \WP_REST_Response(['message' => 'SiteSync not available'], 400);
        return new \WP_REST_Response(\PiDashboardV2\SiteSync::syncMenu(), 200);
    }
}
