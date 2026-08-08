<?php
/**
 * WP-CLI eval-file script
 * Usage: wp eval-file tests/eval-full.php [save-baseline=file] [compare=file]
 */
if (!defined('ABSPATH')) exit;

$harness_path = dirname(__FILE__) . '/lib/harness.php';
if (file_exists($harness_path)) {
    require_once $harness_path;
}

require_once dirname(__FILE__) . '/evaluator.php';

$options = [];
global $argv;
if (isset($argv)) {
    foreach ($argv as $arg) {
        if (strpos($arg, '=') !== false) {
            list($key, $val) = explode('=', $arg, 2);
            $options[$key] = $val;
        } else {
            $options[$arg] = true;
        }
    }
}

$dataset_path = dirname(__FILE__) . '/golden-dataset.json';
if (!file_exists($dataset_path)) {
    WP_CLI::error("Dataset not found: $dataset_path");
}

$dataset = json_decode(file_get_contents($dataset_path), true);
if (!$dataset) {
    WP_CLI::error("Invalid JSON in dataset");
}

$results = [];
$total_router_score = 0;
$total_router_max = 0;
$total_gen_score = 0;
$total_gen_max = 0;
$categories = [];

WP_CLI::log("Starting full pipeline evaluation on " . count($dataset) . " test cases...");

$router_enabled = class_exists('Pi_Chatbot_Router');
if (!$router_enabled) {
    WP_CLI::warning("Pi_Chatbot_Router not found. Skipping Router evaluation.");
}

foreach ($dataset as $test_case) {
    $id = $test_case['id'];
    $cat = $test_case['category'];
    if (!isset($categories[$cat])) {
        $categories[$cat] = ['router_score'=>0, 'router_max'=>0, 'gen_score'=>0, 'gen_max'=>0, 'count'=>0];
    }
    
    $categories[$cat]['count']++;
    $result = ['id' => $id, 'question' => $test_case['question']];

    // Router eval
    if ($router_enabled) {
        try {
            $router_output = Pi_Chatbot_Router::enrich($test_case['question']);
            $router_eval = Pi_Chatbot_Evaluator::eval_router($test_case, $router_output);
            
            $result['router_score'] = $router_eval['score'];
            $result['router_max'] = $router_eval['max'];
            $result['router_ratio'] = $router_eval['ratio'];
            
            $total_router_score += $router_eval['score'];
            $total_router_max += $router_eval['max'];
            $categories[$cat]['router_score'] += $router_eval['score'];
            $categories[$cat]['router_max'] += $router_eval['max'];
        } catch (Exception $e) {
            WP_CLI::warning("Router exception on $id: " . $e->getMessage());
        }
    }

    // Generator eval
    if (class_exists('Pi_Chatbot_Generator')) {
        try {
            $intent = $router_output['intent'] ?? 'product';
            $difficulty = $router_output['difficulty'] ?? 'easy';
            $context = [];
            if ($intent !== 'none' && class_exists('Pi_Chatbot_Retriever')) {
                $context = Pi_Chatbot_Retriever::search($test_case['question']);
            }
            $gen_opts = ['intent' => $intent, 'difficulty' => $difficulty];
            $gen_result = Pi_Chatbot_Generator::answer($test_case['question'], $context, [], $gen_opts);
            $answer_text = is_wp_error($gen_result) ? '' : ($gen_result['answer'] ?? '');
            $gen_eval = Pi_Chatbot_Evaluator::eval_answer($test_case, $answer_text);
            
            $result['gen_score'] = $gen_eval['score'];
            $result['gen_max'] = $gen_eval['max'];
            $result['gen_ratio'] = $gen_eval['ratio'];
            $result['answer_preview'] = mb_substr($answer_text, 0, 100);
            
            $total_gen_score += $gen_eval['score'];
            $total_gen_max += $gen_eval['max'];
            $categories[$cat]['gen_score'] += $gen_eval['score'];
            $categories[$cat]['gen_max'] += $gen_eval['max'];
        } catch (Exception $e) {
            WP_CLI::warning("Generator exception on $id: " . $e->getMessage());
        }
    }
    
    $results[] = $result;
}

// Summary
WP_CLI::success("Evaluation complete!");
if ($total_router_max > 0) {
    WP_CLI::log(sprintf("Overall Router Accuracy: %.2f%% (%d/%d)", ($total_router_score/$total_router_max)*100, $total_router_score, $total_router_max));
}
if ($total_gen_max > 0) {
    WP_CLI::log(sprintf("Overall Generator Accuracy: %.2f%% (%d/%d)", ($total_gen_score/$total_gen_max)*100, $total_gen_score, $total_gen_max));
}

WP_CLI::log("\nPer-Category Breakdown:");
foreach ($categories as $cat => $stats) {
    $r_acc = $stats['router_max'] > 0 ? ($stats['router_score']/$stats['router_max'])*100 : 0;
    $g_acc = $stats['gen_max'] > 0 ? ($stats['gen_score']/$stats['gen_max'])*100 : 0;
    WP_CLI::log(sprintf("- %s (%d items): Router %.2f%% | Gen %.2f%%", $cat, $stats['count'], $r_acc, $g_acc));
}

if (isset($options['save-baseline'])) {
    $file = $options['save-baseline'] === true ? 'baseline.json' : $options['save-baseline'];
    file_put_contents(dirname(__FILE__) . '/' . $file, json_encode($results, JSON_PRETTY_PRINT));
    WP_CLI::success("Saved baseline to $file");
}

if (isset($options['compare'])) {
    $file = $options['compare'];
    $path = dirname(__FILE__) . '/' . $file;
    if (file_exists($path)) {
        WP_CLI::log("Comparing with baseline $file...");
        $baseline = json_decode(file_get_contents($path), true);
        if ($baseline) {
            $base_map = [];
            foreach ($baseline as $b) {
                $base_map[$b['id']] = $b;
            }
            $diffs = 0;
            foreach ($results as $r) {
                if (isset($base_map[$r['id']])) {
                    $b = $base_map[$r['id']];
                    if (($r['router_score'] ?? 0) !== ($b['router_score'] ?? 0)) {
                        WP_CLI::log(sprintf("  [%s] Router score changed: %d -> %d", $r['id'], $b['router_score'] ?? 0, $r['router_score']));
                        $diffs++;
                    }
                    if (($r['gen_score'] ?? 0) !== ($b['gen_score'] ?? 0)) {
                        WP_CLI::log(sprintf("  [%s] Generator score changed: %d -> %d", $r['id'], $b['gen_score'] ?? 0, $r['gen_score']));
                        $diffs++;
                    }
                }
            }
            if ($diffs === 0) {
                WP_CLI::success("No regressions found compared to baseline.");
            }
        }
    } else {
        WP_CLI::warning("Compare file not found: $path");
    }
}
