<?php
/**
 * Budget — the plugin's only global (not per-IP) cost control. Before this
 * existed there was no cap at all on how many LLM7 calls a single day of
 * traffic could generate: 6 calls/anonymous POST max (router + generate +
 * retry + rerank + verify + retry-verify on the max-accuracy path), no upper
 * bound on how many POSTs, each holding a PHP-FPM worker up to 60s. A traffic
 * spike (bot, scraper, a viral share) could exhaust the LLM7 free-tier quota
 * for the rest of the day with no degrade path — the plugin would simply
 * start failing every request once every key was rate-limited.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Budget {
    const OPTION_PREFIX = 'pi_chatbot_llmbudget_';

    /**
     * A real wp_option (NOT a transient) so the count survives past its own
     * day without WordPress's transient garbage collection racing it — the
     * plan explicitly calls for this to be "bền qua cleanup". One option per
     * calendar day (same bucketing shape as check_rate_limit()'s per-minute
     * key) rather than a single option read-modified-written in place: a
     * fresh key each day needs no reset step, so there's no race between
     * "day just rolled over" and "someone is still incrementing yesterday's
     * count".
     */
    private static function option_name($date = null) {
        return self::OPTION_PREFIX . ($date ?: current_time('Y-m-d'));
    }

    /**
     * Records one real LLM7 HTTP attempt — called from Pi_Chatbot_LLM_Client
     * right before each wp_remote_post(), so a request that round-robins
     * through 3 keys before succeeding counts as 3 against the budget (each
     * one really did consume quota on that key), not as 1.
     *
     * Same atomic INSERT...ON DUPLICATE KEY UPDATE pattern already proven in
     * Pi_Chatbot_Rest::check_rate_limit() — avoids the read-then-write race
     * a plain get_option()+update_option() would have under concurrent
     * requests.
     */
    public static function record_call() {
        global $wpdb;
        $name = self::option_name();
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $name
        ));
        // Opportunistic prune, same 1-in-N pattern as Pi_Chatbot_Log::record() —
        // no separate cron needed at this traffic scale.
        if (wp_rand(1, 200) === 1) self::prune();
    }

    /**
     * Reads via a raw SQL SELECT, NOT get_option() — record_call() writes via a
     * raw SQL INSERT...ON DUPLICATE KEY UPDATE (same reasoning as
     * Pi_Chatbot_Rest::check_rate_limit()'s bucket), which does NOT go through
     * (and therefore does not invalidate) WordPress's own object-cache entry
     * for this option. Reading through get_option() afterward would return
     * that stale cached value instead of what was actually just written — a
     * real bug caught by this class's own test, not a hypothetical: reading
     * and writing must use the SAME path (both raw SQL) for the two to agree.
     */
    public static function today_count() {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", self::option_name()
        ));
    }

    public static function daily_limit() {
        $settings = pi_chatbot_settings();
        return max(1, (int) $settings['llm_daily_budget']);
    }

    public static function is_over_limit() {
        return self::today_count() >= self::daily_limit();
    }

    /** True once today's count reaches the given share of the daily limit
     *  (default 80%, per the plan: ">80% budget -> skip rerank + verify"). */
    public static function is_near_limit($ratio = 0.8) {
        return self::today_count() >= (int) floor(self::daily_limit() * $ratio);
    }

    /**
     * Deletes budget-counter options older than yesterday. Safe as a plain
     * string comparison because the day suffix is YYYY-MM-DD — that format
     * sorts lexically in the same order as chronologically, so
     * `option_name < 'pi_chatbot_llmbudget_<yesterday>'` is exactly "strictly
     * before yesterday" with no date parsing needed.
     */
    private static function prune() {
        global $wpdb;
        $cutoff = self::option_name(gmdate('Y-m-d', current_time('timestamp') - DAY_IN_SECONDS));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name < %s",
            $wpdb->esc_like(self::OPTION_PREFIX) . '%',
            $cutoff
        ));
    }
}
