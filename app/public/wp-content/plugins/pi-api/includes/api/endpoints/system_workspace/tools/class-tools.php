<?php
/**
 * Pi API Server Tools Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Tools {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/system/tables', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_tables'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/crons', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_crons'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/system/error-log', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_error_log'],   'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'clear_error_log'], 'permission_callback' => $admin],
        ]);
    }

    public static function get_tables() {
        global $wpdb;
        $tables = $wpdb->get_results("SHOW TABLE STATUS FROM `" . DB_NAME . "`", ARRAY_A);
        $result = [];
        foreach ($tables as $t) {
            $size = ((int) ($t['Data_length'] ?? 0)) + ((int) ($t['Index_length'] ?? 0));
            $result[] = [
                'name'     => $t['Name'],
                'rows'     => (int) ($t['Rows'] ?? 0),
                'size'     => $size,
                'size_fmt' => size_format($size),
            ];
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function get_crons() {
        $crons = _get_cron_array();
        $events = [];
        if ($crons) {
            foreach ($crons as $ts => $hooks) {
                foreach ($hooks as $hook => $data) {
                    foreach ($data as $info) {
                        $events[] = [
                            'hook'     => $hook,
                            'next_run' => date('Y-m-d H:i:s', $ts),
                            'schedule' => $info['schedule'] ?: 'once',
                        ];
                    }
                }
            }
        }
        return new \WP_REST_Response($events, 200);
    }

    public static function get_error_log() {
        $log_file = ini_get('error_log');
        if (!$log_file || !file_exists($log_file)) $log_file = ABSPATH . 'wp-content/debug.log';
        if (!file_exists($log_file)) return new \WP_REST_Response(['lines' => []], 200);

        $content = file_get_contents($log_file, false, null, -51200); // Read last 50KB
        $lines = array_filter(explode("\n", $content));
        
        return new \WP_REST_Response(['lines' => array_slice($lines, -100)], 200);
    }

    public static function clear_error_log() {
        $log_file = ini_get('error_log');
        if (!$log_file || !file_exists($log_file)) $log_file = ABSPATH . 'wp-content/debug.log';
        if (file_exists($log_file)) file_put_contents($log_file, '');
        return new \WP_REST_Response(['success' => true], 200);
    }
}
