<?php
/**
 * Pi API Migrate Site Package Endpoints.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_MigrateSite {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/migrate/site/export', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'export_site'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/site/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_site'],
            'permission_callback' => $admin,
        ]);
    }

    public static function export_site($request) {
        $params = $request->get_json_params() ?: [];
        $dir = $params['output_dir'] ?? sys_get_temp_dir() . '/pi-migrate-site-' . gmdate('Ymd-His');

        if (!class_exists('Pi_Migrate_Site_Package')) {
            return new WP_REST_Response(['error' => 'Engine Pi_Migrate_Site_Package chưa sẵn sàng.'], 500);
        }

        $result = Pi_Migrate_Site_Package::export($dir);
        if (is_wp_error($result)) {
            return new WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new WP_REST_Response($result, 200);
    }

    public static function import_site($request) {
        $params = $request->get_json_params() ?: [];
        $source_dir = $params['source_dir'] ?? '';
        $new_url    = $params['new_url'] ?? '';

        if (!$source_dir) {
            return new WP_REST_Response(['error' => 'Vui lòng cung cấp thư mục gói nguồn (source_dir).'], 400);
        }

        if (!class_exists('Pi_Migrate_Site_Package')) {
            return new WP_REST_Response(['error' => 'Engine Pi_Migrate_Site_Package chưa sẵn sàng.'], 500);
        }

        $result = Pi_Migrate_Site_Package::import($source_dir, $new_url);
        if (is_wp_error($result)) {
            return new WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new WP_REST_Response($result, 200);
    }
}
