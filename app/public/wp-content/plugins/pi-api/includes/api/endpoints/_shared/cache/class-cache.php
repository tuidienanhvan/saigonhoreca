<?php
/**
 * Pi API — Cache management REST endpoints.
 *
 * Lets the pi-dashboard (and any authorized client) inspect + control the
 * theme-agnostic edge HTML cache implemented in PiApi\CacheManager.
 *
 * Routes (namespace pi/v1):
 *   GET  /cache/status   → stats: enabled, version, file count, total bytes
 *   POST /cache/purge    → flush all cached HTML (+ bump version + CDN purge)
 *   POST /cache/enable   → turn caching on  (installs advanced-cache.php drop-in)
 *   POST /cache/disable  → turn caching off (removes drop-in)
 *
 * @package Pi\Api
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pi_Api_Cache {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/cache/status', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'status'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/cache/purge', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'purge'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/cache/enable', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'enable'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/cache/disable', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'disable'],
            'permission_callback' => $admin,
        ]);
    }

    private static function available(): bool {
        return class_exists('\PiApi\Core\CacheManager');
    }

    public static function status(\WP_REST_Request $request) {
        if (!self::available()) {
            return new \WP_REST_Response(['error' => 'CacheManager not loaded'], 500);
        }
        return new \WP_REST_Response(\PiApi\Core\CacheManager::stats(), 200);
    }

    public static function purge(\WP_REST_Request $request) {
        if (!self::available()) {
            return new \WP_REST_Response(['error' => 'CacheManager not loaded'], 500);
        }
        \PiApi\Core\CacheManager::flush();
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Cache purged (version bumped + files cleared).',
            'stats'   => \PiApi\Core\CacheManager::stats(),
        ], 200);
    }

    public static function enable(\WP_REST_Request $request) {
        if (!self::available()) {
            return new \WP_REST_Response(['error' => 'CacheManager not loaded'], 500);
        }
        update_option('pi_api_cache_enabled', '1', false);
        $result = \PiApi\Core\CacheManager::installDropin();
        return new \WP_REST_Response([
            'success'  => $result['ok'],
            'steps'    => $result['steps'],
            'warnings' => $result['warnings'],
            'stats'    => \PiApi\Core\CacheManager::stats(),
        ], 200);
    }

    public static function disable(\WP_REST_Request $request) {
        if (!self::available()) {
            return new \WP_REST_Response(['error' => 'CacheManager not loaded'], 500);
        }
        update_option('pi_api_cache_enabled', '0', false);
        \PiApi\Core\CacheManager::flush();        // clear existing files
        \PiApi\Core\CacheManager::uninstallDropin();
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Cache disabled + drop-in removed + files cleared.',
            'stats'   => \PiApi\Core\CacheManager::stats(),
        ], 200);
    }
}
