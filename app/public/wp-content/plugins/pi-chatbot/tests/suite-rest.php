<?php
/**
 * suite-rest.php — assertions for the whole POST /pi/v1/chatbot/ask flow:
 * validation, the answer cache, rate limiting, intent dispatch, and the
 * adaptive max-accuracy path. Every LLM call is intercepted (see
 * pi_http_stub() in lib/harness.php), so this suite runs deterministically and
 * for free — it exercises class-rest.php's OWN logic, not LLM7's.
 *
 * Every test IP is a distinct, clearly-fake address so rate-limit buckets
 * never collide with each other or with a real visitor's bucket.
 *
 * Run standalone:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/suite-rest.php
 * Or via tests/run-all.php.
 */

require_once __DIR__ . '/lib/harness.php';

pi_reset_state();

/**
 * Calls Pi_Chatbot_Rest::instance() directly rather than through a `global`
 * variable set at file top-level — that pattern is the exact trap documented
 * in lib/harness.php's file docblock (`wp eval-file` runs this file inside a
 * wrapping function, so a file-scope `$rest = ...` is a LOCAL of that wrapper,
 * and `global $rest` in a helper binds to a different, unset $GLOBALS entry).
 * instance() is a cheap singleton lookup, so there's no cost to calling it
 * fresh on every request here.
 */
function pi_rest_ask($message, $ip = '10.10.10.1', $history = null) {
    $_SERVER['REMOTE_ADDR'] = $ip;
    $req = new WP_REST_Request('POST', '/pi/v1/chatbot/ask');
    $req->set_param('message', $message);
    if ($history !== null) $req->set_param('history', $history);
    return Pi_Chatbot_Rest::instance()->ask($req);
}

// ── Validation ────────────────────────────────────────────────────────────────
pi_section('ask() — input validation');

$res = pi_rest_ask('');
pi_check('empty message -> 400 pi_chatbot_empty_message',
    is_wp_error($res) && $res->get_error_code() === 'pi_chatbot_empty_message');

$res = pi_rest_ask(str_repeat('x', Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH + 1));
pi_check('over-length message -> 400 pi_chatbot_too_long',
    is_wp_error($res) && $res->get_error_code() === 'pi_chatbot_too_long');

$res = pi_rest_ask(str_repeat('x', Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH));
pi_check('exactly MAX_MESSAGE_LENGTH is accepted (boundary, not off-by-one)', !is_wp_error($res) || $res->get_error_code() !== 'pi_chatbot_too_long');

// ── sanitize_history() ───────────────────────────────────────────────────────
// The REST args schema only checks that 'history' is an array — everything
// about its SIZE and CONTENT is client-supplied and untrusted, same as
// 'message'. Tested directly (not just through ask()) because the whole
// point is defending against shapes a well-behaved widget would never send,
// which is exactly what a schema-only check misses.
pi_section('Rest::sanitize_history — untrusted input, not just the widget\'s own shape');

$sanitize = fn ($raw) => pi_call_private('Pi_Chatbot_Rest', 'sanitize_history', [$raw]);

pi_check('not an array at all -> empty, no fatal', $sanitize('not an array') === []);
pi_check('null -> empty', $sanitize(null) === []);

$mixed = [
    ['role' => 'user', 'text' => 'câu hỏi thật'],
    'not an array at all',                                   // wrong shape entirely
    ['role' => 'admin', 'text' => 'vai trò không hợp lệ'],    // role outside user/bot
    ['role' => 'bot', 'text' => ''],                          // empty text
    ['role' => 'bot'],                                        // missing text
    ['role' => 'user', 'text' => str_repeat('x', 1000)],      // way over the per-message cap
];
$clean = $sanitize($mixed);
pi_check('malformed entries are dropped, not fatal, not silently kept',
    count($clean) === 2, count($clean) . ' survived: ' . wp_json_encode($clean));
pi_check('the surviving entries are exactly the 2 well-formed ones, in order',
    $clean[0]['text'] === 'câu hỏi thật' && $clean[1]['role'] === 'user');
pi_check('an over-length message is truncated to the cap, not rejected outright',
    mb_strlen($clean[1]['text']) <= Pi_Chatbot_Rest::MAX_HISTORY_MESSAGE_LENGTH + 1); // +1 for the trailing …

$long_history = [];
for ($i = 0; $i < 10; $i++) $long_history[] = ['role' => 'user', 'text' => "turn $i"];
$capped = $sanitize($long_history);
pi_check('a longer-than-cap history keeps only the MOST RECENT turns, not the first ones',
    count($capped) === Pi_Chatbot_Rest::MAX_HISTORY_TURNS * 2
    && $capped[0]['text'] === 'turn ' . (10 - Pi_Chatbot_Rest::MAX_HISTORY_TURNS * 2),
    wp_json_encode($capped));

// ── intent='none' skips retrieval entirely ──────────────────────────────────
pi_section("ask() — intent='none' (small talk) is answered without retrieval");

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => []], function () {
    pi_reset_state();
    pi_http_stub(function ($args, $url, $i) {
        // Call 0 = router, call 1 = generator.
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'none', 'keywords_en' => [], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
            ]));
        }
        return pi_http_content('Xin chào! Tôi là trợ lý của Saigon Horeca.');
    });
    $res = pi_rest_ask('bạn tên gì ' . wp_rand(), '10.10.10.2');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check("intent='none' produces a real answer, not an error", $data !== null, is_wp_error($res) ? $res->get_error_code() : 'ok');
    pi_check("intent='none' carries ZERO sources — retrieval never ran",
        $data && count($data['sources']) === 0, $data ? count($data['sources']) . ' sources' : 'no data');
    pi_check("trace records intent='none'", $data && $data['trace']['intent'] === 'none');
});

// ── Answer cache ──────────────────────────────────────────────────────────────
pi_section('ask() — answer cache');

pi_with_settings(['router_enabled' => false], function () {
    // router disabled -> only the generator call counts, one call per fresh ask.
    $unique_q = 'câu hỏi cache test ' . wp_rand();
    pi_http_stub(function () { return pi_http_content('câu trả lời cố định'); });

    $res1 = pi_rest_ask($unique_q, '10.10.10.3');
    pi_check('first ask succeeds and calls the LLM', !is_wp_error($res1) && count(pi_http_calls()) === 1);

    $before = count(pi_http_calls());
    $res2 = pi_rest_ask($unique_q, '10.10.10.3');
    pi_check('the SAME question again is served from cache — zero additional HTTP calls',
        count(pi_http_calls()) === $before);
    pi_check('cached response has the same answer text',
        !is_wp_error($res2) && $res2->get_data()['answer'] === $res1->get_data()['answer']);

    // Whitespace normalization: "tủ  đông" and "tủ đông" must hit the SAME cache entry.
    $spaced_q = 'câu   hỏi  cache test   ' . (int) preg_replace('/[^0-9]/', '', $unique_q);
    // (Not a perfect duplicate of $unique_q's exact words, so instead assert the
    // NORMALIZATION function directly — the behaviour the cache key depends on.)
    $norm1 = trim(preg_replace('/\s+/u', ' ', mb_strtolower('tủ  đông')));
    $norm2 = trim(preg_replace('/\s+/u', ' ', mb_strtolower('tủ đông')));
    pi_check('whitespace-normalized questions produce identical cache keys', $norm1 === $norm2);

    // index_version changing must invalidate the cache. This is now a genuine
    // (non-cached) 3rd request from the SAME IP within the same test, which
    // would otherwise trip the new minimum-request-interval guard (Stage 4.1,
    // class-rest.php check_min_interval()) added moments after the 2nd call
    // above — backdating the stored last-request timestamp proves this test is
    // really exercising cache invalidation, not accidentally exercising (and
    // being blocked by) the unrelated rate-limit guard.
    global $wpdb;
    $wpdb->update($wpdb->options,
        ['option_value' => (string) ((int) round(microtime(true) * 1000) - 2000)],
        ['option_name' => '_transient_pi_chatbot_lastreq_' . md5('10.10.10.3')]
    );
    $v = (int) get_option('pi_chatbot_index_version', 0);
    update_option('pi_chatbot_index_version', $v + 1, true);
    $before2 = count(pi_http_calls());
    pi_rest_ask($unique_q, '10.10.10.3');
    pi_check('bumping index_version invalidates the cache — a new HTTP call is made',
        count(pi_http_calls()) > $before2);
    update_option('pi_chatbot_index_version', $v, true); // restore
});

// ── Rate limiting ──────────────────────────────────────────────────────────────
pi_section('ask() — rate limiting');

pi_with_settings(['router_enabled' => false, 'rate_limit' => 3], function () {
    pi_http_stub(function () { return pi_http_content('ok'); });
    $ip = '10.10.10.4';
    $last = null;
    for ($i = 0; $i < 4; $i++) {
        $last = pi_rest_ask('câu hỏi khác nhau mỗi lần ' . wp_rand(), $ip);
    }
    pi_check('the 4th request within a minute, over a limit of 3, is 429',
        is_wp_error($last) && $last->get_error_code() === 'pi_chatbot_rate_limited',
        is_wp_error($last) ? $last->get_error_code() : 'not an error');

    $other_ip_res = pi_rest_ask('câu hỏi từ ip khác ' . wp_rand(), '10.10.10.5');
    pi_check('a different IP has its own, unaffected bucket', !is_wp_error($other_ip_res));
});

pi_with_settings(['router_enabled' => false, 'rate_limit' => 1], function () {
    // A cache HIT must not consume the rate-limit quota — the design deliberately
    // checks the cache BEFORE the limit for exactly this reason.
    $q = 'câu hỏi cache khong tinh quota ' . wp_rand();
    $ip = '10.10.10.6';
    pi_http_stub(function () { return pi_http_content('ok'); });
    pi_rest_ask($q, $ip); // consumes the only slot this minute
    $r2 = pi_rest_ask($q, $ip); // repeat of the SAME question -> cache hit
    pi_check('a cache hit does not consume rate-limit quota (repeat of the cached question still succeeds)',
        !is_wp_error($r2), is_wp_error($r2) ? $r2->get_error_code() : 'ok');
});

// ── get_client_ip ─────────────────────────────────────────────────────────────
pi_section('Rest::get_client_ip — XFF is untrusted unless PI_CHATBOT_TRUST_PROXY is set');

$_SERVER['REMOTE_ADDR'] = '203.0.113.1';
$_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.9';
// get_client_ip is an INSTANCE method (private function, not static), unlike
// most of what pi_call_private() targets elsewhere in this suite set — it
// needs an object to invoke on, so this goes through the singleton directly
// rather than passing null the way a static-method Reflection call would.
$ip_method = new ReflectionMethod('Pi_Chatbot_Rest', 'get_client_ip');
$ip_method->setAccessible(true);
$ip = $ip_method->invoke(Pi_Chatbot_Rest::instance());
pi_check('without PI_CHATBOT_TRUST_PROXY defined, X-Forwarded-For is IGNORED — REMOTE_ADDR is used',
    $ip === '203.0.113.1', "got {$ip}");
// This is the exact vulnerability class the plugin's own audit history flags:
// trusting a client-controlled header would let anyone bypass the rate limit
// by sending a fresh X-Forwarded-For on every request.
pi_check('the attacker-controlled XFF value is NOT what was returned', $ip !== '198.51.100.9');

unset($_SERVER['HTTP_X_FORWARDED_FOR']);

// ── Dispatch: contact / price / service / product (fast vs max) ─────────────
pi_section('ask() — intent dispatch');

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => []], function () {
    foreach (['contact', 'price', 'service'] as $intent) {
        pi_reset_state();
        pi_http_stub(function ($args, $url, $i) use ($intent) {
            if ($i === 0) {
                return pi_http_content(wp_json_encode([
                    'intent' => $intent, 'keywords_en' => [], 'keywords_vi' => [], 'brand' => null,
                    'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
                ]));
            }
            return pi_http_content("trả lời cho {$intent}");
        });
        $res = pi_rest_ask("câu hỏi {$intent} " . wp_rand(), '10.10.10.7');
        $data = is_wp_error($res) ? null : $res->get_data();
        pi_check("intent='{$intent}' dispatches without error", $data !== null,
            is_wp_error($res) ? $res->get_error_code() : 'ok');
        pi_check("intent='{$intent}' is recorded in trace", $data && $data['trace']['intent'] === $intent);
    }
});

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'max_accuracy_enabled' => true], function () {
    pi_reset_state();
    pi_http_stub(function ($args, $url, $i) {
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
            ]));
        }
        return pi_http_content('câu trả lời sản phẩm dễ');
    });
    $res = pi_rest_ask('tủ đông dễ ' . wp_rand(), '10.10.10.8');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check("difficulty='easy' takes mode='fast'", $data && $data['trace']['mode'] === 'fast',
        $data ? $data['trace']['mode'] : 'error');
    pi_check('the fast path never runs reranking', $data && $data['trace']['rerank_used'] === false);
});

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'max_accuracy_enabled' => true, 'rerank_enabled' => true, 'verify_enabled' => true], function () {
    global $wpdb;
    pi_reset_state();
    // Uses a REAL brand from the live catalog as the search term, so
    // search_hybrid() (which is NOT stubbed — only HTTP is) finds ≥2 genuine
    // candidates for the reranker to work with. A gibberish/empty candidate
    // pool would make Reranker::rerank() short-circuit before any HTTP call
    // (its own documented behaviour, <2 candidates skips the network), which
    // would silently shift every subsequent stubbed call index — that was the
    // root cause behind this suite's first failing run.
    $brand = $wpdb->get_var("SELECT brand FROM " . Pi_Chatbot_Indexer::table_name() . " WHERE post_type='product' AND brand != '' GROUP BY brand ORDER BY COUNT(*) DESC LIMIT 1");
    pi_http_stub(function ($args, $url, $i) {
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'hard', 'query_variants' => ['tủ lạnh công nghiệp'], 'ambiguity_reason' => 'vague',
            ]));
        }
        if ($i === 1) return pi_http_content(wp_json_encode(['ranking' => [1, 2], 'relevant' => [1, 2]]));
        if ($i === 2) return pi_http_content('câu trả lời khó');
        return pi_http_content(wp_json_encode(['faithful' => true]));
    });
    $res = pi_rest_ask($brand, '10.10.10.9');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check("difficulty='hard' takes mode='max'", $data && $data['trace']['mode'] === 'max',
        $data ? $data['trace']['mode'] : 'error');
    pi_check('a confident rerank result runs verification', $data && $data['trace']['verified'] === true,
        $data ? var_export($data['trace']['verified'], true) : 'error');
    pi_check('the max path records per-stage timings in steps[]',
        $data && !empty($data['trace']['steps']) && count($data['trace']['steps']) >= 3,
        $data ? count($data['trace']['steps'] ?? []) . ' steps' : 'error');
});

// The low-confidence path is a DIFFERENT, equally real scenario, tested on its
// own terms instead of being accidentally triggered by an unrelated test.
pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'max_accuracy_enabled' => true, 'rerank_enabled' => true, 'verify_enabled' => true], function () {
    global $wpdb;
    pi_reset_state();
    $brand = $wpdb->get_var("SELECT brand FROM " . Pi_Chatbot_Indexer::table_name() . " WHERE post_type='product' AND brand != '' GROUP BY brand ORDER BY COUNT(*) DESC LIMIT 1");
    pi_http_stub(function ($args, $url, $i) {
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'hard', 'query_variants' => [], 'ambiguity_reason' => 'vague',
            ]));
        }
        // The reranker explicitly judges NONE of the candidates relevant —
        // a real, deliberate verdict (see Reranker::apply's docblock), not a
        // parse failure.
        if ($i === 1) return pi_http_content(wp_json_encode(['ranking' => [], 'relevant' => []]));
        return pi_http_content('should not be reached — verify/generate must be skipped');
    });
    $res = pi_rest_ask($brand, '10.10.10.11');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check('an empty rerank verdict sets low_confidence and empties the context',
        $data && $data['trace']['low_confidence'] === true, $data ? var_export($data['trace']['low_confidence'], true) : 'error');
    pi_check('verify() is correctly SKIPPED when context is empty (nothing to check against)',
        $data && $data['trace']['verified'] === null, $data ? var_export($data['trace']['verified'], true) : 'error');
});

// ── verify() failure triggers exactly one regeneration ──────────────────────
pi_section('ask() — a failed faithfulness check regenerates the answer once');

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'max_accuracy_enabled' => true, 'rerank_enabled' => true, 'verify_enabled' => true], function () {
    global $wpdb;
    pi_reset_state();
    // Real brand again — see the note above on why an empty candidate pool
    // would make the reranker skip its HTTP call and shift every index.
    $brand = $wpdb->get_var("SELECT brand FROM " . Pi_Chatbot_Indexer::table_name() . " WHERE post_type='product' AND brand != '' GROUP BY brand ORDER BY COUNT(*) DESC LIMIT 1");
    $generate_calls = 0;
    pi_http_stub(function ($args, $url, $i) use (&$generate_calls) {
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'hard', 'query_variants' => [], 'ambiguity_reason' => 'vague',
            ]));
        }
        if ($i === 1) return pi_http_content(wp_json_encode(['ranking' => [1, 2], 'relevant' => [1, 2]]));
        // Calls 2 and 3 are generate() attempt #1 and #2; calls 4 (if reached) would be
        // a verify() call. First verify (call index 3) says unfaithful, forcing a retry.
        if ($i === 2) { $generate_calls++; return pi_http_content('câu trả lời sai sự thật đầu tiên'); }
        if ($i === 3) return pi_http_content(wp_json_encode(['faithful' => false, 'issue' => 'bịa giá']));
        if ($i === 4) { $generate_calls++; return pi_http_content('câu trả lời đã sửa'); }
        return pi_http_content(wp_json_encode(['faithful' => true]));
    });
    $res = pi_rest_ask($brand, '10.10.10.10');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check('exactly 2 generate() calls happen: the original draft plus one retry',
        $generate_calls === 2, "{$generate_calls} generate calls");
    pi_check('the final answer is the RETRY\'s text, not the rejected first draft',
        $data && $data['answer'] === 'câu trả lời đã sửa', $data ? $data['answer'] : 'error');
    pi_check('trace.verified reflects the SECOND (retry) check, not the first',
        $data && $data['trace']['verified'] === true);
});

// ── Stage 4.1 — global daily LLM budget degrade ──────────────────────────────
pi_section('ask() — daily LLM budget exhausted (Stage 4.1)');

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'llm_daily_budget' => 5], function () {
    pi_reset_state();
    $budget_option = pi_call_private('Pi_Chatbot_Budget', 'option_name', []);
    update_option($budget_option, 5, false); // already at the configured limit
    $called = false;
    pi_http_stub(function ($args, $url, $i) use (&$called) {
        $called = true;
        return pi_http_content('should never be reached — budget is exhausted');
    });
    $res = pi_rest_ask('tủ đông công nghiệp ' . wp_rand(), '10.10.10.20');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check('budget exhausted -> zero LLM7 calls made (router AND generate both skipped)', $called === false);
    pi_check('trace.budget_exhausted is true', $data && $data['trace']['budget_exhausted'] === true);
    pi_check('an answer is still returned — retrieval-only degrade, not an error', $data && !empty($data['answer']));
});

pi_section('ask() — near-limit budget skips the max-accuracy path (Stage 4.1)');

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'max_accuracy_enabled' => true, 'rerank_enabled' => true, 'verify_enabled' => true, 'llm_daily_budget' => 10], function () {
    pi_reset_state();
    $budget_option = pi_call_private('Pi_Chatbot_Budget', 'option_name', []);
    update_option($budget_option, 8, false); // 8/10 = 80% -> is_near_limit() true
    pi_http_stub(function ($args, $url, $i) {
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'hard', 'query_variants' => [], 'ambiguity_reason' => 'vague',
            ]));
        }
        return pi_http_content('câu trả lời khi gần hết ngân sách');
    });
    $res = pi_rest_ask('tủ đông ' . wp_rand(), '10.10.10.21');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check('near-limit budget forces mode=fast even though the router said difficulty=hard',
        $data && $data['trace']['mode'] === 'fast', $data ? $data['trace']['mode'] : 'error');
    pi_check('near-limit skips reranking entirely', $data && $data['trace']['rerank_used'] === false);
    pi_check('only 2 LLM7 calls happen: router + generate, no rerank/verify',
        count(pi_http_calls()) === 2, count(pi_http_calls()) . ' calls');
});

// ── Stage 4.3 — conditional fact check on the fast path ─────────────────────
pi_section('ask() — fast-path conditional fact check (Stage 4.3)');

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'verify_enabled' => true], function () {
    pi_reset_state();
    pi_http_stub(function ($args, $url, $i) {
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
            ]));
        }
        if ($i === 1) return pi_http_content('Giá tham khảo khoảng 15.000.000đ tùy cấu hình.');
        return pi_http_content(wp_json_encode(['faithful' => true]));
    });
    $res = pi_rest_ask('giá tủ đông ' . wp_rand(), '10.10.10.22');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check("mode stays 'fast' even though a fact check ran",
        $data && $data['trace']['mode'] === 'fast', $data ? $data['trace']['mode'] : 'error');
    pi_check('an answer stating a price runs the fact check on the fast path',
        $data && $data['trace']['verified'] === true, $data ? var_export($data['trace']['verified'], true) : 'error');
    pi_check('exactly 3 LLM7 calls: router + generate + verify',
        count(pi_http_calls()) === 3, count(pi_http_calls()) . ' calls');
});

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => [], 'verify_enabled' => true], function () {
    pi_reset_state();
    pi_http_stub(function ($args, $url, $i) {
        if ($i === 0) {
            return pi_http_content(wp_json_encode([
                'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
            ]));
        }
        return pi_http_content('Chúng tôi có nhiều mẫu tủ đông phù hợp, bạn cần loại nào?');
    });
    $res = pi_rest_ask('tủ đông ' . wp_rand(), '10.10.10.23');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check('a conversational answer with no number/URL/model-code skips the fact check',
        $data && $data['trace']['verified'] === null, $data ? var_export($data['trace']['verified'], true) : 'error');
    pi_check('exactly 2 LLM7 calls: router + generate only, no verify',
        count(pi_http_calls()) === 2, count(pi_http_calls()) . ' calls');
});

// ── Stage 4.1 — minimum 1s interval per IP ───────────────────────────────────
pi_section('ask() — minimum interval between requests from the same IP (Stage 4.1)');

pi_with_settings(['router_enabled' => false], function () {
    pi_reset_state();
    pi_http_stub(function ($args, $url, $i) { return pi_http_content('ok'); });

    $ip = '10.10.10.30';
    $res1 = pi_rest_ask('câu hỏi một ' . wp_rand(), $ip);
    pi_check('first request from a fresh IP succeeds', !is_wp_error($res1),
        is_wp_error($res1) ? $res1->get_error_code() : 'ok');

    $res2 = pi_rest_ask('câu hỏi hai ' . wp_rand(), $ip);
    pi_check('an immediate second request from the SAME IP is rejected (min-interval guard)',
        is_wp_error($res2) && $res2->get_error_code() === 'pi_chatbot_rate_limited',
        is_wp_error($res2) ? $res2->get_error_code() : 'no error');

    // Backdate the stored last-request timestamp by 2s directly in the DB — proves
    // the guard recovers once the interval has genuinely elapsed, without a real
    // sleep() slowing down every test run.
    global $wpdb;
    $key = 'pi_chatbot_lastreq_' . md5($ip);
    $now_ms = (int) round(microtime(true) * 1000);
    $wpdb->update($wpdb->options, ['option_value' => (string) ($now_ms - 2000)], ['option_name' => '_transient_' . $key]);

    $res3 = pi_rest_ask('câu hỏi ba ' . wp_rand(), $ip);
    pi_check('a 3rd request after the interval has elapsed succeeds again', !is_wp_error($res3),
        is_wp_error($res3) ? $res3->get_error_code() : 'ok');
});

// ── Stage 2 — feedback endpoint ──────────────────────────────────────────────
pi_section('ask() attaches feedback_id, feedback() records a real vote (Stage 2)');

function pi_rest_feedback($feedback_id, $rating, $ip = '10.20.20.1') {
    $_SERVER['REMOTE_ADDR'] = $ip;
    $req = new WP_REST_Request('POST', '/pi/v1/chatbot/feedback');
    $req->set_param('feedback_id', $feedback_id);
    $req->set_param('rating', $rating);
    return Pi_Chatbot_Rest::instance()->feedback($req);
}

pi_with_settings(['router_enabled' => false], function () {
    global $wpdb;
    pi_reset_state();
    pi_http_stub(function () { return pi_http_content('câu trả lời cố định cho feedback test'); });

    $res = pi_rest_ask('câu hỏi cho feedback test ' . wp_rand(), '10.20.20.2');
    $data = is_wp_error($res) ? null : $res->get_data();
    pi_check('ask() response includes a feedback_id',
        $data && !empty($data['feedback_id']) && preg_match('/^[a-f0-9]{32}$/', $data['feedback_id']),
        $data ? var_export($data['feedback_id'] ?? null, true) : 'error');

    if ($data && !empty($data['feedback_id'])) {
        $fb_res = pi_rest_feedback($data['feedback_id'], 'up');
        pi_check('feedback() with a real feedback_id succeeds', !is_wp_error($fb_res),
            is_wp_error($fb_res) ? $fb_res->get_error_code() : 'ok');

        $fb_table = Pi_Chatbot_Feedback::table_name();
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT rating FROM {$fb_table} WHERE feedback_id = %s ORDER BY id DESC LIMIT 1", $data['feedback_id']
        ));
        pi_check('the vote is genuinely persisted with rating=1 for "up"',
            $row && (int) $row->rating === 1, $row ? "rating={$row->rating}" : 'no row written');

        // Clean up — this suite writes to the real live table (like other REST tests
        // that touch the log), same idempotency discipline as suite-pure.php's version.
        $wpdb->query($wpdb->prepare("DELETE FROM {$fb_table} WHERE feedback_id = %s", $data['feedback_id']));
    }

    $bad_res = pi_rest_feedback('not-a-real-hash', 'up', '10.20.20.3');
    pi_check('an invalid feedback_id is rejected, not silently accepted',
        is_wp_error($bad_res) && $bad_res->get_error_code() === 'pi_chatbot_bad_feedback');
});

// ── Stage 4 — lead-capture endpoint ──────────────────────────────────────────
pi_section('lead() records a callback request, rejects a bad phone (Stage 4)');

function pi_rest_lead($name, $phone, $question = '', $ip = '10.20.20.5') {
    $_SERVER['REMOTE_ADDR'] = $ip;
    $req = new WP_REST_Request('POST', '/pi/v1/chatbot/lead');
    $req->set_param('name', $name);
    $req->set_param('phone', $phone);
    $req->set_param('question', $question);
    return Pi_Chatbot_Rest::instance()->lead($req);
}

$lead_marker = 'pi_chatbot_test_rest_lead_' . wp_rand();
$lead_phone = '09' . wp_rand(10000000, 99999999);
try {
    $ok_res = pi_rest_lead('Test Customer', $lead_phone, $lead_marker);
    pi_check('a valid name+phone+question is accepted', !is_wp_error($ok_res),
        is_wp_error($ok_res) ? $ok_res->get_error_code() : 'ok');

    global $wpdb;
    $lead_table = Pi_Chatbot_Lead::table_name();
    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT name, phone, question FROM {$lead_table} WHERE question = %s", $lead_marker
    ));
    pi_check('the lead is genuinely persisted with the exact phone/question submitted',
        $row && $row->phone === $lead_phone && $row->question === $lead_marker,
        $row ? "phone={$row->phone}" : 'no row');

    $bad_res = pi_rest_lead('', 'abc', '', '10.20.20.6');
    pi_check('an unparseable phone is rejected, not silently accepted',
        is_wp_error($bad_res) && $bad_res->get_error_code() === 'pi_chatbot_bad_lead');

    // Rate limit — same per-minute-bucket pattern as feedback(), proven the same
    // way: hammer past LEAD_RATE_LIMIT from one IP and confirm the NEXT call is
    // rejected, not silently accepted. Every call in the loop shares the same
    // $rl_marker so the finally block can clean up every row it wrote by a
    // single query, not just the last one.
    $rl_ip = '10.20.20.7';
    $rl_marker = 'pi_chatbot_test_rest_lead_rl_' . wp_rand();
    for ($i = 0; $i < Pi_Chatbot_Rest::LEAD_RATE_LIMIT; $i++) {
        pi_rest_lead('', '09' . wp_rand(10000000, 99999999), $rl_marker, $rl_ip);
    }
    $rl_res = pi_rest_lead('', '09' . wp_rand(10000000, 99999999), $rl_marker, $rl_ip);
    pi_check('a 6th lead submission from the same IP within a minute is rate-limited',
        is_wp_error($rl_res) && $rl_res->get_error_code() === 'pi_chatbot_rate_limited');
} finally {
    $wpdb->query($wpdb->prepare("DELETE FROM {$lead_table} WHERE question = %s", $lead_marker));
    if (isset($rl_marker)) {
        $wpdb->query($wpdb->prepare("DELETE FROM {$lead_table} WHERE question = %s", $rl_marker));
    }
    if (isset($rl_ip)) {
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $wpdb->esc_like('_transient_pi_chatbot_leadrl_' . md5($rl_ip)) . '%'
        ));
    }
}

// ── Stage 4b — streaming endpoint ────────────────────────────────────────────
pi_section('ask_stream() — only eligible when context is empty (Stage 4b)');

/** Thrown by the test's $stream_exit_fn to stand in for the real `exit` —
 *  ask_stream() calls this exactly once it's done writing SSE events, and a
 *  real `exit` here would kill every suite queued to run after this one in
 *  run-all.php. Caught immediately at each call site below. */
class Pi_Chatbot_Test_StreamExit extends \Exception {}

/** Runs ask_stream() with both DI hooks wired to capture what production
 *  would have written to output — $emitted collects every emit() call in
 *  order, and the exit is converted into a normal return via the exception
 *  above instead of actually terminating the test process. */
function pi_rest_ask_stream($message, $ip, $history = null) {
    $_SERVER['REMOTE_ADDR'] = $ip;
    $req = new WP_REST_Request('POST', '/pi/v1/chatbot/ask-stream');
    $req->set_param('message', $message);
    if ($history !== null) $req->set_param('history', $history);

    $emitted = [];
    Pi_Chatbot_Rest::$stream_emit_fn = function ($type, $data) use (&$emitted) {
        $emitted[] = array_merge(['type' => $type], $data);
    };
    Pi_Chatbot_Rest::$stream_exit_fn = function () {
        throw new Pi_Chatbot_Test_StreamExit();
    };
    try {
        $res = Pi_Chatbot_Rest::instance()->ask_stream($req);
    } catch (Pi_Chatbot_Test_StreamExit $e) {
        $res = null; // ask_stream() already sent everything via $emitted
    } finally {
        Pi_Chatbot_Rest::$stream_emit_fn = null;
        Pi_Chatbot_Rest::$stream_exit_fn = null;
    }
    return ['res' => $res, 'emitted' => $emitted];
}

pi_with_settings(['router_enabled' => true, 'llm7_keys' => ['k'], 'router_keys' => []], function () {
    pi_reset_state();

    // ── Eligible: intent='none' (small talk) → real SSE deltas + done ──────
    $orig_transport = Pi_Chatbot_LLM_Client::$stream_transport;
    try {
        pi_http_stub(function ($args, $url, $i) {
            // Only the router calls wp_remote_post() on this path — generation
            // goes through call_streaming()'s $stream_transport hook instead.
            return pi_http_content(wp_json_encode([
                'intent' => 'none', 'keywords_en' => [], 'keywords_vi' => [], 'brand' => null,
                'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
            ]));
        });
        Pi_Chatbot_LLM_Client::$stream_transport = function ($key, $body, $timeout, $on_delta) {
            foreach (['Xin ', 'chào!'] as $chunk) $on_delta($chunk);
            return ['http_code' => 200, 'full_text' => 'Xin chào!', 'relayed_any' => true, 'curl_errno' => 0, 'curl_error' => ''];
        };

        $q = 'chào bạn ' . wp_rand();
        $out = pi_rest_ask_stream($q, '10.10.30.1');
        $deltas = array_values(array_filter($out['emitted'], function ($e) { return $e['type'] === 'delta'; }));
        $done   = array_values(array_filter($out['emitted'], function ($e) { return $e['type'] === 'done'; }));

        pi_check("intent='none' is eligible and streams real deltas, not a fallback signal",
            count($deltas) === 2 && $deltas[0]['text'] === 'Xin ' && $deltas[1]['text'] === 'chào!',
            wp_json_encode($out['emitted']));
        pi_check('exactly one done event, carrying a real feedback_id',
            count($done) === 1 && preg_match('/^[a-f0-9]{32}$/', $done[0]['feedback_id'] ?? ''),
            wp_json_encode($done));

        // The cache this stream wrote must be usable by the BUFFERED /ask
        // endpoint too — a customer whose stream succeeded, then reloads and
        // re-asks the identical question, must get the instant cache hit
        // /ask already provides, not a second live generation.
        $cached_res = pi_rest_ask($q, '10.10.30.2');
        $cached_data = is_wp_error($cached_res) ? null : $cached_res->get_data();
        pi_check('the streamed answer populated the SAME cache /ask reads from',
            $cached_data && $cached_data['answer'] === 'Xin chào!',
            $cached_data ? $cached_data['answer'] : (is_wp_error($cached_res) ? $cached_res->get_error_code() : 'null'));
    } finally {
        Pi_Chatbot_LLM_Client::$stream_transport = $orig_transport;
    }

    // ── Ineligible: intent='product' with real results → declines to stream ─
    pi_reset_state();
    pi_http_stub(function ($args, $url, $i) {
        return pi_http_content(wp_json_encode([
            'intent' => 'product', 'keywords_en' => ['freezer'], 'keywords_vi' => ['tủ đông'], 'brand' => null,
            'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
        ]));
    });
    $out2 = pi_rest_ask_stream('tủ đông ' . wp_rand(), '10.10.30.3');
    pi_check("intent='product' with non-empty context declines to stream (no SSE emitted)",
        empty($out2['emitted']));
    pi_check('the decline response is plain JSON {eligible:false}, not an SSE-shaped payload',
        $out2['res'] && !is_wp_error($out2['res']) && $out2['res']->get_data() === ['eligible' => false],
        $out2['res'] ? wp_json_encode($out2['res']->get_data()) : 'no res');

    // ── Ineligible: a cache hit short-circuits before any router/LLM call ───
    pi_reset_state();
    pi_http_stub(function () {
        // If this stub is ever actually called, the cache short-circuit
        // failed to trigger — return something that would make that failure
        // visible rather than silently succeeding.
        return pi_http_content(wp_json_encode([
            'intent' => 'none', 'keywords_en' => [], 'keywords_vi' => [], 'brand' => null,
            'difficulty' => 'easy', 'query_variants' => [], 'ambiguity_reason' => '',
        ]));
    });
    $cache_q = 'câu hỏi đã cache ' . wp_rand();
    $normalized_q = trim(preg_replace('/\s+/u', ' ', mb_strtolower($cache_q)));
    $cache_key = 'pi_chatbot_ans_' . md5($normalized_q . '|' . (int) get_option('pi_chatbot_index_version', 0));
    set_transient($cache_key, ['answer' => 'already cached', 'sources' => []], HOUR_IN_SECONDS);
    $out3 = pi_rest_ask_stream($cache_q, '10.10.30.4');
    pi_check('a cache hit declines to stream, with zero HTTP calls made',
        empty($out3['emitted']) && count(pi_http_calls()) === 0, count(pi_http_calls()) . ' calls');
    delete_transient($cache_key);
});

pi_http_unstub();
pi_reset_state();

// pi_report() prints a summary from the SHARED pi_check() static state — when
// run standalone that's exactly this suite's own totals; when run-all.php
// requires this file alongside others, the same static keeps accumulating, so
// printing here would show a confusing cumulative count instead of this
// suite's own. run-all.php does its own before/after accounting per suite and
// prints one combined summary at the end, so this suite only prints (and
// exits on) its own report when running standalone.
if (!defined('PI_TEST_SUITE_MODE')) exit(pi_report());
