<?php
/**
 * eval-retrieval.php — Regression harness for Pi_Chatbot_Retriever::search().
 *
 * Runs 60-100 real questions (tests/golden-queries.json), compares the
 * returned results against manually-verified correct product IDs. Reports
 * hit@1/hit@3/recall@5/MRR PER GROUP (not just an overall number — an overall
 * number hides where things are broken, a lesson learned from an earlier
 * evaluation that used only 4 questions and couldn't conclude anything).
 *
 * Run it (eval-file only accepts positional args, NOT --flags — WP-CLI blocks
 * unknown --flags before the file even runs):
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php save-baseline
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php compare=baseline.json
 *
 * mode=hybrid runs Retriever::search_hybrid() instead — the retrieval half of
 * the max-accuracy pipeline. Deliberately WITHOUT the router and the reranker:
 * both are LLM calls, which would make the run cost money, take ~20 minutes, and
 * produce a different number every time. Note that without the router there are
 * no query rewrites to fuse, so this run exercises the fusion plumbing rather
 * than the multi-query benefit it exists for — its job is to prove the plumbing
 * is non-regressive.
 *   wp eval-file .../eval-retrieval.php mode=hybrid compare=baseline.json
 *   wp eval-file .../eval-retrieval.php mode=hybrid save-baseline=baseline-hybrid.json
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Must be run via `wp eval-file`, not directly with the PHP CLI.\n");
    exit(1);
}

$golden_path = __DIR__ . '/golden-queries.json';
if (!file_exists($golden_path)) {
    fwrite(STDERR, "Could not find $golden_path\n");
    exit(1);
}
$golden = json_decode(file_get_contents($golden_path), true);
if (!is_array($golden)) {
    fwrite(STDERR, "golden-queries.json did not parse as valid JSON.\n");
    exit(1);
}

// ── Run retrieval + compute metrics per question ───────────────────────────

/**
 * Two scoring criteria, because not every question has a fixed right answer.
 *
 * "sản phẩm Hoshizaki" ("Hoshizaki products") cannot be scored against a list of
 * ids: Hoshizaki has 206 products in this catalog and ANY of them answers the
 * question. The original label picked 5 of the 206, so retrieval returning five
 * genuine Hoshizaki products scored ZERO — the harness was reporting a failure
 * where the system was right. Those questions now carry
 * "criterion":"brand" and are scored on whether the results ARE that brand.
 *
 * Everything else keeps the id-list criterion, which is the stricter and more
 * useful measure wherever a specific product really is the answer.
 */
function pi_eval_metrics_for_brand($brand, $result_rows) {
    $is_match = function ($row) use ($brand) {
        return isset($row['brand']) && strcasecmp(trim($row['brand']), $brand) === 0;
    };
    $flags = array_map($is_match, $result_rows);

    $hit1 = !empty($flags[0]) ? 1 : 0;
    $hit3 = in_array(true, array_slice($flags, 0, 3), true) ? 1 : 0;
    // For a brand question "recall" against a 206-product brand is meaningless;
    // what matters is that the results the customer sees are on-brand, so this
    // slot reports PRECISION@5. Named in the output as recall@5 for column
    // compatibility — the distinction is documented here rather than widening
    // every report table for two rows.
    $top5 = array_slice($flags, 0, 5);
    $recall5 = $top5 ? count(array_filter($top5)) / count($top5) : 0.0;

    $mrr = 0.0;
    foreach ($flags as $rank => $ok) {
        if ($ok) { $mrr = 1.0 / ($rank + 1); break; }
    }
    return ['hit1' => $hit1, 'hit3' => $hit3, 'recall5' => $recall5, 'mrr' => $mrr, 'is_negative' => false];
}

function pi_eval_metrics_for_query($expected_ids, $result_post_ids) {
    $expected = array_map('intval', $expected_ids);
    $got = array_map('intval', $result_post_ids);

    $hit1 = (!empty($got) && in_array($got[0], $expected, true)) ? 1 : 0;
    $top3 = array_slice($got, 0, 3);
    $hit3 = (array_intersect($top3, $expected)) ? 1 : 0;
    // Denominator is min(5, |expected|), NOT |expected|. Several questions name a
    // product CATEGORY rather than one model ("bàn mát 2 cửa" matches 17 products
    // in this catalog, "lò nướng combi" 7), and dividing 5 result slots by 17
    // possible answers caps the score at 0.29 no matter how perfect the results
    // are — measuring the size of the label set instead of the quality of the
    // retrieval. Capping asks the answerable question: of the slots that COULD
    // have held a correct answer, how many did.
    $top5 = array_slice($got, 0, 5);
    $recall5 = $expected
        ? (count(array_intersect($top5, $expected)) / min(5, count($expected)))
        : 0.0;

    $mrr = 0.0;
    foreach ($got as $rank => $pid) {
        if (in_array($pid, $expected, true)) {
            $mrr = 1.0 / ($rank + 1);
            break;
        }
    }

    // The "small talk / out of scope" group — an empty expected_ids means
    // "should return no products at all". Counted as "correct" if retrieval
    // returns nothing or very little.
    //
    // This group does NOT score 1.000, and that is expected: this harness calls
    // Retriever::search() DIRECTLY, with no router in front of it. In production
    // small talk never reaches retrieval at all — the router classifies it as
    // intent='none' and class-rest.php skips retrieval entirely (see the 'none'
    // case there). So a miss here means "keyword matching alone would have
    // returned products", which is true but not what a customer experiences.
    // Read this group as a measure of the retriever's raw noise floor, not as a
    // live defect count.
    if (empty($expected)) {
        $ok = empty($got) ? 1 : 0;
        return ['hit1' => $ok, 'hit3' => $ok, 'recall5' => $ok, 'mrr' => $ok, 'is_negative' => true];
    }

    return ['hit1' => $hit1, 'hit3' => $hit3, 'recall5' => $recall5, 'mrr' => $mrr, 'is_negative' => false];
}

// `wp eval-file <file> <arg>...` — WP-CLI puts positional args after the
// filename into the $args variable in the file's scope (not $argv/$_SERVER).
// Parsed up here because the retrieval mode has to be known before the loop.
$eval_args = isset($args) && is_array($args) ? $args : [];

// Reject unknown arguments instead of ignoring them. This is not defensive
// pedantry — it is a bug fix. Unknown args used to be dropped in silence, so
// `save=baseline.json` (real flag: `save-baseline=`) and `hybrid=1` (real flag:
// `mode=hybrid`) both ran happily and did NOTHING: the baseline was never
// written, and a run believed to be exercising the hybrid path was actually
// lexical. Both mistakes were made for real on 2026-07-26 and produced two
// commit messages claiming updated baselines that had not been updated. A
// measurement tool that silently ignores the instruction to measure is worse
// than one that crashes.
$known_prefixes = ['mode=', 'compare=', 'save-baseline='];
foreach ($eval_args as $a) {
    if ($a === 'save-baseline') continue;
    $ok = false;
    foreach ($known_prefixes as $p) {
        if (strpos($a, $p) === 0) { $ok = true; break; }
    }
    if (!$ok) {
        fwrite(STDERR, "Unknown argument '{$a}'.\nValid: mode=lexical|hybrid, compare=<file>, save-baseline[=<file>]\n");
        exit(1);
    }
}

$mode = 'lexical';
foreach ($eval_args as $a) {
    if (strpos($a, 'mode=') === 0) $mode = substr($a, strlen('mode='));
}
if (!in_array($mode, ['lexical', 'hybrid'], true)) {
    fwrite(STDERR, "Unknown mode '{$mode}' — expected lexical or hybrid.\n");
    exit(1);
}
$by_group = [];
$rows = [];

foreach ($golden as $item) {
    $q = $item['query'];
    $group = $item['group'];
    $expected = $item['relevant_ids'] ?? [];

    // No extra_queries: those come from the router at runtime, and an LLM call
    // per question would make this harness slow, costly and non-deterministic.
    $results = $mode === 'hybrid'
        ? Pi_Chatbot_Retriever::search_hybrid($q, 6)
        : Pi_Chatbot_Retriever::search($q, 6);
    $got_ids = array_map(function ($r) { return $r['post_id']; }, $results);

    $m = ($item['criterion'] ?? '') === 'brand'
        ? pi_eval_metrics_for_brand($item['criterion_value'], $results)
        : pi_eval_metrics_for_query($expected, $got_ids);
    $m['query'] = $q;
    $m['expected'] = ($item['criterion'] ?? '') === 'brand'
        ? ['brand:' . $item['criterion_value']]
        : $expected;
    $m['got'] = $got_ids;
    $rows[] = $m;

    if (!isset($by_group[$group])) {
        $by_group[$group] = ['n' => 0, 'hit1' => 0, 'hit3' => 0, 'recall5' => 0.0, 'mrr' => 0.0];
    }
    $by_group[$group]['n']++;
    $by_group[$group]['hit1'] += $m['hit1'];
    $by_group[$group]['hit3'] += $m['hit3'];
    $by_group[$group]['recall5'] += $m['recall5'];
    $by_group[$group]['mrr'] += $m['mrr'];
}

// ── Report ──────────────────────────────────────────────────────────────

echo "\n=== pi-chatbot retrieval eval [{$mode}] — " . count($golden) . " queries, " . count($by_group) . " groups ===\n\n";
printf("%-28s %5s %8s %8s %10s %8s\n", "Group", "N", "hit@1", "hit@3", "recall@5", "MRR");
$overall = ['n' => 0, 'hit1' => 0, 'hit3' => 0, 'recall5' => 0.0, 'mrr' => 0.0];
foreach ($by_group as $group => $g) {
    printf(
        "%-28s %5d %8.3f %8.3f %10.3f %8.3f\n",
        $group, $g['n'], $g['hit1'] / $g['n'], $g['hit3'] / $g['n'],
        $g['recall5'] / $g['n'], $g['mrr'] / $g['n']
    );
    $overall['n'] += $g['n'];
    $overall['hit1'] += $g['hit1'];
    $overall['hit3'] += $g['hit3'];
    $overall['recall5'] += $g['recall5'];
    $overall['mrr'] += $g['mrr'];
}
echo str_repeat('-', 72) . "\n";
printf(
    "%-28s %5d %8.3f %8.3f %10.3f %8.3f\n",
    "TOTAL", $overall['n'], $overall['hit1'] / $overall['n'], $overall['hit3'] / $overall['n'],
    $overall['recall5'] / $overall['n'], $overall['mrr'] / $overall['n']
);

// List failed queries (hit@3 = 0, excluding the negative group) — the part that needs the closest look
echo "\n--- Failed queries (hit@3 = 0) ---\n";
$miss_count = 0;
foreach ($rows as $r) {
    if (!$r['is_negative'] && $r['hit3'] === 0) {
        $miss_count++;
        if ($miss_count <= 20) {
            echo "  \"{$r['query']}\" | expected=" . implode(',', $r['expected'])
                . " | got=" . implode(',', array_slice($r['got'], 0, 6)) . "\n";
        }
    }
}
if ($miss_count > 20) echo "  ... and " . ($miss_count - 20) . " more\n";
if ($miss_count === 0) echo "  (none)\n";

// ── Save / compare baseline ────────────────────────────────────────────

// $eval_args was parsed before the run loop (the retrieval mode had to be known
// first). save-baseline accepts an optional filename so a hybrid run can be
// snapshotted without overwriting the lexical baseline every other run
// contract-checks against.
$save_baseline = in_array('save-baseline', $eval_args, true);
$baseline_file = 'baseline.json';
$compare_file = null;
foreach ($eval_args as $a) {
    if (strpos($a, 'compare=') === 0) $compare_file = substr($a, strlen('compare='));
    if (strpos($a, 'save-baseline=') === 0) {
        $save_baseline = true;
        $baseline_file = basename(substr($a, strlen('save-baseline=')));
    }
}

$snapshot = ['mode' => $mode, 'by_group' => [], 'overall' => []];
foreach ($by_group as $group => $g) {
    $snapshot['by_group'][$group] = [
        'n' => $g['n'], 'hit1' => $g['hit1'] / $g['n'], 'hit3' => $g['hit3'] / $g['n'],
        'recall5' => $g['recall5'] / $g['n'], 'mrr' => $g['mrr'] / $g['n'],
    ];
}
$snapshot['overall'] = [
    'n' => $overall['n'], 'hit1' => $overall['hit1'] / $overall['n'], 'hit3' => $overall['hit3'] / $overall['n'],
    'recall5' => $overall['recall5'] / $overall['n'], 'mrr' => $overall['mrr'] / $overall['n'],
];

if ($save_baseline) {
    file_put_contents(__DIR__ . '/' . $baseline_file, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\n[eval] Saved baseline to tests/{$baseline_file}\n";
}

if ($compare_file && file_exists(__DIR__ . '/' . $compare_file)) {
    $base = json_decode(file_get_contents(__DIR__ . '/' . $compare_file), true);
    echo "\n=== Compared to baseline ($compare_file) ===\n";
    printf("%-28s %10s %10s %10s %10s\n", "Group", "Δhit@1", "Δhit@3", "Δrecall@5", "ΔMRR");
    foreach ($snapshot['by_group'] as $group => $g) {
        $b = $base['by_group'][$group] ?? null;
        if (!$b) { printf("%-28s (new group, no baseline)\n", $group); continue; }
        printf(
            "%-28s %+10.3f %+10.3f %+10.3f %+10.3f\n",
            $group, $g['hit1'] - $b['hit1'], $g['hit3'] - $b['hit3'],
            $g['recall5'] - $b['recall5'], $g['mrr'] - $b['mrr']
        );
    }
    $bo = $base['overall'];
    printf(
        "%-28s %+10.3f %+10.3f %+10.3f %+10.3f\n",
        "TOTAL", $snapshot['overall']['hit1'] - $bo['hit1'], $snapshot['overall']['hit3'] - $bo['hit3'],
        $snapshot['overall']['recall5'] - $bo['recall5'], $snapshot['overall']['mrr'] - $bo['mrr']
    );
}

echo "\n";
