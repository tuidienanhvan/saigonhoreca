<?php
/**
 * Uninstall — only runs when the plugin is deleted via wp-admin (not on deactivate).
 * Cleans up the custom table + all options/transients the plugin created — this file
 * was previously missing (flagged by the 2026-07-18 audit): deleting the plugin left
 * behind the wp_pi_chatbot_index table, 4 options (including the API key), and orphaned
 * rate-limit/cache transients in the DB.
 *
 * @package PiChatbot
 */

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

global $wpdb;

$table = $wpdb->prefix . 'pi_chatbot_index';
$wpdb->query("DROP TABLE IF EXISTS {$table}");
// Also clean up the shadow/old table in case a reindex_all() run was interrupted
// midway (power loss, PHP timeout...) before the RENAME TABLE swap could finish —
// see class-indexer.php.
$wpdb->query("DROP TABLE IF EXISTS {$table}_shadow");
$wpdb->query("DROP TABLE IF EXISTS {$table}_old");

$log_table = $wpdb->prefix . 'pi_chatbot_log';
$wpdb->query("DROP TABLE IF EXISTS {$log_table}");

delete_option('pi_chatbot_settings');
delete_option('pi_chatbot_index_count');
delete_option('pi_chatbot_last_reindex');
delete_option('pi_chatbot_key_migrated_v1');
delete_option('pi_chatbot_db_version');
delete_option('pi_chatbot_index_version');
delete_option('pi_chatbot_log_db_version');

// Dynamic transients (rate-limit per IP, answer cache keyed by question hash, cached
// brand list) have an md5 suffix that changes per request — can't delete_option() each
// one individually, so delete by LIKE pattern directly on the options table (including
// the accompanying _transient_timeout_ rows).
$wpdb->query($wpdb->prepare(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
    $wpdb->esc_like('_transient_pi_chatbot_') . '%',
    $wpdb->esc_like('_transient_timeout_pi_chatbot_') . '%'
));
