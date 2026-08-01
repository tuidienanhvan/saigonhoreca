<?php
/**
 * Pi API System Endpoints
 *
 * Phase 4 task B: extends with `/system/health/*` realtime monitoring.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_System {

    const HEALTH_HISTORY_OPTION = 'pi_system_health_history';
    const HEALTH_THRESHOLDS_OPTION = 'pi_system_health_thresholds';
    const HEALTH_HISTORY_MAX = 168; // 7 days hourly
    const HEALTH_CRON_HOOK = 'pi_system_health_snapshot';

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/system', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_status'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/settings', [
            'methods'  => 'POST',
            'callback' => [self::class, 'update_settings'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/security-policy', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_security_policy'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/security-policy', [
            'methods'  => 'PUT',
            'callback' => [self::class, 'update_security_policy'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/sync-permalinks', [
            'methods'  => 'POST',
            'callback' => [self::class, 'sync_permalinks'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/deploy', [
            'methods'  => 'POST',
            'callback' => [self::class, 'handle_deploy'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/theme-upload', [
            'methods'  => 'POST',
            'callback' => [self::class, 'upload_theme'],
            'permission_callback' => $admin
        ]);

        // ── Phase 4 task B — Health endpoints ──
        register_rest_route($ns, '/system/health', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_health'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/health/realtime', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_health_realtime'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/health/history', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_health_history'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/health/alert-config', [
            'methods'  => 'POST',
            'callback' => [self::class, 'set_alert_config'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/health/alert-config', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_alert_config'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/health/alerts', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_active_alerts'],
            'permission_callback' => $admin
        ]);

        // ── Cron lifecycle hook (idempotent) ──
        if (!wp_next_scheduled(self::HEALTH_CRON_HOOK)) {
            wp_schedule_event(time() + 60, 'hourly', self::HEALTH_CRON_HOOK);
        }
        add_action(self::HEALTH_CRON_HOOK, [self::class, 'cron_snapshot']);
    }

    public static function get_status() {
        global $wpdb;
        $current_pl  = (string) get_option('permalink_structure', '');
        $expected_pl = class_exists('\\PiDashboardV2\\SiteStructure') ? \PiDashboardV2\SiteStructure::getPermalinkStructure() : '';

        $deploy_status = [
            'htaccess' => file_exists(ABSPATH . '.htaccess'),
            'robots'   => file_exists(ABSPATH . 'robots.txt'),
            'og_image' => (bool) apply_filters('pi_api_theme_og_image_exists', false),
        ];

        return new \WP_REST_Response([
            'environment' => [
                'wp_version'  => get_bloginfo('version'),
                'php_version' => PHP_VERSION,
                'db_version'  => $wpdb->db_version(),
                'server'      => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            ],
            'theme' => [
                'name'    => wp_get_theme()->get('Name'),
                'version' => wp_get_theme()->get('Version'),
                'author'  => wp_get_theme()->get('Author'),
            ],
            'permalinks' => [
                'current'  => $current_pl,
                'expected' => $expected_pl,
                'is_match' => ($current_pl === $expected_pl),
            ],
            'deploy_status' => $deploy_status,
        ], 200);
    }

    public static function update_settings(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (isset($p['permalink_structure'])) {
            update_option('permalink_structure', $p['permalink_structure']);
            flush_rewrite_rules();
        }
        return new \WP_REST_Response(['success' => true], 200);
    }

    const SECURITY_POLICY_OPTION = 'pi_security_policy';
    const SECURITY_POLICY_DEFAULTS = [
        'jwt_expiry_hours'        => 24,
        'inactive_timeout_mins'   => 30,
        'max_concurrent_sessions' => 3,
        'enforce_2fa'             => true,
        'max_failed_attempts'     => 5,
        'lockout_duration_mins'   => 15,
        'min_password_length'     => 12,
        'require_numbers'         => true,
        'require_symbols'         => true,
        'require_mixed_case'      => true,
        'password_rotate_days'    => 90,
    ];

    public static function get_security_policy() {
        $saved = get_option(self::SECURITY_POLICY_OPTION, []);
        if (!is_array($saved)) $saved = [];
        return new \WP_REST_Response(array_merge(self::SECURITY_POLICY_DEFAULTS, $saved), 200);
    }

    public static function update_security_policy(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (!is_array($p)) $p = [];

        $policy = [];
        foreach (self::SECURITY_POLICY_DEFAULTS as $key => $default) {
            if (!array_key_exists($key, $p)) { $policy[$key] = $default; continue; }
            $policy[$key] = is_bool($default) ? (bool) $p[$key] : (int) $p[$key];
        }

        update_option(self::SECURITY_POLICY_OPTION, $policy, false);
        if (function_exists('pi_audit_log')) {
            pi_audit_log('security_policy.updated', 'system', 0, $policy);
        }

        return new \WP_REST_Response(array_merge(['success' => true], $policy), 200);
    }

    public static function sync_permalinks(\WP_REST_Request $request) {
        if (!class_exists('\\PiDashboardV2\\SiteSync')) return new \WP_REST_Response(['message' => 'SiteSync not available'], 400);
        $p = $request->get_json_params();
        return new \WP_REST_Response(\PiDashboardV2\SiteSync::syncPermalinkStructure(['dry_run' => !empty($p['dry_run'])]), 200);
    }

    public static function handle_deploy(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        $result = apply_filters('pi_api_deploy_action_result', null, $p['action'] ?? '');
        if ($result === null) {
            return new \WP_REST_Response(['message' => 'Deploy action provider not available'], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function upload_theme(\WP_REST_Request $request) {
        $files = $request->get_file_params();
        if (empty($files['theme_zip'])) return new \WP_REST_Response(['message' => 'No ZIP file uploaded.'], 400);

        require_once ABSPATH . 'wp-admin/includes/file.php';
        $uploaded = $files['theme_zip'];

        $dest = get_theme_root() . '/' . basename($uploaded['name']);
        if (!move_uploaded_file($uploaded['tmp_name'], $dest)) {
            return new \WP_REST_Response(['message' => 'Failed to move uploaded file.'], 500);
        }

        $zip = new \ZipArchive();
        if ($zip->open($dest) === TRUE) {
            $zip->extractTo(get_theme_root());
            $zip->close();
            unlink($dest);

            if (class_exists('SGH_Nano_Cache')) {
                $nano = new \SGH_Nano_Cache();
                $nano->flush_all_nano_cache();
            }
            if (function_exists('wp_cache_flush')) wp_cache_flush();

            return new \WP_REST_Response(['success' => true, 'message' => 'Theme updated successfully.'], 200);
        }

        return new \WP_REST_Response(['message' => 'Failed to unzip theme.'], 500);
    }

    /* ════════════════════════════════════════════
       PHASE 4 TASK B — Health endpoints
       ════════════════════════════════════════════ */

    /**
     * Comprehensive health snapshot.
     */
    public static function get_health() {
        global $wpdb;
        $upload = wp_upload_dir();
        $disk_path = $upload['basedir'] ?? ABSPATH;

        $disk_free = @disk_free_space($disk_path) ?: 0;
        $disk_total = @disk_total_space($disk_path) ?: 0;

        // MySQL stats
        $max_packet = (int) ($wpdb->get_var("SELECT @@max_allowed_packet") ?: 0);
        $max_connections = (int) ($wpdb->get_var("SELECT @@max_connections") ?: 0);
        $threads = (int) ($wpdb->get_var("SHOW STATUS LIKE 'Threads_connected'") ?: 0);
        $threads_var = $wpdb->get_row("SHOW STATUS LIKE 'Threads_connected'", ARRAY_A);
        $threads = isset($threads_var['Value']) ? (int) $threads_var['Value'] : 0;

        // Active plugins
        $active_plugins = (array) get_option('active_plugins', []);

        // Recent error count (parse last debug.log if present, last 24h entries)
        $error_count = self::count_recent_errors(24);

        $memory_limit_bytes = self::parse_size(ini_get('memory_limit'));
        $memory_used = memory_get_usage(true);

        return new \WP_REST_Response([
            'php_version'         => PHP_VERSION,
            'wp_version'          => get_bloginfo('version'),
            'mysql_version'       => $wpdb->db_version(),
            'memory_limit'        => ini_get('memory_limit'),
            'memory_limit_bytes'  => $memory_limit_bytes,
            'memory_used_bytes'   => $memory_used,
            'memory_used_percent' => $memory_limit_bytes > 0 ? round(($memory_used / $memory_limit_bytes) * 100, 1) : 0,
            'max_execution_time'  => (int) ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size'       => ini_get('post_max_size'),
            'disk_free'           => $disk_free,
            'disk_total'          => $disk_total,
            'disk_used_percent'   => $disk_total > 0 ? round((($disk_total - $disk_free) / $disk_total) * 100, 1) : 0,
            'mysql_max_packet'    => $max_packet,
            'mysql_max_connections' => $max_connections,
            'mysql_threads_connected' => $threads,
            'recent_error_count'  => $error_count,
            'active_plugins_count' => count($active_plugins),
            'server_software'     => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'timestamp'           => current_time('mysql'),
        ], 200);
    }

    /**
     * Quick realtime metrics (5-min window).
     */
    public static function get_health_realtime() {
        $memory_limit = self::parse_size(ini_get('memory_limit'));
        $memory_used = memory_get_usage(true);

        // Estimate CPU via load average if available
        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
        $cpu_estimate = isset($load[0]) ? round($load[0] * 100 / max(1, self::cpu_cores()), 1) : 0;
        $cpu_estimate = min(100, max(0, $cpu_estimate));

        // Active sessions: rough count via WP transients/users online
        global $wpdb;
        $active_sessions = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key = 'session_tokens'"
        );

        return new \WP_REST_Response([
            'cpu_percent'         => $cpu_estimate,
            'memory_used_bytes'   => $memory_used,
            'memory_used_percent' => $memory_limit > 0 ? round(($memory_used / $memory_limit) * 100, 1) : 0,
            'active_sessions'     => $active_sessions,
            'recent_errors_5min'  => self::count_recent_errors(0.083), // ~5 min
            'load_average'        => $load,
            'timestamp'           => time(),
        ], 200);
    }

    /**
     * Time-series history of health snapshots.
     */
    public static function get_health_history(\WP_REST_Request $request) {
        $hours = max(1, min(168, (int) $request->get_param('hours') ?: 24));
        $history = (array) get_option(self::HEALTH_HISTORY_OPTION, []);
        $cutoff = time() - ($hours * 3600);
        $filtered = array_values(array_filter($history, fn($s) => ($s['timestamp'] ?? 0) >= $cutoff));

        return new \WP_REST_Response([
            'hours'    => $hours,
            'count'    => count($filtered),
            'snapshots' => $filtered,
        ], 200);
    }

    /**
     * Set alert thresholds + email recipient.
     */
    public static function set_alert_config(\WP_REST_Request $request) {
        $p = $request->get_json_params() ?: [];
        $config = [
            'memory_threshold' => max(0, min(100, (float) ($p['memory_threshold'] ?? 85))),
            'disk_threshold'   => max(0, min(100, (float) ($p['disk_threshold'] ?? 90))),
            'errors_per_min'   => max(0, (int) ($p['errors_per_min'] ?? 10)),
            'cpu_threshold'    => max(0, min(100, (float) ($p['cpu_threshold'] ?? 90))),
            'email_recipient'  => sanitize_email($p['email_recipient'] ?? get_option('admin_email')),
            'enabled'          => !empty($p['enabled']),
        ];
        update_option(self::HEALTH_THRESHOLDS_OPTION, $config);
        return new \WP_REST_Response(['success' => true, 'config' => $config], 200);
    }

    public static function get_alert_config() {
        return new \WP_REST_Response(self::load_alert_config(), 200);
    }

    /**
     * Active firing alerts.
     */
    public static function get_active_alerts() {
        $config = self::load_alert_config();
        $health = json_decode(wp_json_encode(self::get_health()->get_data()), true);
        $alerts = self::evaluate_thresholds($health, $config);
        return new \WP_REST_Response([
            'alerts' => $alerts,
            'config' => $config,
            'evaluated_at' => time(),
        ], 200);
    }

    /**
     * Hourly cron: snapshot + threshold check + email alert.
     */
    public static function cron_snapshot() {
        $health = self::get_health()->get_data();
        $snapshot = [
            'timestamp'           => time(),
            'memory_used_percent' => $health['memory_used_percent'] ?? 0,
            'disk_used_percent'   => $health['disk_used_percent'] ?? 0,
            'recent_error_count'  => $health['recent_error_count'] ?? 0,
            'cpu_percent'         => self::cpu_estimate_now(),
            'active_plugins'      => $health['active_plugins_count'] ?? 0,
        ];
        $history = (array) get_option(self::HEALTH_HISTORY_OPTION, []);
        $history[] = $snapshot;
        if (count($history) > self::HEALTH_HISTORY_MAX) {
            $history = array_slice($history, -self::HEALTH_HISTORY_MAX);
        }
        update_option(self::HEALTH_HISTORY_OPTION, $history, false);

        // Threshold check + alert
        $config = self::load_alert_config();
        if (!empty($config['enabled'])) {
            $alerts = self::evaluate_thresholds($health, $config);
            $critical = array_filter($alerts, fn($a) => $a['severity'] === 'critical');
            if (!empty($critical) && !empty($config['email_recipient'])) {
                self::send_alert_email($config['email_recipient'], $critical);
            }
        }
    }

    /* ── Helpers ── */

    public static function load_alert_config() {
        $defaults = [
            'memory_threshold' => 85.0,
            'disk_threshold'   => 90.0,
            'cpu_threshold'    => 90.0,
            'errors_per_min'   => 10,
            'email_recipient'  => get_option('admin_email'),
            'enabled'          => false,
        ];
        return wp_parse_args((array) get_option(self::HEALTH_THRESHOLDS_OPTION, []), $defaults);
    }

    /**
     * Pure threshold evaluator — testable.
     *
     * @param array $health  Health snapshot (memory_used_percent, disk_used_percent, recent_error_count, cpu_percent).
     * @param array $config  Thresholds config.
     * @return array<int,array{key:string,severity:string,message:string,value:float,threshold:float}>
     */
    public static function evaluate_thresholds(array $health, array $config) {
        $alerts = [];

        $checks = [
            ['key' => 'memory', 'value' => (float) ($health['memory_used_percent'] ?? 0), 'threshold' => (float) ($config['memory_threshold'] ?? 85), 'label' => 'Bộ nhớ'],
            ['key' => 'disk',   'value' => (float) ($health['disk_used_percent'] ?? 0),   'threshold' => (float) ($config['disk_threshold'] ?? 90),   'label' => 'Đĩa cứng'],
            ['key' => 'cpu',    'value' => (float) ($health['cpu_percent'] ?? 0),         'threshold' => (float) ($config['cpu_threshold'] ?? 90),    'label' => 'CPU'],
        ];

        foreach ($checks as $c) {
            if ($c['value'] >= $c['threshold']) {
                $severity = $c['value'] >= ($c['threshold'] + 10) ? 'critical' : 'warning';
                $alerts[] = [
                    'key'       => $c['key'],
                    'severity'  => $severity,
                    'message'   => sprintf('%s đã vượt ngưỡng: %.1f%% (ngưỡng %.0f%%)', $c['label'], $c['value'], $c['threshold']),
                    'value'     => $c['value'],
                    'threshold' => $c['threshold'],
                ];
            } elseif ($c['value'] >= $c['threshold'] - 5) {
                $alerts[] = [
                    'key'       => $c['key'],
                    'severity'  => 'info',
                    'message'   => sprintf('%s gần ngưỡng: %.1f%% (ngưỡng %.0f%%)', $c['label'], $c['value'], $c['threshold']),
                    'value'     => $c['value'],
                    'threshold' => $c['threshold'],
                ];
            }
        }

        $errors_per_min = (float) ($health['recent_error_count'] ?? 0) / (24 * 60);
        if ($errors_per_min >= ($config['errors_per_min'] ?? 10)) {
            $alerts[] = [
                'key'       => 'errors',
                'severity'  => 'critical',
                'message'   => sprintf('Lỗi PHP cao: %.2f/phút (ngưỡng %d)', $errors_per_min, (int) $config['errors_per_min']),
                'value'     => $errors_per_min,
                'threshold' => (float) $config['errors_per_min'],
            ];
        }

        return $alerts;
    }

    /**
     * Best-effort recent error count via WP debug.log tail.
     *
     * @param float $hours
     */
    private static function count_recent_errors($hours = 24) {
        $log = WP_CONTENT_DIR . '/debug.log';
        if (!file_exists($log) || !is_readable($log)) return 0;
        $size = @filesize($log);
        if (!$size) return 0;
        $read = min($size, 256 * 1024); // last 256KB
        $fp = @fopen($log, 'r');
        if (!$fp) return 0;
        @fseek($fp, -$read, SEEK_END);
        $content = (string) @fread($fp, $read);
        fclose($fp);

        $cutoff = time() - (int) ($hours * 3600);
        $count = 0;
        foreach (explode("\n", $content) as $line) {
            if (preg_match('/\[(\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2})/', $line, $m)) {
                $ts = strtotime($m[1]);
                if ($ts && $ts >= $cutoff && (stripos($line, 'PHP Fatal') !== false || stripos($line, 'PHP Warning') !== false || stripos($line, 'PHP Error') !== false)) {
                    $count++;
                }
            }
        }
        return $count;
    }

    private static function parse_size($str) {
        if (!$str) return 0;
        $str = trim($str);
        $unit = strtolower(substr($str, -1));
        $num = (float) $str;
        switch ($unit) {
            case 'g': $num *= 1024;
            case 'm': $num *= 1024;
            case 'k': $num *= 1024;
        }
        return (int) $num;
    }

    private static function cpu_cores() {
        if (function_exists('shell_exec') && stripos(PHP_OS, 'win') === false) {
            $out = @shell_exec('nproc 2>/dev/null');
            if ($out && (int) $out > 0) return (int) $out;
        }
        return 1;
    }

    private static function cpu_estimate_now() {
        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0];
        return min(100, max(0, round(($load[0] ?? 0) * 100 / max(1, self::cpu_cores()), 1)));
    }

    private static function send_alert_email($to, $alerts) {
        $subject = '[Pi Dashboard] Cảnh báo hệ thống — ' . count($alerts) . ' alert nghiêm trọng';
        $body = "Hệ thống đang gặp vấn đề:\n\n";
        foreach ($alerts as $a) {
            $body .= "• [{$a['severity']}] {$a['message']}\n";
        }
        $body .= "\nXem chi tiết tại: " . admin_url('admin.php?page=pi-dashboard#/system-health');
        wp_mail($to, $subject, $body);
    }
}
