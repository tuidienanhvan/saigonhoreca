<?php
/**
 * Pi API Migrate DB Endpoints — Database Export/Import & Search-Replace.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_MigrateDb {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/migrate/db/export', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'export_db'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/db/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_db'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/search-replace', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'search_replace'],
            'permission_callback' => $admin,
        ]);
    }

    public static function export_db($request) {
        if (!class_exists('Pi_Migrate_DB_Migrator')) {
            return new WP_REST_Response(['error' => 'Pi Migrate DB Migrator engine not available'], 500);
        }
        $file = wp_upload_dir()['basedir'] . '/pi-migrate-db-' . gmdate('Ymd-His') . '.sql';
        $result = Pi_Migrate_DB_Migrator::export($file);
        if (is_wp_error($result)) {
            return new WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new WP_REST_Response($result, 200);
    }

    public static function import_db($request) {
        $params = $request->get_json_params() ?: [];
        $file = $params['file'] ?? '';
        if (!$file || !file_exists($file)) {
            return new WP_REST_Response(['error' => 'File SQL không tồn tại.'], 400);
        }
        if (!class_exists('Pi_Migrate_DB_Migrator')) {
            return new WP_REST_Response(['error' => 'Engine Pi_Migrate_DB_Migrator chưa sẵn sàng.'], 500);
        }
        $result = Pi_Migrate_DB_Migrator::import($file);
        if (is_wp_error($result)) {
            return new WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new WP_REST_Response($result, 200);
    }

    public static function search_replace($request) {
        $params = $request->get_json_params() ?: [];
        $old = $params['old'] ?? '';
        $new = $params['new'] ?? '';
        $dry_run = !empty($params['dry_run']);

        if (!$old || !$new) {
            return new WP_REST_Response(['error' => 'Vui lòng cung cấp cả URL cũ và URL mới.'], 400);
        }
        if (!class_exists('Pi_Migrate_DB_Migrator')) {
            return new WP_REST_Response(['error' => 'Engine Search-Replace chưa sẵn sàng.'], 500);
        }

        $result = Pi_Migrate_DB_Migrator::search_replace($old, $new, $dry_run);
        return new WP_REST_Response($result, 200);
    }
}
