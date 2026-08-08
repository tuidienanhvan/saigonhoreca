<?php
/**
 * Context Budget Planner for Pi Chatbot.
 *
 * Implements GSSC (Gather, Select, Structure, Compress) token budget allocation across
 * 5 context zones (System, History, Evidence, User, Output) to prevent context rot
 * and token truncation.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Context_Budget {

    /**
     * Budget allocation ratios across 5 context zones for an ~8K/16K token context window.
     */
    const ZONES = [
        'system'   => 0.25, // System prompt (Role, Policies, Security)
        'history'  => 0.10, // Conversation history
        'evidence' => 0.45, // REFERENCE_DATA from catalog (largest allocation)
        'user'     => 0.08, // Customer question + language note
        'output'   => 0.12, // Reserved output tokens
    ];

    /**
     * Default max tokens allocated for the EVIDENCE (REFERENCE_DATA) zone.
     */
    const DEFAULT_EVIDENCE_MAX_TOKENS = 1800;

    /**
     * Default max tokens allocated for HISTORY zone.
     */
    const DEFAULT_HISTORY_MAX_TOKENS = 400;

    /**
     * Estimates the number of tokens in a string.
     * Uses word count + language awareness multiplier (Vietnamese ~1.5, English ~1.3).
     *
     * @param string $text
     * @return int Estimated token count
     */
    public static function estimate_tokens($text) {
        $clean_text = trim((string) $text);
        if ($clean_text === '') return 0;

        $spaces = preg_match_all('/\s+/u', $clean_text, $m);
        $word_count = $spaces ? $spaces + 1 : 1;

        $has_diacritics = preg_match('/[\x{00C0}-\x{024F}\x{1E00}-\x{1EFF}]/u', $clean_text);
        $multiplier = $has_diacritics ? 1.5 : 1.3;

        return (int) ceil($word_count * $multiplier);
    }

    /**
     * Selects and fits catalog context rows within the EVIDENCE token budget.
     * Iterates through ranked context rows and trims items that exceed max_tokens.
     *
     * @param array $context_rows Array of context rows/snippets from Retriever
     * @param int   $max_tokens   Maximum allowed tokens for evidence zone
     * @return array Fitted array of context rows
     */
    public static function fit_context($context_rows, $max_tokens = self::DEFAULT_EVIDENCE_MAX_TOKENS) {
        if (empty($context_rows) || !is_array($context_rows)) {
            return [];
        }

        $fitted = [];
        $total_tokens = 0;

        foreach ($context_rows as $row) {
            $snippet_text = '';
            if (is_string($row)) {
                $snippet_text = $row;
            } elseif (is_array($row)) {
                $excerpt = $row['excerpt'] ?? $row['snippet'] ?? '';
                $snippet_text = ($row['title'] ?? '') . ' ' . $excerpt . ' ' . ($row['url'] ?? '');
            }

            $tokens = self::estimate_tokens($snippet_text);

            if ($total_tokens + $tokens <= $max_tokens) {
                $fitted[] = $row;
                $total_tokens += $tokens;
            } else {
                // Excerpt trimming based strictly on remaining token budget
                $remaining_tokens = max(0, $max_tokens - $total_tokens);
                if ($remaining_tokens > 20) {
                    $max_chars = (int) ($remaining_tokens * 3);
                    if (is_array($row)) {
                        $excerpt_key = isset($row['excerpt']) ? 'excerpt' : 'snippet';
                        $row[$excerpt_key] = mb_substr($row[$excerpt_key] ?? '', 0, $max_chars) . '...';
                    }
                    $fitted[] = $row;
                }
                break;
            }
        }

        return $fitted;
    }

    /**
     * Fit conversation history within history token budget.
     *
     * @param array $history
     * @param int   $max_tokens
     * @return array
     */
    public static function fit_history($history, $max_tokens = self::DEFAULT_HISTORY_MAX_TOKENS) {
        if (!is_array($history)) return [];
        $fitted_reversed = [];
        $total = 0;

        for ($i = count($history) - 1; $i >= 0; $i--) {
            $turn = $history[$i];
            $text = $turn['text'] ?? ($turn['content'] ?? '');
            $tokens = self::estimate_tokens($text);

            if ($total + $tokens > $max_tokens) break;
            $fitted_reversed[] = $turn;
            $total += $tokens;
        }

        return array_reverse($fitted_reversed);
    }
}
