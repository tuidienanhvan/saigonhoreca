<?php
/**
 * FAQ Assetization Engine — turns verified Q&A into reusable assets (hello-agents Ch15/Ex10).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_FAQ_Engine {
    const TABLE_NAME = 'pi_chatbot_faq';
    const DB_VERSION = 1;

    public static function maybe_upgrade() {
        $installed = (int) get_option('pi_chatbot_faq_db_version', 0);
        if ($installed < self::DB_VERSION) {
            self::create_table();
            update_option('pi_chatbot_faq_db_version', self::DB_VERSION);
        }
    }

    /**
     * Create DB table for FAQs on plugin activation / update.
     */
    public static function create_table() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            question text NOT NULL,
            question_norm varchar(255) NOT NULL,
            answer text NOT NULL,
            intent varchar(50) DEFAULT 'product',
            verified_count int(11) DEFAULT 1,
            is_approved tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY question_norm_approved (question_norm, is_approved)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Assetize a verified Q&A pair.
     *
     * @param string $question
     * @param string $answer
     * @param string $intent
     * @return int|bool Insert ID or status
     */
    public static function assetize($question, $answer, $intent = 'product') {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $norm = mb_strtolower(trim(wp_strip_all_tags((string) $question)));
        $norm = mb_substr($norm, 0, 255);

        if ($norm === '' || trim((string) $answer) === '') return false;

        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id, verified_count FROM {$table} WHERE question_norm = %s",
            $norm
        ));

        if ($existing) {
            return $wpdb->update(
                $table,
                ['verified_count' => $existing->verified_count + 1, 'answer' => $answer],
                ['id' => $existing->id],
                ['%d', '%s'],
                ['%d']
            );
        }

        return $wpdb->insert(
            $table,
            [
                'question'       => $question,
                'question_norm'  => $norm,
                'answer'         => $answer,
                'intent'         => $intent,
                'verified_count' => 1,
                'is_approved'    => 0, // Admin approval required
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d']
        );
    }

    /**
     * Search approved FAQs for an instant answer.
     *
     * CALLER CONTRACT — read before using this anywhere new: matching is on
     * question_norm ALONE. It does not consider conversation history, and it
     * cannot: an approved FAQ is a standalone question/answer pair. That makes
     * it unsafe for follow-up turns, where identical text means different
     * things ("giá bao nhiêu?" after two different products is two different
     * questions — which is exactly why Pi_Chatbot_Rest::ask()'s own answer
     * cache folds a history hash into its key). Pi_Chatbot_Rest::ask()
     * therefore only consults this when history is EMPTY.
     *
     * @param string $question
     * @return array|null
     */
    public static function find_faq($question) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $norm = mb_strtolower(trim(wp_strip_all_tags((string) $question)));
        $norm = mb_substr($norm, 0, 255);

        if ($norm === '') return null;

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT question, answer, intent FROM {$table} WHERE question_norm = %s AND is_approved = 1",
            $norm
        ), ARRAY_A);

        return $row ?: null;
    }

    /**
     * Candidates awaiting human approval, most-repeated first.
     *
     * Ordered by verified_count so the questions customers actually ask
     * repeatedly surface first — but note what that number does and does not
     * mean: it counts how many times the BOT produced this answer for this
     * question, not how many people confirmed it is correct. It is a
     * frequency signal, never a correctness signal. That distinction is why
     * approval is manual (see the admin screen's own warning).
     */
    public static function get_pending($limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT id, question, answer, intent, verified_count, updated_at
             FROM {$table} WHERE is_approved = 0
             ORDER BY verified_count DESC, updated_at DESC
             LIMIT %d",
            max(1, (int) $limit)
        ));
    }

    /** Count of approved FAQs — what find_faq() can actually serve from. */
    public static function count_approved() {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}" . self::TABLE_NAME . " WHERE is_approved = 1");
    }

    /** Count still waiting on a human. */
    public static function count_pending() {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}" . self::TABLE_NAME . " WHERE is_approved = 0");
    }

    /**
     * Approve one candidate. Deliberately takes an explicit id chosen by a
     * human on the admin screen — there is no bulk/auto approval, and there
     * must not be: the only frequency signal available (verified_count)
     * measures the bot agreeing with itself.
     */
    public static function approve($id) {
        global $wpdb;
        $id = (int) $id;
        if ($id <= 0) return false;
        return (bool) $wpdb->update(
            $wpdb->prefix . self::TABLE_NAME,
            ['is_approved' => 1],
            ['id' => $id],
            ['%d'],
            ['%d']
        );
    }

    /** Removes a candidate outright (wrong answer, or no longer relevant). */
    public static function delete_faq($id) {
        global $wpdb;
        $id = (int) $id;
        if ($id <= 0) return false;
        return (bool) $wpdb->delete($wpdb->prefix . self::TABLE_NAME, ['id' => $id], ['%d']);
    }
}
