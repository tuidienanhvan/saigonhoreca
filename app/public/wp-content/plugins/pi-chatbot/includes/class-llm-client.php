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
            // Counted here, not once per logical caller — a request that round-robins
            // through 2 failing keys before a 3rd succeeds really did spend quota on
            // all 3, so all 3 count against the daily budget (see class-budget.php).
            Pi_Chatbot_Budget::record_call();

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

    /**
     * Same key-rotation contract as call(), but relays each token DELTA to
     * $on_delta as it arrives instead of waiting for the full response — this
     * is what lets class-rest.php's ask_stream() start showing the customer
     * text within ~1-2s instead of only after the whole answer (up to ~9.6s
     * measured, see the Log::stats_latency() docblock) has finished
     * generating. wp_remote_post() (used by call() above) buffers the entire
     * response before returning — WP's HTTP API has no streaming callback —
     * so this uses raw cURL with CURLOPT_WRITEFUNCTION instead, the only way
     * to read bytes as they arrive over PHP-FPM.
     *
     * Only ever called from a path already proven safe to stream (see
     * ask_stream()'s docblock — context must be empty, so the faithfulness
     * check can never apply) — this method itself has no opinion on that; it
     * only relays tokens.
     *
     * Key rotation is CONSERVATIVE compared to call(): once even one delta
     * has been relayed to $on_delta, that content is already on its way to
     * the customer and cannot be un-sent, so a later failure on the SAME key
     * returns whatever was relayed instead of silently trying a 2nd key
     * (which would look like the customer's answer restarting from
     * scratch). Rotation to the next key only happens when a key fails
     * BEFORE any content was relayed (a bad key, a connection error, a
     * non-2xx status) — the same case call()'s retry already covers.
     *
     * @param array    $body    Same shape as call() — 'stream' => true is
     *   added here, callers must not set it themselves.
     * @param callable $on_delta function(string $text): void, called once per
     *   token chunk as it streams in.
     * @return string|WP_Error The full assembled answer text, or WP_Error if
     *   nothing could be streamed from any key.
     */
    /**
     * Test hook — replaces the real cURL transport below with a fake one, for
     * the same reason tests/lib/harness.php's pi_http_stub() exists: a test
     * suite must run deterministically, offline, and without spending real
     * LLM7 quota. pi_http_stub() cannot cover this method — it only
     * intercepts wp_remote_post() via the pre_http_request filter, and raw
     * cURL (required here — see call_streaming()'s docblock for why
     * wp_remote_post can't stream) never goes through that filter at all.
     *
     * Signature: function(string $key, array $body, int $timeout, callable
     * $on_delta): array{http_code:?int, full_text:string, relayed_any:bool,
     * curl_errno:int, curl_error:string}. Null (the default) uses the real
     * transport.
     */
    public static $stream_transport = null;

    public static function call_streaming($body, $keys, $timeout, $max_attempts, callable $on_delta, $log_prefix = 'pi-chatbot') {
        if (self::$stream_transport === null && !function_exists('curl_init')) {
            return new WP_Error('pi_chatbot_no_curl', 'The cURL extension is required for streaming.');
        }

        $body['stream'] = true;
        $last_error = null;
        $attempts = 0;

        foreach ($keys as $key) {
            if (!$key) continue;
            if ($attempts >= $max_attempts) break;
            $attempts++;
            Pi_Chatbot_Budget::record_call();

            $transport = self::$stream_transport ?: [self::class, 'real_stream_transport'];
            $outcome = $transport($key, $body, $timeout, $on_delta);
            $http_code   = $outcome['http_code']   ?? null;
            $full_text   = $outcome['full_text']   ?? '';
            $relayed_any = $outcome['relayed_any'] ?? false;
            $curl_errno  = $outcome['curl_errno']  ?? 0;
            $curl_error  = $outcome['curl_error']  ?? '';

            if ($curl_errno) {
                error_log("[{$log_prefix}] LLM7 streaming cURL error (key {$attempts}/{$max_attempts}): {$curl_error}");
                if ($relayed_any) return $full_text; // already shown to the customer — cannot retry cleanly
                $last_error = new WP_Error('pi_chatbot_stream_curl', $curl_error);
                continue;
            }
            if ($http_code !== null && ($http_code < 200 || $http_code >= 300)) {
                error_log("[{$log_prefix}] LLM7 streaming HTTP error {$http_code} (key {$attempts}/{$max_attempts})");
                $last_error = new WP_Error('pi_chatbot_stream_http', 'LLM7 returned an error (HTTP ' . $http_code . ').');
                continue; // nothing relayed on a non-2xx response — safe to retry the next key
            }
            if ($full_text === '') {
                $last_error = new WP_Error('pi_chatbot_stream_empty', 'Empty stream.');
                continue;
            }
            return $full_text;
        }

        return $last_error ?: new WP_Error('pi_chatbot_no_key', 'No LLM7 API key configured.');
    }

    /** The real transport — raw cURL with CURLOPT_WRITEFUNCTION, the only way
     *  to read bytes as they arrive over PHP-FPM (wp_remote_post() buffers
     *  the entire response before returning). Parses the OpenAI-compatible
     *  SSE framing LLM7 uses: lines starting "data: ", a JSON object per
     *  line, terminated by a literal "data: [DONE]" line. */
    private static function real_stream_transport($key, $body, $timeout, callable $on_delta) {
        $buffer = '';
        $full_text = '';
        $http_code = null;
        $relayed_any = false;

        $ch = curl_init(self::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $key,
            ],
            CURLOPT_POSTFIELDS     => wp_json_encode($body),
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => min(10, $timeout),
            // A response body is expected — CURLOPT_RETURNTRANSFER stays
            // default/false, since CURLOPT_WRITEFUNCTION below is what
            // actually consumes the body incrementally instead.
            CURLOPT_WRITEFUNCTION  => function ($ch_res, $chunk) use (&$buffer, &$full_text, &$http_code, &$relayed_any, $on_delta) {
                if ($http_code === null) {
                    $code = curl_getinfo($ch_res, CURLINFO_HTTP_CODE);
                    if ($code) $http_code = (int) $code;
                }
                // Non-2xx: LLM7 returned a plain error body (JSON or text),
                // not an SSE stream — accumulate for logging, never call
                // $on_delta with error-page content.
                if ($http_code !== null && ($http_code < 200 || $http_code >= 300)) {
                    $buffer .= $chunk;
                    return strlen($chunk);
                }

                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);
                    if ($line === '' || strpos($line, 'data:') !== 0) continue;
                    $data = trim(substr($line, 5));
                    if ($data === '[DONE]') continue;
                    $parsed = json_decode($data, true);
                    $delta = $parsed['choices'][0]['delta']['content'] ?? '';
                    if ($delta !== '') {
                        $full_text .= $delta;
                        $relayed_any = true;
                        $on_delta($delta);
                    }
                }
                return strlen($chunk);
            },
        ]);

        curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        return [
            'http_code'   => $http_code,
            'full_text'   => $full_text,
            'relayed_any' => $relayed_any,
            'curl_errno'  => $curl_errno,
            'curl_error'  => $curl_error,
        ];
    }

    /** Extracts the content text from a raw response — shared helper to avoid repeating the same ?? chain in multiple places. */
    public static function content($response) {
        return trim((string) ($response['choices'][0]['message']['content'] ?? ''));
    }

    /**
     * Defensive JSON extraction from a model reply. Originally lived privately in
     * Pi_Chatbot_Router; moved here once the Reranker and the Generator's
     * faithfulness check needed the exact same behaviour — three copies of a
     * parser is three places for a fix to be applied to only two of them.
     *
     * Models routinely wrap JSON in a ```json fence or pad it with a sentence of
     * commentary, so this strips the fence and then walks forward to the first
     * BALANCED {...} block instead of handing the raw string to json_decode()
     * (which fails outright on any surrounding text).
     *
     * @return array|null null when nothing parseable was found.
     */
    public static function extract_json($text) {
        $text = trim((string) $text);
        if ($text === '') return null;
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        $start = strpos($text, '{');
        if ($start === false) return null;
        $depth = 0;
        $end = null;
        $len = strlen($text);
        for ($i = $start; $i < $len; $i++) {
            if ($text[$i] === '{') $depth++;
            elseif ($text[$i] === '}') {
                $depth--;
                if ($depth === 0) { $end = $i; break; }
            }
        }
        if ($end === null) return null;

        $data = json_decode(substr($text, $start, $end - $start + 1), true);
        return is_array($data) ? $data : null;
    }
}
