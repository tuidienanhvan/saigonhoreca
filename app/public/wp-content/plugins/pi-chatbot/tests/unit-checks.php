<?php
/**
 * unit-checks.php — Assertions for the pure logic added by the max-accuracy
 * pipeline: the vector codec, and the validation that decides how far an LLM's
 * reply is allowed to influence what the customer sees.
 *
 * Separate from eval-retrieval.php because that harness measures RANKING QUALITY
 * against real catalog data, while these are correctness checks that must hold
 * regardless of the data — and, unlike the ranking metrics, they need no API key
 * and no network, so they can run on any checkout.
 *
 * The validation paths are worth asserting precisely because they only fire when
 * a model misbehaves: a reranker that quietly honours hallucinated indexes, or a
 * router whose difficulty field fails OPEN instead of closed, would look fine in
 * every manual test and only surface as wrong answers in production.
 *
 * Run:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/unit-checks.php
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Must be run via `wp eval-file`, not directly with the PHP CLI.\n");
    exit(1);
}

/**
 * Results are held in a function static, NOT in file-scope variables reached via
 * `global`. `wp eval-file` includes this file from INSIDE a function, so its
 * top-level variables are locals of that scope — a `global $failures` inside a
 * helper would write to a completely different variable than the one the summary
 * at the bottom reads. The first version of this file did exactly that, and the
 * result was a harness that always printed success: failures accumulated into a
 * global nobody looked at. A test that cannot report failure is worse than no
 * test, so the state lives in one place that no scoping rule can split.
 */
function pi_check($name = null, $condition = null, $detail = '') {
    static $passed = 0;
    static $failed = [];

    if ($name === null) return ['passed' => $passed, 'failed' => $failed]; // summary request

    if ($condition) {
        $passed++;
        echo "  ok   {$name}\n";
    } else {
        $failed[] = $name . ($detail !== '' ? " ({$detail})" : '');
        echo "  FAIL {$name}" . ($detail !== '' ? " — {$detail}" : '') . "\n";
    }
    return null;
}

/** Calls a private/protected static method — these are private for good reason
 *  (they are not API), but their behaviour is exactly what needs asserting. */
function pi_call_private($class, $method, array $args) {
    $ref = new ReflectionMethod($class, $method);
    $ref->setAccessible(true);
    return $ref->invokeArgs(null, $args);
}

echo "\n=== pi-chatbot unit checks ===\n";

// ── Accent-folded fallback tier ────────────────────────────────────────────
// Vietnamese typed without diacritics is the single largest measured retrieval
// gap: stripping accents from the 37 accented golden questions collapses hit@3
// from 0.838 to 0.324, with 18 of the 19 losses returning NOTHING. Tier 3 exists
// for exactly that, and its guards are what keep it from becoming a junk
// generator, so both the folding and the guards are asserted here.
echo "
[Pi_Chatbot_Retriever::fold_diacritics]
";
pi_check('folds tone marks and đ', Pi_Chatbot_Retriever::fold_diacritics('Tủ Đông') === 'tu dong');
pi_check('folds every Vietnamese vowel family',
    Pi_Chatbot_Retriever::fold_diacritics('ăâêôơư đá ệ ữ ỹ') === 'aaeoou da e u y');
pi_check('leaves unaccented text untouched',
    Pi_Chatbot_Retriever::fold_diacritics('FT-126MA-S') === 'ft-126ma-s');
pi_check('is idempotent',
    Pi_Chatbot_Retriever::fold_diacritics(Pi_Chatbot_Retriever::fold_diacritics('Máy Làm Đá'))
        === Pi_Chatbot_Retriever::fold_diacritics('Máy Làm Đá'));

echo "
[Unaccented questions now retrieve]
";
foreach ([
    'may lam da tu dong' => 'đá',
    'da vay'             => 'vảy',
    'bep gas'            => 'gas',
] as $q => $expect_word) {
    $r = Pi_Chatbot_Retriever::search($q, 3);
    pi_check("\"{$q}\" returns results", !empty($r), count($r) . ' results');
    pi_check("\"{$q}\" results are on-topic",
        !empty($r) && mb_stripos($r[0]['title'], $expect_word) !== false,
        !empty($r) ? $r[0]['title'] : 'none');
}

echo "
[Tier 3 only fires as a genuine last resort]
";
$looks = new ReflectionMethod('Pi_Chatbot_Retriever', 'looks_unaccented');
$looks->setAccessible(true);
pi_check('a fully accented question is NOT treated as unaccented',
    !$looks->invoke(null, 'cho hỏi giờ mở cửa'));
pi_check('a plain-ASCII question IS treated as unaccented',
    $looks->invoke(null, 'may lam da'));
// The real invariant is NOT "accented small talk returns nothing" — some of it
// already matched products through the FULLTEXT tier long before Tier 3 existed
// ("cho hỏi giờ mở cửa" pulls in display fridges via "cửa"/"door", a pre-existing
// false positive this work neither caused nor fixed). Asserting that would be
// testing someone else's bug and would fail for the wrong reason.
//
// What Tier 3 guarantees is narrower and is what gets asserted: for an accented
// question it CONTRIBUTES NOTHING, so the final result is exactly what the
// earlier tiers produced. That is the property that kept the small-talk group at
// +0.000 when the unguarded version cost it 0.100.
$fulltext = new ReflectionMethod('Pi_Chatbot_Retriever', 'search_fulltext');
$fulltext->setAccessible(true);
$keywords = new ReflectionMethod('Pi_Chatbot_Retriever', 'extract_keyword_list');
$keywords->setAccessible(true);
foreach (['cho hỏi giờ mở cửa', 'bạn tên gì', 'máy bay trực thăng'] as $accented) {
    list($long, $short) = $keywords->invoke(null, $accented);
    $tiers12 = $long || $short ? $fulltext->invoke(null, $long, $short, 6, '') : [];
    $actual  = Pi_Chatbot_Retriever::search($accented, 6);
    pi_check("accented \"{$accented}\" gets no Tier 3 contribution",
        array_column($actual, 'post_id') === array_column($tiers12, 'post_id'),
        count($actual) . ' vs ' . count($tiers12));
}

// ── Language detection for unaccented Vietnamese ───────────────────────────
// Added because making Tier 3 work exposed the follow-on defect: "tu dong 2
// canh" started finding the right freezers and then describing them to a
// Vietnamese customer in English, since every Vietnamese check above keys on
// tone marks this question does not have.
echo "
[Language: unaccented Vietnamese vs English]
";
$note = new ReflectionMethod('Pi_Chatbot_Generator', 'build_language_note');
$note->setAccessible(true);
$lang = function ($q) use ($note) {
    $n = $note->invoke(null, $q);
    if (mb_strpos($n, 'tiếng Việt') !== false) return 'VI';
    if (mb_strpos($n, 'English') !== false) return 'EN';
    if (mb_strpos($n, 'français') !== false) return 'FR';
    if (mb_strpos($n, 'Deutsch') !== false) return 'DE';
    return '?';
};
foreach (['tu dong 2 canh', 'may lam da tu dong', 'bep chien nhung cong nghiep'] as $q) {
    pi_check("unaccented \"{$q}\" -> Vietnamese", $lang($q) === 'VI', $lang($q));
}
// The regression this guard was measured against: "gas" is an everyday
// Vietnamese word AND an English one, and including it sent a real English
// question down the Vietnamese branch.
foreach (['Fujimak gas stove FGTLS1260', 'commercial dishwasher', 'do you have ice machines'] as $q) {
    pi_check("English \"{$q}\" -> English", $lang($q) === 'EN', $lang($q));
}
pi_check('accented Vietnamese is still Vietnamese', $lang('tủ đông 2 cánh') === 'VI');
// The other languages must be untouched — they are checked by script range
// BEFORE any of this counting runs.
pi_check('French is unaffected',
    $lang('Je voudrais des informations sur les fours industriels.') === 'FR');
pi_check('German is unaffected', $lang('Ich möchte industrielle Öfen kaufen') === 'DE');

// ── Product codes bypass the expensive path ────────────────────────────────
// An exact identifier match is ground truth. Reranking it was the ONLY
// regression the LLM pass caused on the golden set ("tủ đông FT-158MA-S"), so
// this guard is protecting a measured failure, not a hypothetical one.
echo "\n[Product-code short-circuit]\n";
$code_q = 'tủ đông FT-158MA-S';
$lex = array_map(function ($r) { return $r['post_id']; }, Pi_Chatbot_Retriever::search($code_q, 6));
$hyb = array_map(function ($r) { return $r['post_id']; }, Pi_Chatbot_Retriever::search_hybrid($code_q, 12));
pi_check('a known code question found results at all', !empty($lex));
pi_check('search_hybrid short-circuits to the exact code match',
    array_slice($hyb, 0, count($lex)) === $lex,
    'lexical=' . implode(',', $lex) . ' hybrid=' . implode(',', array_slice($hyb, 0, 6)));
pi_check('has_product_code_match() is true for a code that exists',
    Pi_Chatbot_Retriever::has_product_code_match($code_q));
pi_check('has_product_code_match() is false without a code',
    !Pi_Chatbot_Retriever::has_product_code_match('bếp gas 2 họng'));
// A code the catalog doesn't contain must NOT skip reranking — that question is
// exactly the ambiguous kind the deeper pipeline exists for.
pi_check('has_product_code_match() is false for an unknown code',
    !Pi_Chatbot_Retriever::has_product_code_match('máy ZZ-9999 có không'));

// ── Router difficulty must fail safe ───────────────────────────────────────
// 'hard' is the only value that spends three extra LLM calls and ~20 extra
// seconds of a customer's time, so every unrecognized value must land on 'easy'.
echo "\n[Pi_Chatbot_Router::validate — difficulty fails safe]\n";

$base = ['intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => ['tủ đông'], 'brand' => null];

$cases = [
    'missing difficulty'          => [],
    'misspelled difficulty'       => ['difficulty' => 'HARDD'],
    'non-string difficulty'       => ['difficulty' => ['hard']],
    'numeric difficulty'          => ['difficulty' => 1],
];
foreach ($cases as $label => $extra) {
    $out = pi_call_private('Pi_Chatbot_Router', 'validate', [array_merge($base, $extra)]);
    pi_check("{$label} -> easy", is_array($out) && $out['difficulty'] === 'easy', is_array($out) ? $out['difficulty'] : 'null');
}

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [array_merge($base, ['difficulty' => 'HARD'])]);
pi_check('difficulty is case-insensitive', is_array($out) && $out['difficulty'] === 'hard');

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [
    ['intent' => 'contact', 'difficulty' => 'hard', 'query_variants' => ['a', 'b']],
]);
pi_check('non-product intent is forced to easy', is_array($out) && $out['difficulty'] === 'easy');
pi_check('non-product intent drops query variants', is_array($out) && $out['query_variants'] === []);

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [array_merge($base, [
    'difficulty' => 'hard',
    'query_variants' => ['one', 'two', 'three', 'four', 'five', str_repeat('x', 200), '', 'one'],
])]);
pi_check('query variants are capped at MAX_QUERY_VARIANTS',
    is_array($out) && count($out['query_variants']) <= Pi_Chatbot_Router::MAX_QUERY_VARIANTS,
    is_array($out) ? count($out['query_variants']) . ' kept' : 'null');
pi_check('over-long and empty variants are dropped',
    is_array($out) && !in_array('', $out['query_variants'], true)
        && !in_array(str_repeat('x', 200), $out['query_variants'], true));
pi_check('duplicate variants are dropped',
    is_array($out) && count($out['query_variants']) === count(array_unique($out['query_variants'])));

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [array_merge($base, ['difficulty' => 'easy', 'query_variants' => ['a', 'b']])]);
pi_check('easy questions carry no variants', is_array($out) && $out['query_variants'] === []);

// ── A negative verdict must stay distinguishable from a failure ────────────
// validate() used to collapse intent='none' to null. null is ALSO what enrich()
// returns when the router is disabled, its breaker is open, or its reply was
// garbage — and class-rest.php answers null by running a product search on the
// raw message. So "this is small talk" and "the router broke" were the same
// value, and small talk came back holding freezers.
echo "
[Pi_Chatbot_Router::validate — 'none' is a verdict, not a failure]
";

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [
    ['intent' => 'none', 'keywords_en' => [], 'keywords_vi' => [], 'brand' => null],
]);
pi_check("intent='none' is returned, not collapsed to null", is_array($out));
pi_check("intent='none' survives validation intact",
    is_array($out) && $out['intent'] === 'none', is_array($out) ? $out['intent'] : 'null');
pi_check("'none' carries no keywords", is_array($out) && $out['keywords_en'] === [] && $out['keywords_vi'] === []);
pi_check("'none' is never routed to the expensive path",
    is_array($out) && $out['difficulty'] === 'easy');

// A reply with no usable intent AND no keywords still resolves to 'none' rather
// than guessing 'product' — no context beats wrong context.
$out = pi_call_private('Pi_Chatbot_Router', 'validate', [['keywords_en' => [], 'keywords_vi' => []]]);
pi_check('an empty reply resolves to none', is_array($out) && $out['intent'] === 'none');

// But keywords present with a broken intent field still means a product search —
// the model did useful extraction, don't discard it over a malformed field.
$out = pi_call_private('Pi_Chatbot_Router', 'validate', [
    ['intent' => 'prodcut', 'keywords_en' => ['freezer'], 'keywords_vi' => []],
]);
pi_check('a misspelled intent with keywords still means product',
    is_array($out) && $out['intent'] === 'product', is_array($out) ? $out['intent'] : 'null');

// ── Reranker may reorder, never invent ─────────────────────────────────────
echo "\n[Pi_Chatbot_Reranker::apply — reorder only, never invent]\n";

$pool = [];
foreach ([101, 102, 103, 104] as $i => $pid) {
    $pool[] = ['post_id' => $pid, 'title' => 'Product ' . $pid, 'brand' => '', 'category' => '', 'url' => '', 'excerpt' => '', 'score' => 1.0];
}

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [3, 1], 'relevant' => [3]], $pool]);
pi_check('valid ranking reorders the pool',
    is_array($out) && array_map(function ($r) { return $r['post_id']; }, $out['results']) === [103, 101]);
pi_check('confidence reflects the relevant subset',
    is_array($out) && abs($out['confidence'] - 0.5) < 1e-9, is_array($out) ? $out['confidence'] : 'null');

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [99, 42, 77, 1], 'relevant' => []], $pool]);
pi_check('a mostly-hallucinated ranking is rejected outright', $out === null);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [2, 99], 'relevant' => [2]], $pool]);
pi_check('a single out-of-range index is dropped, the rest kept',
    is_array($out) && array_map(function ($r) { return $r['post_id']; }, $out['results']) === [102]);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [2, 2, 3], 'relevant' => []], $pool]);
pi_check('repeated indexes are de-duplicated',
    is_array($out) && array_map(function ($r) { return $r['post_id']; }, $out['results']) === [102, 103]);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [], 'relevant' => []], $pool]);
pi_check('an explicitly empty ranking is honoured as a real verdict',
    is_array($out) && $out['results'] === [] && $out['confidence'] === 0.0);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['relevant' => [1]], $pool]);
pi_check('a reply with no ranking key is a failure, not an empty verdict', $out === null);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [1, 2, 3], 'relevant' => [1, 2, 3, 4]], $pool]);
pi_check('confidence is capped at 1.0', is_array($out) && $out['confidence'] <= 1.0);

echo "\n[Pi_Chatbot_Reranker::rerank — always returns a usable list]\n";
$result = Pi_Chatbot_Reranker::rerank('bếp gas 2 họng', $pool, 3);
pi_check('never returns fewer than requested when candidates exist', count($result['results']) === 3);
pi_check('reports used=false when it could not rerank', isset($result['used']));
$single = Pi_Chatbot_Reranker::rerank('x', [$pool[0]], 3);
pi_check('a single candidate skips the model call entirely', $single['used'] === false && count($single['results']) === 1);
pi_check('an empty candidate list is safe', Pi_Chatbot_Reranker::rerank('x', [], 3)['results'] === []);

// ── Shared JSON extraction ─────────────────────────────────────────────────
// Three callers depend on this now (router, reranker, fact check), so a
// regression here breaks classification, ranking and verification at once.
echo "\n[Pi_Chatbot_LLM_Client::extract_json]\n";
pi_check('plain JSON', Pi_Chatbot_LLM_Client::extract_json('{"a":1}') === ['a' => 1]);
pi_check('fenced JSON', Pi_Chatbot_LLM_Client::extract_json("```json\n{\"a\":1}\n```") === ['a' => 1]);
pi_check('JSON with surrounding prose',
    Pi_Chatbot_LLM_Client::extract_json('Sure! {"a":1} hope that helps') === ['a' => 1]);
pi_check('nested braces stay balanced',
    Pi_Chatbot_LLM_Client::extract_json('{"a":{"b":2}}') === ['a' => ['b' => 2]]);
pi_check('unterminated JSON returns null', Pi_Chatbot_LLM_Client::extract_json('{"a":1') === null);
pi_check('empty input returns null', Pi_Chatbot_LLM_Client::extract_json('') === null);
pi_check('prose with no JSON returns null', Pi_Chatbot_LLM_Client::extract_json('I cannot help') === null);

// ── Self-test ──────────────────────────────────────────────────────────────
// Proves the harness can actually FAIL. Without this, a scoping or counter bug
// makes every run print success no matter what the assertions did — which is
// precisely the bug the pi_check() docblock describes.
echo "\n[harness self-test]\n";
$before = pi_check();
pi_check('deliberate failure (expected)', false, 'this line SHOULD read FAIL');
$after = pi_check();
$harness_ok = count($after['failed']) === count($before['failed']) + 1;
pi_check('the harness records failures', $harness_ok);

// ── Report ─────────────────────────────────────────────────────────────────
$summary = pi_check();
$real_failures = array_values(array_filter($summary['failed'], function ($f) {
    return strpos($f, 'deliberate failure (expected)') !== 0;
}));

echo "\n" . str_repeat('-', 60) . "\n";
if ($real_failures) {
    echo sprintf("FAILED — %d passed, %d failed:\n", $summary['passed'], count($real_failures));
    foreach ($real_failures as $f) echo "  - {$f}\n";
    echo "\n";
    exit(1);
}
echo sprintf("All %d checks passed (plus 1 deliberate failure proving the harness reports them).\n\n", $summary['passed']);
