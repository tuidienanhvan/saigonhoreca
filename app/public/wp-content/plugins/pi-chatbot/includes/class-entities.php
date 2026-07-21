<?php
/**
 * Entities — detect REAL brands in the customer's question by matching against
 * the list of REAL indexed brands, instead of letting full-text guess via tokens.
 *
 * Rationale (distilled after several rounds of retrieval bug fixes): full-text
 * bag-of-words treats "Hoshizaki" the same as every other word in the sentence —
 * if the sentence has extra common words (hi/at/contact...), those words can
 * overpower it, or the brand gets diluted inside one large AND condition.
 * Detecting the REAL brand first and then applying a HARD filter on the `brand`
 * column (WHERE brand = X) guarantees 100% of results match the brand the
 * customer actually asked about; full-text is only used to RANK within the
 * already-filtered scope — the "entity-first structured filtering" pattern
 * recommended for small-scale RAG without a vector DB.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Entities {
    const CACHE_KEY = 'pi_chatbot_known_brands';
    const TAXONOMY_CACHE_KEY = 'pi_chatbot_taxonomy_brands';

    /**
     * Build a map [commonly_used_name => canonical_full_name] from a list of full
     * brand names. Shared by both known_brands() (source: index table) and
     * taxonomy_brands() (source: taxonomy) — two different sources, but the
     * matching rules must stay identical; writing them twice separately would
     * cause them to drift apart on the next edit.
     */
    private static function build_brand_map($full_names) {
        $map = [];
        // Group base -> [full,...] first, do NOT write directly into $map —
        // unconditional overwrite (the old version) meant that two brands sharing
        // the same prefix before the dash (e.g. a future "Giorik-Italia" +
        // "Giorik-Spain") would collapse into one base "giorik", and whichever
        // brand MySQL returned last would win while the other silently vanished
        // forever from the hard-filter path WHERE brand = X — no error, no
        // warning, it would simply never be found again.
        $bases = [];
        foreach ($full_names as $full) {
            $full = trim((string) $full);
            if ($full === '') continue;
            $map[mb_strtolower($full)] = $full; // the full name always matches unambiguously

            // "Giorik-Italia" -> base "Giorik" (the part before the first dash) —
            // customers usually type the short name, without the country-of-origin
            // suffix (-Italia/-Germany...).
            $base = trim(preg_replace('/[\-–—].*$/u', '', $full));
            if ($base === '') continue;
            $lb = mb_strtolower($base);
            if (!isset($bases[$lb])) $bases[$lb] = [];
            if (!in_array($full, $bases[$lb], true)) $bases[$lb][] = $full;
        }
        foreach ($bases as $lb => $fulls) {
            // Ambiguous base (>=2 brands sharing the same root name) OR it collides
            // with another brand's full name -> do NOT create a shortcut. Both
            // brands can still be matched via their full name; only the short-name
            // shortcut is lost — same principle as detect_brand_from_title()
            // returning null on ambiguity instead of guessing blindly.
            if (count($fulls) === 1 && !isset($map[$lb])) $map[$lb] = $fulls[0];
        }
        // Compare longest names first — avoids a short brand (e.g. "Sammic")
        // accidentally matching as a substring inside another, longer brand name.
        // Sort once, then cache.
        uksort($map, function ($a, $b) { return mb_strlen($b) - mb_strlen($a); });
        return $map;
    }

    /**
     * List of REAL, INDEXED brands, as [common_base_name => full_name_in_DB] —
     * ALREADY SORTED by descending name length before caching, instead of calling
     * uksort() again on every extract_brand()/extract_brands() call (audit flag:
     * re-sorting the whole map every request even with only 6 brands today is
     * cheap but pointless when the sort result never changes while the cache is
     * still valid for the day).
     *
     * Used on the QUESTION side: only recognizes brands that actually have
     * products in the index, avoiding a hard filter on a brand with zero records
     * that would wrongly return an empty result.
     */
    public static function known_brands() {
        $cached = get_transient(self::CACHE_KEY);
        if (is_array($cached)) return $cached;

        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $full_names = $wpdb->get_col("SELECT DISTINCT brand FROM {$table} WHERE brand != ''");

        $map = self::build_brand_map($full_names);
        set_transient(self::CACHE_KEY, $map, DAY_IN_SECONDS);
        return $map;
    }

    /**
     * List of brands from the `product_brand` TAXONOMY — the canonical, complete
     * source (12 terms), NOT dependent on the index table.
     *
     * MUST be kept separate from known_brands(): known_brands() reads the index
     * table itself, and detect_brand_from_title() is called WHILE that shadow
     * table is still being built — using known_brands() there would cause a
     * chicken-and-egg bootstrap bug (reading a table that isn't finished yet, or
     * worse, reading the old table and locking itself to the 6 brands currently
     * present instead of seeing all 12 real brands in the taxonomy).
     */
    public static function taxonomy_brands() {
        $cached = get_transient(self::TAXONOMY_CACHE_KEY);
        if (is_array($cached)) return $cached;

        if (!taxonomy_exists('product_brand')) return [];
        $names = get_terms([
            'taxonomy'   => 'product_brand',
            'hide_empty' => false,
            'fields'     => 'names',
        ]);
        if (is_wp_error($names) || !is_array($names)) return [];

        $map = self::build_brand_map($names);
        set_transient(self::TAXONOMY_CACHE_KEY, $map, DAY_IN_SECONDS);
        return $map;
    }

    /**
     * Extract the brand from a product TITLE when the taxonomy field is empty.
     *
     * Matches against the list of 12 REAL brands in the taxonomy and returns the
     * CANONICAL FULL NAME — does NOT split the string on a dash. Splitting on the
     * dash is a real, measured bug: 8/12 brands follow the "Brand-Country" pattern
     * (Fujimak-Japan, Berjaya-Malaysia, Winterhalter-Germany...), so that approach
     * would extract "Japan"/"Malaysia"/"Germany" — wrong in 96/271 records, and
     * worse, that garbage value would leak into both the brand column and the
     * FULLTEXT index.
     *
     * Returns null when no brand matches OR when 2 or more brands match
     * (ambiguous — better to leave it empty and fall back to full-text than to
     * silently assign the wrong brand).
     */
    public static function detect_brand_from_title($title) {
        $haystack = mb_strtolower((string) $title);
        if ($haystack === '') return null;

        $found = [];
        foreach (self::taxonomy_brands() as $needle => $full) {
            if (mb_strlen($needle) < 3) continue;
            if (mb_strpos($haystack, $needle) === false) continue;
            if (!in_array($full, $found, true)) $found[] = $full;
        }
        return count($found) === 1 ? $found[0] : null;
    }

    public static function flush_cache() {
        delete_transient(self::CACHE_KEY);
        delete_transient(self::TAXONOMY_CACHE_KEY);
    }

    /** Returns the first REAL brand name matched (exactly as stored in the DB) if the question mentions one, otherwise null. */
    public static function extract_brand($question) {
        $brands = self::extract_brands($question);
        return $brands ? $brands[0] : null;
    }

    /**
     * Returns ALL real brands matched in the question (not just one) — allows
     * cross-brand comparisons like "Hoshizaki vs Giorik, which is better"
     * (previously only the first brand was recognized, and a second brand was
     * completely ignored — audit flag).
     */
    public static function extract_brands($question) {
        $q = mb_strtolower((string) $question);
        $brands = self::known_brands();
        $found = [];
        foreach ($brands as $needle => $full) {
            if (mb_strlen($needle) < 3) continue;
            if (mb_strpos($q, $needle) === false) continue;
            if (!in_array($full, $found, true)) $found[] = $full;
        }
        return $found;
    }
}
