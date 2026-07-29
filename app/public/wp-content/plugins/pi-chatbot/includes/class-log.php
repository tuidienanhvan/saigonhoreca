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

    // v2 (2026-07-26): dropped FULLTEXT KEY question_ft — no UI has ever read
    // it (the docblock that added it said as much at the time), so it was
    // pure write amplification on every single INSERT for zero benefit.
    // Added a plain KEY on created_at instead: prune() and stats()'s
    // "last 24h" count both filter `created_at < / >= %s` alone, and the
    // existing had_context_created composite index has created_at as its
    // TRAILING column, which cannot serve a standalone predicate on it —
    // prune() was running a full table scan + row-by-row delete inside a
    // random customer's chat request (the 1-in-500 opportunistic trigger).
    // v3 (2026-07-27): added duration_ms — dbDelta() adds a NEW column to an
    // already-installed table automatically (unlike dropping a removed index,
    // which it never does — see the v2 note above), so no explicit ALTER TABLE
    // is needed here, just the bumped version to trigger dbDelta on upgrade.
    const DB_VERSION = 3;

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
        // dbDelta() only ever ADDS columns/keys found in the SQL string — it
        // never drops one that's been removed from it (a documented WP
        // limitation, not a bug here). An already-installed site's table still
        // carries the old FULLTEXT question_ft after create_table() runs, so
        // dropping it needs an explicit statement. Guarded by SHOW INDEX
        // rather than assuming it exists — a fresh install's create_table()
        // above never creates it in the first place, so DROP INDEX on that
        // fresh table would error.
        global $wpdb;
        $table = self::table_name();
        $has_old_index = (bool) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM information_schema.STATISTICS
             WHERE table_schema = %s AND table_name = %s AND index_name = 'question_ft'",
            DB_NAME, $table
        ));
        if ($has_old_index) {
            $wpdb->query("ALTER TABLE {$table} DROP INDEX question_ft");
        }
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
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            question_norm VARCHAR(500) NOT NULL,
            had_context TINYINT(1) NOT NULL DEFAULT 0,
            source_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
            router_used TINYINT(1) NOT NULL DEFAULT 0,
            duration_ms MEDIUMINT UNSIGNED NULL DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY had_context_created (had_context, created_at),
            KEY created_at (created_at)
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
     *
     * @return int|false The inserted row's id (for record_duration()), or false
     *   on failure/empty question. Deliberately called BEFORE Generator::answer()
     *   in class-rest.php, so "was context found" is captured even if the LLM
     *   call itself then fails — duration_ms starts NULL and is filled in
     *   afterward (see record_duration()) only for requests that complete.
     */
    public static function record($question_norm, $had_context, $source_count, $router_used) {
        global $wpdb;
        $question_norm = trim((string) $question_norm);
        if ($question_norm === '') return false;

        $inserted = $wpdb->insert(
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

        return $inserted ? (int) $wpdb->insert_id : false;
    }

    /**
     * Fills in duration_ms for a row record() already wrote — called once the
     * FULL request (including generation/verify) has actually finished, since
     * record() itself runs before those stages so it can capture "had_context"
     * even if generation fails. A request that errors out before reaching this
     * point simply keeps duration_ms NULL, which is correct: latency stats
     * (see stats_latency()) should reflect real completed answers, not
     * failures — a slow failure isn't the same signal as a slow success.
     */
    public static function record_duration($id, $duration_ms) {
        global $wpdb;
        $id = (int) $id;
        if ($id <= 0) return false;
        return (bool) $wpdb->update(
            self::table_name(),
            ['duration_ms' => max(0, min(16777215, (int) $duration_ms))],
            ['id' => $id],
            ['%d'],
            ['%d']
        );
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

    /**
     * p50/p90/max response latency over completed (duration_ms IS NOT NULL —
     * see record_duration()'s docblock for why a failed request has none)
     * requests in the last $days. Computed directly from real logged data, no
     * external analytics service — matches the plan's "no unnecessary
     * dependency" call: the plugin already has the numbers, they just weren't
     * displayed anywhere.
     */
    public static function stats_latency($days = 7) {
        global $wpdb;
        $table = self::table_name();
        $since = gmdate('Y-m-d H:i:s', time() - max(1, (int) $days) * DAY_IN_SECONDS);
        $values = array_map('intval', $wpdb->get_col($wpdb->prepare(
            "SELECT duration_ms FROM {$table} WHERE duration_ms IS NOT NULL AND created_at >= %s ORDER BY duration_ms ASC",
            $since
        )));
        $n = count($values);
        if ($n === 0) return ['n' => 0, 'p50' => null, 'p90' => null, 'max' => null];
        $percentile = function ($p) use ($values, $n) {
            return $values[(int) floor($p * ($n - 1))];
        };
        return [
            'n'   => $n,
            'p50' => $percentile(0.5),
            'p90' => $percentile(0.9),
            'max' => end($values),
        ];
    }
}
