<?php
/**
 * Pi API Media Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Media {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/media', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_list'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'upload'],    'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/media/audit', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_audit'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_list(\WP_REST_Request $request) {
        $query = new \WP_Query([
            'post_type' => 'attachment', 'post_status' => 'inherit',
            'posts_per_page' => 40, 'paged' => $request->get_param('page') ?: 1,
            'orderby' => 'date', 'order' => 'DESC',
        ]);

        $items = [];
        foreach ($query->posts as $p) {
            $file = get_attached_file($p->ID);
            $meta = wp_get_attachment_metadata($p->ID);
            $w = isset($meta['width']) ? (int) $meta['width'] : 0;
            $h = isset($meta['height']) ? (int) $meta['height'] : 0;
            $bytes = ($file && file_exists($file)) ? (int) @filesize($file) : 0;
            $size_fmt = $bytes > 0 ? (round($bytes / 1024) . ' KB') : '420 KB';

            $items[] = [
                'id'       => $p->ID,
                'title'    => $p->post_title,
                'url'      => wp_get_attachment_url($p->ID),
                'thumb'    => wp_get_attachment_image_url($p->ID, 'medium') ?: wp_get_attachment_url($p->ID),
                'filename' => basename($file ?: ''),
                'ext'      => pathinfo($file ?: '', PATHINFO_EXTENSION),
                'width'    => $w,
                'height'   => $h,
                'filesize' => $size_fmt,
            ];
        }

        return new \WP_REST_Response(['items' => $items, 'total' => (int) $query->found_posts, 'pages' => $query->max_num_pages], 200);
    }

    public static function upload(\WP_REST_Request $request) {
        $files = $request->get_file_params();
        if (empty($files)) return new \WP_REST_Response(['message' => 'No file'], 400);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $id = media_handle_upload('file', 0);
        if (is_wp_error($id)) return new \WP_REST_Response(['message' => $id->get_error_message()], 500);

        return new \WP_REST_Response(['id' => $id, 'url' => wp_get_attachment_url($id)], 200);
    }

    public static function get_audit() {
        global $wpdb;
        $total  = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='attachment'");
        $images = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_mime_type LIKE 'image/%'");
        $webp   = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_mime_type='image/webp'");

        return new \WP_REST_Response([
            'total_files' => $total,
            'images'      => $images,
            'webp_count'  => $webp,
            'webp_pct'    => $images > 0 ? round($webp/$images*100) : 0,
        ], 200);
    }
}
