<?php
/**
 * Feedback — 👍/👎 on individual answers. The golden-set regression gate
 * (tests/eval-retrieval.php) only measures RETRIEVAL against a fixed 68-query
 * set; it has no way to know whether a real customer was actually satisfied
 * with the final generated answer. This is the missing signal, same
 * "measure, don't guess" principle as everything else in this plugin —
 * same shape/pattern as class-log.php (dbDelta + maybe_upgrade + DB_VERSION),
 * deliberately its own table rather than a column bolted onto the log table,
 * since a vote can arrive well after the original request/log row.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Feedback {
    const DB_VERSION = 1;

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . 'pi_chatbot_feedback';
    }

    public static function maybe_upgrade() {
        if ((int) get_option('pi_chatbot_feedback_db_version') === self::DB_VERSION) return;
        self::create_table();
        update_option('pi_chatbot_feedback_db_version', self::DB_VERSION, false);
    }

    public static function on_activate() {
        self::create_table();
        update_option('pi_chatbot_feedback_db_version', self::DB_VERSION, false);
    }

    public static function create_table() {
        global $wpdb;
        $table = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            feedback_id CHAR(32) NOT NULL,
            rating TINYINT NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY feedback_id (feedback_id),
            KEY created_at (created_at)
        ) {$charset_collate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    /**
     * Records one vote — 1 (👍) or -1 (👎) — against a feedback_id (see
     * Pi_Chatbot_Rest::ask(), which computes it as md5(question + answer) so
     * the SAME answer always gets the same id, and never exposes anything
     * about the question/answer text itself, only an opaque hash). Multiple
     * votes for the same feedback_id are all kept (not upserted) — a
     * distribution of votes on a popular repeated answer is itself a signal,
     * collapsing to "last vote wins" would throw that away.
     *
     * Never blocks the customer path (same principle as Log::record()) —
     * this is called from a dedicated endpoint the customer only reaches
     * AFTER already having their answer, so there's no turn left to protect,
     * but the same "fail silently, don't fatal" discipline still applies.
     */
    public static function record($feedback_id, $rating) {
        global $wpdb;
        $feedback_id = (string) $feedback_id;
        if (!preg_match('/^[a-f0-9]{32}$/', $feedback_id)) return false;
        $rating = (int) $rating;
        if ($rating !== 1 && $rating !== -1) return false;

        return (bool) $wpdb->insert(self::table_name(), [
            'feedback_id' => $feedback_id,
            'rating'      => $rating,
            'created_at'  => current_time('mysql'),
        ], ['%s', '%d', '%s']);
    }

    /** Overview stats for the admin page — up/down counts over the last $days. */
    public static function stats($days = 30) {
        global $wpdb;
        $table = self::table_name();
        $since = gmdate('Y-m-d H:i:s', time() - max(1, (int) $days) * DAY_IN_SECONDS);
        $up = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE rating = 1 AND created_at >= %s", $since
        ));
        $down = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE rating = -1 AND created_at >= %s", $since
        ));
        return ['up' => $up, 'down' => $down, 'total' => $up + $down];
    }
}
