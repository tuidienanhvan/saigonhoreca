<?php
/**
 * Self-Evolution Engine — automatic FAQ candidate curation and data gap analysis (hello-agents Ch15).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Self_Evolution {
    /**
     * Get candidate FAQs eligible for auto-publishing / admin approval.
     * Criteria: verified_count >= min_verifications (default 3)
     *
     * @param int $min_verifications
     * @return array
     */
    public static function get_faq_candidates($min_verifications = 3) {
        if (!class_exists('Pi_Chatbot_FAQ_Engine')) return [];
        global $wpdb;
        $table = $wpdb->prefix . Pi_Chatbot_FAQ_Engine::TABLE_NAME;

        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
        if (!$table_exists) return [];

        return $wpdb->get_results($wpdb->prepare(
            "SELECT id, question, answer, intent, verified_count, is_approved, created_at 
             FROM {$table} 
             WHERE verified_count >= %d 
             ORDER BY verified_count DESC",
            $min_verifications
        ), ARRAY_A);
    }

    /**
     * Auto-approve FAQs that have high verification confidence (>= 5 verified answers).
     *
     * @param int $threshold
     * @return int Number of approved FAQs
     */
    public static function auto_approve_candidates($threshold = 5) {
        if (!class_exists('Pi_Chatbot_FAQ_Engine')) return 0;
        global $wpdb;
        $table = $wpdb->prefix . Pi_Chatbot_FAQ_Engine::TABLE_NAME;

        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
        if (!$table_exists) return 0;

        return (int) $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET is_approved = 1 WHERE verified_count >= %d AND is_approved = 0",
            $threshold
        ));
    }

    /**
     * Analyze unresolved data gaps from search log to suggest missing catalog items.
     *
     * @param int $limit
     * @return array
     */
    public static function analyze_data_gaps($limit = 20) {
        if (!class_exists('Pi_Chatbot_Log')) return [];

        $gaps = Pi_Chatbot_Log::top_unanswered($limit);
        $suggestions = [];

        foreach ($gaps as $gap) {
            $q = (string) ($gap->question_norm ?? '');
            if ($q === '') continue;

            $suggestions[] = [
                'query'            => $q,
                'count'            => (int) ($gap->cnt ?? 0),
                'suggested_action' => (strpos($q, 'hoshizaki') !== false || strpos($q, 'berjaya') !== false) 
                    ? 'Check brand mapping or catalog indexing for ' . $q 
                    : 'Add catalog post / product listing for keyword: ' . $q,
            ];
        }

        return $suggestions;
    }
}
