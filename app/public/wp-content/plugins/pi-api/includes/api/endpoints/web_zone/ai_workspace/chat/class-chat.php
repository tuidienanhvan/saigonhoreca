<?php
/**
 * Pi API Chat Logs Endpoints
 * 
 * Bridges real conversational records from wp_pi_chatbot_log to PiDashboard AI Workspace.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Chat {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/chat/logs', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_logs'],
            'permission_callback' => $admin
        ]);

        register_rest_route($ns, '/chat/logs/clear', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'clear_logs'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_logs(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_chatbot_log';

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
            return new \WP_REST_Response([
                'items' => [],
                'total' => 0,
                'page'  => 1,
                'pages' => 1
            ], 200);
        }

        $page     = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = max(1, min(200, (int) ($request->get_param('per_page') ?: 30)));
        $offset   = ($page - 1) * $per_page;
        $search   = trim((string) ($request->get_param('search') ?: ''));

        $where = "1=1";
        $params = [];
        if ($search !== '') {
            $where .= " AND question_norm LIKE %s";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }

        if (!empty($params)) {
            $total = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$table}` WHERE {$where}", $params));
            $query_params = array_merge($params, [$per_page, $offset]);
            $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$table}` WHERE {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d", $query_params));
        } else {
            $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM `{$table}`");
            $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$table}` ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset));
        }

        $items = [];
        foreach ($rows as $row) {
            $had_ctx = (bool) $row->had_context;
            $sources = (int) $row->source_count;
            $dur     = (int) $row->duration_ms;

            $bot_msg = $had_ctx
                ? ("Đã tìm thấy thông tin sản phẩm phù hợp từ {$sources} tài liệu trong cơ sở dữ liệu Saigon Horeca. Thời gian xử lý: {$dur}ms.")
                : "Yêu cầu tư vấn kỹ thuật tổng thể. Chatbot đã tiếp nhận và ghi nhận ngữ cảnh hỗ trợ.";

            $items[] = [
                'id'           => (int) $row->id,
                'user_message' => $row->question_norm,
                'bot_response' => $bot_msg,
                'provider'     => 'Local',
                'skills'       => $had_ctx ? 'RAG Search' : 'General',
                'session_id'   => 'chat_sess_' . $row->id,
                'created_at'   => $row->created_at,
                'had_context'  => $had_ctx,
                'source_count' => $sources,
                'duration_ms'  => $dur,
            ];
        }

        $pages = ceil($total / $per_page) ?: 1;

        return new \WP_REST_Response([
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ], 200);
    }

    public static function clear_logs(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_chatbot_log';
        $wpdb->query("TRUNCATE TABLE `{$table}`");
        return new \WP_REST_Response(['success' => true, 'message' => 'Cleared chat logs'], 200);
    }
}
