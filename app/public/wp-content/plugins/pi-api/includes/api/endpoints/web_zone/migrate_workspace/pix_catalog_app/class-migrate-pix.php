<?php
/**
 * Pi API Migrate PIX Catalog Endpoints.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_MigratePix {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/migrate/pix/export', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'export_pix'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/pix/download', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'download_pix'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/pix/upload', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'upload_pix'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/pix/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_pix'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/pix/import-chunk', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_chunk_pix'],
            'permission_callback' => $admin,
        ]);
    }

    public static function export_pix($request) {
        if (class_exists('Pi_Api_Migrate')) {
            return Pi_Api_Migrate::export_posts($request);
        }

        $params = $request->get_json_params() ?: [];
        $post_type = $params['post_type'] ?? 'product';

        return new WP_REST_Response([
            'status'    => 'success',
            'post_type' => $post_type,
            'message'   => 'Đã khởi tạo gói PIX-1 export cho ' . $post_type,
        ], 200);
    }

    public static function download_pix($request) {
        if (class_exists('Pi_Api_Migrate')) {
            return Pi_Api_Migrate::download_export($request);
        }
        return new WP_REST_Response(['error' => 'Endpoint không sẵn sàng.'], 500);
    }

    public static function upload_pix($request) {
        if (class_exists('Pi_Api_Migrate')) {
            return Pi_Api_Migrate::upload_bundle($request);
        }
        return new WP_REST_Response(['error' => 'Endpoint không sẵn sàng.'], 500);
    }

    public static function import_pix($request) {
        if (class_exists('Pi_Api_Migrate')) {
            return Pi_Api_Migrate::import_posts($request);
        }

        $params = $request->get_json_params() ?: [];
        $path = $params['path'] ?? ($params['file_path'] ?? '');
        $post_type = $params['post_type'] ?? 'product';
        $conflict_mode = $params['conflict_mode'] ?? 'update';

        if (class_exists('Pi_Migrate_Catalog_Importer')) {
            $result = Pi_Migrate_Catalog_Importer::run($path, [
                'post_type'     => $post_type,
                'publish'       => true,
                'conflict_mode' => $conflict_mode,
            ]);
            if (is_wp_error($result)) {
                return new WP_REST_Response(['error' => $result->get_error_message()], 400);
            }
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response(['error' => 'Importer chưa sẵn sàng.'], 500);
    }

    public static function import_chunk_pix($request) {
        if (class_exists('Pi_Api_Migrate')) {
            return Pi_Api_Migrate::import_chunk($request);
        }
        return new WP_REST_Response(['error' => 'Endpoint không sẵn sàng.'], 500);
    }
}
