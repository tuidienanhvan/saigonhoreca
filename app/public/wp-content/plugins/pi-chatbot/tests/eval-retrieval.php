<?php
/**
 * eval-retrieval.php — Bộ đo hồi quy cho Pi_Chatbot_Retriever::search().
 *
 * Chạy 60-100 câu hỏi thật (tests/golden-queries.json), so kết quả trả về với
 * ID sản phẩm đúng đã xác minh tay. Báo cáo hit@1/hit@3/recall@5/MRR THEO TỪNG
 * NHÓM (không chỉ số tổng — số tổng che mất chỗ hỏng, bài học rút ra từ đợt
 * đánh giá lần trước chỉ dùng 4 câu và không kết luận được gì).
 *
 * Chạy (eval-file chỉ nhận positional arg, KHÔNG nhận --flag — WP-CLI tự
 * chặn --flag lạ trước khi vào file):
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php save-baseline
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php compare=baseline.json
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Phải chạy qua `wp eval-file`, không chạy PHP CLI trực tiếp.\n");
    exit(1);
}

$golden_path = __DIR__ . '/golden-queries.json';
if (!file_exists($golden_path)) {
    fwrite(STDERR, "Không tìm thấy $golden_path\n");
    exit(1);
}
$golden = json_decode(file_get_contents($golden_path), true);
if (!is_array($golden)) {
    fwrite(STDERR, "golden-queries.json không parse được JSON hợp lệ.\n");
    exit(1);
}

// ── Chạy retrieval + tính metric cho từng câu ──────────────────────────────

function pi_eval_metrics_for_query($expected_ids, $result_post_ids) {
    $expected = array_map('intval', $expected_ids);
    $got = array_map('intval', $result_post_ids);

    $hit1 = (!empty($got) && in_array($got[0], $expected, true)) ? 1 : 0;
    $top3 = array_slice($got, 0, 3);
    $hit3 = (array_intersect($top3, $expected)) ? 1 : 0;
    $top5 = array_slice($got, 0, 5);
    $recall5 = $expected ? (count(array_intersect($top5, $expected)) / count($expected)) : 0.0;

    $mrr = 0.0;
    foreach ($got as $rank => $pid) {
        if (in_array($pid, $expected, true)) {
            $mrr = 1.0 / ($rank + 1);
            break;
        }
    }

    // Nhóm "tán gẫu / ngoài phạm vi" — expected_ids rỗng nghĩa là "không nên trả
    // sản phẩm nào". Coi là "đúng" nếu retrieval trả rỗng hoặc rất ít.
    if (empty($expected)) {
        $ok = empty($got) ? 1 : 0;
        return ['hit1' => $ok, 'hit3' => $ok, 'recall5' => $ok, 'mrr' => $ok, 'is_negative' => true];
    }

    return ['hit1' => $hit1, 'hit3' => $hit3, 'recall5' => $recall5, 'mrr' => $mrr, 'is_negative' => false];
}

$by_group = [];
$rows = [];

foreach ($golden as $item) {
    $q = $item['query'];
    $group = $item['group'];
    $expected = $item['relevant_ids'] ?? [];

    $results = Pi_Chatbot_Retriever::search($q, 6);
    $got_ids = array_map(function ($r) { return $r['post_id']; }, $results);

    $m = pi_eval_metrics_for_query($expected, $got_ids);
    $m['query'] = $q;
    $m['expected'] = $expected;
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

// ── Báo cáo ─────────────────────────────────────────────────────────────

echo "\n=== pi-chatbot retrieval eval — " . count($golden) . " câu, " . count($by_group) . " nhóm ===\n\n";
printf("%-28s %5s %8s %8s %10s %8s\n", "Nhóm", "N", "hit@1", "hit@3", "recall@5", "MRR");
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
    "TỔNG", $overall['n'], $overall['hit1'] / $overall['n'], $overall['hit3'] / $overall['n'],
    $overall['recall5'] / $overall['n'], $overall['mrr'] / $overall['n']
);

// Liệt kê câu trượt (hit@3 = 0, không phải nhóm negative) — chỗ cần nhìn kỹ nhất
echo "\n--- Câu trượt (hit@3 = 0) ---\n";
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
if ($miss_count > 20) echo "  ... và " . ($miss_count - 20) . " câu khác\n";
if ($miss_count === 0) echo "  (không có)\n";

// ── Lưu / so sánh baseline ──────────────────────────────────────────────

// `wp eval-file <file> <arg>...` — WP-CLI đưa các positional arg sau tên file
// vào biến $args trong scope của file (không phải $argv/$_SERVER).
$eval_args = isset($args) && is_array($args) ? $args : [];
$save_baseline = in_array('save-baseline', $eval_args, true);
$compare_file = null;
foreach ($eval_args as $a) {
    if (strpos($a, 'compare=') === 0) $compare_file = substr($a, strlen('compare='));
}

$snapshot = ['by_group' => [], 'overall' => []];
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
    file_put_contents(__DIR__ . '/baseline.json', json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\n[eval] Đã lưu baseline vào tests/baseline.json\n";
}

if ($compare_file && file_exists(__DIR__ . '/' . $compare_file)) {
    $base = json_decode(file_get_contents(__DIR__ . '/' . $compare_file), true);
    echo "\n=== So với baseline ($compare_file) ===\n";
    printf("%-28s %10s %10s %10s %10s\n", "Nhóm", "Δhit@1", "Δhit@3", "Δrecall@5", "ΔMRR");
    foreach ($snapshot['by_group'] as $group => $g) {
        $b = $base['by_group'][$group] ?? null;
        if (!$b) { printf("%-28s (nhóm mới, không có baseline)\n", $group); continue; }
        printf(
            "%-28s %+10.3f %+10.3f %+10.3f %+10.3f\n",
            $group, $g['hit1'] - $b['hit1'], $g['hit3'] - $b['hit3'],
            $g['recall5'] - $b['recall5'], $g['mrr'] - $b['mrr']
        );
    }
    $bo = $base['overall'];
    printf(
        "%-28s %+10.3f %+10.3f %+10.3f %+10.3f\n",
        "TỔNG", $snapshot['overall']['hit1'] - $bo['hit1'], $snapshot['overall']['hit3'] - $bo['hit3'],
        $snapshot['overall']['recall5'] - $bo['recall5'], $snapshot['overall']['mrr'] - $bo['mrr']
    );
}

echo "\n";
