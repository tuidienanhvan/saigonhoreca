<?php
/**
 * Router — LLM classifier that runs on EVERY message: decides intent
 * (price/contact/service/project/product/none) and, for product questions,
 * extracts search keywords + brand. Replaces the old rule-based keyword
 * matching (is_price_intent()/is_contact_intent()/... — removed from
 * class-retriever.php) now that the "3-tier" pipeline (classify -> retrieve
 * -> answer) is fully LLM-driven end to end, per explicit product decision:
 * the old fast-path kept classification instant and free, but the team opted
 * to trade that for a single LLM-first design instead of maintaining two
 * parallel classifiers (Vietnamese keyword lists AND an LLM fallback).
 *
 * Cost/latency trade-off accepted: every message now costs one LLM7 call
 * (router model, ~1.8s avg) before generation, vs. only on rule-based misses
 * previously. See docs/plans/pi-chatbot-adaptive-router.md for the original
 * academic grounding (Self-RAG arXiv:2310.11511, "Small Models Big Insights"
 * arXiv:2402.12052, "To Retrieve or Not to Retrieve?" arXiv:2501.09292) —
 * that design treated the router as a fallback-only safety net; this version
 * promotes it to the primary classifier per product decision.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Router {
    /** Bump this when the prompt/schema changes — old cached responses become
     *  invalid automatically instead of needing a manual cache purge. */
    const PROMPT_VERSION = 2;

    const MODEL_DEFAULT = 'codestral-latest';
    // Single attempt, no retry — unlike Generator (2 keys). An extra retry
    // second here is not worth the added wait on the now-mandatory classify step.
    const TIMEOUT_SECONDS = 8;
    const MAX_KEY_ATTEMPTS = 1;

    const CACHE_TTL_POSITIVE = 30 * DAY_IN_SECONDS; // a phrase's classification doesn't change with the catalog
    const CACHE_TTL_NEGATIVE = 10 * MINUTE_IN_SECONDS; // avoid hammering LLM7 on a repeatedly-failing question

    // Circuit breaker — same atomic-bucket pattern as
    // Pi_Chatbot_Rest::check_rate_limit() (lesson learned: a real race
    // condition previously existed with the non-atomic get_transient()/
    // set_transient() read-modify-write).
    const BREAKER_WINDOW_SECONDS = 300; // 5-minute bucket
    const BREAKER_FAIL_THRESHOLD = 5;

    /** Valid intent values the model may return. */
    const VALID_INTENTS = ['price', 'contact', 'service', 'project', 'product', 'none'];

    /**
     * @param string $question Raw customer message.
     * @return array{intent:string,keywords_en:string[],keywords_vi:string[],brand:?string}|null
     *   null = router disabled/breaker open/error — caller must treat null the
     *   same as "could not classify" and fall back to a plain product search.
     */
    public static function enrich($question) {
        $settings = pi_chatbot_settings();
        if (empty($settings['router_enabled'])) return null;

        $normalized = trim(preg_replace('/\s+/u', ' ', mb_strtolower((string) $question)));
        if ($normalized === '') return null;

        $cache_key = 'pi_chatbot_rtr_' . self::PROMPT_VERSION . '_' . md5($normalized);
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached === 'NEGATIVE' ? null : $cached;
        }

        if (self::breaker_open()) return null;

        $keys = !empty($settings['router_keys']) ? $settings['router_keys'] : $settings['llm7_keys'];
        if (empty($keys)) return null;

        $model = !empty($settings['router_model']) ? $settings['router_model'] : self::MODEL_DEFAULT;
        $body = [
            'model'       => $model,
            'messages'    => self::build_messages($question),
            'temperature' => 0,
            'max_tokens'  => 300,
        ];

        $response = Pi_Chatbot_LLM_Client::call(
            $body, $keys, self::TIMEOUT_SECONDS, self::MAX_KEY_ATTEMPTS, 'pi-chatbot-router'
        );

        if (is_wp_error($response)) {
            self::bump_breaker_failure();
            set_transient($cache_key, 'NEGATIVE', self::CACHE_TTL_NEGATIVE);
            return null;
        }

        $parsed = self::parse_response(Pi_Chatbot_LLM_Client::content($response));
        if ($parsed === null) {
            // Model responded but not with usable JSON — not a downstream failure
            // (breaker stays closed), but still cache negative to avoid re-asking
            // immediately.
            set_transient($cache_key, 'NEGATIVE', self::CACHE_TTL_NEGATIVE);
            return null;
        }

        $validated = self::validate($parsed);
        set_transient($cache_key, $validated ?: 'NEGATIVE', $validated ? self::CACHE_TTL_POSITIVE : self::CACHE_TTL_NEGATIVE);
        return $validated;
    }

    /**
     * Real bilingual few-shot examples pulled straight from the DB instead of
     * letting the model translate blind (per arXiv:2409.12880 — real bilingual
     * examples improve +15.3% over zero-shot for weak-resource language pairs).
     * Cached for 1 day — the catalog doesn't change fast enough to need fresher.
     */
    private static function few_shot_examples() {
        $cache_key = 'pi_chatbot_rtr_fewshot';
        $cached = get_transient($cache_key);
        if (is_array($cached)) return $cached;

        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $rows = $wpdb->get_results(
            "SELECT DISTINCT category, title FROM {$table}
             WHERE post_type = 'product' AND category != '' AND title != ''
             ORDER BY RAND() LIMIT 5"
        );
        $examples = [];
        foreach ($rows as $r) {
            $examples[] = ['vi' => $r->category, 'en_context' => $r->title];
        }
        set_transient($cache_key, $examples, DAY_IN_SECONDS);
        return $examples;
    }

    private static function build_messages($question) {
        $examples = self::few_shot_examples();
        $example_lines = array_map(function ($e) {
            return sprintf('- "%s" appears on products whose English title is: "%s"', $e['vi'], $e['en_context']);
        }, $examples);

        $known_brands = array_values(array_unique(array_values(Pi_Chatbot_Entities::known_brands())));

        $system = "You are the intent classifier + translator for a commercial kitchen equipment chatbot "
            . "(catalog titles are in ENGLISH; customers may ask in Vietnamese/English/Japanese/other languages).\n"
            . "Real industry-term examples from the catalog (use these for accurate translation, don't guess generically):\n"
            . implode("\n", $example_lines) . "\n\n"
            . "Known REAL brand list (pick verbatim from here only, never invent a brand): "
            . implode(', ', $known_brands) . "\n\n"
            . "Reply with ONLY this JSON, no other text:\n"
            . "{\"intent\":\"price|contact|service|project|product|none\",\"keywords_en\":[\"...\"],\"keywords_vi\":[\"...\"],\"brand\":\"...\"or null}\n"
            . "intent meanings:\n"
            . "  price   — asking about pricing/quote/cost (no keywords needed)\n"
            . "  contact — asking for contact info/hotline/address/email (no keywords needed)\n"
            . "  service — asking about services offered (design consulting, installation, warranty; no keywords needed)\n"
            . "  project — asking about completed projects/design samples/portfolio (no keywords needed)\n"
            . "  product — asking about a specific piece of equipment/product — MUST include keywords_en/keywords_vi\n"
            . "  none    — greeting/small talk/out of scope (not about kitchen equipment, pricing, services, contact, or projects) — leave other fields empty\n"
            . "keywords_en: 2-5 English keywords describing the EXACT equipment type (use real commercial-kitchen "
            . "industry terms, not a literal word-for-word translation). Only for intent=product.\n"
            . "keywords_vi: the original Vietnamese keywords remaining after stripping greeting/filler words. Only for intent=product.";

        return [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => (string) $question],
        ];
    }

    /**
     * Defensive parsing — the model often wraps JSON in a ```json fence or adds
     * stray text before/after (observed in practice with Generator/analyzer
     * before). Strip the fence, then find the first balanced {...} block
     * instead of calling json_decode() on the raw string directly.
     */
    private static function parse_response($text) {
        if ($text === '') return null;
        $text = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $text = preg_replace('/\s*```$/', '', $text);

        $start = strpos($text, '{');
        if ($start === false) return null;
        $depth = 0;
        $end = null;
        for ($i = $start; $i < strlen($text); $i++) {
            if ($text[$i] === '{') $depth++;
            elseif ($text[$i] === '}') {
                $depth--;
                if ($depth === 0) { $end = $i; break; }
            }
        }
        if ($end === null) return null;

        $json = substr($text, $start, $end - $start + 1);
        $data = json_decode($json, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Validate + guard against hallucination:
     * - intent must be one of VALID_INTENTS; anything else (missing, typo,
     *   invalid value) falls back to 'product' IF keywords are present
     *   (model still did useful extraction work, don't discard it over a
     *   malformed intent field), otherwise to 'none' (safest default — no
     *   context is better than wrong context).
     * - brand MUST match a real brand in known_brands() verbatim — no match
     *   drops the field (partial degrade, same principle used by
     *   detect_brand_from_title()), it does NOT discard the whole result just
     *   because the brand guess was wrong.
     * - keywords_en/keywords_vi capped at 8 items, each ≤ 40 chars — stops the
     *   model from dumping long text. Empty/non-string entries are dropped.
     */
    private static function validate($parsed) {
        $intent = isset($parsed['intent']) && is_string($parsed['intent']) ? trim($parsed['intent']) : '';
        if (!in_array($intent, self::VALID_INTENTS, true)) {
            $intent = null; // resolved below once we know whether keywords exist
        }

        $clean_list = function ($v) {
            if (!is_array($v)) return [];
            $out = [];
            foreach ($v as $item) {
                if (!is_string($item)) continue;
                $item = trim($item);
                if ($item === '' || mb_strlen($item) > 40) continue;
                $out[] = $item;
                if (count($out) >= 8) break;
            }
            return $out;
        };

        $keywords_en = $clean_list($parsed['keywords_en'] ?? []);
        $keywords_vi = $clean_list($parsed['keywords_vi'] ?? []);

        if ($intent === null) {
            $intent = ($keywords_en || $keywords_vi) ? 'product' : 'none';
        }
        if ($intent === 'none') return null;

        $brand = null;
        if (!empty($parsed['brand']) && is_string($parsed['brand'])) {
            $claimed = trim($parsed['brand']);
            foreach (Pi_Chatbot_Entities::known_brands() as $full) {
                if (strcasecmp($full, $claimed) === 0) { $brand = $full; break; }
            }
            // No match -> $brand stays null, never use the model's made-up string.
        }

        return ['intent' => $intent, 'keywords_en' => $keywords_en, 'keywords_vi' => $keywords_vi, 'brand' => $brand];
    }

    private static function breaker_bucket() {
        return (int) floor(time() / self::BREAKER_WINDOW_SECONDS);
    }

    private static function breaker_open() {
        global $wpdb;
        $key = '_transient_pi_chatbot_rtr_fail_' . self::breaker_bucket();
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $key
        ));
        return $count >= self::BREAKER_FAIL_THRESHOLD;
    }

    /** Atomic increment — same INSERT...ON DUPLICATE KEY UPDATE pattern used by
     *  check_rate_limit(), not a get_transient()/set_transient() read-modify-write. */
    private static function bump_breaker_failure() {
        global $wpdb;
        $key = '_transient_pi_chatbot_rtr_fail_' . self::breaker_bucket();
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $key
        ));
        add_option('_transient_timeout_pi_chatbot_rtr_fail_' . self::breaker_bucket(), time() + self::BREAKER_WINDOW_SECONDS, '', false);
    }
}
