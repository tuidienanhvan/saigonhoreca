<?php
/**
 * Conversation log — records every REAL question (not a cache hit) along with
 * whether context was found for it, so data gaps can be surfaced with plain SQL
 * instead of guesswork.
 *
 * Inspired by the MemoryProvider.sync_turn() pattern from Hermes Agent
 * (NousResearch) — logging every conversation turn so a later "curator" layer
 * can distill it into insights — but radically simplified to match pi-chatbot's
 * scale: Hermes runs as a long-lived process, so sync_turn() has to dodge onto
 * a separate executor plus a timeout so it doesn't hang the turn in progress.
 * pi-chatbot is a single request/response cycle in PHP-FPM, where one INSERT
 * costs <5ms — no background thread needed, no circuit breaker needed. The
 * "curator" here also isn't an LLM summarizing the conversation (costly, ~8s
 * per call) but a single GROUP BY query finding questions asked repeatedly with
 * 0 results — same goal (turning raw logs into a learnable signal), different
 * tool.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Log {

    const DB_VERSION = 1;

    /** Maximum log retention period — the table is self-bounding, no separate cron needed. */
    const RETENTION_DAYS = 180;

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . 'pi_chatbot_log';
    }

    /** Same pattern as the Indexer's maybe_upgrade() — compare DB_VERSION, run dbDelta if mismatched. */
    public static function maybe_upgrade() {
        if ((int) get_option('pi_chatbot_log_db_version') === self::DB_VERSION) return;
        self::create_table();
        update_option('pi_chatbot_log_db_version', self::DB_VERSION, false);
    }

    public static function on_activate() {
        self::create_table();
        update_option('pi_chatbot_log_db_version', self::DB_VERSION, false);
    }

    public static function create_table() {
        global $wpdb;
        $table = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // FULLTEXT on question_norm so the log can be searched later (similar to
        // Hermes's FTS5) — no UI uses it yet, but adding it now is cheaper than an
        // ALTER TABLE later once the table already holds data.
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            question_norm VARCHAR(500) NOT NULL,
            had_context TINYINT(1) NOT NULL DEFAULT 0,
            source_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
            router_used TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY had_context_created (had_context, created_at),
            FULLTEXT KEY question_ft (question_norm)
        ) {$charset_collate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    /**
     * Records one turn's question. Must NEVER be allowed to break the answer
     * path for the customer — on failure, it fails silently (the same
     * "provider failure never blocks the turn" principle as Hermes's
     * MemoryManager, the only difference being that no background thread is
     * needed here since the request itself is already synchronous and the
     * insert is cheap).
     */
    public static function record($question_norm, $had_context, $source_count, $router_used) {
        global $wpdb;
        $question_norm = trim((string) $question_norm);
        if ($question_norm === '') return;

        $wpdb->insert(
            self::table_name(),
            [
                'question_norm' => mb_substr($question_norm, 0, 500),
                'had_context'   => $had_context ? 1 : 0,
                'source_count'  => min(255, max(0, (int) $source_count)),
                'router_used'   => $router_used ? 1 : 0,
                'created_at'    => current_time('mysql'),
            ],
            ['%s', '%d', '%d', '%d', '%s']
        );

        // Opportunistically prune old log entries (the same way WP core cleans up
        // expired transients) — 1/500 writes instead of a dedicated cron, enough
        // to keep the table from growing unbounded at a scale of a few hundred
        // questions/day without adding extra infrastructure.
        if (wp_rand(1, 500) === 1) {
            self::prune();
        }
    }

    private static function prune() {
        global $wpdb;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . self::table_name() . " WHERE created_at < %s",
            gmdate('Y-m-d H:i:s', time() - self::RETENTION_DAYS * DAY_IN_SECONDS)
        ));
    }

    /**
     * The most frequently repeated questions that FOUND NO context — a measured
     * (not guessed) signal of real data/keyword gaps. This is pi-chatbot's
     * "curator": no LLM summarization needed, a GROUP BY over the raw data is
     * enough.
     */
    public static function top_unanswered($limit = 15, $days = 30) {
        global $wpdb;
        $table = self::table_name();
        return $wpdb->get_results($wpdb->prepare(
            "SELECT question_norm, COUNT(*) AS cnt, MAX(created_at) AS last_seen
             FROM {$table}
             WHERE had_context = 0 AND created_at >= %s
             GROUP BY question_norm
             ORDER BY cnt DESC, last_seen DESC
             LIMIT %d",
            gmdate('Y-m-d H:i:s', time() - max(1, (int) $days) * DAY_IN_SECONDS),
            max(1, (int) $limit)
        ));
    }

    /** Overview stats for the admin page. */
    public static function stats() {
        global $wpdb;
        $table = self::table_name();
        $day_ago = gmdate('Y-m-d H:i:s', time() - DAY_IN_SECONDS);

        return [
            'total'               => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}"),
            'total_unanswered'    => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE had_context = 0"),
            'distinct_unanswered' => (int) $wpdb->get_var("SELECT COUNT(DISTINCT question_norm) FROM {$table} WHERE had_context = 0"),
            'last_24h'            => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s", $day_ago
            )),
        ];
    }
}
