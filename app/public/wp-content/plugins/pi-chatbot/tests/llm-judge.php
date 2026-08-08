<?php
/**
 * Usage: wp eval-file tests/llm-judge.php [dataset=golden-dataset.json] [limit=10]
 */
if (!defined('ABSPATH')) exit;

$options = [];
global $argv;
if (isset($argv)) {
    foreach ($argv as $arg) {
        if (strpos($arg, '=') !== false) {
            list($key, $val) = explode('=', $arg, 2);
            $options[$key] = $val;
        }
    }
}

if (!class_exists('Pi_Chatbot_LLM_Client')) {
    WP_CLI::error("Pi_Chatbot_LLM_Client not found.");
}

$dataset_file = $options['dataset'] ?? 'golden-dataset.json';
$dataset_path = dirname(__FILE__) . '/' . $dataset_file;

if (!file_exists($dataset_path)) {
    WP_CLI::error("Dataset not found: $dataset_path");
}

$dataset = json_decode(file_get_contents($dataset_path), true);
$limit = isset($options['limit']) ? (int)$options['limit'] : count($dataset);
$dataset = array_slice($dataset, 0, $limit);

WP_CLI::log("Starting LLM Judge evaluation on " . count($dataset) . " test cases. This consumes LLM quota.");


$total_scores = ['Relevance' => 0, 'Faithfulness' => 0, 'Completeness' => 0, 'Tone' => 0, 'Language' => 0];
$count = 0;

foreach ($dataset as $test_case) {
    WP_CLI::log("Evaluating TC: {$test_case['id']} - {$test_case['question']}");
    
    // Assume generator produces an answer
    $router_output = class_exists('Pi_Chatbot_Router') ? Pi_Chatbot_Router::enrich($test_case['question']) : null;
    $intent = $router_output['intent'] ?? 'product';
    $context = [];
    if ($intent !== 'none' && class_exists('Pi_Chatbot_Retriever')) {
        $context = Pi_Chatbot_Retriever::search($test_case['question']);
    }
    $gen_opts = ['intent' => $intent, 'difficulty' => $router_output['difficulty'] ?? 'easy'];
    $gen_result = class_exists('Pi_Chatbot_Generator') ? Pi_Chatbot_Generator::answer($test_case['question'], $context, [], $gen_opts) : null;
    $answer_text = (is_array($gen_result) && isset($gen_result['answer'])) ? $gen_result['answer'] : 'No answer generated';

    $prompt = "You are an expert judge evaluating a chatbot's answer.
Question: {$test_case['question']}
Answer: {$answer_text}

Score the answer on 5 dimensions from 0 to 10. Output ONLY JSON in this format:
{\"Relevance\": 0, \"Faithfulness\": 0, \"Completeness\": 0, \"Tone\": 0, \"Language\": 0}";

    try {
        $settings = pi_chatbot_settings();
        $body = [
            'model' => $settings['model'],
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.1,
            'max_tokens' => 200,
        ];
        $response = Pi_Chatbot_LLM_Client::call($body, $settings['llm7_keys'], 30, 1, 'pi-chatbot-judge');
        if (is_wp_error($response)) {
            WP_CLI::warning("API Error on {$test_case['id']}: " . $response->get_error_message());
            continue;
        }
        $response_text = Pi_Chatbot_LLM_Client::content($response);
        // Extract JSON
        if (preg_match('/\{.*\}/s', $response_text, $matches)) {
            $scores = json_decode($matches[0], true);
            if ($scores) {
                foreach (['Relevance', 'Faithfulness', 'Completeness', 'Tone', 'Language'] as $dim) {
                    $total_scores[$dim] += $scores[$dim] ?? 0;
                }
                $count++;
                WP_CLI::log("Scores: " . json_encode($scores));
            } else {
                WP_CLI::warning("Failed to parse JSON: $response_text");
            }
        } else {
            WP_CLI::warning("No JSON found: " . mb_substr($response_text, 0, 100));
        }
    } catch (Exception $e) {
        WP_CLI::warning("API Error: " . $e->getMessage());
    }
}

if ($count > 0) {
    WP_CLI::success("LLM Judge Evaluation Complete!");
    WP_CLI::log("Average Scores:");
    foreach ($total_scores as $dim => $total) {
        WP_CLI::log(sprintf("- %s: %.2f/10", $dim, $total / $count));
    }
} else {
    WP_CLI::warning("No evaluations completed successfully.");
}
