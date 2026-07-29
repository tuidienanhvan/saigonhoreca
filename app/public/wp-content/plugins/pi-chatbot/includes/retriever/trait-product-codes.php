<?php
/**
 * Retriever — exact product-code matching (Tier 0), which short-circuits the
 * rest of the pipeline when the customer names an identifier directly. Split
 * out of class-retriever.php (2026-07-29, file had grown past 1000 lines) —
 * pure code motion, no logic changed; see class-retriever.php for the class
 * this trait belongs to.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

trait Pi_Chatbot_Retriever_ProductCodes {

    /**
     * Extracts product-code-like strings (letters + digits, possibly with
     * hyphens) from the RAW question (not yet lowercased/normalized) —
     * "FT-126MA-S", "UX-100", "DCM-60KE", "D650". Requires starting with 1-4
     * letters followed by at least 1 digit — this excludes ordinary
     * Vietnamese/English words (which contain no digits) without needing a
     * character whitelist.
     */
    private static function extract_product_codes($question) {
        if (!preg_match_all('/\b[A-Za-z]{1,4}-?\d[A-Za-z0-9-]{0,14}\b/u', $question, $m)) {
            return [];
        }
        return array_values(array_unique($m[0]));
    }

    /**
     * True when the question names something that looks like a product code AND
     * that code actually matches a product. Used by the REST layer to skip LLM
     * reranking: reranking reorders by judgement, and there is nothing to judge
     * once an exact identifier has already selected the row. Checking that the
     * code MATCHES (not merely that one is present) matters — a customer can
     * mention a code that isn't in the catalog, and that question does still
     * benefit from the full pipeline.
     */
    public static function has_product_code_match($question) {
        $codes = self::extract_product_codes(trim(wp_strip_all_tags((string) $question)));
        if (!$codes) return false;
        return (bool) self::search_by_product_code($codes, 1);
    }

    /** Request-scoped memo, keyed by the (order-independent) code list — see
     *  search_by_product_code()'s docblock for why this exists: the query it
     *  runs does not depend on $top_k at all, only the final PHP-side slice
     *  does, so 2 callers asking about the SAME codes with different $top_k
     *  (has_product_code_match($msg) with top_k=1, then search()'s own Tier 0
     *  with the real top_k moments later on the exact same request) were
     *  running the identical SQL twice. */
    private static $product_code_match_cache = [];

    /**
     * Whole-catalog scan for a product code, deduped/sorted ONCE per request
     * regardless of how many times a caller asks (with whatever $top_k) —
     * see $product_code_match_cache. The DB round trip and the exactness
     * scoring are the expensive part; array_slice($top_k) is free.
     */
    private static function search_by_product_code($codes, $top_k) {
        $cache_key = implode('|', array_map('mb_strtolower', $codes));
        if (!isset(self::$product_code_match_cache[$cache_key])) {
            self::$product_code_match_cache[$cache_key] = self::fetch_product_code_matches($codes);
        }
        $rows = array_slice(self::$product_code_match_cache[$cache_key], 0, $top_k);
        return self::format_rows($rows, $codes);
    }

    /**
     * Matches each code directly against the title column (LIKE, not FULLTEXT
     * — product codes are ASCII strings, so they don't hit the Vietnamese
     * diacritic collation bug, but LOWER()+bin is still forced for consistency
     * and to prevent MySQL from treating visually similar characters as
     * equal). Ranking: a WHOLE-WORD match (bounded by non-alphanumeric
     * characters on both sides, or the start/end of the string) is given
     * absolute priority over a substring match within a longer code — this is
     * exactly how "RT-126MA-S" (whole-word match) is distinguished from
     * "RT-126MA-SSG"/"RT-126MA-S-3D" (mere substrings). Within the same
     * priority group, a SHORTER title ranks first — a variant with an added
     * suffix is always longer than the base one.
     *
     * The full sorted/deduped match list for a code set, unsliced and
     * unformatted — the part search_by_product_code() memoizes.
     */
    private static function fetch_product_code_matches($codes) {
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $candidates = [];
        foreach ($codes as $code) {
            $code_l = mb_strtolower($code);
            $pattern = '%' . $wpdb->esc_like($code_l) . '%';
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT post_id, post_type, title, brand, category, url, content_text, 0.0 AS score
                 FROM {$table}
                 WHERE post_type = 'product' AND LOWER(title) LIKE %s COLLATE utf8mb4_bin
                 LIMIT 50",
                $pattern
            ));
            foreach ($rows as $r) {
                $title_l = mb_strtolower($r->title);
                // The boundary must also reject a code that CONTINUES past the
                // searched one. Measured on "tủ đông FT-158MA-S": both
                // "FT-158MA-S" (post 1053) and "FT-158MA-S-ML" (post 1542) were
                // scored exact, because a plain (?![a-z0-9]) treats the "-" of
                // "-ML" as a word boundary. They then tied at 1000 and the
                // length penalty picked the SHORTER title — which is the
                // variant, not the model the customer named. "-ML" is part of a
                // different model code, not punctuation: a hyphen followed by
                // more alphanumerics means the title carries a longer code.
                //
                // The real separator in this catalog is always a SPACE before
                // the brand (" - Hoshizaki" / " – Hoshizaki"), so requiring
                // "not immediately hyphen+alphanumeric" keeps every genuine
                // exact match and demotes only true code extensions. Same
                // reasoning applied on the left, for a code prefixed by another
                // segment.
                $is_exact = (bool) preg_match(
                    '/(?<![a-z0-9])(?<![a-z0-9]-)' . preg_quote($code_l, '/') . '(?![a-z0-9])(?!-[a-z0-9])/u',
                    $title_l
                );
                $score = ($is_exact ? 1000.0 : 100.0) - min(90.0, mb_strlen($title_l) * 0.1);
                if (!isset($candidates[$r->post_id]) || $candidates[$r->post_id]->score < $score) {
                    $r->score = $score;
                    $candidates[$r->post_id] = $r;
                }
            }
        }
        if (!$candidates) return [];

        $rows = array_values($candidates);
        usort($rows, function ($a, $b) {
            $cmp = (float) $b->score <=> (float) $a->score;
            // Same deterministic tie-break as verify_and_score() below — an
            // exact-match score already bakes in a length penalty
            // (min(90, strlen*0.1)), so an exact tie here means identical
            // title length too, which the length penalty alone can't break.
            return $cmp !== 0 ? $cmp : (int) $a->post_id <=> (int) $b->post_id;
        });
        return self::dedupe_by_content($rows);
    }
}
