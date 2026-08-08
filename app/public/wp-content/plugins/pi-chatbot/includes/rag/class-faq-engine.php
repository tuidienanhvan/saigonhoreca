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
     * Search approved FAQs for instant answer.
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
}
