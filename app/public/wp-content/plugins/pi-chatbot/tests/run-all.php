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
    'suite-pure.php'        => 'Pure logic (no DB, no network)',
    'suite-retrieval.php'   => 'Retriever against the live catalog (read-only)',
    'suite-indexer.php'     => 'Indexer + Log (writes, isolated + torn down)',
    'suite-llm.php'         => 'Router / Reranker / Generator / LLM client (HTTP stubbed)',
    'suite-rest.php'        => 'Full REST ask() flow (HTTP stubbed)',
    // suite-spam-agents.php REMOVED 2026-08-08. 79 assertions, and a measured
    // audit found: 0 touched the real request path, 61 could not go red from
    // any production change, 49 stayed green even if every tool returned [],
    // 18 asserted fabricated numbers were CORRECT (so they defended the
    // defect), and it contained a DELETE that could take out a real
    // production graph row (upsert_node('Hoshizaki') with no test prefix, then
    // DELETE by the returned id). It also leaked a node + edge per run —
    // 62 orphan rows had accumulated by the time it was removed.
];

echo "=== pi-chatbot full test run — " . count($suites) . " suites ===\n";

// Log teardown, part 1 of 2 — remember the highest log id that exists BEFORE
// any suite runs.
//
// Why this exists: suites drive the real ask()/ask_stream() path, and that
// path legitimately writes a row to wp_pi_chatbot_log. Those rows are real
// enough to pollute the wp-admin report "questions the bot could not answer":
// by 2026-08-09 the top of that report was 159 rows of "xxxxx…" (the
// MAX_MESSAGE_LENGTH probe) and 329 of "tủ đông <random>" — 703 rows total,
// which is what a site owner would have read as customer demand. Purged then;
// this stops it recurring.
//
// Deleting by id > watermark rather than by matching question text on purpose.
// Text matching guesses, and the guess is dangerous: the obvious pattern
// `question_norm LIKE 'tủ đông 1%'` also matches "tủ đông 1 cánh", an ordinary
// product question. An id watermark needs no guessing — every row a test run
// creates is above it, and every row below it is untouched, whatever the
// wording.
//
// Safe because this is a dev/CI database. If a real visitor somehow asked a
// question mid-run their row would also be above the watermark; that trade is
// accepted here and would NOT be on production.
global $wpdb;
$pi_log_table     = Pi_Chatbot_Log::table_name();
$pi_log_watermark = (int) $wpdb->get_var("SELECT COALESCE(MAX(id), 0) FROM {$pi_log_table}");

// Same treatment for the FAQ table, and this one mattered more than the log.
// assetize() runs on every answered question, so suites were filing their own
// fixtures into the REAL approval queue an admin reads. By 2026-08-09 that
// queue held 95 rows of which 63 were the test string "Giá tham khảo khoảng
// 15.000.000đ tùy cấu hình" — a price nobody at the shop ever quoted.
//
// That is worse than log noise: approving a row makes find_faq() serve its
// answer VERBATIM, skipping the model and skipping verify(). Approving the
// queue as it stood would have published an invented price as the shop's
// answer to every freezer price question. Purged (65 test rows, plus 2 real-
// looking rows carrying "bảo hành 24 tháng" left over from the deleted
// warranty_checker tool); 28 genuine pending rows remain.
$pi_faq_table     = $wpdb->prefix . 'pi_chatbot_faq';
$pi_faq_watermark = (int) $wpdb->get_var("SELECT COALESCE(MAX(id), 0) FROM {$pi_faq_table}");

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

// Log teardown, part 2 of 2 — drop every row this run created. Runs before the
// summary so the count is visible in the same output the developer already
// reads, and unconditionally: a failing run pollutes the log exactly as much as
// a passing one, so an early `exit(1)` below must not skip it.
$pi_log_purged = (int) $wpdb->query(
    $wpdb->prepare("DELETE FROM {$pi_log_table} WHERE id > %d", $pi_log_watermark)
);
$pi_faq_purged = (int) $wpdb->query(
    $wpdb->prepare("DELETE FROM {$pi_faq_table} WHERE id > %d", $pi_faq_watermark)
);
printf("\n[teardown] removed %d log row(s) and %d FAQ row(s) created by this run\n", $pi_log_purged, $pi_faq_purged);

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
