<?php
/**
 * Retriever — the tiered FULLTEXT search pipeline (mandatory AND → quorum OR
 * → diacritic-folded fallback) and brand-scoped search. Split out of
 * class-retriever.php (2026-07-29, file had grown past 1000 lines) — pure
 * code motion, no logic changed; see class-retriever.php for the class this
 * trait belongs to.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

trait Pi_Chatbot_Retriever_FulltextSearch {

    /**
     * Vietnamese typed without diacritics ("tu dong" for "tủ đông") is not an
     * edge case — measured against the golden set, stripping diacritics from the
     * 37 accented questions collapses hit@3 from 0.838 to 0.324, and 18 of the
     * 19 lost queries return NOTHING AT ALL rather than something wrong. Phone
     * keyboards, habit and speed all produce it constantly.
     *
     * The exact-diacritic matching that causes this is deliberate and must stay:
     * it was added to fix real false positives where MySQL's default collation
     * treats "đá"/"đa"/"da" as equal, and relaxing it globally would reopen that.
     * So the folded match is a separate, LAST tier instead — the loose comparison
     * only ever applies to questions that already had no answer, where a
     * plausible match strictly beats an empty result.
     *
     * Two guards keep it from becoming a junk generator on those questions:
     *   1. EVERY substantial word must be present in folded form (not "any word
     *      matched"), and
     *   2. at least one of them must hit title/brand/category — the same
     *      "strong field hit" rule that stops a coincidental word buried in a
     *      long description from qualifying elsewhere in this file.
     *
     * @param string[] $long  Keywords at/above the FULLTEXT token length.
     * @param string[] $short Keywords below it.
     */
    private static function search_unaccented($long, $short, $top_k) {
        if (!$long) return []; // nothing substantial enough to match on

        global $wpdb;
        $table = self::table_name_for_search();

        // NOT re-filtering against a folded stopword list here, despite the
        // obvious appeal: several unaccented function words ("ban"/"bạn",
        // "gia"/"giá", "cua"/"của") ARE already on $stopwords in accented form
        // and do slip through. Adding that filter was tried and measured — it
        // cost 0.054 hit@3 on the unaccented benchmark (folding makes "cửa"
        // (door), a real product attribute, collide with "của" (of)) and
        // prevented nothing, because every small-talk question that leaks today
        // is matched by the FULLTEXT tier above and never reaches this one. The
        // file's own rule applies: only add a stopword against a real failing
        // case, or it is just whack-a-mole with a measurable price.
        $folded = [];
        foreach ($long as $w) {
            $f = self::fold_diacritics($w);
            if ($f !== '') $folded[] = $f;
        }
        if (!$folded) return [];

        // utf8mb4_unicode_520_ci is the collation that folds Vietnamese
        // diacritics (verified against this catalog: '%tu dong%' matches 31
        // products under 520_ci and 0 under unicode_ci/general_ci). It is applied
        // to the COMPARISON only, never to the stored data.
        $sql = "SELECT post_id, post_type, title, brand, category, url, content_text, 1.0 AS score
                FROM {$table} WHERE post_type = 'product'";
        $args = [];
        foreach ($folded as $f) {
            $sql .= " AND (title COLLATE utf8mb4_unicode_520_ci LIKE %s
                       OR brand COLLATE utf8mb4_unicode_520_ci LIKE %s
                       OR category COLLATE utf8mb4_unicode_520_ci LIKE %s
                       OR content_text COLLATE utf8mb4_unicode_520_ci LIKE %s)";
            $pattern = '%' . $wpdb->esc_like($f) . '%';
            array_push($args, $pattern, $pattern, $pattern, $pattern);
        }
        // Pull deeper than $top_k: the SQL filter is loose by design, and the
        // PHP scoring below is what actually decides the order.
        $sql .= ' LIMIT %d';
        $args[] = max(1, (int) $top_k) * 8;

        $rows = $wpdb->get_results($wpdb->prepare($sql, $args));
        if ($rows === null && $wpdb->last_error) {
            error_log('[pi-chatbot] unaccented fallback query failed: ' . $wpdb->last_error);
            return [];
        }
        if (!$rows) return [];

        $scored = [];
        foreach (self::only_products($rows) as $row) {
            $title_f = self::fold_diacritics($row->title);
            $brand_f = self::fold_diacritics($row->brand);
            $cat_f   = self::fold_diacritics($row->category);
            $body_f  = self::fold_diacritics($row->content_text);

            $weighted = 0.0;
            $strong = false;
            $all_present = true;
            foreach ($folded as $f) {
                if (mb_strpos($title_f, $f) !== false) {
                    $weighted += self::FIELD_WEIGHT_TITLE;
                    $strong = true;
                } elseif (mb_strpos($brand_f, $f) !== false || mb_strpos($cat_f, $f) !== false) {
                    $weighted += self::FIELD_WEIGHT_BRAND_CATEGORY;
                    $strong = true;
                } elseif (mb_strpos($body_f, $f) !== false) {
                    $weighted += self::FIELD_WEIGHT_CONTENT;
                } else {
                    $all_present = false;
                    break;
                }
            }
            if (!$all_present || !$strong) continue;

            // Short words (below the FULLTEXT token length) can't gate the SQL,
            // but they still carry meaning — "2 cửa", "4 họng" — so they add
            // score when present rather than being discarded entirely.
            foreach ($short as $w) {
                $f = self::fold_diacritics($w);
                if ($f !== '' && mb_strpos($title_f, $f) !== false) $weighted += self::FIELD_WEIGHT_TITLE;
            }

            $row->score = $weighted;
            $scored[] = $row;
        }
        if (!$scored) return [];

        usort($scored, function ($a, $b) {
            if ($a->score === $b->score) {
                // Tie-break on the shorter title: with no diacritics to
                // discriminate, the base model beats a longer variant of it, the
                // same reasoning search_by_product_code() uses.
                return mb_strlen($a->title) <=> mb_strlen($b->title);
            }
            return (float) $b->score <=> (float) $a->score;
        });

        return self::format_rows(
            array_slice(self::dedupe_by_content($scored), 0, $top_k),
            array_merge($long, $short)
        );
    }

    /**
     * Fetches index rows for the given ids, RETURNED IN THE ORDER GIVEN — a
     * plain `WHERE post_id IN (...)` comes back in whatever order the storage
     * engine likes, which would silently discard the ranking the caller just
     * computed.
     */
    private static function fetch_rows_by_ids(array $ids) {
        global $wpdb;
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) return [];
        $table = Pi_Chatbot_Indexer::table_name();
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, post_type, title, brand, category, url, content_text, 0.0 AS score
             FROM {$table} WHERE post_id IN ({$placeholders})",
            $ids
        ));
        if (!$rows) return [];

        $by_id = [];
        foreach ($rows as $row) $by_id[(int) $row->post_id] = $row;
        $ordered = [];
        foreach ($ids as $id) {
            if (isset($by_id[$id])) $ordered[] = $by_id[$id];
        }
        return self::only_products($ordered);
    }

    /**
     * Recognize MULTIPLE brands in a single question (previously only the first
     * matched brand was used, any brand from the second onward was dropped
     * entirely — audit flag: a comparison question like "Hoshizaki với Giorik
     * cái nào tốt hơn" ["which is better, Hoshizaki or Giorik"] only hard-filtered
     * on one of the two brands). 1 brand: handled as before. ≥2 brands: split
     * $top_k evenly across each brand and merge the results — ensuring EVERY
     * brand gets representation in the context, preventing a brand with a
     * higher full-text score from completely drowning out the other brand in a
     * comparison question.
     */
    private static function search_within_brands($brands, $long, $short, $top_k) {
        if (count($brands) === 1) {
            return self::search_within_brand($brands[0], $long, $short, $top_k);
        }
        $per_brand = max(2, (int) floor($top_k / count($brands)));
        $results = [];
        foreach ($brands as $brand) {
            $results = array_merge($results, self::search_within_brand($brand, $long, $short, $per_brand));
        }
        return $results;
    }

    /**
     * Search within the scope of a single confidently-recognized brand. Try
     * full-text on the remaining keywords first (ranks specific products more
     * accurately); if empty (the question only mentions the brand with no
     * description, or the description matches no words) still return a few
     * REAL products of that brand (no keyword filtering) — the AI always has
     * real data to answer from instead of wrongly falling into "not found".
     */
    private static function search_within_brand($brand, $long, $short, $top_k) {
        if ($long || $short) {
            $results = self::search_fulltext($long, $short, $top_k, $brand);
            if ($results) return $results;
        }

        // Fallback: full-text matched nothing within the brand's scope (the
        // question only mentions the brand with no description, or the
        // description matches no words). Previously this was missing
        // "AND post_type = 'product'" and called format_rows() directly,
        // bypassing only_products() — violating the products-only rule that
        // every other path in this file follows, letting a project/page tagged
        // with the brand slip in as a source. Added the SQL condition + still
        // re-filter with only_products() (belt and suspenders), and pass $words
        // into format_rows() so make_excerpt() trims at the actual match point
        // instead of grabbing the intro paragraph.
        //
        // ORDER BY CHAR_LENGTH DESC instead of post_id DESC: this path has no
        // relevance signal by definition (full-text was already tried and
        // matched nothing), so "product with the fullest description" is a
        // better representative than "most recently created product" — measured
        // 43.6% of products under 200 characters are just a bare spec sheet.
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, post_type, title, brand, category, url, content_text, 1.0 AS score
             FROM {$table} WHERE brand = %s AND post_type = 'product'
             ORDER BY CHAR_LENGTH(content_text) DESC LIMIT %d",
            $brand, $top_k
        ));
        return self::format_rows(self::only_products($rows), array_merge($long, $short));
    }

    /**
     * Full-text BOOLEAN MODE — 2 tiers, no longer forcing AND for EVERY question
     * (bug found during audit: a natural multi-word question like "tủ lạnh công
     * nghiệp inox 4 cánh" ["industrial 4-door stainless steel fridge"] produces
     * ~4 mandatory tokens; if no record has all 4, the result was wrongly empty
     * even though a product matched 3/4).
     *
     * Tier 1 — AND (each word +mandatory): tried first, ALWAYS filters the
     *   result down to post_type=product afterward — not just "check whether any
     *   product exists then return the raw rows" like the previous version
     *   (2 real bugs caught from that old approach):
     *     (a) AND matched product AND project simultaneously — the old version
     *         returned raw rows, so unrelated projects leaked into the context
     *         even when real products were also present.
     *     (b) The shortcut "if only 1 keyword remains, skip the product check"
     *         (old audit) turned out to be UNSAFE: the small-talk message "Tôi
     *         muốn có người yêu" ["I want a girlfriend/boyfriend"], after
     *         stopword filtering, left only "người" ("person") — an extremely
     *         common word in every product description ("phù hợp cho nhiều
     *         người" ["suits many people"], "đội ngũ nhân viên" ["staff
     *         team"]...) — the old version blindly accepted this 1-word AND
     *         result as a source even though it had nothing to do with any
     *         product.
     *   Fix: the count($words) shortcut was REMOVED ENTIRELY — post_type=product
     *   is always filtered regardless of how many keywords remain — even a
     *   single common keyword must now prove it matches a real product before
     *   being treated as a result.
     * Tier 2 — quorum (OR weighted by number of matched words, no + sign): runs
     *   when tier 1 has no products AND there are ≥2 keywords (with 1 keyword,
     *   AND=OR, so no fallback is needed — it's already certain no product
     *   matched). Filters by a relative score threshold (≥35% of the top score),
     *   then KEEPS ONLY real products — avoiding a repeat of the old
     *   "chào"/"bên"/"xe máy điện"/"người" bugs. If quorum also finds no products
     *   → return empty; better to say "no information" than to hand back an
     *   irrelevant source.
     *
     * @param string[] $words Keywords with stopwords already filtered (not yet suffixed with +/*).
     * @param string   $brand If non-empty, adds an additional WHERE brand = $brand constraint.
     */
    private static function search_fulltext($long, $short, $top_k, $brand = '') {
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $all_words = array_merge($long, $short);
        // Over-fetch: dedupe_by_content() can shrink the result set (a group of
        // 8 products with identical content_text keeps only 2 representatives)
        // — fetch extra before the real LIMIT, then trim back down to exactly
        // $top_k after merging, so one duplicate group doesn't shrink the
        // result count.
        $fetch_k = $top_k * 3;

        if (empty($long)) {
            // Scan ALL 4 columns that verify_and_score() uses as its haystack
            // (title, brand, category, content_text) — previously only
            // content_text was filtered, so a 2-character token that only lived
            // in the title (a product code like "hr", "se", "ob") got dropped by
            // SQL before PHP ever had a chance to verify it. Principle: SQL is
            // the RECALL filter, verify_and_score() is the PRECISION filter —
            // recall narrower than precision is backwards.
            $like_query = "";
            $args = [];
            foreach ($short as $w) {
                // LOWER(...) COLLATE utf8mb4_bin: matches EXACT diacritics (bin)
                // but is still case-insensitive (tokens are already lowercased,
                // while product titles capitalize model codes like "HR-", "FT-"
                // — plain bin without LOWER() would mismatch on case and lose
                // valid results; this bug was caught while testing
                // HR-126MA-S).
                $like_query .= " AND (LOWER(title) LIKE %s COLLATE utf8mb4_bin OR LOWER(brand) LIKE %s COLLATE utf8mb4_bin
                                  OR LOWER(category) LIKE %s COLLATE utf8mb4_bin OR LOWER(content_text) LIKE %s COLLATE utf8mb4_bin)";
                $pattern = self::like_pattern(mb_strtolower($w));
                array_push($args, $pattern, $pattern, $pattern, $pattern);
            }
            $sql = "SELECT post_id, post_type, title, brand, category, url, content_text, 1.0 AS score
                    FROM {$table} WHERE 1=1 " . $like_query;
            if ($brand !== '') {
                $sql .= " AND brand = %s";
                $args[] = $brand;
            }
            $sql .= " ORDER BY (post_type = 'product') DESC LIMIT %d";
            $args[] = $fetch_k;
            $rows = $wpdb->get_results($wpdb->prepare($sql, $args));
            $rows = self::verify_and_score($rows, $long, $short);
            $product_rows = self::dedupe_by_content(self::only_products($rows));
            if ($product_rows) return self::format_rows(array_slice($product_rows, 0, $top_k), $all_words);
            return [];
        }

        // P2 audit 2026-07-20 — true IDF for EVERY keyword (including $short),
        // shared between building the SQL and score weighting so the two stay
        // consistent.
        $idf = self::compute_idf($all_words);

        // Tier 1 — FULLTEXT on $long: only RARE words (high idf) are
        // +mandatory, common words remain optional (P2: relaxing the hard AND —
        // previously EVERY $long word was mandatory, so a natural multi-word
        // question like "tủ lạnh công nghiệp inox 4 cánh" easily returned
        // wrongly empty because no product had all 4 words).
        // Also adds a mandatory LIKE for RARE $short words — this is the actual
        // fix for the "đá" bug: innodb_ft_min_token_size=3 means words under 3
        // characters CANNOT be put into MATCH AGAINST, but they still need to
        // actually FILTER results, not just add a meaningless 0.01 score bonus
        // like the old version did.
        $rare_long = array_values(array_filter($long, function ($w) use ($idf) {
            return ($idf[mb_strtolower($w)]['idf'] ?? self::IDF_FLOOR) > self::IDF_RARE_THRESHOLD;
        }));
        $and_terms = array_map(function ($w) use ($rare_long) {
            return (in_array($w, $rare_long, true) ? '+' : '') . $w . '*';
        }, $long);
        $fulltext_query = implode(' ', $and_terms);

        $rare_short = array_values(array_filter($short, function ($w) use ($idf) {
            return ($idf[mb_strtolower($w)]['idf'] ?? self::IDF_FLOOR) > self::IDF_RARE_THRESHOLD;
        }));

        // P2 audit 2026-07-20 (regression patch) — MATCH()...AGAINST() itself
        // has NEVER had its collation fixed (only the auxiliary LIKE clauses in
        // compute_idf()/mandatory_like were forced to utf8mb4_bin) — the
        // FULLTEXT index uses the column's default collation
        // (utf8mb4_unicode_520_ci, loose diacritic matching) to build tokens at
        // index time, which cannot be fixed with a COLLATE on the query side.
        // Measured in practice: 'cửa' = 'của' under the default collation → 1
        // (SQL considers them equal!) — "+cửa*" ["door"] as a mandatory AND term
        // silently and wrongly matched "của" ["of" — an extremely common filler
        // word present in almost every Vietnamese sentence]. The old
        // verify_and_score() only required that AT LEAST 1 word matched for real
        // in total (accent-sensitive, via mb_strpos) — not that EVERY mandatory
        // word matched for real — so a product that genuinely only contained
        // "giờ"/"mở" ["hour"/"open" — matched randomly in the content,
        // unrelated] still slipped through because "của" (not "cửa") had
        // silently unlocked the AND at the SQL tier. Fixed at the root: after
        // SQL returns candidates, EVERY mandatory word (rare_long + rare_short)
        // must be verified to match EXACTLY (diacritic-sensitive) via PHP —
        // restoring the actual "AND" promise that tier 1 claims to make.
        $rows = self::run_fulltext_query($table, $fulltext_query, $brand, $fetch_k, $rare_short);
        $rows = self::verify_and_score($rows, $long, $short, $idf);
        $product_rows = self::require_field_evidence(self::require_literal_all(
            self::dedupe_by_content(self::only_products($rows)),
            array_merge($rare_long, $rare_short)
        ));
        if ($product_rows) {
            return self::format_rows(array_slice($product_rows, 0, $top_k), $all_words);
        }

        // Fallback A — drop the mandatory LIKE constraint on rare $short words
        // (may be too strict for a question that describes something
        // indirectly without using that exact word). "đá" still affects the
        // score via verify_and_score() even though it's no longer hard-filtered
        // here. Still keeps require_literal_all for $rare_long — relaxing
        // $short, not the meaning of AND.
        if ($rare_short) {
            $rows = self::run_fulltext_query($table, $fulltext_query, $brand, $fetch_k, []);
            $rows = self::verify_and_score($rows, $long, $short, $idf);
            $product_rows = self::require_field_evidence(self::require_literal_all(
                self::dedupe_by_content(self::only_products($rows)),
                $rare_long
            ));
            if ($product_rows) {
                return self::format_rows(array_slice($product_rows, 0, $top_k), $all_words);
            }
        }

        if (count($long) < 2) return [];

        // Tier 2 — pure quorum (no + sign) on $long, benchmarked against the
        // IDF-weighted score instead of the raw MySQL score like the old version.
        $or_query = implode(' ', array_map(function ($w) { return $w . '*'; }, $long));
        $rows = self::run_fulltext_query($table, $or_query, $brand, $fetch_k);
        if (!$rows) return [];

        $rows = self::verify_and_score($rows, $long, $short, $idf);
        if (!$rows) return [];

        // Benchmark the score ONLY among products, not across all of $rows
        // (products mixed with projects). verify_and_score() re-sorts purely by
        // score, no longer preserving the "products before projects" order that
        // run_fulltext_query() deliberately applies (see the comment there); if
        // $top_score were taken from $rows[0] after the re-sort, a project page
        // inflated by keyword repetition could push the threshold up and wrongly
        // exclude a valid, lower-scoring product — reproducing exactly the bug
        // that the old SQL ordering was designed to prevent.
        $products = self::only_products($rows);
        if (!$products) return [];

        // P2 audit 2026-07-20 (regression patch) — Tier 2 (quorum) is the
        // RISKIEST tier in the file: no + sign, accepts partial matches. The
        // relative threshold (35% of the top score) isn't enough when EVERY
        // candidate is just a random match inside content_text — there's no
        // "genuinely relevant real product" signal to compare 35% against.
        // Pre-filter: keep only products with AT LEAST 1 word matching in
        // title/brand/category (has_strong_field_hit, see verify_and_score()).
        // If no products survive this filter → return empty, consistent with
        // the "better to say no information than hand back the wrong source"
        // principle documented in this function's docblock.
        $products = array_values(array_filter($products, function ($r) {
            return !empty($r->has_strong_field_hit);
        }));
        if (!$products) return [];

        // 2026-07-27 fix — a real customer-visible bug: "hút mùi" (exhaust hood,
        // literally "extract odor") and "hút chân không" (vacuum sealer, literally
        // "extract vacuum") share only the generic Vietnamese verb "hút" ("to
        // extract/suck") — the two are otherwise unrelated appliance categories.
        // A quorum match on "hút" ALONE (1 word, landing in the title, so it
        // passes the has_strong_field_hit filter above) was enough to seat 3
        // completely unrelated Sammic vacuum-sealer products next to the real
        // Halton exhaust-hood results, and the generator — handed that mixed,
        // contradictory context — produced a garbled, hallucinated answer.
        // General fix, not a special case for "hút": Tier 2 quorum is the ONLY
        // tier with no mandatory-AND word at all, so it is the one place a
        // single shared morpheme between two unrelated compound terms can seat a
        // product on its own. Requiring at least 2 DISTINCT query words to
        // genuinely match (when the query has ≥2 distinct substantive words to
        // begin with) closes that gap without touching Tier 1 (still governed by
        // require_literal_all's real AND) or the brand-scoped path (a different
        // function entirely, search_within_brand(), unaffected by this change).
        $distinct_long = count(array_unique($long));
        $min_hits = min(2, $distinct_long);
        $products = array_values(array_filter($products, function ($r) use ($min_hits) {
            return (int) ($r->hits ?? 0) >= $min_hits;
        }));
        if (!$products) return [];

        $top_score = max(array_map(function ($r) { return (float) $r->score; }, $products));
        $products = array_values(array_filter($products, function ($r) use ($top_score) {
            return $top_score > 0 && ((float) $r->score) >= $top_score * self::OR_FALLBACK_SCORE_RATIO;
        }));
        $products = self::dedupe_by_content($products);
        return self::format_rows(array_slice($products, 0, $top_k), $all_words);
    }
}
