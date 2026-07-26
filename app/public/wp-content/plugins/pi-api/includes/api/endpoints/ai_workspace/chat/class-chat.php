<?php
/**
 * Pi API Chat Logs Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Chat {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/chat/logs', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_logs'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_logs(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'sh_chat_logs';

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
            return new \WP_REST_Response(['items' => [], 'total' => 0], 200);
        }

        $page     = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = 25;
        $offset   = ($page - 1) * $per_page;
        $safe_table = esc_sql($table);

        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $safe_table . "` ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset));
        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM `" . $safe_table . "`");

        return new \WP_REST_Response(['items' => $rows, 'total' => $total], 200);
    }
}
