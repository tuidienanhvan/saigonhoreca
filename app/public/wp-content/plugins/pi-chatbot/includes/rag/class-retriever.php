<?php
/**
 * Retriever — weighted full-text search, no embeddings/vector DB.
 *
 * 2026-07-29: split across trait files (this file had grown past 1000 lines)
 * — pure code motion, the class and its behavior are unchanged:
 *   - retriever/trait-tokenizer.php       — diacritic folding, keyword
 *     extraction, word-boundary matching, excerpt windowing.
 *   - retriever/trait-scoring.php         — IDF measurement, field/rarity
 *     weighted scoring, dedup, the low-level fulltext query runner.
 *   - retriever/trait-fulltext-search.php — the tiered search pipeline
 *     (mandatory AND → quorum OR → diacritic-folded fallback), brand scoping.
 *   - retriever/trait-product-codes.php   — Tier 0 exact product-code match.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/traits/trait-tokenizer.php';
require_once __DIR__ . '/traits/trait-scoring.php';
require_once __DIR__ . '/traits/trait-fulltext-search.php';
require_once __DIR__ . '/traits/trait-product-codes.php';
require_once __DIR__ . '/traits/trait-semantic-search.php';

class Pi_Chatbot_Retriever {
    use Pi_Chatbot_Retriever_Tokenizer;
    use Pi_Chatbot_Retriever_Scoring;
    use Pi_Chatbot_Retriever_FulltextSearch;
    use Pi_Chatbot_Retriever_ProductCodes;
    use Pi_Chatbot_Retriever_Semantic;

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
     * How much MySQL's own MATCH() relevance is allowed to contribute, AFTER
     * being normalized to 0..1 against the top-scoring row of the same result
     * set (see verify_and_score()).
     *
     * Why normalize at all: MySQL's boolean-mode score is an unbounded
     * term-frequency sum, so it rewards REPETITION, and the category column on
     * this catalog is SEO-stuffed ("Bếp Gas Công Nghiệp, Thiết Bị Bếp Công
     * Nghiệp, Thiết Bị Bếp Công Nghiệp Sài Gòn" — "bếp" 3×). Measured on
     * "bếp gas 2 họng": the products that genuinely have "họng" in the title
     * scored 4.003 while products WITHOUT the word anywhere in the title scored
     * 8.971 purely on that repetition — a 4.97 gap the IDF×field bonus (max
     * ~1.88 here) could never overcome. The raw score was being ADDED to the
     * IDF-weighted score, so the stuffed field silently outvoted every
     * carefully-weighted signal in this file.
     *
     * Normalized, MySQL relevance keeps the job it is actually good at — a
     * cheap tie-break among rows the PHP scorer rates equally — without being
     * able to override it.
     *
     * Value chosen by measurement, not taste. Swept over the 68-query golden
     * set (tests/eval-retrieval.php), hit@1 / hit@3:
     *
     *     w=0.0   0.912 / 0.956   <- dropping MySQL entirely is WORSE
     *     w=0.1   0.912 / 0.971
     *     w=0.25  0.912 / 0.971
     *     w=0.5   0.912 / 0.971
     *     w=0.75  0.912 / 0.971   <- flat optimum, 0.1 .. 1.25
     *     w=1.0   0.912 / 0.971
     *     w=1.25  0.912 / 0.971
     *     w=1.5   0.897 / 0.971   <- hit@1 starts degrading
     *     w=3.0   0.897 / 0.971
     *
     * 0.7 is the middle of the flat region, deliberately not an edge: both
     * cliffs (0.0 and 1.5) are far away, so the value is not tuned to this
     * particular golden set's boundary.
     */
    const MYSQL_SCORE_WEIGHT = 0.7;

    const MAX_PER_DUPLICATE_GROUP = 2;

    /** DF is a property of the CATALOG, not of one question — it only changes
     *  when the catalog changes, and that already bumps pi_chatbot_index_version
     *  (class-indexer.php). 7 days is generous rather than tight on purpose:
     *  the transient key below folds in the version, so a reindex invalidates
     *  it immediately regardless of how long the TTL is — the TTL only bounds
     *  how long an entry can outlive a version nobody ever bumped (a quiet
     *  catalog), not how stale it can get relative to a real change. */
    const IDF_CACHE_TTL = 7 * DAY_IN_SECONDS;

    /**
     * Vietnamese stopwords — MySQL FULLTEXT only ships a default English stopword
     * list, so words like "của/và/có/không/về..." ("of/and/have/not/about") don't
     * get filtered, skewing scores toward long pages (projects) that repeat many
     * function words rather than pages that are actually relevant. Filtered
     * manually at the application layer before building the FULLTEXT query.
     *
     * Every entry belongs to exactly one CLOSED word class below, each with its
     * own one-line membership test. This is deliberately NOT derived from a
     * corpus-frequency/IDF measurement over the catalog — that was tried and
     * measured to not work: on a 528-product catalog, "công"/"nghiệp" (generic
     * tagline nouns that genuinely should be filtered) sit at document-frequency
     * ratios of 0.667/0.231, while "shop"/"bé"/"người" (also genuinely harmful)
     * sit at 0.006/0.006/0.045 — and real, useful keywords like "tủ"/"máy"/"bếp"
     * sit at 0.288/0.322/0.273, squarely BETWEEN the two "bad" clusters. No
     * single frequency threshold can separate "harmful" from "useful" here; the
     * corpus is too small and noisy for a statistical rule to substitute for a
     * linguistic one. What actually makes a word harmful is that it belongs to
     * Vietnamese SOCIAL/TRANSACTIONAL speech rather than equipment vocabulary —
     * a closed grammatical category, the same kind every stopword list in any
     * language already is.
     *
     * Adding a new word to any class below: check it against that class's
     * stated test, not against whether some specific message broke.
     */
    private static $stopwords = [
        // Grammatical function words — pronouns, particles, conjunctions,
        // prepositions: words whose only job is sentence structure, carrying no
        // content of their own in ANY sentence, product-related or not.
        'là', 'và', 'của', 'có', 'không', 'về', 'cho', 'với', 'các', 'những',
        'tôi', 'bạn', 'mình', 'này', 'đó', 'kia', 'để', 'khi', 'trong', 'ngoài',
        'theo', 'đã', 'sẽ', 'đang', 'một', 'hỏi', 'muốn', 'xin', 'cho hỏi',
        'ạ', 'nhé', 'nha', 'à', 'ơi', 'vậy', 'thì', 'mà', 'rằng', 'được',
        'như', 'từ', 'trên', 'dưới', 'nào', 'gì', 'sao', 'làm', 'bị', 'còn',
        'hay', 'hoặc', 'nếu', 'vì', 'nên', 'do', 'bởi', 'lại', 'đây', 'kìa',
        'bên', 'ấy', 'vầy', 'ha', 'nhỉ',

        // Generic commerce/transaction nouns — words that describe the ACT of
        // buying/selling or a product's genericness ("a type of", "an item of",
        // "how much") rather than naming any specific kind of equipment. A
        // customer or a page can use these about ANY product, so they carry no
        // information about WHICH product. "công"/"nghiệp" ("industrial") are
        // included here even though they can be part of a real technical term
        // ("bếp công nghiệp" = "industrial stove"), because on their own — the
        // way they surface after grammatical filtering — they function as this
        // catalog's boilerplate tagline ("thiết bị bếp công nghiệp") rather than
        // a distinguishing spec; the equipment TYPE word ("bếp"/"tủ"/"máy"...)
        // is never filtered and still carries the real signal.
        'sản', 'phẩm', 'loại', 'cái', 'chiếc', 'giá', 'bao', 'nhiêu', 'hàng', 'đồ',
        'công', 'nghiệp', 'shop',

        // Greetings/pleasantries — fixed politeness phrases with no product
        // content, appearing identically whether the page/message is about any
        // product or none.
        'chào', 'chao', 'hi', 'hello', 'alo', 'dạ', 'vâng', 'ok', 'oke', 'okay',
        'cảm', 'ơn', 'cám', 'thanks', 'hey',

        // Social-address vocabulary — the pronoun set Vietnamese uses to address
        // or refer to PEOPLE in conversation ("anh/chị/em" as forms of "you",
        // "người"/"bé" as generic ways to refer to a person), plus the small,
        // closed set of emotion/opinion verbs and adjectives that only ever
        // describe how someone feels about something, never what the something
        // IS. None of these name equipment; they only ever occur when the
        // sentence is ABOUT a person, not about a product.
        'anh', 'chị', 'cô', 'chú', 'bác', 'ông', 'bà', 'em', 'tao', 'mày', 'nó', 'ta',
        'người', 'bé',
        'thích', 'yêu', 'ghét', 'nhớ', 'thương', 'đẹp', 'xấu', 'giỏi', 'vui', 'buồn', 'cưng',
        'ghê', 'quá', 'lắm', 'trời', 'ối', 'ủa', 'ừ', 'ừm', 'dạo',

        // English grammatical function words — the same closed class as the
        // Vietnamese list above (pronouns, articles, prepositions, auxiliary
        // and request verbs), added because this catalog is NOT Vietnamese-only
        // (~28% of titles are English-language imported lines, and the widget's
        // own quick-reply buttons — 4_tieng_anh_truc_tiep in the golden set
        // covers real English product questions too) yet this list previously
        // filtered nothing in English at all: every English function word
        // survived as a "real" keyword.
        //
        // Measured, concretely: "Please give me your contact information" —
        // the widget's own Contact quick-reply button — matched an unrelated
        // "Meat Ageing Cabinet" product purely because its description happens
        // to say "...for all YOUR meat ageing needs"; "your" is exactly as
        // empty of product signal as Vietnamese "của"/"bạn" already stopworded
        // above, it simply had no English counterpart in the list.
        //
        // This is a standard, closed, language-general set (the same category
        // any English NLP stopword list ships) — it does not name a single
        // catalog word or a single observed bug the way the discovery example
        // is described; membership test: the word carries no meaning on its
        // own in ANY English sentence, equipment-related or not.
        'a', 'an', 'the', 'is', 'are', 'am', 'was', 'were', 'be', 'been', 'being',
        'do', 'does', 'did', 'have', 'has', 'had', 'will', 'would', 'shall', 'should',
        'can', 'could', 'may', 'might', 'must',
        'i', 'you', 'your', 'yours', 'he', 'she', 'it', 'its', 'we', 'us', 'our',
        'they', 'them', 'their', 'me', 'my', 'mine', 'him', 'her', 'hers',
        'this', 'that', 'these', 'those',
        'of', 'for', 'with', 'about', 'to', 'from', 'in', 'on', 'at', 'by', 'as',
        'and', 'or', 'but', 'if', 'so', 'than',
        'what', 'which', 'who', 'whom', 'how', 'when', 'where', 'why',
        'please', 'give', 'show', 'tell', 'let',
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
        // 2026-08-09 — company_full and email_secondary added, the same bug
        // class as the Facebook/Zalo one above, found by diffing what
        // saigonhouse_get_contact_info() actually returns against what reaches
        // this excerpt. Both were verified live before the fix:
        //
        //   "tên công ty đầy đủ trên hoá đơn là gì"
        //     -> "Xin lỗi, tôi hiện chưa có thông tin…"
        //        while CÔNG TY TNHH SÀI GÒN HORECA sat in theme config.
        //
        //   "còn email nào khác ngoài contact@ không"
        //     -> "Chỉ có một địa chỉ email chính…"
        //        which is not a refusal but a false statement — gin@ is real
        //        and is published on the /lien-he/ page.
        //
        // The second is the worse failure: a missing field does not just make
        // the bot unhelpful, it makes it confidently deny something true,
        // because SYSTEM_PROMPT tells it the reference block is all there is.
        //
        // Deliberately NOT added: slogan and description (marketing copy —
        // inviting the model to make claims about capabilities is how the
        // deleted tools started), address_short (a duplicate of address that
        // only gives the model two spellings to choose between), city and
        // country_code (already inside address), and the *_raw phone variants
        // (same numbers without spaces; the formatted ones are what a person
        // reads back). mst is empty in config, so there is nothing to add —
        // and an empty tax code must stay absent rather than become a blank
        // the model might fill in.
        $company = $c['company_full'] ?? ($c['company_name'] ?? '');
        $emails  = trim((string) ($c['email_primary'] ?? ''));
        if (!empty($c['email_secondary'])) {
            $emails .= ' (or ' . $c['email_secondary'] . ')';
        }
        $excerpt = sprintf(
            'Company: %s. Hotline: %s (or %s). Email: %s. Address: %s. Website: %s. Facebook: %s. Zalo: %s. YouTube: %s.',
            $company, $c['hotline'] ?? '', $c['hotline_alt'] ?? '', $emails,
            $c['address'] ?? '', $c['website'] ?? '', $c['facebook'] ?? '',
            $c['zalo'] ?? '', $c['youtube'] ?? ''
        );
        return [[
            'post_id'  => 0,
            'title'    => pi_chatbot_brand()['name'] . ' contact information',
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
            'title'    => pi_chatbot_brand()['name'] . ' pricing policy',
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
            'title'    => pi_chatbot_brand()['name'] . ' services',
            'brand'    => '',
            'category' => '',
            // 2026-07-27: was home_url('/gioi-thieu/') — that slug never existed
            // on this site (real 404, caught live from a customer clicking the
            // Sources link). '/ve-saigon-horeca/' is the real, currently
            // published About page (verified against wp_posts directly, not
            // guessed) — see the regression test in suite-retrieval.php that
            // resolves this URL against get_page_by_path() so a future page
            // rename breaks the test instead of silently shipping a dead link.
            'url'      => home_url('/ve-saigon-horeca/'),
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
     * @param array $opts extra_queries: string[] router rewrites — OPTIONAL extra
     *              phrasings, budget-gated by the multi_query_max setting.
     *              alt_queries: string[] alternative expressions of the SAME
     *              information need that are structurally always present (today:
     *              the router's English keyword set alongside its Vietnamese
     *              one). NOT gated by multi_query_max — that setting is a budget
     *              for optional rewrites, and using it to gate these would make
     *              a value of 0 silently drop half the query.
     * @return array Same shape as search().
     */
    public static function search_hybrid($question, $top_k = 12, $opts = []) {
        $start_time = microtime(true);
        $return_structured = !empty($opts['return_structured']);
        $question = trim(wp_strip_all_tags((string) $question));
        if ($question === '') {
            return $return_structured ? ['rows' => [], 'meta' => ['search_time_ms' => 0.0], 'status' => 'empty'] : [];
        }

        $codes = self::extract_product_codes($question);
        if ($codes) {
            $code_results = self::search_by_product_code($codes, $top_k);
            if ($code_results) {
                if ($return_structured) {
                    return [
                        'rows' => $code_results,
                        'meta' => [
                            'tier_used' => 'tier0_product_code',
                            'queries_run' => 1,
                            'rrf_k' => 0,
                            'total_candidates' => count($code_results),
                            'deduped_count' => count($code_results),
                            'search_time_ms' => round((microtime(true) - $start_time) * 1000, 2),
                        ],
                        'status' => 'ok',
                    ];
                }
                return $code_results;
            }
        }

        $settings = pi_chatbot_settings();
        $rrf_k = (int) $settings['rrf_k'];
        $pool = max(20, (int) $top_k * 3);

        $queries = [$question];
        foreach ((array) ($opts['alt_queries'] ?? []) as $alt) {
            $alt = trim((string) $alt);
            if ($alt !== '' && !in_array($alt, $queries, true)) $queries[] = $alt;
        }
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

        // Semantic integration point for future vector DB results
        $semantic_res = self::search_semantic($question, $top_k);
        if (!empty($semantic_res['rows'])) {
            $ranked_lists[] = $semantic_res['rows'];
        }

        if (!$ranked_lists) {
            return $return_structured ? [
                'rows' => [],
                'meta' => [
                    'tier_used' => 'none',
                    'queries_run' => count($queries),
                    'rrf_k' => $rrf_k,
                    'total_candidates' => 0,
                    'deduped_count' => 0,
                    'search_time_ms' => round((microtime(true) - $start_time) * 1000, 2),
                ],
                'status' => 'empty',
            ] : [];
        }

        $fused = [];
        foreach ($ranked_lists as $list) {
            foreach (array_values($list) as $rank => $row) {
                $post_id = (int) $row['post_id'];
                $fused[$post_id] = ($fused[$post_id] ?? 0.0) + 1.0 / ($rrf_k + $rank + 1);
            }
        }
        arsort($fused);
        $top_ids = array_slice(array_keys($fused), 0, max(1, (int) $top_k));
        if (!$top_ids) {
            return $return_structured ? [
                'rows' => [],
                'meta' => [
                    'tier_used' => 'none',
                    'queries_run' => count($queries),
                    'rrf_k' => $rrf_k,
                    'total_candidates' => 0,
                    'deduped_count' => 0,
                    'search_time_ms' => round((microtime(true) - $start_time) * 1000, 2),
                ],
                'status' => 'empty',
            ] : [];
        }

        $rows = self::fetch_rows_by_ids($top_ids);
        foreach ($rows as $row) {
            $row->score = (float) ($fused[(int) $row->post_id] ?? 0.0);
        }
        $all_words = [];
        foreach ($queries as $q) {
            list($long, $short) = self::extract_keyword_list($q);
            $all_words = array_merge($all_words, $long, $short);
        }

        $deduped = self::dedupe_by_content($rows);
        $formatted = self::format_rows($deduped, array_values(array_unique($all_words)));

        if ($return_structured) {
            $token_est = 0;
            foreach ($formatted as $r) {
                $token_est += (int) ceil(mb_strlen($r['title'] . ' ' . $r['excerpt']) / 4);
            }
            return [
                'rows' => $formatted,
                'meta' => [
                    'tier_used' => 'hybrid_rrf',
                    'queries_run' => count($queries),
                    'rrf_k' => $rrf_k,
                    'total_candidates' => count($rows),
                    'deduped_count' => count($formatted),
                    'token_estimate' => $token_est,
                    'search_time_ms' => round((microtime(true) - $start_time) * 1000, 2),
                ],
                'status' => 'ok',
            ];
        }

        return $formatted;
    }
}
