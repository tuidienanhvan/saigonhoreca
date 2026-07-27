<?php
/**
 * Pi API Audit Log Endpoints — Phase 5 task B.
 *
 * Endpoints
 * ─────────────────────────────────────────────────────────────
 *   GET    /pi/v1/audit-log?user_id=&action=&date_from=&date_to=&limit=&page=&search=&resource_type=
 *   GET    /pi/v1/audit-log/actions          List unique action types (for dropdown)
 *   GET    /pi/v1/audit-log/stats            Stats card (today total, top users, top actions)
 *   POST   /pi/v1/audit-log/export           Body { filter } → returns CSV download URL
 *   DELETE /pi/v1/audit-log/older-than?days=90
 *
 * Schema
 * ─────────────────────────────────────────────────────────────
 *   wp_pi_audit_log (id, user_id, action, resource_type, resource_id,
 *                    ip, user_agent, data JSON, created_at)
 *
 * Helper (exposed globally)
 *   pi_audit_log($action, $resource_type = '', $resource_id = 0, $data = [])
 *
 * Hooks: registered on register_routes() so they fire alongside REST init.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_AuditLog {

    const TABLE = 'pi_audit_log';

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/audit-log', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_logs'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/audit-log/actions', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_actions'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/audit-log/stats', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_stats'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/audit-log/export', [
            'methods'  => 'POST',
            'callback' => [self::class, 'export_csv'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/audit-log/older-than', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'purge_old'],
            'permission_callback' => $admin,
        ]);

        self::ensure_table();
        self::register_global_hooks();
    }

    /* ─────────────────────────── DB schema ─────────────────────────── */
    public static function ensure_table() {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE;
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            action VARCHAR(100) NOT NULL DEFAULT '',
            resource_type VARCHAR(50) DEFAULT '',
            resource_id BIGINT UNSIGNED DEFAULT 0,
            ip VARCHAR(64) DEFAULT '',
            user_agent VARCHAR(500) DEFAULT '',
            data LONGTEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_user (user_id),
            KEY idx_action (action),
            KEY idx_created_at (created_at)
        ) {$charset};";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /* ─────────────────────────── helpers ─────────────────────────── */
    private static function client_ip() {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = trim(explode(',', (string)$_SERVER[$k])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '';
    }

    public static function record($action, $resource_type = '', $resource_id = 0, $data = []) {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE;
        $uid = function_exists('get_current_user_id') ? get_current_user_id() : 0;
        $wpdb->insert($tbl, [
            'user_id'       => (int)$uid,
            'action'        => substr((string)$action, 0, 100),
            'resource_type' => substr((string)$resource_type, 0, 50),
            'resource_id'   => (int)$resource_id,
            'ip'            => self::client_ip(),
            'user_agent'    => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
            'data'          => is_string($data) ? $data : wp_json_encode($data),
            'created_at'    => current_time('mysql'),
        ]);
        return (int)$wpdb->insert_id;
    }

    /* ─────────────────────────── global hooks ─────────────────────────── */
    private static $hooks_registered = false;
    public static function register_global_hooks() {
        if (self::$hooks_registered) return;
        self::$hooks_registered = true;

        add_action('wp_logout', function() {
            pi_audit_log('user.logout', 'user', get_current_user_id(), []);
        });
        add_action('user_register', function($user_id) {
            pi_audit_log('user.created', 'user', $user_id, []);
        });
        add_action('delete_user', function($user_id) {
            pi_audit_log('user.deleted', 'user', $user_id, []);
        });
        add_action('set_user_role', function($user_id, $role, $old_roles) {
            pi_audit_log('user.role_changed', 'user', $user_id, ['from' => $old_roles, 'to' => $role]);
        }, 10, 3);
        add_action('activated_plugin', function($plugin) {
            pi_audit_log('plugin.activated', 'plugin', 0, ['plugin' => $plugin]);
        });
        add_action('deactivated_plugin', function($plugin) {
            pi_audit_log('plugin.deactivated', 'plugin', 0, ['plugin' => $plugin]);
        });
        add_action('switch_theme', function($new_name) {
            pi_audit_log('theme.switched', 'theme', 0, ['theme' => $new_name]);
        });
        add_action('transition_post_status', function($new_status, $old_status, $post) {
            if ($new_status === $old_status) return;
            if ($new_status === 'publish' && $old_status !== 'publish') {
                pi_audit_log('post.published', 'post', $post->ID, ['type' => $post->post_type]);
            }
            if ($new_status === 'trash') {
                pi_audit_log('post.deleted', 'post', $post->ID, ['type' => $post->post_type]);
            }
        }, 10, 3);
        add_action('updated_option', function($option) {
            // Only track Pi-prefixed options to avoid noise.
            if (strpos((string)$option, 'pi_') === 0) {
                pi_audit_log('settings.updated', 'option', 0, ['option' => $option]);
            }
        });
    }

    /* ─────────────────────────── GET /audit-log ─────────────────────────── */
    public static function list_logs(\WP_REST_Request $req) {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE;
        $limit = max(1, min(500, (int)($req->get_param('limit') ?: 100)));
        $page  = max(1, (int)($req->get_param('page') ?: 1));
        $offset = ($page - 1) * $limit;
        $where = ['1=1']; $args = [];
        if ($uid = (int)$req->get_param('user_id')) { $where[] = 'user_id=%d'; $args[] = $uid; }
        if ($a   = sanitize_text_field((string)$req->get_param('action'))) {
            // Allow comma-list of actions.
            $list = array_filter(array_map('trim', explode(',', $a)));
            if ($list) {
                $place = implode(',', array_fill(0, count($list), '%s'));
                $where[] = "action IN ({$place})";
                foreach ($list as $x) $args[] = $x;
            }
        }
        if ($rt = sanitize_text_field((string)$req->get_param('resource_type'))) {
            $where[] = 'resource_type=%s'; $args[] = $rt;
        }
        if ($df = sanitize_text_field((string)$req->get_param('date_from'))) {
            $where[] = 'created_at>=%s'; $args[] = $df . ' 00:00:00';
        }
        if ($dt = sanitize_text_field((string)$req->get_param('date_to'))) {
            $where[] = 'created_at<=%s'; $args[] = $dt . ' 23:59:59';
        }
        if ($q = sanitize_text_field((string)$req->get_param('search'))) {
            $like = '%' . $wpdb->esc_like($q) . '%';
            $where[] = '(action LIKE %s OR data LIKE %s OR ip LIKE %s)';
            $args[] = $like; $args[] = $like; $args[] = $like;
        }
        $sql = "SELECT a.*, u.display_name AS user_name, u.user_login AS user_login
                FROM {$tbl} a
                LEFT JOIN {$wpdb->users} u ON u.ID = a.user_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY a.created_at DESC LIMIT %d OFFSET %d";
        $args[] = $limit; $args[] = $offset;
        $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A) ?: [];
        // Decode JSON data
        foreach ($rows as &$r) {
            if (!empty($r['data'])) {
                $decoded = json_decode($r['data'], true);
                $r['data'] = is_array($decoded) ? $decoded : $r['data'];
            }
        }
        unset($r);

        $countSql = "SELECT COUNT(*) FROM {$tbl} a WHERE " . implode(' AND ', $where);
        $total = (int) $wpdb->get_var($args ? $wpdb->prepare($countSql, array_slice($args, 0, -2)) : $countSql);

        return new \WP_REST_Response([
            'logs'  => $rows,
            'count' => count($rows),
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'pages' => max(1, (int)ceil($total / $limit)),
        ], 200);
    }

    public static function list_actions() {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE;
        $rows = $wpdb->get_col("SELECT DISTINCT action FROM {$tbl} ORDER BY action ASC") ?: [];
        // Always include the canonical action types so the dropdown is not empty.
        $canonical = [
            'user.login','user.logout','user.created','user.deleted','user.role_changed','user.updated',
            'user.password_reset_sent','user.2fa_enabled','user.2fa_disabled','user.sessions_revoked',
            'plugin.activated','plugin.deactivated','theme.switched',
            'license.activated','license.deactivated',
            'lead.created','lead.deleted','lead.status_changed',
            'post.published','post.deleted',
            'settings.updated','backup.created','backup.restored',
            'role.created','role.updated','role.deleted',
        ];
        $merged = array_values(array_unique(array_merge($canonical, $rows)));
        sort($merged);
        return new \WP_REST_Response(['actions' => $merged], 200);
    }

    public static function get_stats() {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE;
        $today = current_time('Y-m-d');
        $todayCount = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$tbl} WHERE created_at >= %s", $today . ' 00:00:00'
        ));
        $topUsers = $wpdb->get_results(
            "SELECT a.user_id, u.display_name, COUNT(*) AS cnt
             FROM {$tbl} a LEFT JOIN {$wpdb->users} u ON u.ID = a.user_id
             WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY a.user_id ORDER BY cnt DESC LIMIT 5", ARRAY_A
        ) ?: [];
        $topActions = $wpdb->get_results(
            "SELECT action, COUNT(*) AS cnt FROM {$tbl}
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY action ORDER BY cnt DESC LIMIT 5", ARRAY_A
        ) ?: [];
        $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$tbl}");
        return new \WP_REST_Response([
            'today_count' => $todayCount,
            'total'       => $total,
            'top_users'   => $topUsers,
            'top_actions' => $topActions,
        ], 200);
    }

    /* ─────────────────────────── POST /audit-log/export ─────────────────────────── */
    public static function export_csv(\WP_REST_Request $req) {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE;
        $params = $req->get_json_params() ?: [];
        $filter = is_array($params['filter'] ?? null) ? $params['filter'] : [];
        $where = ['1=1']; $args = [];
        if (!empty($filter['user_id']))      { $where[] = 'user_id=%d'; $args[] = (int)$filter['user_id']; }
        if (!empty($filter['action']))       { $where[] = 'action=%s'; $args[] = sanitize_text_field((string)$filter['action']); }
        if (!empty($filter['date_from']))    { $where[] = 'created_at>=%s'; $args[] = sanitize_text_field((string)$filter['date_from']) . ' 00:00:00'; }
        if (!empty($filter['date_to']))      { $where[] = 'created_at<=%s'; $args[] = sanitize_text_field((string)$filter['date_to']) . ' 23:59:59'; }
        $sql = "SELECT * FROM {$tbl} WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT 10000";
        $rows = $args ? $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A) : $wpdb->get_results($sql, ARRAY_A);
        $rows = $rows ?: [];

        $upload = wp_upload_dir();
        $dir = trailingslashit($upload['basedir']) . 'pi-audit-exports';
        if (!file_exists($dir)) wp_mkdir_p($dir);
        $filename = 'audit-' . date('Ymd-His') . '-' . wp_generate_password(6, false, false) . '.csv';
        $path = $dir . '/' . $filename;
        $fh = fopen($path, 'w');
        fputcsv($fh, ['id','user_id','action','resource_type','resource_id','ip','user_agent','data','created_at']);
        foreach ($rows as $r) {
            fputcsv($fh, [
                $r['id'],$r['user_id'],$r['action'],$r['resource_type'],$r['resource_id'],
                $r['ip'],$r['user_agent'],(string)$r['data'],$r['created_at'],
            ]);
        }
        fclose($fh);
        $url = trailingslashit($upload['baseurl']) . 'pi-audit-exports/' . $filename;
        if (function_exists('pi_audit_log')) {
            pi_audit_log('audit.exported', 'audit', 0, ['rows' => count($rows), 'file' => $filename]);
        }
        return new \WP_REST_Response([
            'success' => true,
            'url'     => $url,
            'rows'    => count($rows),
            'filename'=> $filename,
        ], 200);
    }

    /* ─────────────────────────── DELETE /audit-log/older-than ─────────────────────────── */
    public static function purge_old(\WP_REST_Request $req) {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE;
        $days = max(1, (int)($req->get_param('days') ?: 90));
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$tbl} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)", $days
        ));
        if (function_exists('pi_audit_log')) {
            pi_audit_log('audit.purged', 'audit', 0, ['days' => $days, 'deleted' => (int)$deleted]);
        }
        return new \WP_REST_Response(['success' => true, 'deleted' => (int)$deleted], 200);
    }
}

/* ─────────────────────────── global helper ─────────────────────────── */
if (!function_exists('pi_audit_log')) {
    /**
     * Append an audit-log row.
     *
     * @param string $action        e.g. 'user.login'
     * @param string $resource_type e.g. 'user', 'post', 'plugin'
     * @param int    $resource_id   numeric id
     * @param array  $data          extra context
     * @return int                  new row id (0 on failure)
     */
    function pi_audit_log($action, $resource_type = '', $resource_id = 0, $data = []) {
        if (!class_exists('Pi_Api_AuditLog')) return 0;
        return Pi_Api_AuditLog::record($action, $resource_type, $resource_id, $data);
    }
}
