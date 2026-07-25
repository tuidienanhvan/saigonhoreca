<?php
/**
 * suite-pure.php — assertions for functions that touch neither the database
 * nor the network. Fastest, most stable suite: a failure here can only be a
 * real logic regression, never flaky infrastructure.
 *
 * Run standalone:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/suite-pure.php
 * Or via tests/run-all.php.
 */

require_once __DIR__ . '/lib/harness.php';

// ── Retriever::extract_keyword_list ─────────────────────────────────────────
pi_section('Retriever::extract_keyword_list');

list($long, $short) = pi_call_private('Pi_Chatbot_Retriever', 'extract_keyword_list', ['tủ đông 2 cánh Hoshizaki']);
pi_check('splits into long/short by MIN_TOKEN_LENGTH', in_array('hoshizaki', $long, true) && in_array('đông', $long, true));
// A single-character token ("2") is dropped entirely by the mb_strlen < 2 noise
// filter — it never reaches the short/long split at all. "tủ" (2 chars) clears
// that filter and, being below MIN_TOKEN_LENGTH (3), lands in $short. Verified
// directly against the method (in_array('2', $short) is FALSE) before asserting
// — an earlier version of this check wrongly assumed the digit would appear.
pi_check('a single-character token is dropped as noise, not kept in $short',
    !in_array('2', $short, true), implode(',', $short));
pi_check('a genuine short (<MIN_TOKEN_LENGTH) word lands in $short',
    in_array('tủ', $short, true), implode(',', $short));

list($long2, $short2) = pi_call_private('Pi_Chatbot_Retriever', 'extract_keyword_list', ['xin chào bạn có khỏe không']);
// "xin chào"/"bạn"/"có"/"không" are genuine stopwords and are stripped, but
// "khỏe" ("healthy" — the actual content word) is not on the stopword list and
// correctly survives extraction. A greeting with a real content word inside it
// is not the same case as pure small talk (see suite-rest.php for that: routing
// small talk away from retrieval happens at the ROUTER layer, via intent='none'
// — extract_keyword_list has no small-talk awareness and isn't supposed to).
pi_check('greeting filler words are stripped but a real content word survives',
    $long2 === ['khỏe'] && $short2 === [],
    'long=' . implode(',', $long2) . ' short=' . implode(',', $short2));

list($long3, ) = pi_call_private('Pi_Chatbot_Retriever', 'extract_keyword_list', ['FT-126MA-S tủ đông']);
pi_check('punctuation splits a product code into its tokens',
    in_array('ft', $long3, true) || in_array('126ma', $long3, true), implode(',', $long3));

// ── Retriever::extract_product_codes ────────────────────────────────────────
pi_section('Retriever::extract_product_codes');

$codes = pi_call_private('Pi_Chatbot_Retriever', 'extract_product_codes', ['Cho hỏi giá FT-126MA-S và HR-78MA-S-B nhé']);
pi_check('extracts multiple codes', count($codes) === 2, implode(',', $codes));
pi_check('codes are unique', $codes === array_unique($codes));

$none = pi_call_private('Pi_Chatbot_Retriever', 'extract_product_codes', ['tủ đông 2 cánh']);
pi_check('a plain question has no product codes', $none === [], implode(',', $none));

$digit_only = pi_call_private('Pi_Chatbot_Retriever', 'extract_product_codes', ['tôi cần 4 cái']);
pi_check('a bare number is not mistaken for a product code', $digit_only === [], implode(',', $digit_only));

// ── Retriever::make_excerpt ──────────────────────────────────────────────────
pi_section('Retriever::make_excerpt');

$short_text = 'Sản phẩm nhỏ gọn.';
pi_check('text shorter than the limit is returned untouched',
    pi_call_private('Pi_Chatbot_Retriever', 'make_excerpt', [$short_text, 400, []]) === $short_text);

$long_text = str_repeat('lorem ipsum dolor sit amet ', 30) . 'TỦĐÔNGKEYWORD' . str_repeat(' consectetur adipiscing elit', 30);
$excerpt = pi_call_private('Pi_Chatbot_Retriever', 'make_excerpt', [$long_text, 100, ['TỦĐÔNGKEYWORD']]);
pi_check('excerpt is centered on the keyword, not the start of the string',
    mb_stripos($excerpt, 'TỦĐÔNGKEYWORD') !== false, $excerpt);
pi_check('excerpt marks a truncated start with an ellipsis', mb_substr($excerpt, 0, 1) === '…', $excerpt);

$no_keyword_excerpt = pi_call_private('Pi_Chatbot_Retriever', 'make_excerpt', [$long_text, 50, []]);
pi_check('with no keywords, excerpt falls back to the start of the string',
    mb_substr($no_keyword_excerpt, 0, 5) === mb_substr($long_text, 0, 5));

// ── Retriever::dedupe_by_content / only_products ────────────────────────────
pi_section('Retriever::dedupe_by_content / only_products');

$dupe_rows = [
    pi_row(['post_id' => 1, 'content_text' => 'same spec sheet']),
    pi_row(['post_id' => 2, 'content_text' => 'same spec sheet']),
    pi_row(['post_id' => 3, 'content_text' => 'same spec sheet']),
    pi_row(['post_id' => 4, 'content_text' => 'different spec sheet']),
];
$deduped = pi_call_private('Pi_Chatbot_Retriever', 'dedupe_by_content', [$dupe_rows]);
pi_check('duplicate content is capped at MAX_PER_DUPLICATE_GROUP',
    count(array_filter($deduped, function ($r) { return $r->content_text === 'same spec sheet'; })) === 2,
    count($deduped) . ' total rows kept');
pi_check('a distinct row always survives dedupe',
    (bool) array_filter($deduped, function ($r) { return $r->content_text === 'different spec sheet'; }));

$mixed = [pi_row(['post_id' => 1, 'post_type' => 'product']), pi_row(['post_id' => 2, 'post_type' => 'project'])];
$only = pi_call_private('Pi_Chatbot_Retriever', 'only_products', [$mixed]);
pi_check('only_products drops non-product rows', count($only) === 1 && $only[0]->post_id === 1);
pi_check('only_products on empty/non-array input returns []',
    pi_call_private('Pi_Chatbot_Retriever', 'only_products', [null]) === []);

// ── Retriever::require_literal_all ──────────────────────────────────────────
pi_section('Retriever::require_literal_all');

$rows = [
    pi_row(['post_id' => 1, 'title' => 'Tủ Đông 2 Cánh']),
    pi_row(['post_id' => 2, 'title' => 'Tủ Mát 2 Cánh']),
];
pi_check('empty mandatory list passes every row through unchanged',
    pi_call_private('Pi_Chatbot_Retriever', 'require_literal_all', [$rows, []]) === $rows);

$filtered = pi_call_private('Pi_Chatbot_Retriever', 'require_literal_all', [$rows, ['đông']]);
pi_check('only the row with the literal, diacritic-exact word survives',
    count($filtered) === 1 && $filtered[0]->post_id === 1);

$collation_bug = pi_call_private('Pi_Chatbot_Retriever', 'require_literal_all', [
    [pi_row(['post_id' => 1, 'title' => 'Bàn đông FT-126MA-S'])],
    ['đá'], // "đá" (ice) must NOT match "đông" (freeze) despite MySQL's loose default collation
]);
pi_check('literal check is diacritic-exact ("đá" does not match "đông")', $collation_bug === []);

// ── Retriever::verify_and_score (called with $idf pre-supplied — pure) ──────
pi_section('Retriever::verify_and_score');

$idf = ['tủ' => ['df' => 100, 'idf' => 0.2], 'đông' => ['df' => 5, 'idf' => 3.0]];
$score_rows = [
    pi_row(['post_id' => 1, 'title' => 'Tủ Đông Hoshizaki', 'score' => 0.0]), // hit in TITLE
    pi_row(['post_id' => 2, 'title' => 'Bàn Mát', 'content_text' => 'có tủ đông kèm theo', 'score' => 0.0]), // hit in CONTENT only
    pi_row(['post_id' => 3, 'title' => 'Bếp Gas', 'content_text' => 'không liên quan', 'score' => 0.0]), // no hit
];
$scored = pi_call_private('Pi_Chatbot_Retriever', 'verify_and_score', [$score_rows, ['tủ', 'đông'], [], $idf]);
pi_check('rows with zero keyword hits are dropped', count($scored) === 2);
pi_check('title hits outrank content-only hits (higher field weight)',
    $scored[0]->post_id === 1, isset($scored[0]) ? $scored[0]->post_id : 'none');
pi_check('a title hit sets has_strong_field_hit', !empty($scored[0]->has_strong_field_hit));
pi_check('a content-only hit does NOT set has_strong_field_hit',
    isset($scored[1]) && empty($scored[1]->has_strong_field_hit));

// Deterministic tie-break: two rows with genuinely EQUAL computed scores must
// always resolve the same way, not depend on the physical row order the
// database happened to hand back. Traced from a real regression: "tủ chiên
// nhúng đôi" ("double dip freezer") returned a different top-1 answer after
// unrelated writes elsewhere in the live table changed MySQL's tie order,
// while neither the query nor the code had changed — a customer asking the
// exact same question could get a different first answer depending on server
// state. Fixed by breaking ties on title length (shorter = the base model,
// same reasoning search_unaccented() already used one tier up).
$tie_idf = ['tu' => ['df' => 50, 'idf' => 1.0]];
$tie_rows = [
    pi_row(['post_id' => 101, 'title' => 'Tu Chien Nhung Doi FSSDF12D', 'score' => 0.0]),
    pi_row(['post_id' => 102, 'title' => 'Tu Chien Nhung Doi SDF12D', 'score' => 0.0]),
];
$tie_scored = pi_call_private('Pi_Chatbot_Retriever', 'verify_and_score', [$tie_rows, ['tu'], [], $tie_idf]);
pi_check('tied scores are broken by title length, not left to row order',
    count($tie_scored) === 2 && abs($tie_scored[0]->score - $tie_scored[1]->score) < 1e-9
        && $tie_scored[0]->post_id === 102,
    isset($tie_scored[0]) ? "top1={$tie_scored[0]->post_id} score={$tie_scored[0]->score}" : 'empty');
// Reversing the input order must not change the outcome — proves the result
// comes from the tie-break rule, not from whichever row PHP saw first.
$tie_scored_rev = pi_call_private('Pi_Chatbot_Retriever', 'verify_and_score', [array_reverse($tie_rows), ['tu'], [], $tie_idf]);
pi_check('tie-break result is independent of input order',
    isset($tie_scored_rev[0]) && $tie_scored_rev[0]->post_id === 102);

// ── Retriever::format_rows ───────────────────────────────────────────────────
pi_section('Retriever::format_rows');

$formatted = pi_call_private('Pi_Chatbot_Retriever', 'format_rows', [[pi_row(['post_id' => 5])], []]);
pi_check('format_rows produces the expected assoc-array shape',
    isset($formatted[0]['post_id'], $formatted[0]['title'], $formatted[0]['brand'],
          $formatted[0]['category'], $formatted[0]['url'], $formatted[0]['excerpt'], $formatted[0]['score']));
pi_check('format_rows on empty input returns []', pi_call_private('Pi_Chatbot_Retriever', 'format_rows', [null]) === []);

// ── Retriever::fold_diacritics / looks_unaccented ───────────────────────────
pi_section('Retriever::fold_diacritics / looks_unaccented');

pi_check('folds tone marks and đ', Pi_Chatbot_Retriever::fold_diacritics('Tủ Đông') === 'tu dong');
pi_check('folds every Vietnamese vowel family',
    Pi_Chatbot_Retriever::fold_diacritics('ăâêôơư đá ệ ữ ỹ') === 'aaeoou da e u y');
pi_check('leaves unaccented text untouched',
    Pi_Chatbot_Retriever::fold_diacritics('FT-126MA-S') === 'ft-126ma-s');
pi_check('folding is idempotent',
    Pi_Chatbot_Retriever::fold_diacritics(Pi_Chatbot_Retriever::fold_diacritics('Máy Làm Đá'))
        === Pi_Chatbot_Retriever::fold_diacritics('Máy Làm Đá'));

$looks = fn ($q) => pi_call_private('Pi_Chatbot_Retriever', 'looks_unaccented', [$q]);
pi_check('a fully accented question is NOT unaccented', !$looks('cho hỏi giờ mở cửa'));
pi_check('a plain-ASCII question IS unaccented', $looks('may lam da'));
pi_check('a mixed question (any diacritic present) is NOT unaccented', !$looks('may lam da tủ'));

// ── Generator::context_to_text ──────────────────────────────────────────────
pi_section('Generator::context_to_text');

pi_check('empty context renders the "no data" placeholder',
    strpos(Pi_Chatbot_Generator::context_to_text([]), 'No matching') !== false);
$ctx_text = Pi_Chatbot_Generator::context_to_text([pi_result(['title' => 'Tủ Đông X', 'brand' => 'Hoshizaki', 'category' => 'Tủ đông'])]);
pi_check('context includes title/brand/category/url/excerpt',
    strpos($ctx_text, 'Tủ Đông X') !== false && strpos($ctx_text, 'Hoshizaki') !== false
        && strpos($ctx_text, 'Tủ đông') !== false);
$ctx_no_brand = Pi_Chatbot_Generator::context_to_text([pi_result(['brand' => '', 'category' => ''])]);
pi_check('brand/category are omitted, not rendered empty, when absent',
    strpos($ctx_no_brand, '(Brand: )') === false && strpos($ctx_no_brand, '[]') === false);

// ── Generator::looks_degenerate ─────────────────────────────────────────────
pi_section('Generator::looks_degenerate');

$looks_degenerate = fn ($t) => pi_call_private('Pi_Chatbot_Generator', 'looks_degenerate', [$t]);
pi_check('short text is never flagged (sample too small to judge)',
    !$looks_degenerate('lặp lặp lặp lặp lặp'));
$repeated = str_repeat('lặp ', 30);
pi_check('one word repeating past the 25% threshold is flagged', $looks_degenerate($repeated));
$normal = 'Sản phẩm này có dung tích lớn, phù hợp cho nhà hàng quy mô vừa và lớn, tiết kiệm điện năng hiệu quả rõ rệt';
pi_check('normal prose is not flagged', !$looks_degenerate($normal));
// Real false positive this guard was measured against: bullet markers/CJK text
// with no spaces produce a small token count where a repeated "-" bullet alone
// can look like 25%+ repetition.
$bulleted = "- Loại: tủ đông\n- Dung tích: 500L\n- Điện áp: 220V\n- Xuất xứ: Nhật Bản\n- Bảo hành: 12 tháng\n- Màu sắc: trắng bạc hiện đại sang trọng";
pi_check('bullet-point lists are not flagged as repetition', !$looks_degenerate($bulleted), $bulleted);

// ── Generator::strip_markdown ────────────────────────────────────────────────
pi_section('Generator::strip_markdown');

$strip = fn ($t) => pi_call_private('Pi_Chatbot_Generator', 'strip_markdown', [$t]);
pi_check('bold markers are stripped, text kept', $strip('**Hoshizaki**') === 'Hoshizaki');
pi_check('a leading heading marker is stripped', trim($strip("# Tiêu đề\nnội dung")) === "Tiêu đề\nnội dung");
pi_check('plain text passes through untouched', $strip('không có markdown') === 'không có markdown');

// The real case that slipped through during a live quality audit: asked to
// compare two product types, the model answered with a full Markdown table
// despite the system prompt forbidding it — the raw `|` characters would have
// rendered as garbage in the plain-text chat widget.
$table = "Trước bảng.\n| Loại | Đặc điểm |\n|---|---|\n| Đá viên | Bền |\n| Đá vảy | Nhanh |\nSau bảng.";
$stripped_table = $strip($table);
pi_check('a Markdown table is rewritten into plain dash-joined lines',
    strpos($stripped_table, '|') === false, $stripped_table);
pi_check('table rows keep their information (header + both data rows survive)',
    strpos($stripped_table, 'Loại - Đặc điểm') !== false
        && strpos($stripped_table, 'Đá viên - Bền') !== false
        && strpos($stripped_table, 'Đá vảy - Nhanh') !== false,
    $stripped_table);
pi_check('prose before and after the table is preserved untouched',
    strpos($stripped_table, 'Trước bảng.') !== false && strpos($stripped_table, 'Sau bảng.') !== false);

// A lone "|" in ordinary prose (e.g. "A hoặc B | tuỳ nhu cầu") is NOT a table —
// no separator row follows it — and must be left alone, not mangled.
$not_a_table = 'Sản phẩm A hoặc B | tuỳ nhu cầu của bạn.';
pi_check('a stray pipe with no separator row is left untouched (not a real table)',
    $strip($not_a_table) === $not_a_table, $strip($not_a_table));

// ── Generator::build_language_note ──────────────────────────────────────────
pi_section('Generator::build_language_note (script-range + word-count detection)');

$note = fn ($q) => pi_call_private('Pi_Chatbot_Generator', 'build_language_note', [$q]);
$lang = function ($q) use ($note) {
    $n = $note($q);
    if (mb_strpos($n, 'tiếng Việt') !== false) return 'VI';
    if (mb_strpos($n, 'English') !== false) return 'EN';
    if (mb_strpos($n, 'français') !== false) return 'FR';
    if (mb_strpos($n, 'Deutsch') !== false) return 'DE';
    if (mb_strpos($n, '日本語') !== false) return 'JA';
    if (mb_strpos($n, '한국어') !== false) return 'KO';
    if (mb_strpos($n, '中文') !== false) return 'ZH';
    if (mb_strpos($n, 'русском') !== false) return 'RU';
    if (mb_strpos($n, 'ไทย') !== false) return 'TH';
    return '?';
};
pi_check('accented Vietnamese -> VI', $lang('tủ đông 2 cánh') === 'VI');
pi_check('unaccented Vietnamese -> VI (function-word/vocab counting)', $lang('tu dong 2 canh') === 'VI');
pi_check('plain English -> EN', $lang('do you have ice machines') === 'EN');
// The exact regression this branch was measured against: "gas" is an everyday
// Vietnamese word AND English, and including it in the VI wordlist previously
// misrouted a genuine English question.
pi_check('an English question containing "gas" still resolves to EN',
    $lang('Fujimak gas stove FGTLS1260') === 'EN', $lang('Fujimak gas stove FGTLS1260'));
pi_check('French sentence -> FR', $lang('Je voudrais des informations sur les fours industriels.') === 'FR');
pi_check('German sentence -> DE', $lang('Ich möchte industrielle Öfen kaufen') === 'DE');
pi_check('Japanese script -> JA', $lang('業務用冷凍庫はありますか') === 'JA');
pi_check('Korean script -> KO', $lang('업소용 냉동고 있나요') === 'KO');
pi_check('Chinese script (no kana) -> ZH', $lang('你们有商用冰柜吗') === 'ZH');
pi_check('Russian script -> RU', $lang('Есть ли у вас промышленные морозильники') === 'RU');
pi_check('Thai script -> TH', $lang('คุณมีตู้แช่แข็งเชิงพาณิชย์ไหม') === 'TH');

// ── Reranker::build_messages ─────────────────────────────────────────────────
pi_section('Reranker::build_messages');

$pool = [pi_result(['post_id' => 1, 'title' => 'Sản phẩm A']), pi_result(['post_id' => 2, 'title' => 'Sản phẩm B'])];
$messages = pi_call_private('Pi_Chatbot_Reranker', 'build_messages', ['câu hỏi', $pool]);
pi_check('build_messages returns a system+user pair',
    count($messages) === 2 && $messages[0]['role'] === 'system' && $messages[1]['role'] === 'user');
pi_check('candidates are numbered starting from 1',
    strpos($messages[1]['content'], '1. Sản phẩm A') !== false
        && strpos($messages[1]['content'], '2. Sản phẩm B') !== false, $messages[1]['content']);
$long_excerpt_pool = [pi_result(['post_id' => 1, 'excerpt' => str_repeat('x', 500)])];
$msg2 = pi_call_private('Pi_Chatbot_Reranker', 'build_messages', ['q', $long_excerpt_pool]);
pi_check('candidate excerpts are truncated to SNIPPET_LENGTH',
    substr_count($msg2[1]['content'], 'x') <= Pi_Chatbot_Reranker::SNIPPET_LENGTH,
    substr_count($msg2[1]['content'], 'x') . ' x-chars in prompt');

// ── Entities::build_brand_map ────────────────────────────────────────────────
pi_section('Entities::build_brand_map (pure logic over an array)');

$brand_map = pi_call_private('Pi_Chatbot_Entities', 'build_brand_map', [['Hoshizaki', 'Fujimak-Japan', 'Berjaya-Malaysia']]);
pi_check('lowercased full names map to themselves',
    ($brand_map['hoshizaki'] ?? null) === 'Hoshizaki', json_encode($brand_map, JSON_UNESCAPED_UNICODE));
pi_check('an unambiguous pre-dash prefix becomes a shortcut',
    ($brand_map['fujimak'] ?? null) === 'Fujimak-Japan', json_encode($brand_map, JSON_UNESCAPED_UNICODE));

$ambiguous_map = pi_call_private('Pi_Chatbot_Entities', 'build_brand_map', [['Fujimak-Japan', 'Fujimak-Korea']]);
pi_check('an AMBIGUOUS pre-dash prefix is not turned into a shortcut (two brands share it)',
    !isset($ambiguous_map['fujimak']), json_encode($ambiguous_map, JSON_UNESCAPED_UNICODE));

// ── LLM_Client::extract_json / content ──────────────────────────────────────
pi_section('LLM_Client::extract_json / content');

pi_check('plain JSON', Pi_Chatbot_LLM_Client::extract_json('{"a":1}') === ['a' => 1]);
pi_check('fenced JSON', Pi_Chatbot_LLM_Client::extract_json("```json\n{\"a\":1}\n```") === ['a' => 1]);
pi_check('JSON with surrounding prose', Pi_Chatbot_LLM_Client::extract_json('Sure! {"a":1} hope that helps') === ['a' => 1]);
pi_check('nested braces stay balanced', Pi_Chatbot_LLM_Client::extract_json('{"a":{"b":2}}') === ['a' => ['b' => 2]]);
pi_check('unterminated JSON returns null', Pi_Chatbot_LLM_Client::extract_json('{"a":1') === null);
pi_check('empty input returns null', Pi_Chatbot_LLM_Client::extract_json('') === null);
pi_check('prose with no JSON returns null', Pi_Chatbot_LLM_Client::extract_json('I cannot help') === null);

pi_check('content() extracts the message text',
    Pi_Chatbot_LLM_Client::content(['choices' => [['message' => ['content' => '  hi  ']]]]) === 'hi');
pi_check('content() on a malformed response returns empty string, not a warning',
    Pi_Chatbot_LLM_Client::content([]) === '');

// ── Router::validate (pure aside from the brand branch, which needs no DB
//    when no brand claim is made) ────────────────────────────────────────────
pi_section('Router::validate — difficulty fails safe, keywords are bounded');

$base = ['intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => ['tủ đông'], 'brand' => null];
foreach ([
    'missing difficulty'    => [],
    'misspelled difficulty' => ['difficulty' => 'HARDD'],
    'non-string difficulty' => ['difficulty' => ['hard']],
    'numeric difficulty'    => ['difficulty' => 1],
] as $label => $extra) {
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
    is_array($out) && !in_array('', $out['query_variants'], true) && !in_array(str_repeat('x', 200), $out['query_variants'], true));
pi_check('duplicate variants are dropped',
    is_array($out) && count($out['query_variants']) === count(array_unique($out['query_variants'])));

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [array_merge($base, ['difficulty' => 'easy', 'query_variants' => ['a', 'b']])]);
pi_check('easy questions carry no variants', is_array($out) && $out['query_variants'] === []);

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [
    ['intent' => 'none', 'keywords_en' => [], 'keywords_vi' => [], 'brand' => null],
]);
pi_check("intent='none' is returned, not collapsed to null (small talk must stay a verdict, not a failure)",
    is_array($out) && $out['intent'] === 'none', is_array($out) ? $out['intent'] : 'null');
pi_check("'none' carries no keywords", is_array($out) && $out['keywords_en'] === [] && $out['keywords_vi'] === []);
pi_check("'none' is never routed to the expensive path", is_array($out) && $out['difficulty'] === 'easy');

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [['keywords_en' => [], 'keywords_vi' => []]]);
pi_check('an empty reply with no intent field resolves to none', is_array($out) && $out['intent'] === 'none');

$out = pi_call_private('Pi_Chatbot_Router', 'validate', [
    ['intent' => 'prodcut', 'keywords_en' => ['freezer'], 'keywords_vi' => []],
]);
pi_check('a misspelled intent WITH keywords still means product (extraction was still useful)',
    is_array($out) && $out['intent'] === 'product', is_array($out) ? $out['intent'] : 'null');

$many_keywords = array_fill(0, 20, 'x');
$out = pi_call_private('Pi_Chatbot_Router', 'validate', [array_merge($base, [
    'keywords_en' => $many_keywords, 'keywords_vi' => array_merge($many_keywords, [str_repeat('y', 60)]),
])]);
pi_check('keyword lists are capped at 8 items', is_array($out) && count($out['keywords_en']) <= 8);
pi_check('an over-long keyword (>40 chars) is dropped',
    is_array($out) && !in_array(str_repeat('y', 60), $out['keywords_vi'], true));

// ── Reranker::apply (pure — takes an already-parsed array) ──────────────────
pi_section('Reranker::apply — reorder only, never invent');

$rr_pool = [];
foreach ([101, 102, 103, 104] as $pid) $rr_pool[] = pi_result(['post_id' => $pid, 'title' => "Product {$pid}"]);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [3, 1], 'relevant' => [3]], $rr_pool]);
pi_check('valid ranking reorders the pool',
    is_array($out) && array_column($out['results'], 'post_id') === [103, 101]);
pi_check('confidence reflects the relevant subset', is_array($out) && abs($out['confidence'] - 0.5) < 1e-9);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [99, 42, 77, 1], 'relevant' => []], $rr_pool]);
pi_check('a mostly-hallucinated ranking is rejected outright (kept fusion order upstream)', $out === null);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [2, 99], 'relevant' => [2]], $rr_pool]);
pi_check('a single out-of-range index is dropped, the valid one kept',
    is_array($out) && array_column($out['results'], 'post_id') === [102]);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [2, 2, 3], 'relevant' => []], $rr_pool]);
pi_check('repeated indexes are de-duplicated',
    is_array($out) && array_column($out['results'], 'post_id') === [102, 103]);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [], 'relevant' => []], $rr_pool]);
pi_check('an explicitly empty ranking is a real verdict ("none of these answer it"), not a parse failure',
    is_array($out) && $out['results'] === [] && $out['confidence'] === 0.0);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['relevant' => [1]], $rr_pool]);
pi_check('a reply with no "ranking" key at all IS a failure', $out === null);

$out = pi_call_private('Pi_Chatbot_Reranker', 'apply', [['ranking' => [1, 2, 3], 'relevant' => [1, 2, 3, 4]], $rr_pool]);
pi_check('confidence never exceeds 1.0', is_array($out) && $out['confidence'] <= 1.0);

// pi_report() prints a summary from the SHARED pi_check() static state — when
// run standalone that's exactly this suite's own totals; when run-all.php
// requires this file alongside others, the same static keeps accumulating, so
// printing here would show a confusing cumulative count instead of this
// suite's own. run-all.php does its own before/after accounting per suite and
// prints one combined summary at the end, so this suite only prints (and
// exits on) its own report when running standalone.
if (!defined('PI_TEST_SUITE_MODE')) exit(pi_report());
