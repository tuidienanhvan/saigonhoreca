<?php
/**
 * Pi API Migrate Media Endpoints.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_MigrateMedia {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/migrate/files/sync', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'sync_files'],
            'permission_callback' => $admin,
        ]);
    }

    public static function sync_files($request) {
        $params = $request->get_json_params() ?: [];
        $source = $params['source'] ?? wp_upload_dir()['basedir'];
        $dest   = $params['dest'] ?? '';
        $dry_run = !empty($params['dry_run']);

        if (!$dest) {
            return new WP_REST_Response(['error' => 'Vui lòng cung cấp thư mục đích (destination).'], 400);
        }

        if (!class_exists('Pi_Migrate_File_Sync')) {
            return new WP_REST_Response(['error' => 'Engine Pi_Migrate_File_Sync chưa sẵn sàng.'], 500);
        }

        $result = Pi_Migrate_File_Sync::sync_directory($source, $dest, $dry_run);
        if (is_wp_error($result)) {
            return new WP_REST_Response(['error' => $result->get_error_message()], 400);
        }

        return new WP_REST_Response($result, 200);
    }
}
