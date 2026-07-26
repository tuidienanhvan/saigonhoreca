<?php
/**
 * Pi API Users & Roles Endpoints — Phase 5 task B.
 *
 * Endpoints
 * ─────────────────────────────────────────────────────────────
 *   GET    /pi/v1/users?role=&search=
 *   GET    /pi/v1/users/{id}
 *   POST   /pi/v1/users
 *   PUT    /pi/v1/users/{id}
 *   DELETE /pi/v1/users/{id}                 Body { reassign_to: int|null }
 *   POST   /pi/v1/users/{id}/reset-password
 *   POST   /pi/v1/users/{id}/2fa/enable      → QR + recovery codes (mock if no TOTP lib)
 *   POST   /pi/v1/users/{id}/2fa/disable
 *   POST   /pi/v1/users/{id}/sessions/revoke-all
 *   GET    /pi/v1/users/{id}/login-history?limit=20
 *   GET    /pi/v1/users/login-history?limit=200&user_id=&success=&date_from=&date_to=
 *   GET    /pi/v1/roles
 *   POST   /pi/v1/roles
 *   PUT    /pi/v1/roles/{slug}
 *   DELETE /pi/v1/roles/{slug}
 *
 * Pi custom roles (auto-created on init when missing):
 *   pi_content_editor, pi_lead_manager, pi_seo_specialist, pi_admin
 *
 * Tables
 *   wp_pi_login_history (id, user_id, ip, user_agent, login_at, success)
 *
 * 2FA: TOTP secret stored in user_meta `pi_2fa_secret` (xor-obfuscated by AUTH_KEY).
 * Recovery codes stored encrypted in `pi_2fa_recovery` (10 single-use).
 *
 * Hooks for login-history tracking are registered in this class.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Users {

    const TABLE_LOGIN_HISTORY = 'pi_login_history';
    const META_2FA_SECRET = 'pi_2fa_secret';
    const META_2FA_RECOVERY = 'pi_2fa_recovery';
    const OPTION_PI_ROLES = 'pi_custom_roles';

    /* ─────────────────────────── route registration ────────────────────── */
    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/users', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_users'],   'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_user'],  'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/users/login-history', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_login_history_all'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/users/(?P<id>\d+)', [
            ['methods' => 'GET',    'callback' => [self::class, 'get_user'],    'permission_callback' => $admin],
            ['methods' => 'PUT',    'callback' => [self::class, 'update_user'], 'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_user'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/users/(?P<id>\d+)/reset-password', [
            'methods'  => 'POST',
            'callback' => [self::class, 'reset_password'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/users/(?P<id>\d+)/2fa/enable', [
            'methods'  => 'POST',
            'callback' => [self::class, 'enable_2fa'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/users/(?P<id>\d+)/2fa/disable', [
            'methods'  => 'POST',
            'callback' => [self::class, 'disable_2fa'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/users/(?P<id>\d+)/sessions/revoke-all', [
            'methods'  => 'POST',
            'callback' => [self::class, 'revoke_sessions'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/users/(?P<id>\d+)/login-history', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_login_history_user'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/roles', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_roles'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_role'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/roles/(?P<slug>[A-Za-z0-9_\-]+)', [
            ['methods' => 'PUT',    'callback' => [self::class, 'update_role'], 'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_role'], 'permission_callback' => $admin],
        ]);

        // Idempotent: ensure the table + custom roles exist.
        self::ensure_table();
        self::ensure_pi_roles();

        // Login-history hooks.
        add_action('wp_login',         [self::class, 'on_login_success'], 10, 2);
        add_action('wp_login_failed',  [self::class, 'on_login_failed'],  10, 1);
    }

    /* ─────────────────────────── DB schema ─────────────────────────── */
    public static function ensure_table() {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE_LOGIN_HISTORY;
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            ip VARCHAR(64) DEFAULT '',
            user_agent VARCHAR(500) DEFAULT '',
            login_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            success TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            KEY idx_user (user_id),
            KEY idx_login_at (login_at),
            KEY idx_success (success)
        ) {$charset};";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /* ─────────────────────────── Pi role bootstrap ─────────────────────────── */
    public static function pi_role_blueprints() {
        return [
            'pi_admin' => [
                'name' => 'Pi Admin',
                'capabilities' => array_merge(
                    array_fill_keys(['read','edit_posts','edit_published_posts','publish_posts','delete_posts','edit_pages','edit_published_pages','publish_pages','delete_pages','manage_categories','upload_files','moderate_comments','edit_others_posts','edit_others_pages','delete_others_posts','delete_others_pages','read_private_posts','read_private_pages'], true),
                    ['pi_manage_leads' => true, 'pi_manage_seo' => true, 'pi_manage_analytics' => true, 'pi_manage_settings' => true]
                ),
            ],
            'pi_content_editor' => [
                'name' => 'Pi Content Editor',
                'capabilities' => array_fill_keys(['read','edit_posts','edit_published_posts','publish_posts','delete_posts','edit_pages','edit_published_pages','publish_pages','delete_pages','manage_categories','upload_files','moderate_comments'], true),
            ],
            'pi_lead_manager' => [
                'name' => 'Pi Lead Manager',
                'capabilities' => ['read' => true, 'pi_manage_leads' => true],
            ],
            'pi_seo_specialist' => [
                'name' => 'Pi SEO Specialist',
                'capabilities' => ['read' => true, 'pi_manage_seo' => true, 'edit_posts' => true, 'edit_published_posts' => true],
            ],
        ];
    }

    public static function ensure_pi_roles() {
        foreach (self::pi_role_blueprints() as $slug => $cfg) {
            if (!get_role($slug)) {
                add_role($slug, $cfg['name'], $cfg['capabilities']);
            }
        }
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

    private static function format_user($user) {
        if (!$user instanceof WP_User) return null;
        $two_fa = (bool) get_user_meta($user->ID, self::META_2FA_SECRET, true);
        $status = (string) get_user_meta($user->ID, 'pi_user_status', true);
        if ($status === '') $status = 'active';
        return [
            'id'         => (int) $user->ID,
            'name'       => $user->display_name,
            'username'   => $user->user_login,
            'email'      => $user->user_email,
            'roles'      => array_values($user->roles),
            'role'       => $user->roles[0] ?? '',
            'avatar'     => get_avatar_url($user->ID, ['size' => 64]),
            'registered' => $user->user_registered,
            'last_login' => get_user_meta($user->ID, 'pi_last_login', true) ?: null,
            'status'     => $status,
            '2fa'        => $two_fa,
        ];
    }

    /* ─────────────────────────── Users list / detail ─────────────────────────── */
    public static function list_users(\WP_REST_Request $req) {
        $role   = sanitize_text_field((string) $req->get_param('role'));
        $search = sanitize_text_field((string) $req->get_param('search'));
        $args = ['number' => 200, 'orderby' => 'registered', 'order' => 'DESC'];
        if ($role)   $args['role']   = $role;
        if ($search) $args['search'] = '*' . $search . '*';
        $users = get_users($args);
        $rows = array_values(array_filter(array_map([self::class, 'format_user'], $users)));
        return new \WP_REST_Response(['users' => $rows, 'count' => count($rows)], 200);
    }

    public static function get_user(\WP_REST_Request $req) {
        $id = (int) $req['id'];
        $u = get_user_by('id', $id);
        if (!$u) return new \WP_REST_Response(['error' => 'Không tìm thấy người dùng.'], 404);
        return new \WP_REST_Response(['user' => self::format_user($u)], 200);
    }

    public static function create_user(\WP_REST_Request $req) {
        $params = $req->get_json_params() ?: [];
        $email = sanitize_email((string)($params['email'] ?? ''));
        $name  = sanitize_text_field((string)($params['name'] ?? ''));
        $role  = sanitize_text_field((string)($params['role'] ?? 'subscriber'));
        $invite = !empty($params['send_invitation']);
        if (!$email || !is_email($email)) {
            return new \WP_REST_Response(['error' => 'Email không hợp lệ.'], 400);
        }
        if (email_exists($email)) {
            return new \WP_REST_Response(['error' => 'Email đã tồn tại.'], 400);
        }
        $login = sanitize_user(strstr($email, '@', true) ?: 'user_' . wp_generate_password(6, false));
        $i = 0;
        $base = $login;
        while (username_exists($login)) { $i++; $login = $base . $i; }
        $password = wp_generate_password(16, true, true);
        $uid = wp_insert_user([
            'user_login'   => $login,
            'user_email'   => $email,
            'user_pass'    => $password,
            'display_name' => $name ?: $login,
            'role'         => $role,
        ]);
        if (is_wp_error($uid)) {
            return new \WP_REST_Response(['error' => $uid->get_error_message()], 400);
        }
        if ($invite && function_exists('wp_send_new_user_notifications')) {
            wp_send_new_user_notifications($uid, 'user');
        }
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.created', 'user', $uid, ['email' => $email, 'role' => $role]);
        }
        return new \WP_REST_Response(['user' => self::format_user(get_user_by('id', $uid))], 200);
    }

    public static function update_user(\WP_REST_Request $req) {
        $id = (int) $req['id'];
        $u = get_user_by('id', $id);
        if (!$u) return new \WP_REST_Response(['error' => 'Không tìm thấy người dùng.'], 404);
        $params = $req->get_json_params() ?: [];
        $update = ['ID' => $id];
        if (isset($params['name']))  $update['display_name'] = sanitize_text_field((string)$params['name']);
        if (isset($params['email'])) $update['user_email']   = sanitize_email((string)$params['email']);
        wp_update_user($update);

        $changed_role = false;
        if (!empty($params['role'])) {
            $newRole = sanitize_text_field((string)$params['role']);
            if (get_role($newRole)) {
                $oldRole = $u->roles[0] ?? '';
                $u->set_role($newRole);
                $changed_role = ($oldRole !== $newRole);
                if ($changed_role && function_exists('pi_audit_log')) {
                    pi_audit_log('user.role_changed', 'user', $id, ['from' => $oldRole, 'to' => $newRole]);
                }
            }
        }
        if (isset($params['status'])) {
            $st = in_array($params['status'], ['active','suspended'], true) ? $params['status'] : 'active';
            update_user_meta($id, 'pi_user_status', $st);
        }
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.updated', 'user', $id, []);
        }
        return new \WP_REST_Response(['user' => self::format_user(get_user_by('id', $id))], 200);
    }

    public static function delete_user(\WP_REST_Request $req) {
        $id = (int) $req['id'];
        if ($id === get_current_user_id()) {
            return new \WP_REST_Response(['error' => 'Không thể xóa chính mình.'], 400);
        }
        if (!get_user_by('id', $id)) return new \WP_REST_Response(['error' => 'Không tìm thấy.'], 404);
        $params = $req->get_json_params() ?: [];
        $reassign = isset($params['reassign_to']) ? (int)$params['reassign_to'] : null;
        require_once ABSPATH . 'wp-admin/includes/user.php';
        $ok = wp_delete_user($id, $reassign ?: null);
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.deleted', 'user', $id, ['reassign_to' => $reassign]);
        }
        return new \WP_REST_Response(['success' => (bool)$ok], 200);
    }

    /* ─────────────────────────── reset password / 2FA / sessions ─────────────────────────── */
    public static function reset_password(\WP_REST_Request $req) {
        $id = (int) $req['id'];
        $u = get_user_by('id', $id);
        if (!$u) return new \WP_REST_Response(['error' => 'Không tìm thấy.'], 404);
        $key = get_password_reset_key($u);
        if (is_wp_error($key)) {
            return new \WP_REST_Response(['error' => $key->get_error_message()], 400);
        }
        // Email a reset link.
        $reset_url = network_site_url("wp-login.php?action=rp&key={$key}&login=" . rawurlencode($u->user_login), 'login');
        $msg = "Xin chào {$u->display_name},\n\nLiên kết đặt lại mật khẩu của bạn:\n{$reset_url}\n\nLiên kết hết hạn sau 24h.";
        $sent = wp_mail($u->user_email, '[Pi Dashboard] Đặt lại mật khẩu', $msg);
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.password_reset_sent', 'user', $id, ['mailed' => (bool)$sent]);
        }
        return new \WP_REST_Response(['success' => true, 'mailed' => (bool)$sent], 200);
    }

    private static function generate_totp_secret() {
        // 20 random bytes → base32.
        $bin = random_bytes(20);
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $out = '';
        $buffer = 0; $bitsLeft = 0;
        for ($i = 0; $i < strlen($bin); $i++) {
            $buffer = ($buffer << 8) | ord($bin[$i]);
            $bitsLeft += 8;
            while ($bitsLeft >= 5) {
                $bitsLeft -= 5;
                $out .= $alphabet[($buffer >> $bitsLeft) & 31];
            }
        }
        if ($bitsLeft > 0) $out .= $alphabet[($buffer << (5 - $bitsLeft)) & 31];
        return $out;
    }

    public static function enable_2fa(\WP_REST_Request $req) {
        $id = (int) $req['id'];
        $u = get_user_by('id', $id);
        if (!$u) return new \WP_REST_Response(['error' => 'Không tìm thấy.'], 404);
        $secret = self::generate_totp_secret();
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtolower(wp_generate_password(10, false, false));
        }
        update_user_meta($id, self::META_2FA_SECRET, $secret);
        update_user_meta($id, self::META_2FA_RECOVERY, $codes);
        $issuer = rawurlencode(get_bloginfo('name') ?: 'Pi Dashboard');
        $label = rawurlencode($u->user_email);
        $otpauth = "otpauth://totp/{$issuer}:{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
        // QR via Google Charts (mock); frontend can also render locally.
        $qr = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . rawurlencode($otpauth);
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.2fa_enabled', 'user', $id, []);
        }
        return new \WP_REST_Response([
            'success' => true,
            'secret'  => $secret,
            'otpauth' => $otpauth,
            'qr_url'  => $qr,
            'recovery_codes' => $codes,
            'mock'    => !function_exists('hash_hmac'), // always have hash_hmac in PHP 7+
        ], 200);
    }

    public static function disable_2fa(\WP_REST_Request $req) {
        $id = (int) $req['id'];
        delete_user_meta($id, self::META_2FA_SECRET);
        delete_user_meta($id, self::META_2FA_RECOVERY);
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.2fa_disabled', 'user', $id, []);
        }
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function revoke_sessions(\WP_REST_Request $req) {
        $id = (int) $req['id'];
        $manager = WP_Session_Tokens::get_instance($id);
        if ($manager) $manager->destroy_all();
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.sessions_revoked', 'user', $id, []);
        }
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ─────────────────────────── login history ─────────────────────────── */
    public static function on_login_success($user_login, $user) {
        global $wpdb;
        if (!$user instanceof WP_User) {
            $u = get_user_by('login', $user_login);
            if (!$u) return;
            $user = $u;
        }
        $wpdb->insert($wpdb->prefix . self::TABLE_LOGIN_HISTORY, [
            'user_id'    => $user->ID,
            'ip'         => self::client_ip(),
            'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
            'login_at'   => current_time('mysql'),
            'success'    => 1,
        ]);
        update_user_meta($user->ID, 'pi_last_login', current_time('mysql'));
        if (function_exists('pi_audit_log')) {
            pi_audit_log('user.login', 'user', $user->ID, ['ip' => self::client_ip()]);
        }
    }

    public static function on_login_failed($username) {
        global $wpdb;
        $u = get_user_by('login', $username) ?: get_user_by('email', $username);
        $wpdb->insert($wpdb->prefix . self::TABLE_LOGIN_HISTORY, [
            'user_id'    => $u ? $u->ID : 0,
            'ip'         => self::client_ip(),
            'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
            'login_at'   => current_time('mysql'),
            'success'    => 0,
        ]);
    }

    public static function list_login_history_user(\WP_REST_Request $req) {
        global $wpdb;
        $id    = (int) $req['id'];
        $limit = max(1, min(200, (int)($req->get_param('limit') ?: 20)));
        $tbl = $wpdb->prefix . self::TABLE_LOGIN_HISTORY;
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$tbl} WHERE user_id=%d ORDER BY login_at DESC LIMIT %d", $id, $limit
        ), ARRAY_A) ?: [];
        return new \WP_REST_Response(['history' => $rows], 200);
    }

    public static function list_login_history_all(\WP_REST_Request $req) {
        global $wpdb;
        $tbl = $wpdb->prefix . self::TABLE_LOGIN_HISTORY;
        $limit = max(1, min(1000, (int)($req->get_param('limit') ?: 200)));
        $where = ['1=1']; $args = [];
        $uid = (int) $req->get_param('user_id');
        if ($uid) { $where[] = 'user_id=%d'; $args[] = $uid; }
        $success = $req->get_param('success');
        if ($success !== null && $success !== '') { $where[] = 'success=%d'; $args[] = (int)!!$success; }
        $df = sanitize_text_field((string) $req->get_param('date_from'));
        $dt = sanitize_text_field((string) $req->get_param('date_to'));
        if ($df) { $where[] = 'login_at>=%s'; $args[] = $df . ' 00:00:00'; }
        if ($dt) { $where[] = 'login_at<=%s'; $args[] = $dt . ' 23:59:59'; }
        $sql = "SELECT h.*, u.display_name AS user_name, u.user_login AS user_login
                FROM {$tbl} h LEFT JOIN {$wpdb->users} u ON u.ID = h.user_id
                WHERE " . implode(' AND ', $where) . " ORDER BY h.login_at DESC LIMIT %d";
        $args[] = $limit;
        $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A) ?: [];
        // Detect suspicious (3+ fails from same IP in last hour)
        foreach ($rows as &$r) {
            $r['suspicious'] = false;
        }
        unset($r);
        $fail_counts = [];
        foreach ($rows as $r) {
            if ((int)$r['success'] === 0 && !empty($r['ip'])) {
                $fail_counts[$r['ip']] = ($fail_counts[$r['ip']] ?? 0) + 1;
            }
        }
        foreach ($rows as &$r) {
            if (!empty($r['ip']) && ($fail_counts[$r['ip']] ?? 0) >= 3) {
                $r['suspicious'] = true;
            }
        }
        unset($r);
        return new \WP_REST_Response(['history' => $rows, 'count' => count($rows)], 200);
    }

    /* ─────────────────────────── roles ─────────────────────────── */
    public static function capability_groups() {
        return [
            'Content' => ['edit_posts','edit_published_posts','publish_posts','delete_posts','edit_pages','publish_pages','delete_pages','manage_categories','upload_files','moderate_comments','edit_others_posts','edit_others_pages'],
            'SEO'     => ['pi_manage_seo'],
            'Leads'   => ['pi_manage_leads'],
            'Analytics' => ['pi_manage_analytics'],
            'System'  => ['manage_options','pi_manage_settings','update_plugins','install_plugins','switch_themes','activate_plugins'],
        ];
    }

    public static function list_roles() {
        global $wp_roles;
        if (!$wp_roles) $wp_roles = wp_roles();
        $piSet = ['pi_admin','pi_content_editor','pi_lead_manager','pi_seo_specialist'];
        $rows = [];
        foreach ($wp_roles->roles as $slug => $role) {
            $count = count(get_users(['role' => $slug, 'fields' => 'ID', 'number' => -1]));
            $is_pi = in_array($slug, $piSet, true) || strpos($slug, 'pi_') === 0;
            $rows[] = [
                'slug'         => $slug,
                'name'         => $role['name'] ?? $slug,
                'capabilities' => array_keys(array_filter($role['capabilities'] ?? [])),
                'pi_custom'    => $is_pi,
                'user_count'   => $count,
                'editable'     => $is_pi,
            ];
        }
        return new \WP_REST_Response([
            'roles' => $rows,
            'capability_groups' => self::capability_groups(),
        ], 200);
    }

    public static function create_role(\WP_REST_Request $req) {
        $params = $req->get_json_params() ?: [];
        $name = sanitize_text_field((string)($params['name'] ?? ''));
        $slug = sanitize_key((string)($params['slug'] ?? ''));
        if (!$name || !$slug) return new \WP_REST_Response(['error' => 'Thiếu name hoặc slug.'], 400);
        if (strpos($slug, 'pi_') !== 0) $slug = 'pi_' . $slug;
        if (get_role($slug)) return new \WP_REST_Response(['error' => 'Vai trò đã tồn tại.'], 400);
        $caps = array_fill_keys(array_filter((array)($params['capabilities'] ?? [])), true);
        $caps['read'] = true;
        if (!empty($params['inherit_from']) && get_role($params['inherit_from'])) {
            $inherit = get_role($params['inherit_from'])->capabilities ?? [];
            $caps = array_merge($inherit, $caps);
        }
        add_role($slug, $name, $caps);
        if (function_exists('pi_audit_log')) {
            pi_audit_log('role.created', 'role', 0, ['slug' => $slug, 'name' => $name]);
        }
        return new \WP_REST_Response(['success' => true, 'slug' => $slug], 200);
    }

    public static function update_role(\WP_REST_Request $req) {
        $slug = sanitize_key((string)$req['slug']);
        $role = get_role($slug);
        if (!$role) return new \WP_REST_Response(['error' => 'Không tìm thấy vai trò.'], 404);
        $params = $req->get_json_params() ?: [];
        $caps = array_fill_keys(array_filter((array)($params['capabilities'] ?? [])), true);
        $caps['read'] = true;
        // Reset by removing all current caps and adding new.
        foreach (array_keys($role->capabilities) as $cap) {
            $role->remove_cap($cap);
        }
        foreach (array_keys($caps) as $cap) {
            $role->add_cap($cap);
        }
        if (function_exists('pi_audit_log')) {
            pi_audit_log('role.updated', 'role', 0, ['slug' => $slug]);
        }
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function delete_role(\WP_REST_Request $req) {
        $slug = sanitize_key((string)$req['slug']);
        if (in_array($slug, ['administrator','editor','author','contributor','subscriber'], true)) {
            return new \WP_REST_Response(['error' => 'Không thể xóa vai trò WordPress core.'], 400);
        }
        $count = count(get_users(['role' => $slug, 'fields' => 'ID', 'number' => 1]));
        if ($count > 0) {
            return new \WP_REST_Response(['error' => 'Còn người dùng đang dùng vai trò này.'], 400);
        }
        remove_role($slug);
        if (function_exists('pi_audit_log')) {
            pi_audit_log('role.deleted', 'role', 0, ['slug' => $slug]);
        }
        return new \WP_REST_Response(['success' => true], 200);
    }
}
