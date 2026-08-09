<?php
/**
 * suite-retrieval.php — assertions against Pi_Chatbot_Retriever's DB-backed
 * tiers, run over the LIVE catalog (read-only, no writes here — writes belong
 * in suite-indexer.php against a throwaway table).
 *
 * Because this reads real data, a few checks pull the fixture value from the
 * database itself (a real brand count, a real product code) rather than
 * hardcoding a guess — the assertion is then about the CODE's behavior, not
 * about catalog contents this suite doesn't control.
 *
 * Run standalone:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/suite-retrieval.php
 * Or via tests/run-all.php.
 */

require_once __DIR__ . '/lib/harness.php';

global $wpdb;
$table = Pi_Chatbot_Indexer::table_name();
pi_reset_state();

// ── Fixtures pulled from the real catalog ───────────────────────────────────

$known_code_row = $wpdb->get_row("SELECT post_id, title FROM {$table}
    WHERE post_type = 'product' AND title REGEXP '[A-Za-z]{1,4}-?[0-9][A-Za-z0-9-]*' LIMIT 1");
$known_brand = $wpdb->get_var("SELECT brand FROM {$table}
    WHERE post_type = 'product' AND brand != '' GROUP BY brand ORDER BY COUNT(*) DESC LIMIT 1");
$brand_product_count = $known_brand
    ? (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE post_type='product' AND brand=%s", $known_brand))
    : 0;
$has_project = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE post_type='project'") > 0;

if (!$known_code_row || !$known_brand) {
    fwrite(STDERR, "suite-retrieval.php: the index table has no usable fixture data — is it populated? Skipping.\n");
    exit(0);
}

// Extract the actual code substring from the fixture title so the test
// searches with something guaranteed to be in the index, not a guess.
preg_match('/[A-Za-z]{1,4}-?[0-9][A-Za-z0-9-]*/', $known_code_row->title, $m);
$known_code = $m[0];

// ── Product-code tier (Tier 0) ──────────────────────────────────────────────
pi_section('Retriever::search — Tier 0 (product code)');

$by_code = Pi_Chatbot_Retriever::search($known_code, 6);
pi_check('a real product code returns at least one result', !empty($by_code), $known_code);
pi_check('the exact code appears in the top result',
    !empty($by_code) && stripos($by_code[0]['title'], $known_code) !== false,
    !empty($by_code) ? $by_code[0]['title'] : 'none');

pi_check('has_product_code_match() is true for a code that really exists',
    Pi_Chatbot_Retriever::has_product_code_match($known_code));
pi_check('has_product_code_match() is false for prose with no code shape',
    !Pi_Chatbot_Retriever::has_product_code_match('bếp gas 2 họng'));
pi_check('has_product_code_match() is false for a code-shaped string the catalog does not have',
    !Pi_Chatbot_Retriever::has_product_code_match('ZZ-99999-NOPE'));

// ── Brand tier ───────────────────────────────────────────────────────────────
pi_section('Retriever::search — brand hard filter');

$by_brand = Pi_Chatbot_Retriever::search($known_brand, 20);
pi_check('a bare brand name returns only that brand\'s products',
    !empty($by_brand) && count(array_filter($by_brand, function ($r) use ($known_brand) {
        return strcasecmp($r['brand'], $known_brand) === 0;
    })) === count($by_brand),
    'brand=' . $known_brand . ' got ' . count($by_brand) . ' rows');

// ── The accent-folded fallback tier only fires as a genuine last resort ─────
pi_section('Retriever::search — Tier 3 (accent-folded) stays a LAST resort');

foreach (['cho hỏi giờ mở cửa', 'bạn tên gì', 'máy bay trực thăng'] as $accented) {
    list($long, $short) = pi_call_private('Pi_Chatbot_Retriever', 'extract_keyword_list', [$accented]);
    $tiers12 = $long || $short
        ? pi_call_private('Pi_Chatbot_Retriever', 'search_fulltext', [$long, $short, 6, ''])
        : [];
    $actual = Pi_Chatbot_Retriever::search($accented, 6);
    // The real invariant is NOT "accented small talk returns nothing" — some of
    // it already matches products through the earlier FULLTEXT tiers (a known,
    // pre-existing false positive this suite documents rather than hides). What
    // Tier 3 guarantees, and what actually kept the small-talk group's score
    // stable when it was added, is that for an ACCENTED question it contributes
    // NOTHING — the result is exactly what tiers 1-2 already produced.
    pi_check("accented \"{$accented}\" gets no Tier 3 contribution",
        array_column($actual, 'post_id') === array_column($tiers12, 'post_id'),
        count($actual) . ' vs ' . count($tiers12));
}

$unaccented_hit = Pi_Chatbot_Retriever::search(Pi_Chatbot_Retriever::fold_diacritics($known_code), 6);
pi_check('an unaccented ASCII code still matches (no diacritics to fold, but the flow must not error)',
    is_array($unaccented_hit));

// ── search_hybrid ─────────────────────────────────────────────────────────────
pi_section('Retriever::search_hybrid');

$hybrid_by_code = Pi_Chatbot_Retriever::search_hybrid($known_code, 12);
pi_check('search_hybrid short-circuits a product-code question to the exact match',
    !empty($hybrid_by_code) && stripos($hybrid_by_code[0]['title'], $known_code) !== false);

pi_reset_state();
$plain = array_column(Pi_Chatbot_Retriever::search($known_brand, 6), 'post_id');
$hybrid = array_column(Pi_Chatbot_Retriever::search_hybrid($known_brand, 6), 'post_id');
pi_check('search_hybrid with no query variants matches plain search (RRF over one list is a no-op reorder of one ranking)',
    $hybrid === $plain, 'plain=' . implode(',', $plain) . ' hybrid=' . implode(',', $hybrid));

$fused = Pi_Chatbot_Retriever::search_hybrid($known_brand, 6, ['extra_queries' => [$known_brand . ' sản phẩm', $known_brand . ' chất lượng cao']]);
pi_check('search_hybrid with extra query variants still returns results', !empty($fused));
pi_check('every fused result is on-brand (brand filter carries through fusion)',
    !empty($fused) && count(array_filter($fused, function ($r) use ($known_brand) {
        return strcasecmp($r['brand'], $known_brand) === 0;
    })) === count($fused));

// ── compute_idf: the collation fix that started this whole file ────────────
pi_section('Retriever::compute_idf — diacritic-exact document frequency');

$idf = pi_call_private('Pi_Chatbot_Retriever', 'compute_idf', [['đá', 'đông']]);
pi_check('compute_idf returns a df/idf pair per word', isset($idf['đá']['df'], $idf['đá']['idf']));
pi_check('"đá" (ice) and "đông" (freeze) get DIFFERENT document frequencies — the collation bug this fixed made them equal',
    $idf['đá']['df'] !== $idf['đông']['df'] || $idf['đá']['idf'] !== $idf['đông']['idf'],
    "đá:{$idf['đá']['df']} đông:{$idf['đông']['df']}");
pi_check('idf never drops below IDF_FLOOR',
    $idf['đá']['idf'] >= Pi_Chatbot_Retriever::IDF_FLOOR && $idf['đông']['idf'] >= Pi_Chatbot_Retriever::IDF_FLOOR);

// ── Static context builders ─────────────────────────────────────────────────
pi_section('Retriever context builders (contact/price/service)');

foreach (['contact_context', 'price_context', 'service_context'] as $method) {
    $result = call_user_func(['Pi_Chatbot_Retriever', $method]);
    pi_check("{$method}() never errors, always returns an array", is_array($result));
    if (function_exists('saigonhouse_get_contact_info')) {
        pi_check("{$method}() returns real data when the theme function exists", !empty($result), 'got ' . count($result));
        // 2026-07-27: catches the exact bug a customer hit live — service_context()
        // hardcoded home_url('/gioi-thieu/'), a slug that never existed on this
        // site, and the Sources link in the widget led to a real 404. The URL
        // string alone always "looked" valid (well-formed, same-origin) — only
        // resolving it against a REAL published page catches a slug that's wrong
        // or has since been renamed/deleted. get_page_by_path() needs the bare
        // slug, not the full URL, so the home_url() prefix is stripped first.
        if ($result) {
            $url = $result[0]['url'];
            $slug = trim(str_replace(home_url(), '', $url), '/');
            $page = get_page_by_path($slug);
            pi_check("{$method}()'s Sources URL resolves to a REAL published page (not a dead link)",
                $page !== null && $page->post_status === 'publish',
                "slug='{$slug}' -> " . ($page ? "status={$page->post_status}" : 'NOT FOUND'));
        }
    } else {
        pi_check("{$method}() returns [] when the theme function is absent", $result === []);
    }
}

// contact_context() is the ONLY way any contact detail can reach the model —
// SYSTEM_PROMPT forbids answering from anything outside the reference block. So
// a field the theme publishes but this excerpt omits is a field the bot can
// never mention, and worse, may actively deny.
//
// That has now happened twice. First Facebook/Zalo/YouTube (a customer asked
// for the Facebook page and was told it wasn't known). Then, found 2026-08-09
// by diffing the theme's config against the excerpt:
//
//   "tên công ty đầy đủ trên hoá đơn là gì"
//     -> "Xin lỗi, tôi hiện chưa có thông tin…"
//   "còn email nào khác ngoài contact@ không"
//     -> "Chỉ có một địa chỉ email chính…"   <- false, gin@ is published
//
// This asserts the excerpt against the LIVE theme values rather than against
// hardcoded strings, so it keeps working when the shop changes its number and
// fails when a field stops being carried.
if (function_exists('saigonhouse_get_contact_info')) {
    $c = saigonhouse_get_contact_info();
    $excerpt = Pi_Chatbot_Retriever::contact_context()[0]['excerpt'] ?? '';

    $must_carry = ['hotline', 'hotline_alt', 'email_primary', 'email_secondary',
                   'address', 'website', 'facebook', 'zalo', 'youtube', 'company_full'];
    foreach ($must_carry as $field) {
        if (empty($c[$field])) continue; // absent in config = nothing to carry
        pi_check("contact_context() carries the theme's {$field} into the model's context",
            mb_strpos($excerpt, (string) $c[$field]) !== false,
            "missing: " . mb_substr((string) $c[$field], 0, 40));
    }

    // The empty tax code must stay ABSENT, not become a blank label the model
    // could treat as a field it is expected to fill.
    if (empty($c['mst'])) {
        pi_check('an empty tax code is omitted entirely rather than left blank',
            mb_stripos($excerpt, 'mst') === false && mb_stripos($excerpt, 'tax') === false,
            $excerpt);
    }
}

if ($has_project) {
    pi_section('Retriever::search_projects');
    $projects = Pi_Chatbot_Retriever::search_projects('dự án', 4);
    pi_check('search_projects returns only post_type=project rows', is_array($projects));
    // search_projects's internal SQL is already scoped to post_type='project';
    // this asserts the CONTRACT (formatted rows have no post_type field to leak,
    // but a non-empty result with real ids is still evidence the query worked).
    if ($projects) {
        pi_check('search_projects results carry real post_ids', $projects[0]['post_id'] > 0);
    }
}

// ── Multi-brand comparison questions ────────────────────────────────────────
pi_section('Retriever::search — multi-brand comparison splits top_k');

$second_brand = $wpdb->get_var($wpdb->prepare(
    "SELECT brand FROM {$table} WHERE post_type='product' AND brand != '' AND brand != %s GROUP BY brand ORDER BY COUNT(*) DESC LIMIT 1",
    $known_brand
));
if ($second_brand) {
    $compare = Pi_Chatbot_Retriever::search("{$known_brand} với {$second_brand} cái nào tốt hơn", 8);
    $brands_seen = array_unique(array_map(function ($r) { return $r['brand']; }, $compare));
    pi_check('a two-brand comparison question surfaces BOTH brands, not just one',
        count(array_intersect([$known_brand, $second_brand], $brands_seen)) === 2,
        'seen: ' . implode(',', $brands_seen));
} else {
    echo "  (skip: catalog has only one brand with products)\n";
}

// ── MySQL relevance must not outvote the IDF × field scorer ─────────────────
// Regression guard for the defect found on 2026-07-26: MySQL's boolean-mode
// score is an unbounded term-frequency sum and this catalog's `category`
// column is SEO-stuffed, so a product that merely REPEATS a common word in its
// category outscored products that genuinely have the discriminating word in
// their TITLE. Measured on this exact query: the stuffed rows scored 8.971 raw
// vs 4.003 for the correct ones — a gap no field weight could close, because
// the raw score was being added to the weighted score unnormalized.
//
// Asserted at the behavioural level (which products come back), not by
// inspecting MYSQL_SCORE_WEIGHT: the constant is an implementation detail and
// may legitimately be retuned, but "the title match wins" must not regress.
pi_section('Retriever::search — a keyword-stuffed category cannot outrank a title match');

$rows = Pi_Chatbot_Retriever::search('bếp gas 2 họng', 6);
$titles = array_map(function ($r) { return mb_strtolower($r['title']); }, $rows);
$top3 = array_slice($titles, 0, 3);
$with_hong = array_values(array_filter($top3, function ($t) { return mb_strpos($t, 'họng') !== false; }));

pi_check('"bếp gas 2 họng" returns results at all', !empty($rows));
pi_check('every one of the top 3 has the discriminating word "họng" in its title',
    count($top3) === 3 && count($with_hong) === 3,
    'top3: ' . implode(' | ', $top3));

// Same defect, different shape: here the discriminating word is SHORTER than
// innodb_ft_min_token_size (2 chars), so it never reaches MATCH() at all and
// only the PHP scorer can tell "máy rửa ly" (glass washer) from "máy rửa chén"
// (dish washer) — both sit in the same "Máy rửa ly chén" category.
$rows_ly = Pi_Chatbot_Retriever::search('máy rửa ly', 6);
$top3_ly = array_slice(array_map(function ($r) { return mb_strtolower($r['title']); }, $rows_ly), 0, 3);
$is_ly = array_values(array_filter($top3_ly, function ($t) { return mb_strpos($t, 'rửa ly') !== false; }));
pi_check('"máy rửa ly" ranks glass washers above dish washers despite the shared category',
    !empty($top3_ly) && count($is_ly) >= 2,
    'top3: ' . implode(' | ', $top3_ly));

// ── An exact product code must outrank a code that merely EXTENDS it ────────
// Regression guard for the defect found 2026-07-26: the exactness test used a
// plain (?![a-z0-9]) right boundary, which treats the "-" of "-ML" as the end
// of a word. So for "tủ đông FT-158MA-S" BOTH "FT-158MA-S" (post 1053) and
// "FT-158MA-S-ML" (post 1542) scored 1000 = exact; they tied, and the
// title-length penalty then picked the SHORTER title — which is the variant,
// not the model the customer actually named.
//
// Asserted behaviourally (which product ranks first), not by inspecting the
// regex: the pattern may legitimately be rewritten, but a customer typing a
// full model code must land on that exact model.
pi_section('Retriever::search — an exact code beats a longer code containing it');

$code_rows = Pi_Chatbot_Retriever::search('tủ đông FT-158MA-S', 5);
$code_top = $code_rows ? mb_strtolower($code_rows[0]['title']) : '';
pi_check('a full model code returns results', !empty($code_rows));
pi_check('the exact code FT-158MA-S ranks above the FT-158MA-S-ML variant',
    $code_top !== '' && mb_strpos($code_top, 'ft-158ma-s') !== false
        && mb_strpos($code_top, 'ft-158ma-s-ml') === false,
    'top1: ' . $code_top);

// The other direction must still hold — asking for the VARIANT must return the
// variant, so the fix cannot have simply demoted every hyphen-suffixed code.
$ml_rows = Pi_Chatbot_Retriever::search('FT-158MA-S-ML', 5);
pi_check('asking for the -ML variant explicitly still returns the -ML variant',
    !empty($ml_rows) && mb_strpos(mb_strtolower($ml_rows[0]['title']), 'ft-158ma-s-ml') !== false,
    'top1: ' . ($ml_rows ? $ml_rows[0]['title'] : '(none)'));

// ── Quick-reply buttons must never leak a product ───────────────────────────
// assets/chatbot.js's QUICK_REPLIES sends these 4 exact English strings as the
// raw customer message when the router is unavailable (disabled/circuit-open/
// LLM7 down — see class-rest.php's "Router unavailable" fallback, which calls
// Retriever::search() directly on the raw message). The widget comment used
// to claim these were "verified via Pi_Chatbot_Retriever" — a ONE-TIME manual
// check with nothing enforcing it stayed true. It didn't: re-running it
// 2026-07-26 found 2 of the 4 had silently started leaking a product (a new
// catalog import added "Meat Ageing Cabinet", whose description happens to
// say "...for all YOUR meat ageing needs" — "your" was not an English
// stopword yet, so it was treated as a real keyword). Root-caused and fixed
// by adding a closed set of English grammatical stopwords (mirroring the
// existing Vietnamese one), not by hand-picking new safe button phrases.
//
// This test is what makes "verified" durable: it runs the same check on
// every test run, against the LIVE catalog, so the next accidental collision
// fails a test instead of surfacing as a customer-visible bug.
pi_section('Retriever::search — the widget quick-reply buttons never leak a product');

$quick_replies = [
    'Please give me a quote',
    'Show me your completed project samples',
    'What services do you offer',
    'Please give me your contact information',
];
foreach ($quick_replies as $q) {
    $rows = Pi_Chatbot_Retriever::search($q, 5);
    pi_check("quick-reply \"$q\" returns no products via the raw-message fallback path",
        empty($rows),
        $rows ? 'leaked: ' . implode(' | ', array_column($rows, 'title')) : '');
}

// ── Tier 1 (mandatory AND) must not trust a match with no field evidence ────
// Regression guard for the defect found 2026-07-26, one level deeper than the
// quick-reply test above: Tier 2 (quorum) already required at least one
// keyword to match in TITLE/BRAND/CATEGORY before trusting a row
// (has_strong_field_hit) — Tier 1 (the +mandatory AND tier) had NO such
// check. It trusted "every mandatory word is present somewhere" as
// sufficient, with no regard for WHERE, so a coincidental content_text-only
// hit was treated identically to a real title match.
//
// "Báo giá" ("price quote") is the sharpest reproduction: after grammatical
// stopword filtering it degrades to keywords whose only surviving evidence
// was buried in unrelated product descriptions — Tier 1 returned 3 random
// freezers with full confidence. This exact question is intent='price' in
// production (handled by Retriever::price_context(), never touching this
// tier) — the same "measured against search() directly, not the live
// customer path" caveat as the negative group in eval-retrieval.php's
// golden set applies here too. The fix still matters: this is also the code
// path REST falls back to whenever the router is unavailable.
pi_section('Retriever::search — Tier 1 (mandatory AND) requires field evidence, not just literal presence');

pi_check('"Báo giá" — small talk degrading to content-only-coincidence keywords — returns no products',
    empty(Pi_Chatbot_Retriever::search('Báo giá', 5)));

// Regression guard for the defect found 2026-07-27, live via a real customer
// conversation ("how about another brand" after asking about the Halton KVE
// exhaust hood): Tier 2 (quorum) required at least 1 word to match in
// title/brand/category (has_strong_field_hit), but that 1 word could be the
// generic Vietnamese verb "hút" ("to extract/suck") — which is also the first
// syllable of "hút chân không" ("vacuum sealer", an unrelated Sammic product
// line) as well as "hút mùi" ("exhaust/odor extraction", the actual Halton
// hood category being searched for). A single shared morpheme between two
// unrelated compound terms was enough to seat 3 vacuum-sealer products next
// to the real exhaust-hood results — the generator, handed that mixed
// context, produced a garbled answer. Fixed generally: Tier 2 now requires at
// least 2 DISTINCT query words to genuinely match (verify_and_score()'s new
// $r->hits), not just 1 — this is the only tier with no mandatory-AND word at
// all, so it's the one place a single shared morpheme can seat a product
// alone. Uses only the words "hút" and "mùi" — anything the router's
// keywords_vi commonly extracts for this exact question shape — so this
// exercises the real tier, not a synthetic setup.
pi_section('Retriever::search — Tier 2 (quorum) requires 2+ distinct real word matches, not just 1');

// Vietnamese-only via search() genuinely has no product to find (the Halton
// line is English-titled/described — a pure Vietnamese quorum match must
// legitimately stay empty, not surface a coincidental vacuum-sealer hit).
// search_hybrid() with the English alt_query leg is what production actually
// calls (see class-rest.php's fast/max product dispatch) — testing anything
// less would not exercise the real path a customer's question takes.
$hood_results = Pi_Chatbot_Retriever::search_hybrid('hút mùi hút mùi công nghiệp', 6, [
    'alt_queries' => ['exhaust hood commercial kitchen'],
]);
$leaked_vacuum_sealer = array_filter($hood_results, function ($r) {
    return stripos($r['title'], 'hút chân không') !== false;
});
pi_check('"hút mùi" (exhaust hood) no longer leaks "hút chân không" (vacuum sealer) products',
    empty($leaked_vacuum_sealer),
    empty($hood_results) ? '(no results at all)' : implode(', ', array_column($hood_results, 'title')));
pi_check('"hút mùi" still returns real exhaust-hood results — the fix did not just empty the tier',
    !empty(array_filter($hood_results, function ($r) { return stripos($r['title'], 'exhaust') !== false || stripos($r['brand'], 'halton') !== false; })),
    empty($hood_results) ? '(no results at all)' : implode(', ', array_column($hood_results, 'title')));

pi_section('Phase 3 — Retrieval Enhancement & LLM Fallback Chain');

// 1. search_hybrid return_structured
$struct = Pi_Chatbot_Retriever::search_hybrid('bếp gas', 6, ['return_structured' => true]);
pi_check('search_hybrid return_structured returns status ok', ($struct['status'] ?? '') === 'ok', $struct['status'] ?? 'missing');
pi_check('search_hybrid return_structured returns meta payload', isset($struct['meta']['tier_used']) && isset($struct['meta']['search_time_ms']), json_encode($struct['meta'] ?? []));
pi_check('search_hybrid return_structured estimates token count', ($struct['meta']['token_estimate'] ?? 0) > 0, (string) ($struct['meta']['token_estimate'] ?? 0));

// 2. search_semantic stub
$sem = Pi_Chatbot_Retriever::search_semantic('bếp gas', 5);
pi_check('search_semantic stub returns expected not_implemented status', ($sem['status'] ?? '') === 'not_implemented', $sem['status'] ?? 'missing');

// 3. LLM Client call_with_fallback
pi_check('Pi_Chatbot_LLM_Client has call_with_fallback method', method_exists('Pi_Chatbot_LLM_Client', 'call_with_fallback'), 'method missing');
pi_check('Pi_Chatbot_LLM_Client has MODEL_FALLBACK_CHAIN constant', defined('Pi_Chatbot_LLM_Client::MODEL_FALLBACK_CHAIN'), 'constant missing');

// pi_report() prints a summary from the SHARED pi_check() static state — when
// run standalone that's exactly this suite's own totals; when run-all.php
// requires this file alongside others, the same static keeps accumulating, so
// printing here would show a confusing cumulative count instead of this
// suite's own. run-all.php does its own before/after accounting per suite and
// prints one combined summary at the end, so this suite only prints (and
// exits on) its own report when running standalone.
if (!defined('PI_TEST_SUITE_MODE')) exit(pi_report());
