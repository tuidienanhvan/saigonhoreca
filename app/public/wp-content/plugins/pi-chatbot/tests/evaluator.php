<?php
if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Evaluator {
    /**
     * Evaluate Router output via AST matching (not string comparison)
     * 
     * @param array $test_case
     * @param array $actual_output
     * @return array
     */
    public static function eval_router($test_case, $actual_output) {
        $score = 0;
        $max = 4;
        $details = [];

        // 1. Intent (exact match)
        $expected_intent = $test_case['expected_intent'] ?? 'none';
        $actual_intent = $actual_output['intent'] ?? 'none';
        if ($actual_intent === $expected_intent) {
            $score++;
            $details['intent'] = 'pass';
        } else {
            $details['intent'] = sprintf('fail (expected: %s, got: %s)', $expected_intent, $actual_intent);
        }

        // 2. Difficulty (exact match)
        $expected_diff = $test_case['expected_difficulty'] ?? 'easy';
        $actual_diff = $actual_output['difficulty'] ?? 'easy';
        if ($actual_diff === $expected_diff) {
            $score++;
            $details['difficulty'] = 'pass';
        } else {
            $details['difficulty'] = sprintf('fail (expected: %s, got: %s)', $expected_diff, $actual_diff);
        }

        // 3. Brand (case-insensitive)
        $expected_brand = $test_case['expected_brand'] ?? null;
        $actual_brand = $actual_output['brand'] ?? null;
        if (strtolower((string)$expected_brand) === strtolower((string)$actual_brand)) {
            $score++;
            $details['brand'] = 'pass';
        } else {
            $details['brand'] = sprintf('fail (expected: %s, got: %s)', $expected_brand ?? 'null', $actual_brand ?? 'null');
        }

        // 4. Keywords (>= 50% overlap of expected in actual)
        $expected_kws = $test_case['expected_keywords'] ?? [];
        $actual_kws = array_merge($actual_output['keywords_vi'] ?? [], $actual_output['keywords_en'] ?? []);
        $actual_kws = array_map('strtolower', $actual_kws);
        
        $match_count = 0;
        foreach ($expected_kws as $kw) {
            $kw = strtolower($kw);
            foreach ($actual_kws as $akw) {
                if (strpos($akw, $kw) !== false || strpos($kw, $akw) !== false) {
                    $match_count++;
                    break;
                }
            }
        }
        
        $kw_ratio = count($expected_kws) > 0 ? $match_count / count($expected_kws) : 1.0;
        if ($kw_ratio >= 0.5 || count($expected_kws) === 0) {
            $score++;
            $details['keywords'] = 'pass';
        } else {
            $details['keywords'] = 'fail (less than 50% match)';
        }

        return [
            'score' => $score,
            'max' => $max,
            'ratio' => $max > 0 ? $score / $max : 0,
            'details' => $details
        ];
    }

    /**
     * Evaluate Generator answer via must_contain/must_not_contain
     * 
     * @param array $test_case
     * @param string $answer
     * @return array
     */
    public static function eval_answer($test_case, $answer) {
        $score = 0;
        $max = 0;
        $details = [];
        $answer_lower = strtolower($answer);

        $must_contain = $test_case['must_contain'] ?? [];
        foreach ($must_contain as $phrase) {
            $max++;
            if (strpos($answer_lower, strtolower($phrase)) !== false) {
                $score++;
                $details["must_contain:{$phrase}"] = 'pass';
            } else {
                $details["must_contain:{$phrase}"] = 'fail';
            }
        }

        $must_not_contain = $test_case['must_not_contain'] ?? [];
        foreach ($must_not_contain as $phrase) {
            $max++;
            if (strpos($answer_lower, strtolower($phrase)) === false) {
                $score++;
                $details["must_not_contain:{$phrase}"] = 'pass';
            } else {
                $details["must_not_contain:{$phrase}"] = 'fail';
            }
        }
        
        $must_contain_cat = $test_case['must_contain_categories'] ?? [];
        foreach ($must_contain_cat as $cat) {
            $max++;
            if (strpos($answer_lower, strtolower($cat)) !== false) {
                $score++;
                $details["must_contain_categories:{$cat}"] = 'pass';
            } else {
                $details["must_contain_categories:{$cat}"] = 'fail (not in text)';
            }
        }

        if ($max === 0) {
            return [
                'score' => 1,
                'max' => 1,
                'ratio' => 1.0,
                'details' => ['none' => 'pass (no criteria)']
            ];
        }

        return [
            'score' => $score,
            'max' => $max,
            'ratio' => $score / $max,
            'details' => $details
        ];
    }

    /**
     * Compute Context Precision@K (RAGAs standard)
     */
    public static function eval_context_precision($retrieved_items, $expected_ids, $k = 5) {
        $top_k = array_slice($retrieved_items, 0, $k);
        if (empty($top_k)) return ['score' => 0.0, 'details' => 'empty context'];

        $total_relevant = 0;
        $sum_precision = 0.0;
        $relevant_count = 0;

        foreach ($top_k as $idx => $item) {
            $rank = $idx + 1;
            $item_id = is_array($item) ? ($item['id'] ?? $item['post_id'] ?? null) : $item;
            $is_relevant = in_array((int)$item_id, array_map('intval', (array)$expected_ids), true);

            if ($is_relevant) {
                $relevant_count++;
                $total_relevant++;
                $precision_at_k = $relevant_count / $rank;
                $sum_precision += $precision_at_k;
            }
        }

        $score = $total_relevant > 0 ? $sum_precision / $total_relevant : 0.0;
        return [
            'score' => round($score, 4),
            'relevant_found' => $total_relevant,
            'top_k_count' => count($top_k)
        ];
    }

    /**
     * Compute Cosine Similarity between two embedding vectors
     */
    public static function eval_semantic_similarity($vecA, $vecB) {
        if (count($vecA) !== count($vecB) || empty($vecA)) return 0.0;
        
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        for ($i = 0; $i < count($vecA); $i++) {
            $dot += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] * $vecA[$i];
            $normB += $vecB[$i] * $vecB[$i];
        }
        if ($normA <= 0 || $normB <= 0) return 0.0;
        return round($dot / (sqrt($normA) * sqrt($normB)), 4);
    }

    /**
     * Track Cost & Efficiency metrics
     */
    public static function eval_cost_efficiency($usage_stats, $pricing = ['input' => 0.15 / 1000000, 'output' => 0.60 / 1000000]) {
        $prompt_tokens = $usage_stats['prompt_tokens'] ?? 0;
        $completion_tokens = $usage_stats['completion_tokens'] ?? 0;
        $cost = ($prompt_tokens * $pricing['input']) + ($completion_tokens * $pricing['output']);

        return [
            'total_tokens' => $prompt_tokens + $completion_tokens,
            'estimated_cost_usd' => round($cost, 6),
            'latency_ms' => $usage_stats['latency_ms'] ?? 0
        ];
    }
}
