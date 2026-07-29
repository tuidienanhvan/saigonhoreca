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

/**
 * Multisite added 2026-07-26 (a security-audit finding, not a hypothetical):
 * WordPress runs uninstall.php ONCE for the whole network when a network admin
 * deletes the plugin, in the CONTEXT of whichever site initiated it — every
 * `$wpdb->prefix` reference below resolved to just that one site's tables, and
 * every `delete_option()` only touched that one site's options table. Every
 * OTHER site on the network kept its `pi_chatbot_settings` option — which
 * contains the LLM7 API key — sitting in the database indefinitely with no
 * plugin left installed to ever read or rotate it. That is a secret-retention
 * problem, not just leftover clutter.
 *
 * `switch_to_blog()` inside the loop makes $wpdb->prefix (and therefore every
 * `{$wpdb->prefix}pi_chatbot_*` table name below) resolve to each site in
 * turn; `restore_current_blog()` puts it back so the loop's own next
 * iteration — and anything WordPress does right after this file finishes —
 * isn't left pointed at the wrong site.
 */
function pi_chatbot_uninstall_cleanup() {
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

    $feedback_table = $wpdb->prefix . 'pi_chatbot_feedback';
    $wpdb->query("DROP TABLE IF EXISTS {$feedback_table}");
    delete_option('pi_chatbot_feedback_db_version');

    // Leads carry PII (name/phone) — dropped on uninstall same as every other
    // plugin-owned table, not kept around "just in case".
    $leads_table = $wpdb->prefix . 'pi_chatbot_leads';
    $wpdb->query("DROP TABLE IF EXISTS {$leads_table}");
    delete_option('pi_chatbot_leads_db_version');

    // Semantic embeddings (see class-embeddings.php) — a separate table precisely so
    // it survives reindex swaps, which also means nothing else would ever drop it.
    $vectors_table = $wpdb->prefix . 'pi_chatbot_vectors';
    $wpdb->query("DROP TABLE IF EXISTS {$vectors_table}");

    // The embedding backfill is a self-rescheduling single cron event: if one is
    // pending at uninstall, WordPress would keep firing a hook whose callback no
    // longer exists.
    wp_clear_scheduled_hook('pi_chatbot_embed_backfill');

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

    // The daily LLM-call budget counter (class-budget.php) is a REAL wp_option, not a
    // transient — deliberately, so it survives WordPress's own transient garbage
    // collection past its own day (see the class's docblock) — which means it needs its
    // own sweep here rather than being caught by the _transient_ pattern above.
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like('pi_chatbot_llmbudget_') . '%'
    ));
}

if (is_multisite()) {
    // get_sites() returns every site on the network in one call — acceptable
    // here even for a large network, because uninstall is a one-time, admin-
    // triggered action, not a hot path this plugin's own latency budget
    // applies to.
    $site_ids = get_sites(['fields' => 'ids']);
    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        pi_chatbot_uninstall_cleanup();
        restore_current_blog();
    }
} else {
    pi_chatbot_uninstall_cleanup();
}
