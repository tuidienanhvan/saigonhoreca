<?php
/**
 * Pi API Overview Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Overview {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/overview', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_stats'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_stats(\WP_REST_Request $request) {
        if ($request->get_param('force')) {
            delete_transient('sgh_dashboard_stats');
        }

        $stats = apply_filters('pi_api_overview_stats', null);
        if ($stats === null) {
            $stats = self::fallback_stats();
        }

        // Leads
        $lead_counts = function_exists('sh_get_lead_counts')
            ? sh_get_lead_counts()
            : self::direct_lead_counts();

        $chat_stats = function_exists('sh_chatlog_stats')
            ? sh_chatlog_stats()
            : ['total' => 0, 'today' => 0];

        return new \WP_REST_Response([
            'siteName'    => get_bloginfo('name'),
            'siteUrl'     => home_url(),
            'stats'       => $stats,
            'leads'       => $lead_counts,
            'chat'        => $chat_stats,
            'recentPosts' => self::get_recent_posts(),
        ], 200);
    }

    private static function fallback_stats() {
        $counts   = wp_count_posts();
        $comments = wp_count_comments();
        $att      = (array) wp_count_attachments();
        unset($att['trash']);
        return [
            'wp_posts'    => (int) ($counts->publish ?? 0),
            'wp_comments' => (int) ($comments->approved ?? 0),
            'wp_media'    => (int) array_sum($att),
            'pages'       => ['exist' => 0, 'total' => 0, 'percent' => 0],
            'categories'  => ['exist' => 0, 'total' => 0, 'percent' => 0],
        ];
    }

    private static function direct_lead_counts() {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_leads';
        $counts = ['total' => 0, 'new' => 0];

        // SQL injection guard: $table is internally constructed ($wpdb->prefix + literal)
        // but we still use prepare() for SHOW TABLES + backticks for FROM clause.
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) {
            $counts['total'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM `" . esc_sql($table) . "`");
            $counts['new']   = (int) $wpdb->get_var("SELECT COUNT(*) FROM `" . esc_sql($table) . "` WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        }

        return $counts;
    }

    private static function get_recent_posts() {
        $posts = get_posts(['numberposts' => 5, 'post_status' => 'any']);
        $res = [];
        foreach ($posts as $p) {
            $res[] = [
                'id'     => $p->ID,
                'title'  => $p->post_title,
                'status' => $p->post_status,
                'date'   => get_the_date('Y-m-d', $p),
            ];
        }
        return $res;
    }
}
