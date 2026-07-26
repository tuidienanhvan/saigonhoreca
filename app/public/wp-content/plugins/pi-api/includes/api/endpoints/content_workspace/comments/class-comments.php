<?php
/**
 * Pi API Comments Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Comments {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/comments', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_list'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/comments/moderate', [
            'methods'  => 'POST',
            'callback' => [self::class, 'moderate'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/comments/reply', [
            'methods'  => 'POST',
            'callback' => [self::class, 'reply'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_list(\WP_REST_Request $request) {
        $status   = $request->get_param('status') ?: 'all';
        $search   = $request->get_param('search') ?: '';
        $page     = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = min(100, max(1, (int) ($request->get_param('per_page') ?: 25)));

        $args = [
            'number'  => $per_page,
            'offset'  => ($page - 1) * $per_page,
            'orderby' => 'comment_date',
            'order'   => 'DESC',
        ];

        $status_map = ['approved' => 'approve', 'pending' => 'hold', 'spam' => 'spam', 'trash' => 'trash'];
        $args['status'] = $status_map[$status] ?? $status;

        if (!empty($search)) $args['search'] = $search;

        $comments = get_comments($args);
        $counts   = wp_count_comments();

        $items = [];
        foreach ($comments as $c) {
            $items[] = [
                'id'          => (int) $c->comment_ID,
                'author'      => $c->comment_author,
                'author_email'=> $c->comment_author_email,
                'avatar'      => get_avatar_url($c->comment_author_email, ['size' => 48]),
                'content'     => $c->comment_content,
                'date'        => $c->comment_date,
                'status'      => wp_get_comment_status($c),
                'post_id'     => (int) $c->comment_post_ID,
                'post_title'  => get_the_title($c->comment_post_ID),
            ];
        }

        return new \WP_REST_Response([
            'items'  => $items,
            'total'  => (int) ($counts->total_comments ?? 0),
            'counts' => [
                'all'      => (int) ($counts->total_comments ?? 0),
                'approved' => (int) ($counts->approved ?? 0),
                'pending'  => (int) ($counts->moderated ?? 0),
                'spam'     => (int) ($counts->spam ?? 0),
                'trash'    => (int) ($counts->trash ?? 0),
            ],
        ], 200);
    }

    public static function moderate(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        $id     = (int) ($params['id'] ?? 0);
        $status = $params['status'] ?? '';

        if (!$id || !get_comment($id)) return new \WP_REST_Response(['message' => 'Comment not found'], 404);

        if ($status === 'delete') {
            // Single-call permanent purge bypassed WP's trash entirely — every
            // other destructive action in this plugin (e.g. category-merge)
            // requires an explicit confirm flag before doing anything
            // irreversible. Default to trash (force=false); only force-delete
            // when the caller explicitly confirms.
            $force = !empty($params['confirm']);
            wp_delete_comment($id, $force);
            return new \WP_REST_Response(['success' => true, 'permanently_deleted' => $force], 200);
        }

        $wp_map = ['approve' => '1', 'hold' => '0', 'spam' => 'spam', 'trash' => 'trash'];
        if (!isset($wp_map[$status])) return new \WP_REST_Response(['message' => 'Invalid status'], 400);

        wp_set_comment_status($id, $wp_map[$status]);
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function bulk(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        $ids    = array_map('intval', (array) ($params['ids'] ?? []));
        $action = $params['action'] ?? '';
        $force  = !empty($params['confirm']); // same trash-by-default guard as moderate()

        if (empty($ids)) return new \WP_REST_Response(['message' => 'No IDs'], 400);

        $wp_map = ['approve' => '1', 'hold' => '0', 'spam' => 'spam', 'trash' => 'trash'];
        $done = 0;

        foreach ($ids as $id) {
            if (!get_comment($id)) continue;
            if ($action === 'delete') {
                if (wp_delete_comment($id, $force)) $done++;
            } elseif (isset($wp_map[$action])) {
                if (wp_set_comment_status($id, $wp_map[$action])) $done++;
            }
        }

        return new \WP_REST_Response(['success' => true, 'processed' => $done], 200);
    }

    public static function reply(\WP_REST_Request $request) {
        $params    = $request->get_json_params();
        $parent_id = (int) ($params['parent_id'] ?? 0);
        $content   = wp_kses_post(trim($params['content'] ?? ''));

        $parent = get_comment($parent_id);
        if (!$parent) return new \WP_REST_Response(['message' => 'Parent comment not found'], 404);

        $user = wp_get_current_user();
        $new_id = wp_new_comment([
            'comment_post_ID'      => $parent->comment_post_ID,
            'comment_parent'       => $parent_id,
            'comment_content'      => $content,
            'comment_author'       => $user->display_name,
            'comment_author_email' => $user->user_email,
            'user_id'              => $user->ID,
            'comment_approved'     => 1,
        ], true);

        return new \WP_REST_Response(['success' => true, 'id' => $new_id], 200);
    }
}
