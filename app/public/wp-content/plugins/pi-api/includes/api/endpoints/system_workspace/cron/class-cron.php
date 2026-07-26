<?php
/**
 * Pi API Cron Scheduler Endpoints
 *
 * Phase 4 task B — list/run/unschedule/create WP cron jobs + execution log.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Cron {

    const LOG_TABLE_SUFFIX = 'pi_cron_logs';

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/cron/jobs', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_jobs'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/cron/jobs', [
            'methods'  => 'POST',
            'callback' => [self::class, 'create_job'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/cron/jobs/(?P<hook>[^/]+)/run', [
            'methods'  => 'POST',
            'callback' => [self::class, 'run_job'],
            'permission_callback' => $admin,
            'args' => ['hook' => ['required' => true]],
        ]);
        register_rest_route($ns, '/cron/jobs/(?P<hook>[^/]+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_job'],
            'permission_callback' => $admin,
            'args' => ['hook' => ['required' => true]],
        ]);
        register_rest_route($ns, '/cron/logs', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_logs'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/cron/schedules', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_schedules'],
            'permission_callback' => $admin
        ]);

        // Ensure log table exists
        add_action('init', [self::class, 'maybe_create_table'], 5);

        // Hook into wp_loaded to instrument cron runs
        add_action('wp_loaded', [self::class, 'instrument_cron_runs'], 1);
    }

    public static function maybe_create_table() {
        global $wpdb;
        $table = self::log_table();
        $installed = (string) get_option('pi_cron_logs_db_version', '0');
        if (version_compare($installed, '1.0', '>=')) return;

        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            hook VARCHAR(191) NOT NULL,
            args LONGTEXT NULL,
            started_at DATETIME NOT NULL,
            finished_at DATETIME NULL,
            duration_ms INT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'running',
            error_message TEXT NULL,
            PRIMARY KEY (id),
            KEY idx_hook (hook),
            KEY idx_started (started_at),
            KEY idx_status (status)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        update_option('pi_cron_logs_db_version', '1.0');
    }

    public static function log_table() {
        global $wpdb;
        return $wpdb->prefix . self::LOG_TABLE_SUFFIX;
    }

    /**
     * Instrument all WP cron hooks: log started/finished/error around each `do_action_ref_array`.
     */
    public static function instrument_cron_runs() {
        if (!defined('DOING_CRON') || !DOING_CRON) return;
        // Use WordPress' cron_request to detect hook execution boundaries.
        add_action('all', [self::class, 'log_hook_start'], 1);
    }

    /** @internal — called via 'all' hook, narrows to known cron hooks only. */
    public static function log_hook_start($hook) {
        if (!is_string($hook)) return;
        $crons = (array) _get_cron_array();
        $is_cron_hook = false;
        foreach ($crons as $events) {
            if (isset($events[$hook])) { $is_cron_hook = true; break; }
        }
        if (!$is_cron_hook) return;
        if (!empty($GLOBALS['__pi_cron_active'][$hook])) return;
        $GLOBALS['__pi_cron_active'][$hook] = microtime(true);

        global $wpdb;
        $wpdb->insert(self::log_table(), [
            'hook'       => $hook,
            'args'       => wp_json_encode([]),
            'started_at' => current_time('mysql'),
            'status'     => 'running',
        ]);
        $log_id = $wpdb->insert_id;

        // Schedule a shutdown finisher
        add_action('shutdown', function () use ($hook, $log_id) {
            if (empty($GLOBALS['__pi_cron_active'][$hook]) || !$log_id) return;
            $started = $GLOBALS['__pi_cron_active'][$hook];
            $duration_ms = (int) ((microtime(true) - $started) * 1000);
            global $wpdb;
            $wpdb->update(self::log_table(), [
                'finished_at' => current_time('mysql'),
                'duration_ms' => $duration_ms,
                'status'      => 'success',
            ], ['id' => $log_id]);
            unset($GLOBALS['__pi_cron_active'][$hook]);
        }, 99);
    }

    /**
     * List all scheduled cron jobs.
     */
    public static function list_jobs() {
        $crons = (array) _get_cron_array();
        $jobs = [];
        foreach ($crons as $timestamp => $events) {
            foreach ($events as $hook => $entries) {
                foreach ($entries as $sig => $data) {
                    $schedule = $data['schedule'] ?? false;
                    $is_pi = (strpos($hook, 'pi_') === 0 || strpos($hook, 'sgh_') === 0);
                    $jobs[] = [
                        'hook'       => $hook,
                        'sig'        => $sig,
                        'args'       => $data['args'] ?? [],
                        'next_run'   => (int) $timestamp,
                        'next_run_human' => self::human_time_diff_signed($timestamp),
                        'recurrence' => $schedule ?: 'single',
                        'interval'   => isset($data['interval']) ? (int) $data['interval'] : null,
                        'is_pi'      => $is_pi,
                        'last_run'   => self::get_last_run($hook),
                        'overdue'    => $timestamp < time() - 60,
                    ];
                }
            }
        }
        usort($jobs, fn($a, $b) => $a['next_run'] <=> $b['next_run']);
        return new \WP_REST_Response(['jobs' => $jobs, 'count' => count($jobs)], 200);
    }

    /**
     * Manually trigger a cron job.
     */
    public static function run_job(\WP_REST_Request $request) {
        $hook = (string) $request['hook'];
        if (empty($hook)) return new \WP_REST_Response(['message' => 'Missing hook'], 400);

        global $wpdb;
        $start = microtime(true);
        $log_data = [
            'hook'       => $hook,
            'args'       => wp_json_encode([]),
            'started_at' => current_time('mysql'),
            'status'     => 'running',
        ];
        $wpdb->insert(self::log_table(), $log_data);
        $log_id = $wpdb->insert_id;

        $err = null;
        try {
            do_action_ref_array($hook, []);
            $status = 'success';
        } catch (\Throwable $e) {
            $status = 'failed';
            $err = $e->getMessage();
        }
        $duration_ms = (int) ((microtime(true) - $start) * 1000);
        $wpdb->update(self::log_table(), [
            'finished_at'   => current_time('mysql'),
            'duration_ms'   => $duration_ms,
            'status'        => $status,
            'error_message' => $err,
        ], ['id' => $log_id]);

        return new \WP_REST_Response([
            'success'     => ($status === 'success'),
            'hook'        => $hook,
            'duration_ms' => $duration_ms,
            'status'      => $status,
            'error'       => $err,
        ], $status === 'success' ? 200 : 500);
    }

    /**
     * Unschedule all events for a hook.
     */
    public static function delete_job(\WP_REST_Request $request) {
        $hook = (string) $request['hook'];
        if (empty($hook)) return new \WP_REST_Response(['message' => 'Missing hook'], 400);

        $crons = (array) _get_cron_array();
        $removed = 0;
        foreach ($crons as $timestamp => $events) {
            if (isset($events[$hook])) {
                foreach ($events[$hook] as $entry) {
                    wp_unschedule_event($timestamp, $hook, $entry['args'] ?? []);
                    $removed++;
                }
            }
        }
        return new \WP_REST_Response(['success' => true, 'hook' => $hook, 'removed' => $removed], 200);
    }

    /**
     * Schedule a new cron job.
     */
    public static function create_job(\WP_REST_Request $request) {
        $p = $request->get_json_params() ?: [];
        $hook = sanitize_key($p['hook'] ?? '');
        if (empty($hook)) return new \WP_REST_Response(['message' => 'Missing hook'], 400);

        $recurrence = sanitize_key($p['recurrence'] ?? 'single');
        $start = isset($p['start_time']) ? (int) strtotime((string) $p['start_time']) : time() + 60;
        $args = is_array($p['args'] ?? null) ? $p['args'] : [];

        if ($recurrence === 'single') {
            $ok = wp_schedule_single_event($start, $hook, $args);
        } else {
            $schedules = wp_get_schedules();
            if (!isset($schedules[$recurrence])) {
                return new \WP_REST_Response(['message' => 'Lịch không hợp lệ: ' . $recurrence], 400);
            }
            $ok = wp_schedule_event($start, $recurrence, $hook, $args);
        }

        if ($ok === false) {
            return new \WP_REST_Response(['message' => 'Không thể lập lịch (có thể đã tồn tại).'], 400);
        }

        return new \WP_REST_Response([
            'success'    => true,
            'hook'       => $hook,
            'recurrence' => $recurrence,
            'start'      => $start,
        ], 200);
    }

    /**
     * Execution logs.
     */
    public static function get_logs(\WP_REST_Request $request) {
        global $wpdb;
        $table = self::log_table();
        $limit = max(1, min(500, (int) $request->get_param('limit') ?: 50));
        $hook = $request->get_param('hook');
        $status = $request->get_param('status');

        $where = '1=1';
        $params = [];
        if (!empty($hook)) {
            $where .= ' AND hook = %s';
            $params[] = $hook;
        }
        if (!empty($status)) {
            $where .= ' AND status = %s';
            $params[] = $status;
        }

        $sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY started_at DESC LIMIT %d";
        $params[] = $limit;
        $logs = $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A);

        // Stats today
        $today_start = date('Y-m-d 00:00:00');
        $stats_row = $wpdb->get_row($wpdb->prepare(
            "SELECT COUNT(*) as total, AVG(duration_ms) as avg_ms,
                    SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as failed
             FROM {$table} WHERE started_at >= %s",
            $today_start
        ), ARRAY_A);

        return new \WP_REST_Response([
            'logs'  => $logs ?: [],
            'count' => count($logs ?: []),
            'stats' => [
                'total_today'    => (int) ($stats_row['total'] ?? 0),
                'avg_duration_ms' => (float) ($stats_row['avg_ms'] ?? 0),
                'failed_today'   => (int) ($stats_row['failed'] ?? 0),
                'failure_rate'   => ($stats_row['total'] ?? 0) > 0
                    ? round(($stats_row['failed'] / $stats_row['total']) * 100, 1)
                    : 0,
            ],
        ], 200);
    }

    public static function get_schedules() {
        $schedules = wp_get_schedules();
        $out = [];
        foreach ($schedules as $key => $s) {
            $out[] = [
                'key'      => $key,
                'display'  => $s['display'] ?? $key,
                'interval' => (int) ($s['interval'] ?? 0),
            ];
        }
        return new \WP_REST_Response(['schedules' => $out], 200);
    }

    /* ── Helpers ── */

    private static function get_last_run($hook) {
        global $wpdb;
        $table = self::log_table();
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT started_at, status, duration_ms FROM {$table} WHERE hook = %s ORDER BY started_at DESC LIMIT 1",
            $hook
        ), ARRAY_A);
        if (!$row) return null;
        return [
            'started_at'  => $row['started_at'],
            'status'      => $row['status'],
            'duration_ms' => (int) $row['duration_ms'],
        ];
    }

    private static function human_time_diff_signed($timestamp) {
        $now = time();
        if ($timestamp >= $now) {
            return 'trong ' . human_time_diff($now, $timestamp);
        }
        return human_time_diff($timestamp, $now) . ' trước';
    }
}
