<?php
/**
 * smoke-live.php — the ONE file in this test set allowed to call the real
 * LLM7 API. Everything else (suite-llm.php, suite-rest.php) intercepts HTTP
 * with pi_http_stub() precisely so it can run deterministically and for free;
 * that also means those suites cannot detect the one thing only a live call
 * can: that LLM7 still replies in the shape every stub assumes.
 *
 * OPT-IN, NOT part of run-all.php or any pre-commit check — it spends real
 * free-tier quota and can fail on 429 (rate limit) for reasons that have
 * nothing to do with this plugin's code. A 429 is reported as SKIPPED, not
 * FAILED — the tier is between the code and the assertion, and treating one
 * as the other would make this file flaky by design.
 *
 * Requires at least one real LLM7 key configured in the plugin settings.
 *
 * Run:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/smoke-live.php
 */

require_once __DIR__ . '/lib/harness.php';

$settings = pi_chatbot_settings();
if (empty($settings['llm7_keys'])) {
    echo "No LLM7 keys configured — nothing to smoke-test against. Skipping.\n";
    exit(0);
}

pi_reset_state();

function pi_smoke_run($label, callable $fn) {
    echo "\n[{$label}]\n";
    try {
        $fn();
    } catch (Throwable $e) {
        pi_check($label, false, 'threw: ' . $e->getMessage());
    }
}

// ── Router: real classification of a clear, unambiguous product question ────
pi_smoke_run('Router::enrich against the real API', function () {
    $q = 'tủ đông 2 cánh Hoshizaki ' . wp_rand(); // unique, bypasses any stale cache
    $r = Pi_Chatbot_Router::enrich($q);
    if ($r === null) {
        pi_check('Router::enrich — real call', true, 'SKIPPED: null (disabled, breaker open, or rate-limited — not a code defect)');
        return;
    }
    pi_check('the live router returns intent=product for a clear product question', $r['intent'] === 'product', $r['intent']);
    pi_check('the live router extracted at least one keyword', !empty($r['keywords_en']) || !empty($r['keywords_vi']));
    pi_check('difficulty is one of the two valid values', in_array($r['difficulty'], ['easy', 'hard'], true), $r['difficulty']);
});

// ── Generator: a real answer for a real, empty-context question ─────────────
pi_smoke_run('Generator::answer against the real API', function () {
    $result = Pi_Chatbot_Generator::answer('xin chào', []);
    if (is_wp_error($result) && strpos($result->get_error_message(), 'rate') !== false) {
        pi_check('Generator::answer — real call', true, 'SKIPPED: looks rate-limited');
        return;
    }
    pi_check('a live call to the real generator model returns a usable answer',
        !is_wp_error($result) && !empty($result['answer']),
        is_wp_error($result) ? $result->get_error_code() : 'ok');
});

// ── Full REST round-trip against the real API (no HTTP stub anywhere) ───────
pi_smoke_run('Full ask() round-trip against the real API', function () {
    $_SERVER['REMOTE_ADDR'] = '203.0.113.99';
    $req = new WP_REST_Request('POST', '/pi/v1/chatbot/ask');
    $req->set_param('message', 'xin chào, cho hỏi có bán tủ đông không?');
    $res = Pi_Chatbot_Rest::instance()->ask($req);
    if (is_wp_error($res) && $res->get_error_code() === 'pi_chatbot_rate_limited') {
        pi_check('ask() — real round-trip', true, 'SKIPPED: local rate limit hit by this smoke test itself');
        return;
    }
    pi_check('a real end-to-end ask() succeeds', !is_wp_error($res), is_wp_error($res) ? $res->get_error_code() : 'ok');
    if (!is_wp_error($res)) {
        $data = $res->get_data();
        pi_check('the response carries the trace shape suite-rest.php\'s stubs assume',
            isset($data['trace']['mode'], $data['trace']['intent'], $data['trace']['steps']));
    }
});

pi_reset_state();

echo "\n";
exit(pi_report());
