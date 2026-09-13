<?php
/**
 * Harness chạy uninstall.php của pi-api với $wpdb GIẢ — ghi lại mọi câu SQL, không động vào
 * DB nào. Đừng gọi trực tiếp: chạy qua tests/check-uninstall.py.
 */
$mode = $argv[1] ?? 'normal';
define('WP_UNINSTALL_PLUGIN', 'pi-api/pi-api.php');
if ($mode === 'keep') define('PI_API_KEEP_DATA', true);

$LOG = ['sql' => [], 'cron' => [], 'meta' => [], 'blogs' => [], 'flush' => 0, 'apppw' => []];

class FakeWpdb {
    public $prefix = 'wp_';
    public $options = 'wp_options';
    function query($sql) { global $LOG; $LOG['sql'][] = [$this->prefix, $sql]; return 0; }
    function esc_like($t) { return addcslashes($t, '_%\\'); }   // dung nhu WP core
    function prepare($q, ...$a) { foreach ($a as $v) { $q = preg_replace('/%s/', "'" . addslashes($v) . "'", $q, 1); } return $q; }
}
$wpdb = new FakeWpdb();

function get_option($k, $d = '') { return $d; }
function get_users($a) { return []; }
function wp_clear_scheduled_hook($h) { global $LOG; $LOG['cron'][] = $h; }
function delete_metadata($t, $id, $k, $v, $all) { global $LOG; $LOG['meta'][] = "$t:$k:" . ($all ? 'all' : 'one'); }
function delete_option($k) { }        // ban cu goi — khong ghi nhan vi khong phai SQL tho
function delete_transient($k) { }
function wp_cache_flush() { global $LOG; $LOG['flush']++; }
function is_multisite() { global $mode; return $mode === 'multi'; }
function get_sites($a) { return [1, 2]; }
function switch_to_blog($id) { global $wpdb, $LOG; $LOG['blogs'][] = $id; $wpdb->prefix = $id === 1 ? 'wp_' : "wp_{$id}_"; $wpdb->options = $wpdb->prefix . 'options'; }
function restore_current_blog() { global $wpdb; $wpdb->prefix = 'wp_'; $wpdb->options = 'wp_options'; }

include getenv('UNINSTALL_FILE');

echo json_encode($LOG, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
