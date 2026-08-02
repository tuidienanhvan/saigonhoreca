<?php
/**
 * Pi API AI Providers Endpoints
 *
 * Provides:
 * - GET/POST /ai-providers        (existing provider config)
 * - POST     /ai/editor           (existing editor helper)
 * - GET      /ai-providers/health (NEW — per-provider health ping, 60s cache)
 * - POST     /ai-providers/rotate (NEW — rotate to next backup key, re-pings)
 * - POST     /ai-providers/test-completion (NEW — live test call, returns response/latency/tokens/cost)
 * - GET      /ai-providers/usage  (NEW — 30d usage stats from pi_ai_usage_log option)
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('pi_ai_log_usage')) {
    /**
     * Append a usage event to the pi_ai_usage_log option.
     * Auto-rotates to keep at most 90 days.
     *
     * @param string $provider  e.g. 'openai'
     * @param string $model     e.g. 'gpt-4o-mini'
     * @param int    $tokens    total tokens used
     * @param float  $cost      estimated USD cost
     */
    function pi_ai_log_usage($provider, $model, $tokens, $cost) {
        $provider = sanitize_key((string) $provider);
        $model    = sanitize_text_field((string) $model);
        $tokens   = max(0, (int) $tokens);
        $cost     = max(0, (float) $cost);
        if ($provider === '') return;

        $log = get_option('pi_ai_usage_log', []);
        if (!is_array($log)) $log = [];

        $today = gmdate('Y-m-d');
        if (!isset($log[$today]) || !is_array($log[$today])) {
            $log[$today] = ['events' => [], 'totals' => []];
        }
        if (!isset($log[$today]['events']) || !is_array($log[$today]['events'])) {
            $log[$today]['events'] = [];
        }

        $log[$today]['events'][] = [
            't'        => time(),
            'provider' => $provider,
            'model'    => $model,
            'tokens'   => $tokens,
            'cost'     => round($cost, 6),
        ];

        // Rotation: keep only the last 90 days
        $cutoff = gmdate('Y-m-d', strtotime('-90 days'));
        foreach (array_keys($log) as $date) {
            if ((string) $date < $cutoff) unset($log[$date]);
        }

        update_option('pi_ai_usage_log', $log, false);
    }
}

class Pi_Api_Ai {

    /**
     * Provider metadata — used for health checks and cost estimation.
     */
    private static function providers_meta() {
        return [
            'openai' => [
                'label'       => 'OpenAI',
                'key_option'  => 'sh_ai_openai_key',
                'health_url'  => 'https://api.openai.com/v1/models',
                'health_method' => 'GET',
                'default_model' => 'gpt-4o-mini',
                // Rough USD per 1K tokens — blended input+output for simplicity
                'cost_per_1k' => 0.00075,
            ],
            'anthropic' => [
                'label'       => 'Anthropic',
                'key_option'  => 'sh_ai_anthropic_key',
                'health_url'  => 'https://api.anthropic.com/v1/messages',
                'health_method' => 'POST',
                'default_model' => 'claude-3-5-haiku-20241022',
                'cost_per_1k' => 0.003,
            ],
            'gemini' => [
                'label'       => 'Gemini',
                'key_option'  => 'sh_ai_gemini_key',
                'health_url'  => 'https://generativelanguage.googleapis.com/v1beta/models',
                'health_method' => 'GET',
                'default_model' => 'gemini-1.5-flash',
                'cost_per_1k' => 0.00015,
            ],
            'groq' => [
                'label'       => 'Groq',
                'key_option'  => 'sh_ai_groq_key',
                'health_url'  => 'https://api.groq.com/openai/v1/models',
                'health_method' => 'GET',
                'default_model' => 'llama-3.1-8b-instant',
                'cost_per_1k' => 0.00005,
            ],
        ];
    }

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/ai-providers', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_providers'],    'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'update_provider'],  'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/ai/editor', [
            'methods'  => 'POST',
            'callback' => [self::class, 'ai_editor'],
            'permission_callback' => $admin
        ]);

        // Health / rotate / test / usage
        register_rest_route($ns, '/ai-providers/health', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_health'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/ai-providers/rotate', [
            'methods'  => 'POST',
            'callback' => [self::class, 'rotate_key'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/ai-providers/test-completion', [
            'methods'  => 'POST',
            'callback' => [self::class, 'test_completion'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/ai-providers/usage', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_usage'],
            'permission_callback' => $admin,
        ]);
    }

    /** Mask a secret to prefix+stars+suffix — same scheme AiCloud already uses. */
    private static function mask_key(string $k): string {
        if ($k === '') return '';
        return strlen($k) > 12
            ? substr($k, 0, 4) . str_repeat('*', max(0, strlen($k) - 8)) . substr($k, -4)
            : str_repeat('*', strlen($k));
    }

    public static function get_providers() {
        $meta = self::providers_meta();
        $raw = [
            'openai'    => (string) get_option('sh_ai_openai_key', ''),
            'anthropic' => (string) get_option('sh_ai_anthropic_key', ''),
            'gemini'    => (string) get_option('sh_ai_gemini_key', ''),
            'groq'      => (string) get_option('sh_ai_groq_key', ''),
        ];

        // Mask keys before returning — sibling AiCloud endpoint already does
        // this; this endpoint was previously returning every provider's key
        // (including backups) in full plaintext on every GET.
        $out = [
            'active_provider' => get_option('sh_ai_active_provider', 'openai'),
            'backup_keys' => [],
            'backup_keys_count' => [],
        ];
        foreach ($raw as $pid => $key) {
            $out[$pid]            = self::mask_key($key);
            $out[$pid . '_set']   = $key !== '';
        }

        foreach (array_keys($meta) as $pid) {
            $backup = get_option('pi_ai_' . $pid . '_key_backup', []);
            if (!is_array($backup)) {
                $decoded = json_decode((string) $backup, true);
                $backup = is_array($decoded) ? $decoded : [];
            }
            $backup = array_values(array_filter(array_map('strval', $backup)));
            $out['backup_keys'][$pid] = array_map([self::class, 'mask_key'], $backup);
            $out['backup_keys_count'][$pid] = count($backup);
        }

        return new \WP_REST_Response($out, 200);
    }

    public static function update_provider(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (!is_array($p)) $p = [];

        // Skip any value containing '*' — that's a masked value bounced back
        // from get_providers() unmodified, not a new key the caller intends
        // to set. Without this guard, a naive GET-then-PUT round trip in the
        // dashboard UI would overwrite a real key with literal asterisks.
        $set_if_real = function (string $option, $incoming): void {
            $incoming = (string) $incoming;
            if ($incoming !== '' && strpos($incoming, '*') === false) {
                update_option($option, sanitize_text_field($incoming));
            } elseif ($incoming === '') {
                update_option($option, '');
            }
        };

        if (isset($p['openai']))    $set_if_real('sh_ai_openai_key', $p['openai']);
        if (isset($p['anthropic'])) $set_if_real('sh_ai_anthropic_key', $p['anthropic']);
        if (isset($p['gemini']))    $set_if_real('sh_ai_gemini_key', $p['gemini']);
        if (isset($p['groq']))      $set_if_real('sh_ai_groq_key', $p['groq']);
        if (isset($p['active_provider'])) update_option('sh_ai_active_provider', sanitize_text_field($p['active_provider']));

        // Backup keys: accept { provider: [key1, key2] } map
        if (isset($p['backup_keys']) && is_array($p['backup_keys'])) {
            foreach (self::providers_meta() as $pid => $_) {
                if (isset($p['backup_keys'][$pid])) {
                    $raw = $p['backup_keys'][$pid];
                    if (is_string($raw)) {
                        $raw = preg_split('/\r\n|\r|\n/', $raw);
                    }
                    $clean = [];
                    foreach ((array) $raw as $k) {
                        $k = trim((string) $k);
                        // Same masked-value guard as the primary keys above.
                        if ($k !== '' && strpos($k, '*') === false) $clean[] = $k;
                    }
                    update_option('pi_ai_' . $pid . '_key_backup', array_values(array_unique($clean)), false);
                }
            }
        }

        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function ai_editor(\WP_REST_Request $request) {
        $params   = $request->get_json_params();
        $post_id  = (int) ($params['post_id'] ?? 0);
        $prompt   = sanitize_textarea_field($params['prompt'] ?? '');
        $provider = sanitize_text_field($params['provider'] ?? '');

        if (!$prompt) return new \WP_REST_Response(['message' => 'Prompt không được để trống.'], 400);

        $post = get_post($post_id);
        $content = $post ? $post->post_content : '';

        if (!function_exists('sh_api_call_provider')) {
            return new \WP_REST_Response(['message' => 'AI provider not available.'], 400);
        }

        $system = "Bạn là trợ lý biên tập viên. Chỉ trả về nội dung HTML. Không giải thích.";
        $user_prompt = $prompt . "\n\n---\nNỘI DUNG HIỆN TẠI:\n" . wp_strip_all_tags(mb_substr($content, 0, 4000));

        $result = sh_api_call_provider($provider, $system, $user_prompt);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['message' => $result->get_error_message()], 500);
        }

        return new \WP_REST_Response(['success' => true, 'content' => $result], 200);
    }

    /* ────────────────────────────────────────────────────────────
       NEW: Health checks
       ──────────────────────────────────────────────────────────── */

    public static function get_health(\WP_REST_Request $request) {
        $force = (bool) $request->get_param('force');
        $out   = [];
        foreach (array_keys(self::providers_meta()) as $pid) {
            $out[$pid] = self::ping_provider($pid, $force);
        }
        return new \WP_REST_Response($out, 200);
    }

    /**
     * Ping a single provider (cached 60s unless $force).
     * Returns: [ status, latency_ms, last_check, error ]
     *   status ∈ healthy | error | not_configured
     */
    private static function ping_provider($provider_id, $force = false) {
        $provider_id = sanitize_key($provider_id);
        $meta = self::providers_meta();
        if (!isset($meta[$provider_id])) {
            return ['status' => 'not_configured', 'latency_ms' => null, 'last_check' => null, 'error' => 'Unknown provider'];
        }
        $m = $meta[$provider_id];
        $key = get_option($m['key_option'], '');
        if (!$key) {
            return ['status' => 'not_configured', 'latency_ms' => null, 'last_check' => null, 'error' => null];
        }

        $cache_key = 'pi_ai_health_' . $provider_id;
        if (!$force) {
            $cached = get_transient($cache_key);
            if (is_array($cached)) return $cached;
        }

        $start = microtime(true);
        $args = [
            'timeout'   => 10,
            'sslverify' => (wp_get_environment_type() !== 'local'),
            'headers'   => ['Content-Type' => 'application/json'],
        ];

        $url = $m['health_url'];

        if ($provider_id === 'openai' || $provider_id === 'groq') {
            $args['headers']['Authorization'] = 'Bearer ' . $key;
            $response = wp_remote_get($url, $args);
        } elseif ($provider_id === 'anthropic') {
            $args['headers']['x-api-key'] = $key;
            $args['headers']['anthropic-version'] = '2023-06-01';
            $args['body'] = wp_json_encode([
                'model'      => $m['default_model'],
                'max_tokens' => 1,
                'messages'   => [['role' => 'user', 'content' => 'ping']],
            ]);
            $response = wp_remote_post($url, $args);
        } elseif ($provider_id === 'gemini') {
            $response = wp_remote_get(add_query_arg('key', $key, $url), $args);
        } else {
            $response = new \WP_Error('unknown_provider', 'Unknown provider');
        }

        $latency = (int) ((microtime(true) - $start) * 1000);
        $now_iso = gmdate('c');

        if (is_wp_error($response)) {
            $result = [
                'status'     => 'error',
                'latency_ms' => $latency,
                'last_check' => $now_iso,
                'error'      => $response->get_error_message(),
            ];
        } else {
            $code = (int) wp_remote_retrieve_response_code($response);
            if ($code >= 200 && $code < 300) {
                $result = [
                    'status'     => 'healthy',
                    'latency_ms' => $latency,
                    'last_check' => $now_iso,
                    'error'      => null,
                ];
            } else {
                $body = wp_remote_retrieve_body($response);
                $msg  = self::extract_error_message($body, $code);
                $result = [
                    'status'     => 'error',
                    'latency_ms' => $latency,
                    'last_check' => $now_iso,
                    'error'      => $msg,
                ];
            }
        }

        set_transient($cache_key, $result, 60);
        return $result;
    }

    private static function extract_error_message($body, $code) {
        $decoded = json_decode((string) $body, true);
        if (is_array($decoded)) {
            if (!empty($decoded['error']['message'])) return (string) $decoded['error']['message'];
            if (!empty($decoded['message'])) return (string) $decoded['message'];
            if (!empty($decoded['error']) && is_string($decoded['error'])) return $decoded['error'];
        }
        if ($code === 401 || $code === 403) return 'Invalid API key';
        if ($code === 429) return 'Rate limit exceeded';
        if ($code === 404) return 'Endpoint not found';
        return 'HTTP ' . $code;
    }

    /* ────────────────────────────────────────────────────────────
       NEW: Rotate to next backup key
       ──────────────────────────────────────────────────────────── */

    public static function rotate_key(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        $pid = sanitize_key((string) ($p['provider'] ?? ''));
        $meta = self::providers_meta();
        if (!isset($meta[$pid])) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Unknown provider'], 400);
        }

        $backup = get_option('pi_ai_' . $pid . '_key_backup', []);
        if (!is_array($backup)) $backup = [];
        $backup = array_values(array_filter(array_map('strval', $backup)));

        if (empty($backup)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Không có khóa dự phòng nào được cấu hình cho ' . $pid,
            ], 400);
        }

        $current = (string) get_option($meta[$pid]['key_option'], '');
        $next    = array_shift($backup); // pull first backup

        // Rotate: current → end of backup pool; next → active
        if ($current !== '' && $current !== $next) {
            $backup[] = $current;
        }

        update_option($meta[$pid]['key_option'], sanitize_text_field($next));
        update_option('pi_ai_' . $pid . '_key_backup', array_values($backup), false);

        // Invalidate health cache and re-ping
        delete_transient('pi_ai_health_' . $pid);
        $status = self::ping_provider($pid, true);

        return new \WP_REST_Response([
            'success' => true,
            'provider' => $pid,
            'status'  => $status,
            'remaining_backups' => count($backup),
        ], 200);
    }

    /* ────────────────────────────────────────────────────────────
       NEW: Test completion
       ──────────────────────────────────────────────────────────── */

    public static function test_completion(\WP_REST_Request $request) {
        $p   = $request->get_json_params();
        $pid = sanitize_key((string) ($p['provider'] ?? ''));
        $prompt = sanitize_textarea_field((string) ($p['prompt'] ?? 'Hello'));
        $model  = sanitize_text_field((string) ($p['model'] ?? ''));

        $meta = self::providers_meta();
        if (!isset($meta[$pid])) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Unknown provider'], 400);
        }
        $m = $meta[$pid];
        $key = get_option($m['key_option'], '');
        if (!$key) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Provider chưa được cấu hình API key'], 400);
        }
        if ($model === '') $model = $m['default_model'];

        $start = microtime(true);
        $args = [
            'timeout'   => 20,
            'sslverify' => (wp_get_environment_type() !== 'local'),
            'headers'   => ['Content-Type' => 'application/json'],
        ];

        $tokens_used = 0;
        $text_reply  = '';
        $error_msg   = null;

        try {
            if ($pid === 'openai' || $pid === 'groq') {
                $args['headers']['Authorization'] = 'Bearer ' . $key;
                $args['body'] = wp_json_encode([
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => 50,
                ]);
                $url = $pid === 'openai'
                    ? 'https://api.openai.com/v1/chat/completions'
                    : 'https://api.groq.com/openai/v1/chat/completions';
                $resp = wp_remote_post($url, $args);
                list($text_reply, $tokens_used, $error_msg) = self::parse_openai_like($resp);
            } elseif ($pid === 'anthropic') {
                $args['headers']['x-api-key'] = $key;
                $args['headers']['anthropic-version'] = '2023-06-01';
                $args['body'] = wp_json_encode([
                    'model' => $model,
                    'max_tokens' => 50,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                ]);
                $resp = wp_remote_post('https://api.anthropic.com/v1/messages', $args);
                list($text_reply, $tokens_used, $error_msg) = self::parse_anthropic($resp);
            } elseif ($pid === 'gemini') {
                $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent';
                $url = add_query_arg('key', $key, $url);
                $args['body'] = wp_json_encode([
                    'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => 50],
                ]);
                $resp = wp_remote_post($url, $args);
                list($text_reply, $tokens_used, $error_msg) = self::parse_gemini($resp);
            } else {
                $error_msg = 'Unsupported provider';
            }
        } catch (\Throwable $e) {
            $error_msg = $e->getMessage();
        }

        $latency = (int) ((microtime(true) - $start) * 1000);
        $cost    = round(($tokens_used / 1000) * (float) $m['cost_per_1k'], 6);

        if ($error_msg !== null) {
            return new \WP_REST_Response([
                'success'       => false,
                'provider'      => $pid,
                'model'         => $model,
                'response'      => '',
                'latency_ms'    => $latency,
                'tokens_used'   => $tokens_used,
                'cost_estimate' => $cost,
                'error'         => $error_msg,
            ], 200);
        }

        // Log successful test as a usage event
        pi_ai_log_usage($pid, $model, $tokens_used, $cost);

        return new \WP_REST_Response([
            'success'       => true,
            'provider'      => $pid,
            'model'         => $model,
            'response'      => $text_reply,
            'latency_ms'    => $latency,
            'tokens_used'   => $tokens_used,
            'cost_estimate' => $cost,
        ], 200);
    }

    private static function parse_openai_like($resp) {
        if (is_wp_error($resp)) return ['', 0, $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $d = json_decode($body, true);
        if ($code < 200 || $code >= 300) {
            return ['', 0, self::extract_error_message($body, $code)];
        }
        $text = isset($d['choices'][0]['message']['content']) ? (string) $d['choices'][0]['message']['content'] : '';
        $tokens = isset($d['usage']['total_tokens']) ? (int) $d['usage']['total_tokens'] : 0;
        return [$text, $tokens, null];
    }

    private static function parse_anthropic($resp) {
        if (is_wp_error($resp)) return ['', 0, $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $d = json_decode($body, true);
        if ($code < 200 || $code >= 300) {
            return ['', 0, self::extract_error_message($body, $code)];
        }
        $text = isset($d['content'][0]['text']) ? (string) $d['content'][0]['text'] : '';
        $in   = isset($d['usage']['input_tokens']) ? (int) $d['usage']['input_tokens'] : 0;
        $out  = isset($d['usage']['output_tokens']) ? (int) $d['usage']['output_tokens'] : 0;
        return [$text, $in + $out, null];
    }

    private static function parse_gemini($resp) {
        if (is_wp_error($resp)) return ['', 0, $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $d = json_decode($body, true);
        if ($code < 200 || $code >= 300) {
            return ['', 0, self::extract_error_message($body, $code)];
        }
        $text = isset($d['candidates'][0]['content']['parts'][0]['text']) ? (string) $d['candidates'][0]['content']['parts'][0]['text'] : '';
        $tokens = 0;
        if (isset($d['usageMetadata']['totalTokenCount'])) {
            $tokens = (int) $d['usageMetadata']['totalTokenCount'];
        } elseif (isset($d['usageMetadata']['promptTokenCount'], $d['usageMetadata']['candidatesTokenCount'])) {
            $tokens = (int) $d['usageMetadata']['promptTokenCount'] + (int) $d['usageMetadata']['candidatesTokenCount'];
        }
        return [$text, $tokens, null];
    }

    /* ────────────────────────────────────────────────────────────
       NEW: Usage stats
       ──────────────────────────────────────────────────────────── */

    public static function get_usage() {
        $log = get_option('pi_ai_usage_log', []);
        if (!is_array($log)) $log = [];

        $by_provider = [];
        $by_model    = [];
        $daily       = [];

        // Build last-30-day window (fill gaps with zeros)
        $end   = strtotime(gmdate('Y-m-d'));
        $start = strtotime('-29 days', $end);
        for ($t = $start; $t <= $end; $t += DAY_IN_SECONDS) {
            $d = gmdate('Y-m-d', $t);
            $daily[$d] = ['date' => $d, 'calls' => 0, 'tokens' => 0, 'cost_usd' => 0.0];
        }

        foreach ($log as $date => $bucket) {
            if (!isset($daily[$date])) continue; // outside 30d window
            if (!is_array($bucket) || empty($bucket['events'])) continue;

            foreach ($bucket['events'] as $ev) {
                $provider = isset($ev['provider']) ? (string) $ev['provider'] : 'unknown';
                $model    = isset($ev['model']) ? (string) $ev['model'] : 'unknown';
                $tokens   = isset($ev['tokens']) ? (int) $ev['tokens'] : 0;
                $cost     = isset($ev['cost']) ? (float) $ev['cost'] : 0.0;

                if (!isset($by_provider[$provider])) {
                    $by_provider[$provider] = ['calls' => 0, 'tokens' => 0, 'cost_usd' => 0.0];
                }
                $by_provider[$provider]['calls']++;
                $by_provider[$provider]['tokens']   += $tokens;
                $by_provider[$provider]['cost_usd'] += $cost;

                if (!isset($by_model[$model])) $by_model[$model] = 0;
                $by_model[$model]++;

                $daily[$date]['calls']++;
                $daily[$date]['tokens']   += $tokens;
                $daily[$date]['cost_usd'] += $cost;
            }
        }

        // Round cost display
        foreach ($by_provider as $k => $_) {
            $by_provider[$k]['cost_usd'] = round((float) $by_provider[$k]['cost_usd'], 4);
        }
        foreach ($daily as $d => $_) {
            $daily[$d]['cost_usd'] = round((float) $daily[$d]['cost_usd'], 4);
        }

        $totals = [
            'calls'    => 0,
            'tokens'   => 0,
            'cost_usd' => 0.0,
        ];
        foreach ($by_provider as $row) {
            $totals['calls']    += (int) $row['calls'];
            $totals['tokens']   += (int) $row['tokens'];
            $totals['cost_usd'] += (float) $row['cost_usd'];
        }
        $totals['cost_usd'] = round($totals['cost_usd'], 4);

        arsort($by_model);

        return new \WP_REST_Response([
            'totals'      => $totals,
            'by_provider' => $by_provider,
            'by_model'    => $by_model,
            'daily'       => array_values($daily),
        ], 200);
    }
}
