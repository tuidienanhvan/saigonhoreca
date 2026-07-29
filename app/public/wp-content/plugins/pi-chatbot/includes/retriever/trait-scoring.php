<?php
/**
 * Retriever — IDF measurement, field/rarity-weighted scoring, dedup, and the
 * low-level fulltext query runner. Split out of class-retriever.php
 * (2026-07-29, file had grown past 1000 lines) — pure code motion, no logic
 * changed; see class-retriever.php for the class this trait belongs to.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

trait Pi_Chatbot_Retriever_Scoring {

    /**
     * Total number of live products in the index — the denominator for IDF.
     * Cached per-request (search() only calls this indirectly once per question,
     * via compute_idf()).
     */
    private static $product_count_cache = null;

    private static function total_product_count() {
        if (self::$product_count_cache !== null) return self::$product_count_cache;
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        self::$product_count_cache = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table} WHERE post_type = 'product'"
        );
        return self::$product_count_cache;
    }

    /** Per-REQUEST memo, cleared by nothing (a new PHP process starts empty) —
     *  see compute_idf()'s docblock for why this exists ALONGSIDE the transient
     *  cache below rather than instead of it. */
    private static $idf_request_cache = [];

    /**
     * Measure the TRUE rarity (document frequency) of each keyword — both $long
     * and $short — then convert to IDF. This is the shared figure used both to
     * decide which words are mandatory in the SQL and for the score weighting
     * in verify_and_score(), so the two stay consistent with each other.
     *
     * Cached at two levels, added 2026-07-26 after measuring the actual cost:
     * uncached, this was 1 four-way leading-wildcard `LIKE` COUNT query PER
     * KEYWORD PER QUERY VARIANT, with no de-duplication across variants or
     * requests — 7 full-table scans/request on the fast path, 17-36 on the max
     * path (search_hybrid() runs search() once per query leg, and a keyword
     * repeated across legs — very common, e.g. a brand name appearing in both
     * the original question and a router-generated rewrite — was counted
     * fresh every time). The docblock here previously claimed "COUNT(*) on
     * post_type via the index at under 5ms" — false: there was no index on
     * post_type until this same commit added one (see class-indexer.php
     * DB_VERSION 5), and even with it, a LEADING-wildcard LIKE against
     * content_text can never use ANY index, so the claim was never accurate
     * for the dominant cost here regardless.
     *   1. Request-scoped static array — free, catches the same word appearing
     *      in multiple query variants of ONE request.
     *   2. Transient, keyed on the word + pi_chatbot_index_version — catches
     *      the same word appearing in a LATER, unrelated request. Reindexing
     *      bumps the version (already wired for the answer cache and the
     *      router cache), so this needs no new invalidation hook of its own.
     *
     * Measured directly (counting real SQL statements via the `query` filter,
     * 528-product catalog), not estimated:
     *   "máy rửa chén công nghiệp" cold (first time these words are asked)  — 7 queries
     *   the SAME question again (transient warm)                            — 1 query
     *   a DIFFERENT question sharing 3/4 keywords (transient partly warm)    — 6 queries
     * The 7→1 case is the realistic steady-state win: popular questions repeat.
     *
     * @return array<string,array{df:int,idf:float}> key = lowercased word
     */
    private static function compute_idf($words) {
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $total = max(1, self::total_product_count());
        $index_version = (int) get_option('pi_chatbot_index_version', 0);
        $out = [];
        foreach ($words as $w) {
            $key = mb_strtolower($w);
            if (isset($out[$key])) continue;

            if (isset(self::$idf_request_cache[$key])) {
                $out[$key] = self::$idf_request_cache[$key];
                continue;
            }

            $transient_key = 'pi_chatbot_df_' . $index_version . '_' . md5($key);
            $cached = get_transient($transient_key);
            if (is_array($cached) && isset($cached['df'], $cached['idf'])) {
                self::$idf_request_cache[$key] = $cached;
                $out[$key] = $cached;
                continue;
            }

            // Numeric tokens are matched on word boundaries everywhere else
            // (see word_matches()), so DF must be counted the same way or the
            // two disagree: '%2%' matches every model code containing a 2 and
            // would report df ≈ the whole catalog, flooring IDF to 0.15 and
            // making the very word the customer emphasised ("2 cửa") weightless
            // — the exact failure mode this fix exists to remove. A
            // space-padded LIKE is the SQL approximation of a word boundary;
            // it is intentionally a little narrower than word_matches() (it
            // won't see "2-cửa"), which is the safe direction here: DF is only
            // an estimate of rarity, and PHP does the authoritative matching.
            $like = self::like_pattern($key);
            // P2 audit 2026-07-20 — caught the default collation
            // (utf8mb4_unicode_520_ci) doing LOOSE Vietnamese diacritic matching:
            // LIKE '%đá%' ("ice") WRONGLY matched "Bàn đông" ("freezer table" — does
            // not contain "đá" at all). Measured: df("đá") = 164 under the default
            // collation, but only 66-80 real products actually contain "đá" (counted
            // via PHP's mb_strpos, which does the accurate comparison in
            // verify_and_score()). The inflated df pushed IDF down incorrectly
            // (0.84, below the "rare" threshold of 1.0) → the very word we needed to
            // rescue was NOT treated as rare. Force COLLATE utf8mb4_bin on the LIKE
            // so DF is counted with EXACT diacritic matching, consistent with how
            // verify_and_score() already compares (PHP's mb_strpos is unaffected by
            // MySQL collation, and is already "correct" — the SQL COUNT() must
            // follow it, not the other way around).
            $df = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE post_type = 'product' AND
                 (LOWER(title) LIKE %s COLLATE utf8mb4_bin OR LOWER(brand) LIKE %s COLLATE utf8mb4_bin
                  OR LOWER(category) LIKE %s COLLATE utf8mb4_bin OR LOWER(content_text) LIKE %s COLLATE utf8mb4_bin)",
                $like, $like, $like, $like
            ));
            $df = max(1, $df);
            $result = ['df' => $df, 'idf' => max(self::IDF_FLOOR, log($total / $df))];

            self::$idf_request_cache[$key] = $result;
            set_transient($transient_key, $result, self::IDF_CACHE_TTL);
            $out[$key] = $result;
        }
        return $out;
    }

    /**
     * Merge records whose content_text is IDENTICAL (whitespace-normalized) —
     * measured on real data: 28 products / 9 groups, the largest group having
     * 8 products (same model, different drawer/door configuration — REAL
     * variants, different titles, but identical content_text). Without merging,
     * a query that hits this group could fill the entire top-K with a single
     * spec repeated 8 times.
     * Keeps up to MAX_PER_DUPLICATE_GROUP top-scoring representatives per group
     * — still shows the customer multiple options, without pretending there's
     * only 1 product.
     */
    private static function dedupe_by_content($rows) {
        if (!is_array($rows)) return [];
        $seen = [];
        $kept = [];
        foreach ($rows as $r) {
            $key = md5(trim(preg_replace('/\s+/u', ' ', (string) $r->content_text)));
            $count = $seen[$key] ?? 0;
            if ($count >= self::MAX_PER_DUPLICATE_GROUP) continue;
            $seen[$key] = $count + 1;
            $kept[] = $r;
        }
        return $kept;
    }

    /**
     * P2 audit 2026-07-20 — replaces the flat 0.01/word bonus (which could
     * never flip real rankings — the measured gap between adjacent ranks was
     * 0.1-0.3) with a two-dimensional weighted score: (a) BY FIELD — a match in
     * the title is worth more than brand/category, which is worth more still
     * than content_text (supporting description); (b) BY RARITY (IDF) — a word
     * appearing in only a few products (like "đá" ["ice"], 66/381) is worth
     * much more than a common word (like "động", which appears in many
     * products via "tự động" ["automatic"]). $idf is already computed in
     * search_fulltext() via compute_idf() — passed in to avoid recomputing
     * COUNT() repeatedly for the same question; if called standalone (not via
     * search_fulltext, e.g. in tests) it computes it itself.
     */
    private static function verify_and_score($rows, $long, $short, $idf = null) {
        if (!is_array($rows)) return [];
        // array_unique — a query word repeated in $long/$short (the router has been
        // observed to emit a duplicated phrase, e.g. "hút mùi hút mùi công nghiệp")
        // must not count twice: it inflates both $weighted score AND $hits (the
        // distinct-word-match count added 2026-07-27, see the quorum-tier caller)
        // for a row that only genuinely matched ONE real word.
        $all_words = array_values(array_unique(array_merge($long, $short)));
        if (!$all_words) return [];
        if ($idf === null) $idf = self::compute_idf($all_words);

        $kept = [];
        foreach ($rows as $r) {
            $title_l = mb_strtolower((string) $r->title);
            $brand_l = mb_strtolower((string) $r->brand);
            $cat_l   = mb_strtolower((string) $r->category);
            $body_l  = mb_strtolower((string) $r->content_text);

            $weighted = 0.0;
            $hits = 0;
            // P2 audit 2026-07-20 (regression patch) — the old absolute score
            // floor (OR_FALLBACK_MIN_ABS_SCORE) was removed when switching to
            // the IDF-based scale without an equivalent replacement — reopening
            // an F7-class bug: a message completely unrelated to products
            // ("cho hỏi giờ mở cửa" ["what time do you open"]) matching
            // "giờ"/"mở" ["hour"/"open"] purely by COINCIDENCE inside
            // content_text (e.g. "mỗi giờ" ["every hour"], "mở rộng khả năng"
            // ["expand capability"] — not talking about the product at all)
            // still scored high enough to clear the 35% relative threshold.
            // Measured in practice: content-only field score for these 2 words
            // added up to 2.588, enough to slip through. Fixed at the root: track
            // whether at least one word matched in TITLE/BRAND/CATEGORY — a
            // signal of a GENUINELY relevant product, unlike a coincidental word
            // buried in a long content_text.
            $has_strong_field_hit = false;
            foreach ($all_words as $w) {
                $wl = mb_strtolower($w);
                if (self::word_matches($title_l, $wl)) {
                    $field_w = self::FIELD_WEIGHT_TITLE;
                    $has_strong_field_hit = true;
                } elseif (self::word_matches($brand_l, $wl) || self::word_matches($cat_l, $wl)) {
                    $field_w = self::FIELD_WEIGHT_BRAND_CATEGORY;
                    $has_strong_field_hit = true;
                } elseif (self::word_matches($body_l, $wl)) {
                    $field_w = self::FIELD_WEIGHT_CONTENT;
                } else {
                    continue;
                }
                $hits++;
                $weighted += $field_w * ($idf[$wl]['idf'] ?? self::IDF_FLOOR);
            }
            if ($hits === 0) continue;

            // Keep the raw MySQL relevance aside; it is folded in below, once
            // the whole result set is known and it can be normalized against
            // the set's own maximum (see MYSQL_SCORE_WEIGHT).
            $r->mysql_score = (float) $r->score;
            $r->score = $weighted;
            $r->has_strong_field_hit = $has_strong_field_hit;
            // Distinct query words that genuinely matched this row (title/brand/
            // category/content) — see the quorum-tier minimum-match-count check in
            // search_fulltext() for why this exists.
            $r->hits = $hits;
            $kept[] = $r;
        }

        $max_mysql = 0.0;
        foreach ($kept as $r) {
            if ($r->mysql_score > $max_mysql) $max_mysql = $r->mysql_score;
        }
        if ($max_mysql > 0) {
            foreach ($kept as $r) {
                $r->score += self::MYSQL_SCORE_WEIGHT * ($r->mysql_score / $max_mysql);
            }
        }

        usort($kept, function($a, $b) {
            $cmp = (float) $b->score <=> (float) $a->score;
            if ($cmp !== 0) return $cmp;
            // Tie-break, not just a stability nicety: two near-duplicate
            // products (e.g. "Tủ Chiên Nhúng Đôi FSSDF12D" vs the base model
            // "Tủ Chiên Nhúng Đôi SDF12D") can score EXACTLY equal — same
            // keyword hits, same field weights. Without a deterministic
            // secondary key, which one lands first depends on the physical row
            // order MySQL happens to return, which is NOT guaranteed stable —
            // confirmed live: the same query returned a different top-1 after
            // unrelated writes elsewhere in the table changed that order,
            // while the code computing the tie never changed. Same reasoning
            // Tier 3 (search_unaccented) already uses for this exact shape of
            // tie: a shorter title is the base model, a longer one is usually
            // a suffixed variant of it — shorter wins.
            return mb_strlen((string) $a->title) <=> mb_strlen((string) $b->title);
        });

        return $kept;
    }

    private static function only_products($rows) {
        if (!is_array($rows)) return [];
        return array_values(array_filter($rows, function ($r) { return $r->post_type === 'product'; }));
    }

    /**
     * Requires at least one keyword to have matched in TITLE/BRAND/CATEGORY
     * (verify_and_score()'s has_strong_field_hit), not only in content_text.
     *
     * This is the SAME safety net Tier 2 (quorum) already applies — the fix
     * here is applying it to Tier 1 (the mandatory AND tier) too, where it was
     * previously missing entirely. require_literal_all() only proves every
     * mandatory word is present SOMEWHERE in the row; it says nothing about
     * WHERE, and content_text is long free-text where any short/common word
     * can appear by coincidence.
     *
     * Measured, concretely: "Show me your completed project samples" (a real
     * quick-reply button in the widget) passed Tier 1's AND with ALL of
     * show/your/completed/project/samples individually rare enough to be
     * +mandatory — but every single hit was buried in unrelated content_text
     * ("...designed for projects that require...", "...for all your meat
     * ageing needs..."), on 2 products with nothing to do with the question.
     * Tier 1 returned them with full confidence because — unlike Tier 2 — it
     * never checked WHERE the mandatory words actually matched.
     *
     * Root cause, more precisely: a $short word (< MIN_TOKEN_LENGTH, e.g. the
     * "me" in "show me...") that turns out to be rare becomes a mandatory LIKE
     * filter with NO word-boundary protection (short substring matching is
     * kept on purpose — see like_pattern() — so real product-code fragments
     * like "hr"/"se" still work). An unbounded substring match is exactly the
     * kind of "matched, but not meaningfully" evidence this filter is meant to
     * catch, the same way it already catches a common word buried deep in a
     * long content_text field.
     */
    private static function require_field_evidence($rows) {
        if (!is_array($rows)) return [];
        return array_values(array_filter($rows, function ($r) { return !empty($r->has_strong_field_hit); }));
    }

    /**
     * Requires EVERY word in $mandatory_words to match with EXACT diacritics
     * (via mb_strpos, unaffected by MySQL's diacritic-stripping collation)
     * SOMEWHERE in title/brand/category/content — not just "at least 1 word
     * matched in total" like verify_and_score(). Restores the true meaning of
     * "AND" that +word* in FULLTEXT claims to provide but doesn't, because
     * MATCH()...AGAINST() uses the column's default collation (loose diacritic
     * matching — "cửa"="của"). Empty $mandatory_words → no filtering (no word
     * is mandatory at this tier).
     */
    private static function require_literal_all($rows, $mandatory_words) {
        if (!$mandatory_words) return $rows;
        return array_values(array_filter($rows, function ($r) use ($mandatory_words) {
            return self::all_words_literally_present($r, $mandatory_words);
        }));
    }

    private static function all_words_literally_present($row, $words) {
        $haystack = mb_strtolower(
            (string) $row->title . ' ' . (string) $row->brand . ' ' .
            (string) $row->category . ' ' . (string) $row->content_text
        );
        foreach ($words as $w) {
            if (!self::word_matches($haystack, mb_strtolower($w))) return false;
        }
        return true;
    }

    /**
     * A single prepare() call (no more nested prepare() calls — the old pattern
     * was safe on the current WP setup since $brand always comes from a DB
     * column whitelist, but was still a smell that's easy to misread during
     * review — flagged in the 2026-07-18 audit). The brand condition is folded
     * directly into the SQL + args array instead of pre-concatenated strings.
     *
     * @param string[] $mandatory_like P2: rare short words that can't be indexed
     *   by FULLTEXT (innodb_ft_min_token_size=3) — added as a mandatory LIKE AND
     *   condition so they can actually FILTER results (see search_fulltext()).
     */
    private static function run_fulltext_query($table, $match_query, $brand, $top_k, $mandatory_like = []) {
        global $wpdb;
        $sql = "SELECT post_id, post_type, title, brand, category, url, content_text,
                       MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE) AS score
                FROM {$table}
                WHERE MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE)";
        $args = [$match_query, $match_query];

        foreach ($mandatory_like as $w) {
            // Same reasoning as the COLLATE utf8mb4_bin in compute_idf() — the
            // column's default collation loosely matches Vietnamese diacritics
            // (e.g. "đông" falsely matching "%đá%"), and a "mandatory" filter
            // that matches loosely isn't actually filtering anything. LOWER(...)
            // is applied before COLLATE bin so it stays case-insensitive (model
            // codes are capitalized like "HR-", "FT-" — plain bin without
            // LOWER() would mismatch on case and lose valid results; this bug
            // was caught while testing HR-126MA-S).
            $sql .= " AND (LOWER(title) LIKE %s COLLATE utf8mb4_bin OR LOWER(brand) LIKE %s COLLATE utf8mb4_bin
                      OR LOWER(category) LIKE %s COLLATE utf8mb4_bin OR LOWER(content_text) LIKE %s COLLATE utf8mb4_bin)";
            $pattern = self::like_pattern(mb_strtolower($w));
            array_push($args, $pattern, $pattern, $pattern, $pattern);
        }

        if ($brand !== '') {
            $sql .= ' AND brand = %s';
            $args[] = $brand;
        }

        // Prioritize post_type='product' before ordering by score — a customer
        // asking "do you sell X" almost always wants products, while long,
        // free-flowing project pages can easily score equal or higher at the
        // quorum (OR) tier just by repeating a keyword many times in the text,
        // without actually being "about" what the customer asked.
        $sql .= " ORDER BY (post_type = 'product') DESC, score DESC LIMIT %d";
        $args[] = $top_k;

        $rows = $wpdb->get_results($wpdb->prepare($sql, $args));
        // get_results() returns null (not []) when the query errors — log it in
        // one place at this boundary instead of letting every downstream
        // function raise its own scattered warning, which would only obscure
        // the real cause (missing table, broken FULLTEXT index, etc.).
        if ($rows === null && $wpdb->last_error) {
            error_log('[pi-chatbot] retrieval query that failed: ' . $wpdb->last_error);
        }
        return $rows;
    }

    private static function format_rows($rows, $words = []) {
        if (!is_array($rows)) return [];
        $results = [];
        foreach ($rows as $row) {
            $results[] = [
                'post_id'  => (int) $row->post_id,
                'title'    => $row->title,
                'brand'    => $row->brand,
                'category' => $row->category,
                'url'      => $row->url,
                'excerpt'  => self::make_excerpt($row->content_text, self::EXCERPT_LENGTH, $words),
                'score'    => isset($row->score) ? (float) $row->score : 0.0,
            ];
        }
        return $results;
    }
}
