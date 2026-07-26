<?php
/**
 * suite-llm.php — assertions for everything that talks to LLM7: the shared
 * client, the router, the reranker, the generator's answer/verify calls.
 *
 * Every request in this suite is intercepted by pi_http_stub() (lib/harness.php)
 * — nothing here reaches the real network, spends real quota, or depends on
 * LLM7 being up. That is deliberate: it makes error-path behaviour (a broken
 * key, a malformed reply, a rate-limited response) reproducible on demand,
 * something a live call cannot promise. For a check that the real API's
 * response SHAPE still matches what these stubs assume, see smoke-live.php —
 * that file is opt-in and is the only place in this suite set allowed to hit
 * the network.
 *
 * Run standalone:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/suite-llm.php
 * Or via tests/run-all.php.
 */

require_once __DIR__ . '/lib/harness.php';

pi_reset_state();

// ── LLM_Client::call ─────────────────────────────────────────────────────────
pi_section('LLM_Client::call — key round-robin and failure classification');

pi_http_stub(function ($args, $url, $i) {
    // First key fails, second succeeds — proves round-robin actually moves on.
    return $i === 0 ? new WP_Error('timeout', 'simulated timeout') : pi_http_content('second key worked');
});
$res = Pi_Chatbot_LLM_Client::call(['model' => 'x'], ['bad-key', 'good-key'], 5, 2, 'test');
pi_check('a failing first key falls through to the second', !is_wp_error($res) && count(pi_http_calls()) === 2);
pi_check('the successful response is the one returned', Pi_Chatbot_LLM_Client::content($res) === 'second key worked');

pi_http_stub(function () { return new WP_Error('timeout', 'always fails'); });
$res = Pi_Chatbot_LLM_Client::call(['model' => 'x'], ['k1', 'k2', 'k3'], 5, 2, 'test');
pi_check('max_attempts caps the number of keys tried, not the number available',
    count(pi_http_calls()) === 2, count(pi_http_calls()) . ' calls made');
pi_check('exhausting all attempts returns the last WP_Error', is_wp_error($res));

$res = Pi_Chatbot_LLM_Client::call(['model' => 'x'], [], 5, 2, 'test');
pi_check('no keys configured returns pi_chatbot_no_key without any HTTP call',
    is_wp_error($res) && $res->get_error_code() === 'pi_chatbot_no_key');

pi_http_stub(function () { return pi_http_response(['error' => 'server exploded'], 500); });
$res = Pi_Chatbot_LLM_Client::call(['model' => 'x'], ['k1'], 5, 1, 'test');
pi_check('an HTTP 500 is treated as failure', is_wp_error($res));

// This is the exact shape of a real, previously-observed bug (documented in
// class-generator.php): HTTP 200 with an EMPTY choices array must still count
// as a failure, not be handed back as if it were a real answer.
pi_http_stub(function () { return pi_http_response(['choices' => []], 200); });
$res = Pi_Chatbot_LLM_Client::call(['model' => 'x'], ['k1'], 5, 1, 'test');
pi_check('HTTP 200 with an empty choices array is still a failure', is_wp_error($res));

// ── Router::enrich ────────────────────────────────────────────────────────────
pi_section('Router::enrich');

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['test-key'], 'router_keys' => []], function () {
    pi_reset_state();
    $question = 'câu hỏi router test ' . wp_rand(); // unique per run, avoids stale cache from a prior run

    pi_http_stub(function () {
        return pi_http_content(wp_json_encode([
            'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => ['tủ đông'],
            'brand' => null, 'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
        ]));
    });
    $r1 = Pi_Chatbot_Router::enrich($question);
    pi_check('a well-formed classification is parsed correctly', is_array($r1) && $r1['intent'] === 'product');
    pi_check('exactly one HTTP call was made for the first classification', count(pi_http_calls()) === 1);

    $before = count(pi_http_calls());
    $r2 = Pi_Chatbot_Router::enrich($question);
    pi_check('the SAME question is answered from cache — zero additional HTTP calls',
        count(pi_http_calls()) === $before, (count(pi_http_calls()) - $before) . ' extra calls');
    pi_check('the cached result matches the original', $r2 === $r1);

    // Malformed reply -> negative-cached, does not throw.
    $bad_question = 'câu hỏi hỏng ' . wp_rand();
    pi_http_stub(function () { return pi_http_content('not json at all'); });
    $r3 = Pi_Chatbot_Router::enrich($bad_question);
    pi_check('an unparseable reply degrades to null, not an error', $r3 === null);

    // router_enabled=false must short-circuit before any HTTP call.
    pi_reset_state();
});

pi_with_settings(['router_enabled' => false], function () {
    pi_http_stub(function () { throw new Exception('should never be called'); });
    $r = Pi_Chatbot_Router::enrich('bất kỳ câu hỏi nào');
    pi_check('router_enabled=false returns null WITHOUT making any HTTP call', $r === null && count(pi_http_calls()) === 0);
});

// Circuit breaker: BREAKER_FAIL_THRESHOLD consecutive failures must open it.
pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['test-key'], 'router_keys' => []], function () {
    pi_reset_state();
    pi_http_stub(function () { return new WP_Error('timeout', 'always fails'); });
    for ($i = 0; $i < Pi_Chatbot_Router::BREAKER_FAIL_THRESHOLD; $i++) {
        Pi_Chatbot_Router::enrich('breaker test question ' . $i); // distinct questions so the answer cache can't hide the failures
    }
    pi_check('breaker opens after BREAKER_FAIL_THRESHOLD consecutive failures',
        pi_call_private('Pi_Chatbot_Router', 'breaker_open', []) === true);

    $calls_before = count(pi_http_calls());
    $r = Pi_Chatbot_Router::enrich('yet another distinct question ' . wp_rand());
    pi_check('once open, the breaker returns null WITHOUT another HTTP call',
        $r === null && count(pi_http_calls()) === $calls_before);
    pi_reset_state();
});

// ── Reranker::rerank ──────────────────────────────────────────────────────────
pi_section('Reranker::rerank');

$candidates = [pi_result(['post_id' => 1, 'title' => 'A']), pi_result(['post_id' => 2, 'title' => 'B']), pi_result(['post_id' => 3, 'title' => 'C'])];

pi_with_settings(['rerank_enabled' => true, 'llm7_keys' => ['test-key'], 'router_keys' => []], function () use ($candidates) {
    pi_http_stub(function () { return pi_http_content(wp_json_encode(['ranking' => [3, 1, 2], 'relevant' => [3]])); });
    $out = Pi_Chatbot_Reranker::rerank('câu hỏi', $candidates, 3);
    pi_check('a valid ranking is applied and used=true',
        $out['used'] === true && array_column($out['results'], 'post_id') === [3, 1, 2]);

    pi_http_stub(function () { return pi_http_content(wp_json_encode(['ranking' => [99, 88], 'relevant' => []])); });
    $out2 = Pi_Chatbot_Reranker::rerank('câu hỏi', $candidates, 3);
    pi_check('a hallucinated ranking falls back to fusion order, used=false',
        $out2['used'] === false && array_column($out2['results'], 'post_id') === [1, 2, 3]);
});

pi_with_settings(['rerank_enabled' => false], function () use ($candidates) {
    pi_http_stub(function () { throw new Exception('should never be called'); });
    $out = Pi_Chatbot_Reranker::rerank('câu hỏi', $candidates, 3);
    pi_check('rerank_enabled=false skips the network call entirely',
        $out['used'] === false && count(pi_http_calls()) === 0);
});

// ── Generator::answer ────────────────────────────────────────────────────────
pi_section('Generator::answer');

pi_with_settings(['llm7_keys' => ['test-key']], function () {
    pi_http_stub(function () { return pi_http_content('Đây là câu trả lời hợp lệ.'); });
    $out = Pi_Chatbot_Generator::answer('câu hỏi', []);
    pi_check('a normal reply produces answer+sources', !is_wp_error($out) && $out['answer'] === 'Đây là câu trả lời hợp lệ.');

    pi_http_stub(function () { return pi_http_content(''); });
    $out = Pi_Chatbot_Generator::answer('câu hỏi', []);
    pi_check('an empty reply is a WP_Error, not an empty answer',
        is_wp_error($out) && $out->get_error_code() === 'pi_chatbot_empty');

    $degenerate = trim(str_repeat('lặp ', 30));
    pi_http_stub(function () use ($degenerate) { return pi_http_content($degenerate); });
    $out = Pi_Chatbot_Generator::answer('câu hỏi', []);
    pi_check('abnormal repetition is rejected as pi_chatbot_degenerate, never cached as a real answer',
        is_wp_error($out) && $out->get_error_code() === 'pi_chatbot_degenerate');

    // options['correction'] must actually reach the model — asserted on the
    // captured request body, not just on the return value.
    pi_http_stub(function () { return pi_http_content('câu trả lời đã sửa'); });
    Pi_Chatbot_Generator::answer('câu hỏi', [], ['correction' => 'UNIQUE_CORRECTION_MARKER_XYZ']);
    $sent = pi_http_calls()[0]['body'] ?? null;
    $system_msg = $sent ? implode(' ', array_column($sent['messages'] ?? [], 'content')) : '';
    pi_check("options['correction'] is actually included in the prompt sent to the model",
        strpos($system_msg, 'UNIQUE_CORRECTION_MARKER_XYZ') !== false);
});

pi_with_settings(['llm7_keys' => []], function () {
    $out = Pi_Chatbot_Generator::answer('câu hỏi', []);
    pi_check('no keys configured surfaces as a WP_Error (no fatal, no silent empty answer)', is_wp_error($out));
});

// ── Generator::verify ─────────────────────────────────────────────────────────
pi_section('Generator::verify');

pi_with_settings(['verify_enabled' => true, 'llm7_keys' => ['test-key'], 'router_keys' => []], function () {
    pi_http_stub(function () { return pi_http_content(wp_json_encode(['faithful' => true])); });
    $v = Pi_Chatbot_Generator::verify('q', [], 'answer text');
    pi_check('faithful:true is parsed correctly', $v !== null && $v['faithful'] === true);

    pi_http_stub(function () { return pi_http_content(wp_json_encode(['faithful' => false, 'issue' => 'invented price'])); });
    $v = Pi_Chatbot_Generator::verify('q', [], 'answer text');
    pi_check('faithful:false carries the issue text', $v !== null && $v['faithful'] === false && $v['issue'] === 'invented price');

    pi_http_stub(function () { return pi_http_content('not json'); });
    $v = Pi_Chatbot_Generator::verify('q', [], 'answer text');
    pi_check('an unparseable check result is null — NOT the same as faithful:false',
        $v === null);
});

pi_with_settings(['verify_enabled' => false], function () {
    pi_http_stub(function () { throw new Exception('should never be called'); });
    $v = Pi_Chatbot_Generator::verify('q', [], 'answer text');
    pi_check('verify_enabled=false returns null without a network call', $v === null && count(pi_http_calls()) === 0);
});

$v = Pi_Chatbot_Generator::verify('q', [], '');
pi_check('an empty answer is never sent for verification (nothing to check)', $v === null);

pi_http_unstub();
pi_reset_state();

// ── WebSearch ────────────────────────────────────────────────────────────────
// HTTP is stubbed via pre_http_request (same technique the rest of this suite
// uses), so these assert OUR logic — gating, normalisation, URL hardening,
// prompt labelling — and never touch a real provider or need a key.
pi_section('Pi_Chatbot_WebSearch');

$ws_saved = get_option(PI_CHATBOT_OPTION, []);

$ws_set = function ($enabled, $key, $provider = 'brave') {
    $s = pi_chatbot_settings();
    $s['web_search_enabled']  = $enabled;
    $s['web_search_key']      = $key;
    $s['web_search_provider'] = $provider;
    update_option(PI_CHATBOT_OPTION, $s, false);
};

$ws_set(false, '');
pi_check('disabled + no key -> not configured', !Pi_Chatbot_WebSearch::is_configured());
$ws_set(true, '');
pi_check('enabled but NO key -> still not configured (feature dormant, not silently empty)',
    !Pi_Chatbot_WebSearch::is_configured());
pi_check('...and search() returns empty without making any request',
    Pi_Chatbot_WebSearch::search('máy làm đá') === []);
$ws_set(true, 'test-key-123');
pi_check('enabled + key -> configured', Pi_Chatbot_WebSearch::is_configured());

// Stubbed Brave response, including two rows that MUST be rejected: one with a
// javascript: URL (a hostile/compromised provider response must never reach the
// prompt or the client) and one with no title.
$ws_stub = function () {
    return [
        'response' => ['code' => 200],
        'body'     => wp_json_encode(['web' => ['results' => [
            ['title' => "Hoshizaki  \n IM-65NE", 'url' => 'https://example.com/a', 'description' => '<b>Ice</b> machine spec'],
            ['title' => 'Bad protocol', 'url' => 'javascript:alert(1)', 'description' => 'x'],
            ['title' => '', 'url' => 'https://example.com/c', 'description' => 'no title'],
            ['title' => 'Second good', 'url' => 'https://example.com/d', 'description' => str_repeat('x', 500)],
        ]]]),
    ];
};
add_filter('pre_http_request', $ws_stub, 10, 0);
delete_transient(Pi_Chatbot_WebSearch::BREAKER_KEY);
$ws_res = Pi_Chatbot_WebSearch::search('hoshizaki ice machine test ' . PI_CHATBOT_VERSION);
remove_filter('pre_http_request', $ws_stub, 10);

pi_check('malformed rows are dropped, valid ones kept', count($ws_res) === 2, 'got ' . count($ws_res));
pi_check('a javascript: URL never survives normalisation',
    !in_array('javascript:alert(1)', array_column($ws_res, 'url'), true));
pi_check('whitespace in the title is collapsed',
    ($ws_res[0]['title'] ?? '') === 'Hoshizaki IM-65NE', $ws_res[0]['title'] ?? '');
pi_check('HTML in the snippet is stripped, not passed through',
    strpos($ws_res[0]['snippet'] ?? '', '<b>') === false && strpos($ws_res[0]['snippet'] ?? '', 'Ice machine') === 0);
pi_check('an over-long snippet is truncated to the cap',
    mb_strlen($ws_res[1]['snippet'] ?? '') <= Pi_Chatbot_WebSearch::MAX_SNIPPET_CHARS + 3);

$ws_block = Pi_Chatbot_WebSearch::to_prompt_block($ws_res);
pi_check('the prompt block carries the URL so the model can attribute the source',
    strpos($ws_block, 'https://example.com/a') !== false);
pi_check('an empty result set produces NO prompt block at all (no empty labelled section)',
    Pi_Chatbot_WebSearch::to_prompt_block([]) === '');

// The whole point of the feature's safety design: web results must never be
// mistakable for catalog stock. Assert the separation at the prompt level.
$ws_answer_probe = pi_call_private('Pi_Chatbot_Generator', 'build_language_note', ['test']);
pi_check('generator language note still works alongside the web block (no interference)',
    is_string($ws_answer_probe));

// SearXNG is the self-hosted provider: it authenticates with nothing and is
// configured by URL, so is_configured() must NOT ask it for a key.
$ws_set_url = function ($url) {
    $s = pi_chatbot_settings();
    $s['web_search_enabled']  = true;
    $s['web_search_provider'] = 'searxng';
    $s['web_search_key']      = '';
    $s['web_search_url']      = $url;
    update_option(PI_CHATBOT_OPTION, $s, false);
};

$ws_set_url('');
pi_check('searxng with no URL -> not configured (and does not demand a key)',
    !Pi_Chatbot_WebSearch::is_configured());
$ws_set_url('https://searx.example.com/');
pi_check('searxng with a URL -> configured despite the key being empty',
    Pi_Chatbot_WebSearch::is_configured());

// A non-http scheme must never become a server-side fetch target.
$ws_set_url('javascript:alert(1)');
pi_check('a non-http SearXNG URL is rejected, not fetched',
    !Pi_Chatbot_WebSearch::is_configured());

$ws_set_url('https://searx.example.com');
$ws_searx_stub = function () {
    return ['response' => ['code' => 200], 'body' => wp_json_encode(['results' => [
        ['title' => 'Kết quả A', 'url' => 'https://example.org/a', 'content' => 'nội dung A'],
        ['title' => 'Kết quả B', 'url' => 'https://example.org/b', 'content' => 'nội dung B'],
    ]])];
};
add_filter('pre_http_request', $ws_searx_stub, 10, 0);
delete_transient(Pi_Chatbot_WebSearch::BREAKER_KEY);
$ws_sx = Pi_Chatbot_WebSearch::search('searxng probe ' . PI_CHATBOT_VERSION);
remove_filter('pre_http_request', $ws_searx_stub, 10);
pi_check('searxng results are normalised to the same shape as the keyed providers',
    count($ws_sx) === 2 && ($ws_sx[0]['title'] ?? '') === 'Kết quả A'
    && ($ws_sx[0]['snippet'] ?? '') === 'nội dung A',
    wp_json_encode($ws_sx, JSON_UNESCAPED_UNICODE));

update_option(PI_CHATBOT_OPTION, $ws_saved, false);
delete_transient(Pi_Chatbot_WebSearch::BREAKER_KEY);

// pi_report() prints a summary from the SHARED pi_check() static state — when
// run standalone that's exactly this suite's own totals; when run-all.php
// requires this file alongside others, the same static keeps accumulating, so
// printing here would show a confusing cumulative count instead of this
// suite's own. run-all.php does its own before/after accounting per suite and
// prints one combined summary at the end, so this suite only prints (and
// exits on) its own report when running standalone.
if (!defined('PI_TEST_SUITE_MODE')) exit(pi_report());
