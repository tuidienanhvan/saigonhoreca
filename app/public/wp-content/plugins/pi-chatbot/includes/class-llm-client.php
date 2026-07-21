<?php
/**
 * LLM Client — calls the LLM7 API, shared by the Generator (composing answers)
 * and the Router (deciding whether retrieval enrichment is needed). Split out
 * of Generator so each caller can configure its own timeout/retry count —
 * Router needs a single call with a short timeout (a secondary safety net, not
 * worth trading extra wait time for retries); Generator needs 2 attempts with a
 * longer timeout (the main path, where a higher success rate is worth the
 * trade-off).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_LLM_Client {
    const API_URL = 'https://api.llm7.io/v1/chat/completions';

    /**
     * Round-robins through up to $max_attempts LLM7 keys — if a key fails
     * (quota exhausted/timeout), try the next one. Logic is unchanged from what
     * previously lived in Generator::call_llm7(); only the timeout/max_attempts
     * are now parameterized instead of being Generator's own hardcoded
     * constants.
     *
     * @param array    $body         Payload sent to LLM7 (model, messages, ...).
     * @param string[] $keys         List of keys, tried in order.
     * @param int      $timeout      Seconds, applied to EACH call.
     * @param int      $max_attempts Maximum number of keys to try.
     * @param string   $log_prefix   Log prefix to distinguish Generator/Router in error_log.
     * @return array|WP_Error
     */
    public static function call($body, $keys, $timeout, $max_attempts, $log_prefix = 'pi-chatbot') {
        $last_error = null;
        $attempts = 0;
        foreach ($keys as $key) {
            if (!$key) continue;
            if ($attempts >= $max_attempts) break;
            $attempts++;

            $res = wp_remote_post(self::API_URL, [
                'timeout' => $timeout,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $key,
                ],
                'body' => wp_json_encode($body),
            ]);

            if (is_wp_error($res)) {
                $last_error = $res;
                error_log("[{$log_prefix}] LLM7 request failed (key {$attempts}/{$max_attempts}): " . $res->get_error_message());
                continue;
            }

            $code = wp_remote_retrieve_response_code($res);
            $raw  = wp_remote_retrieve_body($res);
            $data = json_decode($raw, true);

            if ($code >= 200 && $code < 300 && is_array($data) && !empty($data['choices'])) {
                return $data;
            }

            error_log("[{$log_prefix}] LLM7 returned HTTP error {$code} (key {$attempts}/{$max_attempts}): " . substr((string) $raw, 0, 500));
            $last_error = new WP_Error('pi_chatbot_llm7_error', 'LLM7 returned an error (HTTP ' . $code . ').', $raw);
        }

        return $last_error ?: new WP_Error('pi_chatbot_no_key', 'No LLM7 API key configured.');
    }

    /** Extracts the content text from a raw response — shared helper to avoid repeating the same ?? chain in multiple places. */
    public static function content($response) {
        return trim((string) ($response['choices'][0]['message']['content'] ?? ''));
    }
}
