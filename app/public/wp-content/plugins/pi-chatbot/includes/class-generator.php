<?php
/**
 * Generator — builds the prompt from retrieval context, calls the LLM7 API
 * (https://api.llm7.io) directly to generate the answer. NO Luna-Proxy — per
 * user request (Luna-Proxy forces an arbitrary model + is an unnecessary
 * secondary dependency).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Generator {

    const SYSTEM_PROMPT = "You are the customer support assistant for Saigon Horeca — a commercial kitchen equipment supplier for restaurants and hotels (Western/Asian ranges, ovens, commercial refrigeration, dishwashing equipment...).\n"
        . "Answer ONLY from the REFERENCE INFORMATION provided below — never invent products, prices, or specs that aren't in it.\n"
        . "If the customer asks a GENERAL question like \"what do you sell\" or \"what do you have\" with NO specific reference information available: you may briefly introduce the main product CATEGORIES per the company description above (Western/Asian ranges, ovens, commercial refrigeration, dishwashing equipment...) then ask which category they'd like to see — this is a general company description, not inventing a specific product.\n"
        . "If the customer asks a SPECIFIC question (product name/brand/model) and the reference information isn't enough: say clearly that you don't have that information yet and suggest contacting the hotline — never guess.\n"
        . "If the REFERENCE INFORMATION includes a \"Pricing policy\" entry: relay that content as-is (pricing depends on configuration, invite them to contact the hotline) — NEVER estimate or invent a specific price figure, even if the customer presses repeatedly or says \"just a rough number is fine\".\n"
        . "Answer briefly, in English, in a friendly and professional advisory tone. Do not use Markdown tables or the |, #, ** characters — plain prose or simple dashes only, since the chat UI doesn't render Markdown.";

    /**
     * @param string $question
     * @param array  $context Result from Pi_Chatbot_Retriever::search()
     * @return array{answer:string, sources:array}|WP_Error
     */
    public static function answer($question, $context) {
        $settings = pi_chatbot_settings();

        if (empty($context)) {
            $context_text = "(No matching product/page found in the internal data.)";
        } else {
            $lines = [];
            foreach ($context as $item) {
                $lines[] = sprintf(
                    "- %s%s%s — %s\n  %s",
                    $item['title'],
                    $item['brand'] ? " (Brand: {$item['brand']})" : '',
                    $item['category'] ? " [{$item['category']}]" : '',
                    $item['url'],
                    $item['excerpt']
                );
            }
            $context_text = implode("\n", $lines);
        }

        $user_prompt = "REFERENCE INFORMATION:\n{$context_text}\n\nCUSTOMER QUESTION: {$question}";

        $body = [
            'model'    => $settings['model'],
            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => $user_prompt],
            ],
            'temperature' => 0.3,
            // max_tokens guards against infinite repetition (a real loop was
            // observed with gpt-oss:20b: repeating "steam oven range" hundreds
            // of times) — uncapped, that both wastes time (timeout risk) and
            // gets cached verbatim by the REST layer.
            //
            // 2000, NOT 700: gpt-oss:20b is a REASONING model — it emits a
            // `reasoning` field (~700+ chars) BEFORE `content`, and both eat
            // into max_tokens. Capping at 700 let the reasoning eat nearly the
            // whole budget, occasionally leaving content EMPTY -> REST
            // returned intermittent 502s (a real bug caught during
            // verification: the same question sometimes worked, sometimes
            // 502'd). 2000 leaves enough room for reasoning + the actual
            // answer; infinite-repetition is now handled by
            // looks_degenerate() instead.
            'max_tokens' => 2000,
        ];

        $response = Pi_Chatbot_LLM_Client::call(
            $body, $settings['llm7_keys'], self::TIMEOUT_SECONDS, self::MAX_KEY_ATTEMPTS, 'pi-chatbot-generator'
        );
        if (is_wp_error($response)) return $response;

        $answer = Pi_Chatbot_LLM_Client::content($response);
        if ($answer === '') {
            return new WP_Error('pi_chatbot_empty', 'Did not receive an answer from the model.');
        }

        // Extra safeguard: the free-tier model sometimes repeats infinitely
        // even with max_tokens capped (repeating WITHIN the granted token
        // budget) — detected by comparing text length against its length
        // after collapsing consecutive repeated phrases; too large a gap
        // means degenerate output. Do NOT return a degenerate answer (and do
        // NOT cache it — see class-rest.php).
        if (self::looks_degenerate($answer)) {
            return new WP_Error('pi_chatbot_degenerate', 'The model produced an invalid answer (abnormal repetition).');
        }

        $answer = self::strip_markdown($answer);

        return [
            'answer'  => $answer,
            'sources' => array_map(function ($item) {
                return ['title' => $item['title'], 'url' => $item['url']];
            }, $context),
        ];
    }

    /**
     * Detects a degenerate answer that repeats one phrase over and over
     * ("steam oven range steam oven range...") — a real error seen while
     * testing with the free-tier gpt-oss:20b model. Simple heuristic: if one
     * word makes up more than 25% of a long-enough answer, it's almost
     * certainly abnormal repetition, not natural phrasing.
     */
    private static function looks_degenerate($text) {
        $words = preg_split('/\s+/u', trim($text));
        $total = count($words);
        if ($total < 20) return false; // too short a sample to judge reliably, avoid false positives
        $counts = array_count_values(array_map('mb_strtolower', $words));
        arsort($counts);
        $top_count = reset($counts);
        return ($top_count / $total) > 0.25;
    }

    /**
     * Second safety net for the Markdown ban in SYSTEM_PROMPT — the prompt is
     * a SOFT guardrail (the model can still drift even at low temperature),
     * so strip raw syntax here instead of building a Markdown renderer on the
     * client (the client uses plain textContent, not innerHTML — that
     * security invariant is kept on purpose, not traded away for a display
     * feature). Only handles simple INLINE syntax; a stray | table could
     * still slip through if the model deliberately breaks the rule — but the
     * prompt already forbids it clearly, and this is just a backstop.
     */
    private static function strip_markdown($text) {
        $text = preg_replace('/\*\*(.+?)\*\*/u', '$1', $text);   // **bold** -> bold
        $text = preg_replace('/^#{1,6}\s*/mu', '', $text);        // # Heading -> Heading
        return $text;
    }

    /** Short timeout + a capped number of key attempts per request — keeps a
     *  PHP-FPM worker from being held for more than MAX_KEY_ATTEMPTS x
     *  TIMEOUT_SECONDS seconds (previously 30s x N keys with no cap, which
     *  could hold a worker for a long time if an admin configured many keys
     *  and all of them were slow/failing). */
    const TIMEOUT_SECONDS = 20;
    const MAX_KEY_ATTEMPTS = 2;
}
