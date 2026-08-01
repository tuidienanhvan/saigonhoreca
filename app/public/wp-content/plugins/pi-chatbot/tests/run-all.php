<?php
/**
 * run-all.php — runs every deterministic pi-chatbot suite in one process and
 * reports one combined pass/fail. This is the file to run before any commit
 * that touches includes/*.php.
 *
 * Deliberately EXCLUDES eval-retrieval.php (that measures ranking QUALITY
 * against golden-queries.json, a different kind of signal — see its own
 * docblock) and smoke-live.php (opt-in, spends real LLM7 quota).
 *
 * Run:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/run-all.php
 *
 * Recommended full pre-commit check:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/run-all.php
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php compare=baseline.json
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php mode=hybrid compare=baseline.json
 */

require_once __DIR__ . '/lib/harness.php';

// Signals to every included suite: "you are being run from inside run-all.php,
// do not call exit() at the end of your own file" — otherwise the first
// suite's own exit() would terminate the whole batch before the rest ran.
define('PI_TEST_SUITE_MODE', true);

$suites = [
    'suite-pure.php'       => 'Pure logic (no DB, no network)',
    'suite-retrieval.php'  => 'Retriever against the live catalog (read-only)',
    'suite-indexer.php'    => 'Indexer + Log (writes, isolated + torn down)',
    'suite-llm.php'        => 'Router / Reranker / Generator / LLM client (HTTP stubbed)',
    'suite-rest.php'       => 'Full REST ask() flow (HTTP stubbed)',
];

echo "=== pi-chatbot full test run — " . count($suites) . " suites ===\n";

// Harness self-test — proves pi_check()/pi_report() can actually go red before
// trusting them to grade five suites. Without this, a scoping or counter bug
// would make every run print success no matter what the assertions did (the
// exact failure mode documented in lib/harness.php — it happened once already,
// to unit-checks.php's first version). Recorded and then excluded by name so
// it doesn't itself fail the run.
pi_section('Harness self-test (this ONE failure is expected)');
$before_self_test = pi_check();
pi_check('SELF-TEST: deliberate failure — this line SHOULD read FAIL', false, 'proving the reporter goes red');
$after_self_test = pi_check();
$self_test_ok = count($after_self_test['failed']) === count($before_self_test['failed']) + 1;
pi_check('the harness correctly recorded the deliberate failure above', $self_test_ok);

$results = [];
foreach ($suites as $file => $description) {
    $path = __DIR__ . '/' . $file;
    echo "\n" . str_repeat('#', 70) . "\n# {$file} — {$description}\n" . str_repeat('#', 70) . "\n";

    $before = pi_check();
    if (!file_exists($path)) {
        echo "  MISSING FILE — skipped\n";
        $results[$file] = null;
        continue;
    }
    require $path;
    $after = pi_check();
    $results[$file] = [
        'passed' => $after['passed'] - $before['passed'],
        'failed' => array_slice($after['failed'], count($before['failed'])),
    ];

    // Leave state clean for the NEXT suite in this same process — a suite that
    // forgot its own teardown must not poison the one after it.
    pi_http_unstub();
    pi_mail_unstub();
    pi_reset_state();
}

echo "\n" . str_repeat('=', 70) . "\n=== Summary ===\n";
printf("  %-4s %-24s harness self-test (1 deliberate failure expected)\n", $self_test_ok ? 'ok' : 'FAIL', '');
$total_failed = $self_test_ok ? 0 : 1;
foreach ($results as $file => $r) {
    if ($r === null) { printf("  %-4s %-24s SKIPPED (file missing)\n", '?', $file); continue; }
    $status = $r['failed'] ? 'FAIL' : 'ok';
    printf("  %-4s %-24s %d passed, %d failed\n", $status, $file, $r['passed'], count($r['failed']));
    $total_failed += count($r['failed']);
}

echo "\n";
if ($total_failed > 0) {
    echo "FAILED — {$total_failed} unexpected assertion failure(s) across " . count($suites) . " suites.\n\n";
    exit(1);
}
echo "All suites passed (plus the 1 deliberate self-test failure above, which is expected).\n\n";
exit(0);
