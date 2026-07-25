<?php
/**
 * Retriever — weighted full-text search, no embeddings/vector DB.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Retriever {

    // Constants used repeatedly throughout the file (audit flag: previously these
    // were hardcoded numbers scattered around — 3 appeared both in
    // extract_keyword_list and in docblocks, 400 only in make_excerpt — now named
    // for clarity and so they can be tuned in one place when needed).
    const MIN_TOKEN_LENGTH = 3; // matches MySQL's default innodb_ft_min_token_size
    const EXCERPT_LENGTH = 400;
    const OR_FALLBACK_SCORE_RATIO = 0.35; // quorum: keep results scoring ≥35% of the top score

    /**
     * P2 audit 2026-07-20 — bug fix: the flat 0.01/word bonus in verify_and_score()
     * could never actually flip real rankings (the measured gap between ranks was
     * 0.1-0.3, while the old max bonus was 4×0.01=0.04 — smaller than a single rank
     * step). At the same time, 2-character words (below MIN_TOKEN_LENGTH) can't be
     * indexed by FULLTEXT (innodb_ft_min_token_size=3), so they CANNOT be fed
     * directly into MATCH AGAINST — the correct fix is: (a) measure true rarity
     * (IDF) for every word, including short ones, (b) rare short words get added as
     * a mandatory LIKE condition (not just a score bonus), (c) score weighting is
     * IDF × field position (title ≫ brand/category > content) instead of flat.
     * The original bug case: "máy làm đá tự động" ("automatic ice machine") →
     * long=[máy,động] short=[đá,tự]. "đá" ("ice") is rare (66/381 products, high IDF)
     * and should outrank the common "động" ("auto-", low IDF).
     */
    const IDF_RARE_THRESHOLD = 1.0;  // log(N/df) > 1.0 ~ df below ~37% of total products
    const IDF_FLOOR = 0.15;          // floor — extremely common words still keep a little weight
    const FIELD_WEIGHT_TITLE = 1.0;
    const FIELD_WEIGHT_BRAND_CATEGORY = 0.6;
    const FIELD_WEIGHT_CONTENT = 0.3;

    /**
     * Common Vietnamese stopwords — MySQL FULLTEXT only ships a default English
     * stopword list, so words like "của/và/có/không/về..." ("of/and/have/not/about")
     * don't get filtered, skewing scores toward long pages (projects) that repeat
     * many function words rather than pages that are actually relevant. Filtered
     * manually at the application layer before building the FULLTEXT query.
     */
    private static $stopwords = [
        'là', 'và', 'của', 'có', 'không', 'về', 'cho', 'với', 'các', 'những',
        'tôi', 'bạn', 'mình', 'này', 'đó', 'kia', 'để', 'khi', 'trong', 'ngoài',
        'theo', 'đã', 'sẽ', 'đang', 'một', 'hỏi', 'muốn', 'xin', 'cho hỏi',
        'ạ', 'nhé', 'nha', 'à', 'ơi', 'vậy', 'thì', 'mà', 'rằng', 'được',
        'như', 'từ', 'trên', 'dưới', 'nào', 'gì', 'sao', 'làm', 'bị', 'còn',
        'hay', 'hoặc', 'nếu', 'vì', 'nên', 'do', 'bởi', 'lại', 'đây', 'kìa',
        'bên', 'ấy', 'vầy', 'ha', 'nhỉ',
        // Generic nouns that appear in almost every record (don't distinguish any
        // specific subject) — filtered separately from the grammatical stopwords
        // above. "công"/"nghiệp" ("industrial") added after the 2026-07-18 audit:
        // "thiết bị bếp công nghiệp" ("industrial kitchen equipment") is the site's
        // tagline, repeated in nearly every project page as well as product pages —
        // at the quorum tier (OR fallback, see search_fulltext) these two words
        // alone were enough to make a long project page outscore a real product
        // (real bug caught when re-testing a sample audit question: "tủ lạnh công
        // nghiệp inox 4 cánh" ["industrial 4-door stainless steel fridge"] returned
        // only project pages, no fridges at all).
        'sản', 'phẩm', 'loại', 'cái', 'chiếc', 'giá', 'bao', 'nhiêu', 'hàng', 'đồ',
        'công', 'nghiệp',
        // Greetings/pleasantries — pages (especially project and about-us pages)
        // often repeat "xin chào" ("hello"), "cảm ơn quý khách" ("thank you,
        // valued customer") in their content, so a plain greeting easily matches
        // unrelated pages (bug observed: asking "chào" ["hi"] returned scattered,
        // irrelevant sources). Treated as stopwords so a pure greeting doesn't
        // drag in fake sources.
        'chào', 'chao', 'hi', 'hello', 'alo', 'dạ', 'vâng', 'ok', 'oke', 'okay',
        'cảm', 'ơn', 'cám', 'thanks', 'hey',
        // "shop" — an extremely common English loanword in how Vietnamese
        // customers address a seller ("chào shop" ["hi there, shop"], "shop
        // ơi"...). Before the site had 147 English-language products (added in
        // the 2026-07-21 new-brand import), nobody noticed because content_text
        // was entirely Vietnamese — "shop" never appeared anywhere in the data to
        // cause harm. Now the English descriptions of the new products contain
        // marketing copy that literally uses the word "shops" (e.g. "bakeries,
        // pastry shops and hotels"), so "chào shop" genuinely matches unrelated
        // products (bug measured via eval-retrieval.php: the 6_tan_gau
        // ["small talk"] test group dropped 0.800→0.700). Only this word was
        // added — "free"/"new" carry a similar risk (tried) but had NO real
        // failing case in the 68-question test set yet, so they were not added
        // (principle: only add a stopword with real evidence, to avoid
        // whack-a-mole).
        'shop',
        // Address pronouns + common small-talk verbs — real bug caught: "Anh
        // thích em" ("I like you" — small talk, not a product question) still
        // left "anh" ("you"/"brother") + "thích" ("like") after filtering, both
        // very common words in marketing copy/product descriptions ("anh/chị"
        // ["sir/madam"], "khách hàng yêu thích" ["customers love"]...), so they
        // matched unrelated product sources indiscriminately. Treated as
        // stopwords — a pure small-talk message ends up with no keywords left,
        // falls back to empty context, and the AI replies conversationally
        // without attaching sources.
        'anh', 'chị', 'cô', 'chú', 'bác', 'ông', 'bà', 'em', 'tao', 'mày', 'nó', 'ta',
        'thích', 'yêu', 'ghét', 'nhớ', 'thương', 'đẹp', 'xấu', 'giỏi', 'vui', 'buồn', 'cưng',
        'ghê', 'quá', 'lắm', 'trời', 'ối', 'ủa', 'ừ', 'ừm', 'dạo',
        // "người" ("person/people") — real bug caught: "Tôi muốn có người yêu"
        // ("I want a girlfriend/boyfriend") left only "người" after filtering,
        // but this is an EXTREMELY generic noun that occurs naturally in a great
        // many real product descriptions ("phù hợp phục vụ đông người" ["suited
        // for serving large crowds"], "tiết kiệm nhân công" ["saves on
        // labor"]...). This isn't a project-vs-product issue (only_products()
        // already filters correctly) — REAL products also contain this word, so
        // it still matches. Must be blocked at the vocabulary level, since the
        // word itself carries no signal that distinguishes any particular
        // product.
        'người',
        // "bé" ("little/baby") — real bug caught: "Chào em bé" ("hello baby")
        // left only "bé" (2 characters) after filtering, landing in $short (the
        // LIKE fallback for tokens below MIN_TOKEN_LENGTH — kept intentionally so
        // short product codes like "hr"/"se" can still be looked up). But "bé" is
        // NOT a product code, it's a pleasantry ("em bé" = "baby"), and
        // LIKE '%bé%' matches indiscriminately against any word CONTAINING "bé"
        // as a substring — "sắc bén" ("sharp" — knife/blade), "khóa trẻ em"
        // ("child lock")... matched 3 unrelated products (an electric stove, 2
        // meat slicers) just because their descriptions contained "bén" ("sharp").
        // Treated as a stopword — same principle as "người"/"chào": the word is
        // too generic to distinguish any product, and here it also causes a false
        // substring match because it's so short.
        'bé',
    ];

    /** Contact info assembled from the theme's real data — no full-text search
     *  involved. Dispatched by Pi_Chatbot_Router's intent='contact' classification
     *  (rule-based keyword matching was removed — the router now classifies
     *  every message). */
    public static function contact_context() {
        if (!function_exists('saigonhouse_get_contact_info')) return [];
        $c = saigonhouse_get_contact_info();
        // Facebook/Zalo/YouTube were missing here even though
        // saigonhouse_get_contact_info() (theme) has always returned them —
        // a real bug caught live: a customer asked for the Facebook page and
        // the bot said it didn't have that information, despite the URL
        // existing right in the theme's contact-info config the whole time.
        // SYSTEM_PROMPT forbids answering from anything not in this context,
        // so any field missing here is a field the bot can never mention,
        // no matter how obviously "known" it seems.
        $excerpt = sprintf(
            'Hotline: %s (or %s). Email: %s. Address: %s. Website: %s. Facebook: %s. Zalo: %s. YouTube: %s.',
            $c['hotline'] ?? '', $c['hotline_alt'] ?? '', $c['email_primary'] ?? '',
            $c['address'] ?? '', $c['website'] ?? '', $c['facebook'] ?? '',
            $c['zalo'] ?? '', $c['youtube'] ?? ''
        );
        return [[
            'post_id'  => 0,
            'title'    => 'Saigon Horeca contact information',
            'brand'    => '',
            'category' => '',
            'url'      => home_url('/lien-he/'),
            'excerpt'  => $excerpt,
            'score'    => 999,
        ]];
    }

    /**
     * Honest context for pricing questions — no invented numbers, no random
     * product attached as a source (old bug). This MUST live in the context
     * (not just the system prompt) because SYSTEM_PROMPT tells the model to
     * answer only from context — without data here the model would make
     * something up instead of truthfully saying pricing isn't listed.
     * Dispatched by Pi_Chatbot_Router's intent='price'.
     */
    public static function price_context() {
        if (!function_exists('saigonhouse_get_contact_info')) return [];
        $c = saigonhouse_get_contact_info();
        $excerpt = sprintf(
            'Pricing for commercial kitchen equipment depends on configuration, size, and order quantity, so it is not listed on the website. '
            . 'Please contact hotline %s (or %s) or email %s for an accurate quote.',
            $c['hotline'] ?? '', $c['hotline_alt'] ?? '', $c['email_primary'] ?? ''
        );
        return [[
            'post_id'  => 0,
            'title'    => 'Saigon Horeca pricing policy',
            'brand'    => '',
            'category' => '',
            'url'      => home_url('/lien-he/'),
            'excerpt'  => $excerpt,
            'score'    => 999,
        ]];
    }

    /** Real company service description — more accurate than the 8 static
     *  `page` posts (5/8 of which are near-empty). Dispatched by
     *  Pi_Chatbot_Router's intent='service'. */
    public static function service_context() {
        if (!function_exists('saigonhouse_get_contact_info')) return [];
        $c = saigonhouse_get_contact_info();
        $desc = $c['description'] ?? '';
        if ($desc === '') return [];
        return [[
            'post_id'  => 0,
            'title'    => 'Saigon Horeca services',
            'brand'    => '',
            'category' => '',
            'url'      => home_url('/gioi-thieu/'),
            'excerpt'  => $desc,
            'score'    => 999,
        ]];
    }

    /**
     * Search the 30 post_type=project records — dispatched by Pi_Chatbot_Router's
     * intent='project' classification. Projects never mix into the product
     * path (only_products() stays intact everywhere else) — this is a
     * dedicated path, gated by intent, not a relaxation of that rule.
     */
    public static function search_projects($question, $top_k = 4) {
        list($long, $short) = self::extract_keyword_list($question);
        $words = array_merge($long, $short);
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();

        if ($words) {
            $match_query = implode(' ', array_map(function ($w) { return $w . '*'; }, $words));
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT post_id, post_type, title, brand, category, url, content_text,
                        MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE) AS score
                 FROM {$table}
                 WHERE post_type = 'project'
                   AND MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE)
                 ORDER BY score DESC LIMIT %d",
                $match_query, $match_query, $top_k
            ));
            if ($rows) return self::format_rows($rows, $words);
        }

        // No specific keyword matched (a generic question like "cho xem mẫu thiết
        // kế" ["show me a design sample"]) — still return a few real projects
        // instead of wrongly falling back to "not found", in the same spirit as
        // search_within_brand().
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, post_type, title, brand, category, url, content_text, 1.0 AS score
             FROM {$table} WHERE post_type = 'project'
             ORDER BY CHAR_LENGTH(content_text) DESC LIMIT %d",
            $top_k
        ));
        return self::format_rows($rows);
    }

    /**
     * Return the top-K records most relevant to the question.
     *
     * "Entity-first" strategy (arrived at after researching RAG for small
     * datasets with no vector DB — see class-entities.php): if the question
     * mentions a real, already-known BRAND NAME, filter HARD on the `brand`
     * column first (WHERE brand = X — 100% precise), and only THEN use
     * full-text to RANK within that scope. Pure bag-of-words full-text (treating
     * brand as just another word ANDed with everything else) previously caused
     * inaccuracies when the question had extra common words — hard-filtering by
     * brand eliminates that risk entirely. Only falls back to pure full-text
     * over the whole index when no brand is recognized.
     */
    public static function search($question, $top_k = 6) {
        $question = trim(wp_strip_all_tags($question));
        if ($question === '') return [];

        // Tier 0 — product codes (P2 audit 2026-07-20, step 3 of the plan). A
        // code like "FT-126MA-S" is an IDENTIFIER, not a semantic phrase — the
        // tokenizer usually splits on every non-alphanumeric character, breaking
        // it into ft/126ma/s, and the 1-character suffix "s" gets dropped
        // entirely by extract_keyword_list() (< 2 characters) → so "FT-126MA-S"
        // could no longer be distinguished from variants like
        // "FT-126MA-S-3D"/"FT-126MA-S-ML". Matching directly against the title
        // before falling through to FULLTEXT fixes the root cause, rather than
        // patching more rules onto the tokenizer.
        $codes = self::extract_product_codes($question);
        if ($codes) {
            $code_results = self::search_by_product_code($codes, $top_k);
            if ($code_results) return $code_results;
        }

        list($long, $short) = self::extract_keyword_list($question);
        if (!$long && !$short) return [];

        // Pi_Chatbot_Entities is always require_once'd before Retriever in
        // pi-chatbot.php (fixed load order) — no need for a defensive
        // class_exists() check.
        $brands = Pi_Chatbot_Entities::extract_brands($question);
        $results = $brands
            ? self::search_within_brands($brands, $long, $short, $top_k)
            : self::search_fulltext($long, $short, $top_k, '');
        if ($results) return $results;

        // Tier 3 — accent-folded last resort. Runs ONLY when everything above
        // found nothing, which is what makes it safe to add: it can turn an
        // empty answer into a good one, and by construction can never reorder or
        // displace a result the exact-diacritic tiers already produced.
        if (!self::looks_unaccented($question)) return [];
        return self::search_unaccented($long, $short, $top_k);
    }

    /**
     * True when the question contains no Vietnamese tone marks — the signal that
     * the customer typed without diacritics and a folded match is worth trying.
     *
     * This gate is what makes Tier 3 safe, and it was added because of a measured
     * regression, not as a precaution: without it, "cho hỏi giờ mở cửa" ("what
     * time do you open") folded to match "1 Cửa" ("1 door") in a display-fridge
     * title, and "bạn tên gì" ("what's your name") pulled in four products.
     * Both cost the small-talk group 0.100 on the golden set.
     *
     * The reasoning is simple: those questions were typed WITH full diacritics,
     * so there is no transliteration problem to solve. Folding them away only
     * destroys the information that was keeping short Vietnamese function words
     * ("cửa", "mở", "tên") from colliding with real product vocabulary. A
     * customer who typed accents correctly and matched nothing genuinely has no
     * answer in the catalog, and saying so is the right response.
     */
    private static function looks_unaccented($question) {
        return !preg_match(
            '/[àáạảãăằắẳẵặâầấẩẫậèéẹẻẽêềếểễệđìíịỉĩòóọỏõôồốổỗộơờớởỡợùúụủũưừứửữựỳýỵỷỹ]/iu',
            (string) $question
        );
    }

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
     * Strips Vietnamese tone marks and đ/Đ, lowercasing as it goes.
     *
     * An explicit character map rather than iconv()/Normalizer: iconv's
     * //TRANSLIT output depends on the server's locale and libc build (it can
     * emit "d" or "?" or drop the character entirely for đ), and intl is not
     * guaranteed on cPanel shared hosting. A hardcoded table gives the same
     * answer on every host, which matters because retrieval results would
     * otherwise differ between the dev machine and production.
     */
    public static function fold_diacritics($text) {
        static $map = null;
        if ($map === null) {
            $map = [
                'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a',
                'â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a',
                'ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
                'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e',
                'ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
                'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
                'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o',
                'ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o',
                'ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
                'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u',
                'ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
                'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
                'đ'=>'d',
            ];
        }
        return strtr(mb_strtolower((string) $text), $map);
    }

    /** Single place the table name is resolved for search paths — keeps the new
     *  fallback from drifting away from the rest of the file if that ever changes. */
    private static function table_name_for_search() {
        return Pi_Chatbot_Indexer::table_name();
    }


    /**
     * Multi-query retrieval: the lexical pipeline is run once per phrasing — the
     * customer's original question plus the router's rewrites — and the
     * resulting rankings are fused with Reciprocal Rank Fusion.
     *
     * Each rewrite is an independent shot at the catalog's own vocabulary, and
     * fusion is what turns that into a signal: a product ranked mid-table for
     * three different phrasings ends up ABOVE one ranked first for a single
     * phrasing and nowhere for the others. That difference is precisely what
     * separates a genuine match from a lucky keyword collision, and no single
     * query can express it.
     *
     * RRF (score = sum of 1/(k + rank)) rather than summing the underlying
     * scores: those scores are unbounded sums of IDF x field weight, so one
     * phrasing that happens to hit a rare word can outweigh agreement across all
     * the others. RRF reads POSITION only, needs no tuning, and cannot be
     * swamped by one list's scale. k=60 is the constant from the original paper
     * and is deliberately left alone.
     *
     * @param array $opts extra_queries: string[] router rewrites.
     * @return array Same shape as search().
     */
    public static function search_hybrid($question, $top_k = 12, $opts = []) {
        $question = trim(wp_strip_all_tags((string) $question));
        if ($question === '') return [];

        // Tier 0 short-circuits fusion for the same reason it short-circuits
        // search(): a product code is an IDENTIFIER, and an exact identifier
        // match is ground truth, not one opinion among several. Fusing it with
        // near-miss neighbours can only dilute a result that is already certain —
        // measured on the golden set, letting a code query through the full
        // pipeline was the ONLY regression the reranker caused
        // ("tủ đông FT-158MA-S"), because a model re-reading titles has nothing
        // to add to a string that either matches or doesn't.
        $codes = self::extract_product_codes($question);
        if ($codes) {
            $code_results = self::search_by_product_code($codes, $top_k);
            if ($code_results) return $code_results;
        }

        $settings = pi_chatbot_settings();
        $rrf_k = (int) $settings['rrf_k'];

        // Gather deeper than we return: fusion needs to see a result at rank 8
        // in one list to reward it for also appearing at rank 6 in another. A
        // pool cut to $top_k would throw that evidence away before fusing.
        $pool = max(20, (int) $top_k * 3);

        $queries = [$question];
        $max_variants = (int) $settings['multi_query_max'];
        foreach (array_slice((array) ($opts['extra_queries'] ?? []), 0, max(0, $max_variants)) as $variant) {
            $variant = trim((string) $variant);
            if ($variant !== '' && !in_array($variant, $queries, true)) $queries[] = $variant;
        }

        $ranked_lists = [];
        foreach ($queries as $q) {
            $lexical = self::search($q, $pool);
            if ($lexical) $ranked_lists[] = $lexical;
        }
        if (!$ranked_lists) return [];

        $fused = [];
        foreach ($ranked_lists as $list) {
            foreach (array_values($list) as $rank => $row) {
                $post_id = (int) $row['post_id'];
                $fused[$post_id] = ($fused[$post_id] ?? 0.0) + 1.0 / ($rrf_k + $rank + 1);
            }
        }
        arsort($fused);
        $top_ids = array_slice(array_keys($fused), 0, max(1, (int) $top_k));
        if (!$top_ids) return [];

        // Re-fetch as rows instead of reusing the formatted arrays from the legs:
        // dedupe_by_content() compares full content_text (a formatted row only
        // carries a 400-char excerpt, and the same product can produce DIFFERENT
        // excerpts under different query variants), and re-running make_excerpt()
        // over the union of all query words gives the LLM a passage chosen
        // against everything that was asked, not just the first phrasing.
        $rows = self::fetch_rows_by_ids($top_ids);
        foreach ($rows as $row) {
            $row->score = (float) ($fused[(int) $row->post_id] ?? 0.0);
        }
        $all_words = [];
        foreach ($queries as $q) {
            list($long, $short) = self::extract_keyword_list($q);
            $all_words = array_merge($all_words, $long, $short);
        }
        return self::format_rows(self::dedupe_by_content($rows), array_values(array_unique($all_words)));
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

    /**
     * Measure the TRUE rarity (document frequency) of each keyword — both $long
     * and $short — then convert to IDF. This is the shared figure used both to
     * decide which words are mandatory in the SQL and for the score weighting
     * in verify_and_score(), so the two stay consistent with each other. Costs
     * 1 COUNT query per keyword (typically 2-5 words/question) — acceptable,
     * measured COUNT(*) on post_type via the index at under 5ms at a scale of
     * 381-10,000 products.
     *
     * @return array<string,array{df:int,idf:float}> key = lowercased word
     */
    private static function compute_idf($words) {
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $total = max(1, self::total_product_count());
        $out = [];
        foreach ($words as $w) {
            $key = mb_strtolower($w);
            if (isset($out[$key])) continue;
            $like = '%' . $wpdb->esc_like($key) . '%';
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
            $out[$key] = ['df' => $df, 'idf' => max(self::IDF_FLOOR, log($total / $df))];
        }
        return $out;
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
                $pattern = '%' . $wpdb->esc_like(mb_strtolower($w)) . '%';
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
        $product_rows = self::require_literal_all(
            self::dedupe_by_content(self::only_products($rows)),
            array_merge($rare_long, $rare_short)
        );
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
            $product_rows = self::require_literal_all(
                self::dedupe_by_content(self::only_products($rows)),
                $rare_long
            );
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

        $top_score = max(array_map(function ($r) { return (float) $r->score; }, $products));
        $products = array_values(array_filter($products, function ($r) use ($top_score) {
            return $top_score > 0 && ((float) $r->score) >= $top_score * self::OR_FALLBACK_SCORE_RATIO;
        }));
        $products = self::dedupe_by_content($products);
        return self::format_rows(array_slice($products, 0, $top_k), $all_words);
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
    const MAX_PER_DUPLICATE_GROUP = 2;

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
        $all_words = array_merge($long, $short);
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
                if (mb_strpos($title_l, $wl) !== false) {
                    $field_w = self::FIELD_WEIGHT_TITLE;
                    $has_strong_field_hit = true;
                } elseif (mb_strpos($brand_l, $wl) !== false || mb_strpos($cat_l, $wl) !== false) {
                    $field_w = self::FIELD_WEIGHT_BRAND_CATEGORY;
                    $has_strong_field_hit = true;
                } elseif (mb_strpos($body_l, $wl) !== false) {
                    $field_w = self::FIELD_WEIGHT_CONTENT;
                } else {
                    continue;
                }
                $hits++;
                $weighted += $field_w * ($idf[$wl]['idf'] ?? self::IDF_FLOOR);
            }
            if ($hits === 0) continue;

            $r->score = (float) $r->score + $weighted;
            $r->has_strong_field_hit = $has_strong_field_hit;
            $kept[] = $r;
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
            if (mb_strpos($haystack, mb_strtolower($w)) === false) return false;
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
            // codes are capitalized like "HR-126MA-S" but tokens are already
            // lowercased — plain bin without LOWER() would mismatch on case and
            // lose valid results; this bug was caught while testing
            // HR-126MA-S).
            $sql .= " AND (LOWER(title) LIKE %s COLLATE utf8mb4_bin OR LOWER(brand) LIKE %s COLLATE utf8mb4_bin
                      OR LOWER(category) LIKE %s COLLATE utf8mb4_bin OR LOWER(content_text) LIKE %s COLLATE utf8mb4_bin)";
            $pattern = '%' . $wpdb->esc_like(mb_strtolower($w)) . '%';
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

    /**
     * Strips Vietnamese stopwords, returns a RAW keyword LIST (not yet suffixed
     * with +/* — search_fulltext() builds both the AND/OR versions itself from
     * this list). Splits words along the same boundaries MySQL's default
     * FULLTEXT parser uses (every non-alphanumeric character is a separator —
     * including hyphens), so a product code like "FT-126MA-S" matches the
     * exact tokens that were indexed ("ft", "126ma", "s") instead of being
     * glued into one wrong word.
     *
     * Filters out tokens < 3 characters: MySQL (default
     * innodb_ft_min_token_size = 3) doesn't index tokens shorter than this
     * threshold — if "ft" (2 characters) were still used as a mandatory
     * condition, that token could never match anything and the whole AND
     * clause would always return empty, even though the remaining keywords
     * (e.g. "126ma") actually match a real product.
     */
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
     */
    private static function search_by_product_code($codes, $top_k) {
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
                $is_exact = (bool) preg_match(
                    '/(?<![a-z0-9])' . preg_quote($code_l, '/') . '(?![a-z0-9])/u',
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
        $rows = self::dedupe_by_content($rows);
        return self::format_rows(array_slice($rows, 0, $top_k), $codes);
    }

    private static function extract_keyword_list($question) {
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower($question));
        $words = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        $long = []; $short = [];
        foreach ($words as $word) {
            if (in_array($word, self::$stopwords, true)) continue;
            if (mb_strlen($word) < 2) continue;
            if (mb_strlen($word) < self::MIN_TOKEN_LENGTH) {
                $short[] = $word;
            } else {
                $long[] = $word;
            }
        }
        return [$long, $short];
    }

    /**
     * Extracts the excerpt AROUND the position of the first keyword match
     * found, instead of always taking the first $len characters of
     * content_text (bug found during audit: the passage that actually
     * addresses the question — specs, power rating, price — is often deep in
     * the body, while the start of the string is usually just a generic
     * intro/marketing blurb; the LLM would receive the useless part, and the
     * product would rank correctly but still answer weakly or say "no
     * information available"). When there are no $words (e.g. the brand
     * fallback with no keyword filtering), keeps the old behavior — take the
     * start of the string.
     */
    private static function make_excerpt($text, $len, $words = []) {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if (mb_strlen($text) <= $len) return $text;

        $match_pos = null;
        if ($words) {
            $lower = mb_strtolower($text);
            foreach ($words as $w) {
                $pos = mb_strpos($lower, mb_strtolower($w));
                if ($pos !== false && ($match_pos === null || $pos < $match_pos)) {
                    $match_pos = $pos;
                }
            }
        }

        if ($match_pos === null) {
            return mb_substr($text, 0, $len) . '…';
        }

        // Window around the match position: 1/4 of the length before, the rest
        // after — keeps a bit of context preceding the keyword instead of
        // cutting right at the keyword itself.
        $before = (int) floor($len * 0.25);
        $start = max(0, $match_pos - $before);
        $snippet = mb_substr($text, $start, $len);
        $prefix = $start > 0 ? '…' : '';
        $suffix = ($start + $len) < mb_strlen($text) ? '…' : '';
        return $prefix . trim($snippet) . $suffix;
    }
}
