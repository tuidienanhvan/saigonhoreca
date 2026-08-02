<?php
/**
 * Pi API Performance Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Performance {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/performance', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_status'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/performance/cache', [
            'methods'  => 'POST',
            'callback' => [self::class, 'clear_cache'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_status() {
        return new \WP_REST_Response([
            'cache_enabled' => class_exists('SGH_Nano_Cache'),
            'object_cache'  => wp_using_ext_object_cache(),
        ], 200);
    }

    public static function clear_cache() {
        if (class_exists('SGH_Nano_Cache')) {
            $nano = new \SGH_Nano_Cache();
            $nano->flush_all_nano_cache();
        }
        if (function_exists('wp_cache_flush')) wp_cache_flush();
        
        return new \WP_REST_Response(['success' => true, 'message' => 'Cache cleared.'], 200);
    }
}
