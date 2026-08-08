<?php
/**
 * Lead — a customer's name/phone left in chat for a callback, i.e. the
 * "sinh doanh thu trực tiếp" (lead capture) half of the GLM 5.2 report's
 * C recommendation. Same shape as class-feedback.php (dbDelta + maybe_upgrade
 * + DB_VERSION) — a dedicated table rather than a column bolted onto the log
 * table, since a lead carries PII the log table (retained 180 days, see
 * class-log.php) was never designed to hold, and deserves its own retention/
 * access story.
 *
 * Zalo OA handoff (the second half of recommendation C) is deliberately NOT
 * part of this file — it needs a real Zalo OA Access Token/App ID this
 * codebase has no way to obtain on its own. This class only captures the
 * lead and notifies the admin by email; wiring an actual Zalo push is a
 * follow-up once those credentials exist.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Lead {
    const DB_VERSION = 1;

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . 'pi_chatbot_leads';
    }

    public static function maybe_upgrade() {
        if ((int) get_option('pi_chatbot_leads_db_version') === self::DB_VERSION) return;
        self::create_table();
        update_option('pi_chatbot_leads_db_version', self::DB_VERSION, false);
    }

    public static function on_activate() {
        self::create_table();
        update_option('pi_chatbot_leads_db_version', self::DB_VERSION, false);
    }

    public static function create_table() {
        global $wpdb;
        $table = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL DEFAULT '',
            phone VARCHAR(30) NOT NULL,
            question VARCHAR(500) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY created_at (created_at)
        ) {$charset_collate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    /**
     * A Vietnamese mobile/landline number, loosely — 9 to 11 digits after
     * stripping everything but digits and a leading +. Deliberately loose
     * (not validating carrier prefixes) because the cost of a false reject
     * (a real customer's real number bounced) is a lost lead, which is worse
     * than the cost of a false accept (staff calls back a malformed number
     * and it fails — a 5-second discovery, not a lost customer).
     */
    private static function normalize_phone($phone) {
        $digits = preg_replace('/[^0-9+]/', '', (string) $phone);
        $digits = ltrim($digits, '+') !== $digits ? '+' . ltrim($digits, '+') : $digits;
        $bare = preg_replace('/[^0-9]/', '', $digits);
        if (mb_strlen($bare) < 9 || mb_strlen($bare) > 11) return '';
        return $digits;
    }

    /**
     * Records one lead. Never blocks the customer path (same "fail silently"
     * principle as Log::record()/Feedback::record()) — the REST layer already
     * confirms success/failure to the customer via the HTTP response, this
     * return value only tells the caller whether to send the admin
     * notification.
     *
     * @return int|false Inserted row id, or false on invalid/empty phone.
     */
    public static function record($name, $phone, $question = '') {
        global $wpdb;
        $phone_n = self::normalize_phone($phone);
        if ($phone_n === '') return false;

        $name = trim(sanitize_text_field((string) $name));
        if (mb_strlen($name) > 100) $name = mb_substr($name, 0, 100);

        $question = trim(sanitize_text_field((string) $question));
        if (mb_strlen($question) > 500) $question = mb_substr($question, 0, 500);

        $inserted = $wpdb->insert(
            self::table_name(),
            [
                'name'       => $name,
                'phone'      => $phone_n,
                'question'   => $question,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s']
        );

        return $inserted ? (int) $wpdb->insert_id : false;
    }

    /** Most recent leads for the admin page — newest first. */
    public static function recent($limit = 20) {
        global $wpdb;
        $table = self::table_name();
        return $wpdb->get_results($wpdb->prepare(
            "SELECT name, phone, question, created_at FROM {$table} ORDER BY created_at DESC LIMIT %d",
            max(1, (int) $limit)
        ));
    }

    /** Total lead count — admin overview stat. */
    public static function total_count() {
        global $wpdb;
        return (int) $wpdb->get_var('SELECT COUNT(*) FROM ' . self::table_name());
    }
}
