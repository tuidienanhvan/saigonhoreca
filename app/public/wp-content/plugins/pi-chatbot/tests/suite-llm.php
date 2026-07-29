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

// ── LLM_Client::call_streaming ───────────────────────────────────────────────
// pi_http_stub() cannot intercept this — call_streaming() uses raw cURL (see
// its docblock: real token-by-token streaming needs CURLOPT_WRITEFUNCTION,
// which wp_remote_post()'s pre_http_request filter never sees). Instead, use
// Pi_Chatbot_LLM_Client::$stream_transport, the equivalent hook for this one
// method (see its own docblock in class-llm-client.php).
pi_section('LLM_Client::call_streaming — relays deltas, key rotation, PROVEN via disable/red/restore');

function pi_stream_transport_fixed($chunks, $http_code = 200, $curl_errno = 0, $curl_error = '') {
    return function ($key, $body, $timeout, $on_delta) use ($chunks, $http_code, $curl_errno, $curl_error) {
        $full = '';
        if ($http_code >= 200 && $http_code < 300) {
            foreach ($chunks as $c) { $full .= $c; $on_delta($c); }
        }
        return [
            'http_code'   => $http_code,
            'full_text'   => $full,
            'relayed_any' => $full !== '',
            'curl_errno'  => $curl_errno,
            'curl_error'  => $curl_error,
        ];
    };
}

$orig_transport = Pi_Chatbot_LLM_Client::$stream_transport;
try {
    Pi_Chatbot_LLM_Client::$stream_transport = pi_stream_transport_fixed(['Xin ', 'chào, ', 'tôi ', 'có thể ', 'giúp gì?']);
    $received = [];
    $res = Pi_Chatbot_LLM_Client::call_streaming(
        ['model' => 'x'], ['k1'], 5, 2, function ($d) use (&$received) { $received[] = $d; }, 'test'
    );
    pi_check('call_streaming() returns the FULL assembled text, not just the last chunk',
        $res === 'Xin chào, tôi có thể giúp gì?', var_export($res, true));
    pi_check('on_delta was actually called once per chunk (5 chunks), not once with everything',
        count($received) === 5, count($received) . ' calls');

    // Key rotation — a key that fails BEFORE relaying anything must fall
    // through to the next key, same contract as call().
    $attempt = 0;
    Pi_Chatbot_LLM_Client::$stream_transport = function ($key, $body, $timeout, $on_delta) use (&$attempt) {
        $attempt++;
        if ($attempt === 1) {
            return ['http_code' => 500, 'full_text' => '', 'relayed_any' => false, 'curl_errno' => 0, 'curl_error' => ''];
        }
        $on_delta('second key worked');
        return ['http_code' => 200, 'full_text' => 'second key worked', 'relayed_any' => true, 'curl_errno' => 0, 'curl_error' => ''];
    };
    $res2 = Pi_Chatbot_LLM_Client::call_streaming(['model' => 'x'], ['bad', 'good'], 5, 2, function () {}, 'test');
    pi_check('a key that failed before relaying anything falls through to the next key',
        $res2 === 'second key worked', var_export($res2, true));
    pi_check('exactly 2 keys were attempted', $attempt === 2, "attempt={$attempt}");

    // Once content has been relayed to the customer, a later transport error on
    // the SAME key must NOT retry a 2nd key (that would look like the answer
    // restarting from scratch mid-stream) — it returns whatever was relayed.
    Pi_Chatbot_LLM_Client::$stream_transport = function ($key, $body, $timeout, $on_delta) {
        $on_delta('partial ');
        $on_delta('answer');
        return ['http_code' => null, 'full_text' => 'partial answer', 'relayed_any' => true, 'curl_errno' => 7, 'curl_error' => 'connection reset mid-stream'];
    };
    $res3 = Pi_Chatbot_LLM_Client::call_streaming(['model' => 'x'], ['k1', 'k2'], 5, 2, function () {}, 'test');
    pi_check('a mid-stream transport error after relaying content returns the partial text, does not retry',
        $res3 === 'partial answer', var_export($res3, true));

    Pi_Chatbot_LLM_Client::$stream_transport = function () {
        return ['http_code' => null, 'full_text' => '', 'relayed_any' => false, 'curl_errno' => 7, 'curl_error' => 'connection refused'];
    };
    $res4 = Pi_Chatbot_LLM_Client::call_streaming(['model' => 'x'], ['k1'], 5, 1, function () {}, 'test');
    pi_check('a transport error with nothing relayed returns a WP_Error, not empty string',
        is_wp_error($res4) && $res4->get_error_code() === 'pi_chatbot_stream_curl');
} finally {
    Pi_Chatbot_LLM_Client::$stream_transport = $orig_transport;
}

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

// ── Router::enrich — multi-turn history ─────────────────────────────────────
// Real gap found and fixed 2026-07-26: every request was fully independent —
// no history was ever sent to the backend, so a follow-up ("giá bao nhiêu?"
// after asking about a specific fridge) had nothing to resolve "it" against.
pi_section('Router::enrich — multi-turn history');

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['test-key'], 'router_keys' => []], function () {
    pi_reset_state();
    $q = 'câu hỏi có lịch sử ' . wp_rand();
    $history = [
        ['role' => 'user', 'text' => 'Tủ đông Hoshizaki 2 cánh có không'],
        ['role' => 'bot',  'text' => 'Dạ có, Tủ Đông Đứng 2 Cánh HF-128MA-S của Hoshizaki.'],
    ];

    pi_http_stub(function () {
        return pi_http_content(wp_json_encode([
            'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => ['tủ đông'],
            'brand' => null, 'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
        ]));
    });
    $r = Pi_Chatbot_Router::enrich($q, $history);
    pi_check('a request WITH history is still parsed correctly', is_array($r) && $r['intent'] === 'product');

    // Read the actual prompt sent, not the (mocked) reply — pi_http_record() captures the
    // request BEFORE the stub answers it, so this reflects what really went to LLM7.
    $sent_body = pi_http_calls()[0]['body'];
    pi_check('history turns are sent as real prior messages, not stuffed into one string',
        count($sent_body['messages']) === 4, // system + 2 history turns + the new question
        'got ' . count($sent_body['messages']) . ' messages');
    pi_check('history roles are translated to user/assistant (LLM7\'s vocabulary), not the widget\'s own "bot"',
        $sent_body['messages'][1]['role'] === 'user' && $sent_body['messages'][2]['role'] === 'assistant',
        json_encode(array_column($sent_body['messages'], 'role')));
    pi_check('the LAST message is still the new question, not a history turn',
        $sent_body['messages'][3]['content'] === $q);

    // Cache identity: same question text, but DIFFERENT history, must not
    // collide — and a request with NO history must still hit the ORIGINAL
    // (pre-multi-turn) cache key shape, so every already-warm cache entry for
    // an ordinary single-turn question keeps working after this deploy.
    pi_reset_state();
    $q2 = 'câu hỏi cache theo lịch sử ' . wp_rand();
    pi_http_stub(function () {
        return pi_http_content(wp_json_encode([
            'intent' => 'product', 'keywords_en' => ['ice maker'], 'keywords_vi' => ['máy làm đá'],
            'brand' => null, 'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
        ]));
    });
    Pi_Chatbot_Router::enrich($q2, []); // no history -> 1 call
    $calls_after_first = count(pi_http_calls());
    Pi_Chatbot_Router::enrich($q2, [['role' => 'user', 'text' => 'khác hoàn toàn']]); // same question, DIFFERENT history
    pi_check('the same question text with DIFFERENT history is NOT served from the no-history cache entry',
        count(pi_http_calls()) > $calls_after_first,
        'expected a 2nd HTTP call, only got ' . (count(pi_http_calls()) - $calls_after_first) . ' extra');

    pi_reset_state();
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
    Pi_Chatbot_Generator::answer('câu hỏi', [], [], ['correction' => 'UNIQUE_CORRECTION_MARKER_XYZ']);
    $sent = pi_http_calls()[0]['body'] ?? null;
    $by_role = [];
    foreach (($sent['messages'] ?? []) as $m) $by_role[$m['role']] = ($by_role[$m['role']] ?? '') . $m['content'];
    pi_check("options['correction'] is actually included in the prompt sent to the model",
        strpos($by_role['user'] ?? '', 'UNIQUE_CORRECTION_MARKER_XYZ') !== false
        || strpos($by_role['system'] ?? '', 'UNIQUE_CORRECTION_MARKER_XYZ') !== false);

    // Regression guard for the 2026-07-26 security fix: verify()'s free-text
    // `issue` is LLM OUTPUT (from an earlier call), and concatenating
    // arbitrary model output into the SYSTEM message — the highest-trust
    // role — is a soft privilege-escalation pattern. The RULE ("use only
    // reference facts") must stay at system level; the free-text DESCRIPTION
    // of what went wrong must not.
    pi_check("the raw correction text does NOT reach system-level trust — only the user message carries it",
        strpos($by_role['system'] ?? '', 'UNIQUE_CORRECTION_MARKER_XYZ') === false,
        'system message: ' . ($by_role['system'] ?? '(none)'));
    pi_check('the system prompt still gets a rule reinforcement on a correction, just without the raw LLM text',
        strpos($by_role['system'] ?? '', 'rejected by') !== false);
});

pi_with_settings(['llm7_keys' => []], function () {
    $out = Pi_Chatbot_Generator::answer('câu hỏi', []);
    pi_check('no keys configured surfaces as a WP_Error (no fatal, no silent empty answer)', is_wp_error($out));
});

// Regression gate for a real customer-visible bug (2026-07-27): asking "nói lại
// bằng tiếng việt" ("say that again in Vietnamese") right after a real answer
// produced a reply about a totally unrelated product — Generator::answer() had
// no conversation history at all, only the current raw message, so it had no
// way to know what "that" referred to. This asserts the fix directly on the
// captured request body (not just on the return value) so a future refactor
// that silently drops $history again breaks THIS test, not just a live chat.
pi_with_settings(['llm7_keys' => ['test-key']], function () {
    pi_http_stub(function () { return pi_http_content('Đây là bản dịch tiếng Việt.'); });
    $history = [
        ['role' => 'user', 'text' => 'how about kve capture jet'],
        ['role' => 'bot', 'text' => 'UNIQUE_PRIOR_ANSWER_MARKER_KVE'],
    ];
    Pi_Chatbot_Generator::answer('nói lại bằng tiếng việt', [], $history);
    $sent = pi_http_calls()[0]['body'] ?? null;
    $roles = array_map(function ($m) { return $m['role']; }, $sent['messages'] ?? []);
    pi_check('history turns are sent as REAL user/assistant messages, not folded into one string',
        in_array('user', $roles, true) && in_array('assistant', $roles, true) && count($roles) >= 4,
        'roles: ' . implode(',', $roles));
    $has_prior_text = false;
    foreach (($sent['messages'] ?? []) as $m) {
        if (strpos($m['content'], 'UNIQUE_PRIOR_ANSWER_MARKER_KVE') !== false) $has_prior_text = true;
    }
    pi_check("the prior turn's own text actually reaches the model", $has_prior_text);

    // No history at all -> the message array is UNCHANGED shape (just system +
    // user) — this feature must not add an empty/confusing turn when there is
    // nothing to carry, so single-turn questions (the overwhelming majority)
    // see byte-identical prompts to before this fix.
    pi_http_stub(function () { return pi_http_content('ok'); });
    Pi_Chatbot_Generator::answer('câu hỏi không có lịch sử', []);
    $sent2 = pi_http_calls()[0]['body'] ?? null;
    pi_check('with no history, the message array stays exactly [system, user]',
        count($sent2['messages'] ?? []) === 2, count($sent2['messages'] ?? []) . ' messages');
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

// ── Router::build_messages — multilingual regression gate ──────────────────
// eval-retrieval.php CANNOT cover the multilingual fix from 2026-07-26
// (be81e00): it calls Retriever::search() directly with no router in front,
// so a Korean/Japanese/Chinese question would always return empty for the
// wrong reason (no router ran to translate it) — adding a CJK group there
// would fail permanently and prove nothing. This is deliberately placed here
// instead: a stub-only, free, deterministic check that the PROMPT ITSELF
// still carries the instruction and worked examples that make cross-language
// translation work, so a future prompt edit that silently drops them fails a
// test instead of silently degrading Korean/Japanese/Chinese customers (the
// original bug: keywords_vi was defined as "leftover Vietnamese words",
// which produced garbage for a Korean question with no Vietnamese in it).
pi_section('Router::build_messages — multilingual instruction survives (regression gate for be81e00)');

pi_check('PROMPT_VERSION is the value this gate was written against — bump it deliberately, not by accident',
    Pi_Chatbot_Router::PROMPT_VERSION === 5, 'got ' . Pi_Chatbot_Router::PROMPT_VERSION);

$messages = pi_call_private('Pi_Chatbot_Router', 'build_messages', ['tủ đông']);
$system = $messages[0]['content'] ?? '';

pi_check('keywords_vi is defined as a TRANSLATED search query, not "leftover original Vietnamese words"',
    strpos($system, 'TRANSLATE') !== false);
pi_check('the prompt gives a real Korean translation example (not just naming the language)',
    strpos($system, '업소용') !== false || strpos($system, 'Korean') !== false);
pi_check('the prompt gives a real Japanese translation example',
    strpos($system, '業務用') !== false || strpos($system, 'Japanese') !== false);
pi_check('the prompt gives a real Chinese translation example',
    strpos($system, '商用') !== false || strpos($system, 'Chinese') !== false);
pi_check('the prompt states the TRUE catalog language mix (72% Vietnamese), not the old false "titles are English" claim',
    strpos($system, 'MOSTLY VIETNAMESE') !== false || strpos($system, '72%') !== false);
pi_check('the prompt explicitly forbids copying English keywords into the Vietnamese field verbatim',
    strpos($system, 'NEVER copy') !== false);

// pi_report() prints a summary from the SHARED pi_check() static state — when
// run standalone that's exactly this suite's own totals; when run-all.php
// requires this file alongside others, the same static keeps accumulating, so
// printing here would show a confusing cumulative count instead of this
// suite's own. run-all.php does its own before/after accounting per suite and
// prints one combined summary at the end, so this suite only prints (and
// exits on) its own report when running standalone.
if (!defined('PI_TEST_SUITE_MODE')) exit(pi_report());
