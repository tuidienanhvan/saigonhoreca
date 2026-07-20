<?php
/**
 * Uninstall — chỉ chạy khi xoá plugin qua wp-admin (không chạy khi deactivate).
 * Dọn sạch bảng custom + toàn bộ option/transient plugin đã tạo — trước đây thiếu
 * file này (audit 2026-07-18 flag): xoá plugin để lại bảng wp_pi_chatbot_index,
 * 4 option (gồm cả API key), và transient rate-limit/cache mồ côi trong DB.
 *
 * @package PiChatbot
 */

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

global $wpdb;

$table = $wpdb->prefix . 'pi_chatbot_index';
$wpdb->query("DROP TABLE IF EXISTS {$table}");
// Dọn cả bảng shadow/old nếu 1 lần reindex_all() bị ngắt giữa chừng (mất điện,
// timeout PHP...) trước khi kịp RENAME TABLE swap xong — xem class-indexer.php.
$wpdb->query("DROP TABLE IF EXISTS {$table}_shadow");
$wpdb->query("DROP TABLE IF EXISTS {$table}_old");

delete_option('pi_chatbot_settings');
delete_option('pi_chatbot_index_count');
delete_option('pi_chatbot_last_reindex');
delete_option('pi_chatbot_key_migrated_v1');
delete_option('pi_chatbot_db_version');
delete_option('pi_chatbot_index_version');

// Transient động (rate-limit theo IP, cache câu trả lời theo hash câu hỏi, danh
// sách brand cache) có hậu tố md5 thay đổi theo request — không thể delete_option()
// từng cái, xoá theo pattern LIKE trực tiếp trên bảng options (gồm cả bản ghi
// _transient_timeout_ đi kèm).
$wpdb->query($wpdb->prepare(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
    $wpdb->esc_like('_transient_pi_chatbot_') . '%',
    $wpdb->esc_like('_transient_timeout_pi_chatbot_') . '%'
));
