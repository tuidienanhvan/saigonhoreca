<?php
/**
 * Pi API AI Cloud v2 Endpoints
 *
 * Supplies the React 4-tab AI Cloud page (Settings / Test / Usage / Top-up)
 * plus the admin-bar wallet widget.
 *
 * Routes (all under /pi/v1/ai-cloud):
 *   GET|POST /ai-cloud/settings                       - Provider chain, models per task, timeouts, api_key, backend_url
 *   GET      /ai-cloud/wallet                         - Balance + last 10 transactions
 *   GET      /ai-cloud/wallet/transactions            - Paginated transaction list (type, page, limit)
 *   POST     /ai-cloud/wallet/topup                   - Creates pending topup record
 *   POST     /ai-cloud/wallet/topup/{id}/complete     - Admin-only: mark topup as completed (simulated settlement)
 *   POST     /ai-cloud/test                           - Chat completion via backend OR direct-to-provider
 *   GET      /ai-cloud/usage                          - Breakdown by day / model / task
 *   GET      /ai-cloud/usage/export                   - CSV stream
 *
 * Options:
 *   pi_ai_cloud_settings  - { enabled, provider_chain[], models{}, timeouts{}, api_key, backend_url, currency }
 *   pi_ai_cloud_wallet    - { balance_usd, currency, last_topup_at, auto_topup{enabled,threshold,amount} }
 *   pi_ai_cloud_enabled   - bool gate for "Contact admin" mode
 *
 * Storage:
 *   wp_pi_ai_cloud_transactions table (id, user_id, type, amount_usd, currency,
 *   status, method, provider, model, tokens_used, meta, created_at)
 *
 * Helpers (global):
 *   pi_ai_cloud_record_usage($provider, $model, $tokens, $cost_usd)
 *   pi_ai_cloud_record_topup($user_id, $amount, $method)
 *   pi_ai_cloud_complete_topup($transaction_id)
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_AiCloud {

    const OPT_SETTINGS  = 'pi_ai_cloud_settings';
    const OPT_WALLET    = 'pi_ai_cloud_wallet';
    const OPT_ENABLED   = 'pi_ai_cloud_enabled';
    const TABLE         = 'pi_ai_cloud_transactions';

    // -----------------------------------------------------------------
    //  Defaults
    // -----------------------------------------------------------------

    protected static function default_settings() {
        return [
            'enabled'        => true,
            'api_key'        => '',
            'backend_url'    => '',
            'currency'       => 'USD',
            'provider_chain' => [
                ['provider' => 'openai',    'priority' => 1, 'fallback_on_error' => true],
                ['provider' => 'anthropic', 'priority' => 2, 'fallback_on_error' => true],
                ['provider' => 'gemini',    'priority' => 3, 'fallback_on_error' => true],
                ['provider' => 'groq',      'priority' => 4, 'fallback_on_error' => false],
            ],
            'models' => [
                'chat'       => 'gpt-4o-mini',
                'completion' => 'gpt-4o-mini',
                'embedding'  => 'text-embedding-3-small',
                'vision'     => 'gpt-4o',
            ],
            'timeouts' => [
                'connect_ms' => 5000,
                'read_ms'    => 30000,
            ],
        ];
    }

    protected static function default_wallet() {
        return [
            'balance_usd'   => 0.0,
            'currency'      => 'USD',
            'last_topup_at' => '',
            'auto_topup'    => [
                'enabled'   => false,
                'threshold' => 5.0,
                'amount'    => 25.0,
            ],
        ];
    }

    protected static function merge_settings(array $opt): array {
        $d = self::default_settings();
        $out = array_merge($d, $opt);
        $out['provider_chain'] = !empty($opt['provider_chain']) && is_array($opt['provider_chain'])
            ? array_values($opt['provider_chain']) : $d['provider_chain'];
        $out['models']   = array_merge($d['models'],   (array) ($opt['models']   ?? []));
        $out['timeouts'] = array_merge($d['timeouts'], (array) ($opt['timeouts'] ?? []));
        return $out;
    }

    protected static function merge_wallet(array $opt): array {
        $d   = self::default_wallet();
        $out = array_merge($d, $opt);
        $out['auto_topup'] = array_merge($d['auto_topup'], (array) ($opt['auto_topup'] ?? []));
        $out['balance_usd'] = (float) $out['balance_usd'];
        return $out;
    }

    // -----------------------------------------------------------------
    //  Cost estimation (reused from class-ai.php rates)
    // -----------------------------------------------------------------

    protected static function provider_cost_per_1k($provider) {
        $map = [
            'openai'    => 0.00075,
            'anthropic' => 0.003,
            'gemini'    => 0.00015,
            'groq'      => 0.00005,
        ];
        return isset($map[$provider]) ? $map[$provider] : 0.0005;
    }

    // -----------------------------------------------------------------
    //  Table (dbDelta)
    // -----------------------------------------------------------------

    public static function maybe_create_tables() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $installed = (string) get_option('pi_ai_cloud_db_version', '');
        if ($installed === '1.0') return;

        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            type         VARCHAR(20)     NOT NULL DEFAULT 'usage',
            amount_usd   DECIMAL(18,6)   NOT NULL DEFAULT 0,
            currency     VARCHAR(8)      NOT NULL DEFAULT 'USD',
            status       VARCHAR(20)     NOT NULL DEFAULT 'pending',
            method       VARCHAR(32)     NOT NULL DEFAULT '',
            provider     VARCHAR(32)     NOT NULL DEFAULT '',
            model        VARCHAR(64)     NOT NULL DEFAULT '',
            tokens_used  INT UNSIGNED    NOT NULL DEFAULT 0,
            meta         LONGTEXT        NULL,
            created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY type_status (type, status),
            KEY created_at (created_at)
        ) $charset;";

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        dbDelta($sql);
        update_option('pi_ai_cloud_db_version', '1.0', false);
    }

    // -----------------------------------------------------------------
    //  Register routes
    // -----------------------------------------------------------------

    public static function register_routes($ns, $admin) {
        self::maybe_create_tables();

        register_rest_route($ns, '/ai-cloud/settings', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_settings'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'post_settings'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/ai-cloud/wallet', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_wallet'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/ai-cloud/wallet/transactions', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_transactions'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/ai-cloud/wallet/topup', [
            'methods'  => 'POST',
            'callback' => [self::class, 'post_topup'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/ai-cloud/wallet/topup/(?P<id>\d+)/complete', [
            'methods'  => 'POST',
            'callback' => [self::class, 'post_topup_complete'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/ai-cloud/test', [
            'methods'  => 'POST',
            'callback' => [self::class, 'post_test'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/ai-cloud/usage', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_usage'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/ai-cloud/usage/export', [
            'methods'  => 'GET',
            'callback' => [self::class, 'export_usage_csv'],
            'permission_callback' => $admin,
        ]);
    }

    // -----------------------------------------------------------------
    //  Settings
    // -----------------------------------------------------------------

    public static function get_settings() {
        $settings = self::merge_settings((array) get_option(self::OPT_SETTINGS, []));
        $enabled  = (bool) get_option(self::OPT_ENABLED, true);

        // Mask the api_key before returning — just show prefix + suffix.
        if (!empty($settings['api_key'])) {
            $k = (string) $settings['api_key'];
            $settings['api_key_masked'] = strlen($k) > 12
                ? substr($k, 0, 4) . str_repeat('*', max(0, strlen($k) - 8)) . substr($k, -4)
                : str_repeat('*', strlen($k));
            $settings['api_key_set'] = true;
        } else {
            $settings['api_key_masked'] = '';
            $settings['api_key_set']    = false;
        }
        // Never ship raw key to clients.
        unset($settings['api_key']);

        return new \WP_REST_Response([
            'settings' => $settings,
            'enabled'  => $enabled,
        ], 200);
    }

    public static function post_settings(\WP_REST_Request $request) {
        $body    = (array) $request->get_json_params();
        $current = self::merge_settings((array) get_option(self::OPT_SETTINGS, []));

        $clean = $current;

        if (isset($body['enabled']))     $clean['enabled']     = !empty($body['enabled']);
        if (isset($body['backend_url'])) $clean['backend_url'] = esc_url_raw((string) $body['backend_url']);
        if (isset($body['currency']))    $clean['currency']    = strtoupper(substr(sanitize_key((string) $body['currency']), 0, 8)) ?: 'USD';

        // Only overwrite api_key if caller explicitly sends a non-masked, non-empty string.
        if (array_key_exists('api_key', $body)) {
            $incoming = (string) $body['api_key'];
            if ($incoming !== '' && strpos($incoming, '*') === false) {
                $clean['api_key'] = sanitize_text_field($incoming);
            } elseif ($incoming === '') {
                // explicit empty -> unset
                $clean['api_key'] = '';
            }
        }

        if (isset($body['provider_chain']) && is_array($body['provider_chain'])) {
            $chain = [];
            foreach ($body['provider_chain'] as $row) {
                if (!is_array($row)) continue;
                $chain[] = [
                    'provider'          => sanitize_key((string) ($row['provider'] ?? '')),
                    'priority'          => (int) ($row['priority'] ?? (count($chain) + 1)),
                    'fallback_on_error' => !empty($row['fallback_on_error']),
                ];
            }
            if (!empty($chain)) $clean['provider_chain'] = $chain;
        }

        if (isset($body['models']) && is_array($body['models'])) {
            foreach (['chat', 'completion', 'embedding', 'vision'] as $task) {
                if (isset($body['models'][$task])) {
                    $clean['models'][$task] = sanitize_text_field((string) $body['models'][$task]);
                }
            }
        }

        if (isset($body['timeouts']) && is_array($body['timeouts'])) {
            if (isset($body['timeouts']['connect_ms'])) $clean['timeouts']['connect_ms'] = max(1000, (int) $body['timeouts']['connect_ms']);
            if (isset($body['timeouts']['read_ms']))    $clean['timeouts']['read_ms']    = max(1000, (int) $body['timeouts']['read_ms']);
        }

        update_option(self::OPT_SETTINGS, $clean, false);

        if (array_key_exists('enabled_gate', $body)) {
            update_option(self::OPT_ENABLED, !empty($body['enabled_gate']), false);
        }

        // Piggy-back: auto_topup belongs to the wallet option but the React
        // form on the Top-up tab posts here — persist it.
        if (isset($body['auto_topup']) && is_array($body['auto_topup'])) {
            $wallet = self::merge_wallet((array) get_option(self::OPT_WALLET, []));
            $wallet['auto_topup'] = [
                'enabled'   => !empty($body['auto_topup']['enabled']),
                'threshold' => max(0, (float) ($body['auto_topup']['threshold'] ?? 0)),
                'amount'    => max(0, (float) ($body['auto_topup']['amount']    ?? 0)),
            ];
            update_option(self::OPT_WALLET, $wallet, false);
        }

        return new \WP_REST_Response(['success' => true], 200);
    }

    // -----------------------------------------------------------------
    //  Wallet
    // -----------------------------------------------------------------

    public static function get_wallet() {
        global $wpdb;
        $table  = $wpdb->prefix . self::TABLE;
        $wallet = self::merge_wallet((array) get_option(self::OPT_WALLET, []));
        $enabled = (bool) get_option(self::OPT_ENABLED, true);

        $rows = $wpdb->get_results(
            "SELECT * FROM $table ORDER BY id DESC LIMIT 10",
            ARRAY_A
        );
        $recent = [];
        foreach ((array) $rows as $r) {
            $recent[] = self::format_transaction($r);
        }

        return new \WP_REST_Response([
            'enabled'      => $enabled,
            'balance_usd'  => (float) $wallet['balance_usd'],
            'currency'     => $wallet['currency'],
            'last_topup_at' => $wallet['last_topup_at'],
            'auto_topup'   => $wallet['auto_topup'],
            'monthly_quota' => class_exists('\\PiApi\\Core\\Settings') ? \PiApi\Core\Settings::getTokenQuota() : 5000,
            'used_this_month' => (int) get_option('pi_ai_cloud_tokens_used_month', 0),
            'reset_at' => get_option('pi_api_jwt_expires_at') ? gmdate('c', (int) get_option('pi_api_jwt_expires_at')) : null,
            'recent'       => $recent,
        ], 200);
    }

    public static function list_transactions(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $limit = max(1, min(200, (int) $request->get_param('limit') ?: 50));
        $page  = max(1, (int) $request->get_param('page') ?: 1);
        $type  = sanitize_key((string) $request->get_param('type'));
        $offset = ($page - 1) * $limit;

        $where  = 'WHERE 1=1';
        $params = [];
        if ($type !== '' && in_array($type, ['topup', 'usage', 'refund'], true)) {
            $where   .= ' AND type = %s';
            $params[] = $type;
        }

        $sql_count = "SELECT COUNT(*) FROM $table $where";
        $total = $params
            ? (int) $wpdb->get_var($wpdb->prepare($sql_count, $params))
            : (int) $wpdb->get_var($sql_count);

        $sql = "SELECT * FROM $table $where ORDER BY id DESC LIMIT %d OFFSET %d";
        $p2  = array_merge($params, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($sql, $p2), ARRAY_A);

        $items = [];
        foreach ((array) $rows as $r) $items[] = self::format_transaction($r);

        return new \WP_REST_Response([
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'total_pages' => max(1, (int) ceil($total / $limit)),
        ], 200);
    }

    public static function post_topup(\WP_REST_Request $request) {
        if (!(bool) get_option(self::OPT_ENABLED, true)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Ví bị vô hiệu hóa bởi quản trị viên.',
            ], 403);
        }

        $body   = (array) $request->get_json_params();
        $amount = (float) ($body['amount_usd'] ?? 0);
        $method = sanitize_key((string) ($body['method'] ?? 'bank_transfer'));
        $note   = sanitize_text_field((string) ($body['note'] ?? ''));

        if ($amount < 1 || $amount > 10000) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Số tiền không hợp lệ (1-10000 USD).'], 400);
        }
        if (!in_array($method, ['bank_transfer', 'paypal', 'crypto'], true)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Phương thức thanh toán không hợp lệ.'], 400);
        }

        $user_id = get_current_user_id();
        $id      = pi_ai_cloud_record_topup($user_id, $amount, $method, $note);

        if (!$id) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không lưu được giao dịch.'], 500);
        }

        // Build pay_instructions by method.
        $instructions = self::pay_instructions($method, $amount, $id);

        return new \WP_REST_Response([
            'success'          => true,
            'transaction_id'   => $id,
            'amount_usd'       => $amount,
            'method'           => $method,
            'status'           => 'pending',
            'pay_instructions' => $instructions,
        ], 200);
    }

    public static function post_topup_complete(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $ok = pi_ai_cloud_complete_topup($id);
        if (!$ok) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không thể hoàn tất giao dịch.'], 400);
        }
        $wallet = self::merge_wallet((array) get_option(self::OPT_WALLET, []));
        return new \WP_REST_Response([
            'success'     => true,
            'transaction_id' => $id,
            'balance_usd' => (float) $wallet['balance_usd'],
        ], 200);
    }

    protected static function pay_instructions(string $method, float $amount, int $id): array {
        $ref = 'PI-AICLOUD-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
        switch ($method) {
            case 'bank_transfer':
                return [
                    'title'     => 'Chuyển khoản ngân hàng',
                    'reference' => $ref,
                    'steps'     => [
                        'Ngân hàng: Vietcombank — Chi nhánh TP.HCM',
                        'Số tài khoản: 0371000412345',
                        'Chủ tài khoản: CÔNG TY TNHH PI TECH',
                        sprintf('Số tiền: $%s USD', number_format($amount, 2)),
                        sprintf('Nội dung chuyển khoản: %s', $ref),
                        'Sau khi chuyển, nạp tiền sẽ được xác nhận trong vòng 1-2 giờ làm việc.',
                    ],
                ];
            case 'paypal':
                return [
                    'title'     => 'PayPal',
                    'reference' => $ref,
                    'steps'     => [
                        'Gửi thanh toán tới: billing@pitech.co',
                        sprintf('Số tiền: $%s USD', number_format($amount, 2)),
                        sprintf('Ghi chú: %s', $ref),
                        'Nạp tiền được xác nhận tự động sau khi PayPal confirm (~5 phút).',
                    ],
                ];
            case 'crypto':
                return [
                    'title'     => 'Thanh toán tiền điện tử (USDT-TRC20)',
                    'reference' => $ref,
                    'steps'     => [
                        'Địa chỉ ví (USDT-TRC20): TPi1234567890PiTechWalletABCDEF',
                        sprintf('Số tiền USDT: %s', number_format($amount, 2)),
                        sprintf('Memo: %s', $ref),
                        'Xác nhận sau 3 khối (~15 phút).',
                    ],
                ];
        }
        return ['title' => 'Liên hệ quản trị viên', 'reference' => $ref, 'steps' => []];
    }

    protected static function format_transaction(array $row): array {
        $meta = [];
        if (!empty($row['meta'])) {
            $decoded = json_decode((string) $row['meta'], true);
            if (is_array($decoded)) $meta = $decoded;
        }
        return [
            'id'          => (int) $row['id'],
            'user_id'     => (int) $row['user_id'],
            'type'        => (string) $row['type'],
            'amount_usd'  => (float) $row['amount_usd'],
            'currency'    => (string) $row['currency'],
            'status'      => (string) $row['status'],
            'method'      => (string) $row['method'],
            'provider'    => (string) $row['provider'],
            'model'       => (string) $row['model'],
            'tokens_used' => (int) $row['tokens_used'],
            'meta'        => $meta,
            'created_at'  => (string) $row['created_at'],
        ];
    }

    // -----------------------------------------------------------------
    //  Test — routes through backend (if configured) or direct-to-provider
    // -----------------------------------------------------------------

    public static function post_test(\WP_REST_Request $request) {
        $body   = (array) $request->get_json_params();
        $prompt = (string) ($body['prompt'] ?? '');
        $model  = sanitize_text_field((string) ($body['model'] ?? ''));
        $max_t  = max(1, min(4000, (int) ($body['max_tokens'] ?? 500)));
        $temp   = (float) ($body['temperature'] ?? 0.7);
        $task   = sanitize_key((string) ($body['task'] ?? 'chat'));

        if ($prompt === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Prompt không được để trống.'], 400);
        }

        $settings = self::merge_settings((array) get_option(self::OPT_SETTINGS, []));
        if (!$settings['enabled']) {
            return new \WP_REST_Response(['success' => false, 'message' => 'AI Cloud hiện đang tắt.'], 403);
        }

        if ($model === '') {
            $model = $settings['models'][$task] ?? $settings['models']['chat'];
        }

        $start = microtime(true);

        // Backend-first if URL configured.
        if (!empty($settings['backend_url']) && !empty($settings['api_key'])) {
            $resp = self::call_backend($settings, $prompt, $model, $max_t, $temp, $task);
        } else {
            $resp = self::call_provider_chain($settings, $prompt, $model, $max_t, $temp, $task);
        }

        $resp['latency_ms'] = (int) ((microtime(true) - $start) * 1000);

        // Ledger record (usage type), and deduct from wallet if balance is there.
        if ($resp['success']) {
            pi_ai_cloud_record_usage(
                $resp['provider_used'] ?? '',
                $resp['model_used']    ?? $model,
                (int) ($resp['tokens_used'] ?? 0),
                (float) ($resp['cost_usd']   ?? 0)
            );
            $tokens_used = (int) ($resp['tokens_used'] ?? 0);
            update_option('pi_ai_cloud_tokens_used_month', (int) get_option('pi_ai_cloud_tokens_used_month', 0) + $tokens_used, false);
            $jwt = class_exists('\\PiApi\\Core\\Settings') ? \PiApi\Core\Settings::getJwt() : '';
            if ($jwt !== '' && !str_starts_with($jwt, 'mock')) {
                $tokens_in = max(0, min($tokens_used, (int) ceil(strlen($prompt) / 4)));
                wp_schedule_single_event(time(), 'pi_api_report_usage', [$tokens_in, max(0, $tokens_used - $tokens_in), $jwt]);
            }
            // Also mirror into shared pi_ai_usage_log for AiProviders page.
            if (function_exists('pi_ai_log_usage')) {
                pi_ai_log_usage(
                    $resp['provider_used'] ?? '',
                    $resp['model_used']    ?? $model,
                    (int) ($resp['tokens_used'] ?? 0),
                    (float) ($resp['cost_usd']   ?? 0)
                );
            }
        }

        return new \WP_REST_Response($resp, 200);
    }

    /**
     * Route through /ai-cloud/chat on the configured backend.
     */
    protected static function call_backend(array $settings, string $prompt, string $model, int $max_t, float $temp, string $task): array {
        $url  = rtrim($settings['backend_url'], '/') . '/ai-cloud/chat';
        $resp = wp_remote_post($url, [
            'timeout' => max(5, (int) ($settings['timeouts']['read_ms'] / 1000)),
            'headers' => [
                'Authorization' => 'Bearer ' . $settings['api_key'],
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode([
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'model'       => $model,
                'max_tokens'  => $max_t,
                'temperature' => $temp,
                'task'        => $task,
            ]),
        ]);
        if (is_wp_error($resp)) {
            return [
                'success'       => false,
                'message'       => 'Backend lỗi: ' . $resp->get_error_message(),
                'response'      => '',
                'provider_used' => 'backend',
                'model_used'    => $model,
                'tokens_used'   => 0,
                'cost_usd'      => 0,
            ];
        }
        $code = (int) wp_remote_retrieve_response_code($resp);
        $data = json_decode((string) wp_remote_retrieve_body($resp), true);
        if ($code < 200 || $code >= 300 || !is_array($data)) {
            $msg = is_array($data) && !empty($data['message']) ? (string) $data['message'] : ('Backend HTTP ' . $code);
            return [
                'success'       => false,
                'message'       => $msg,
                'response'      => '',
                'provider_used' => 'backend',
                'model_used'    => $model,
                'tokens_used'   => 0,
                'cost_usd'      => 0,
            ];
        }
        return [
            'success'       => true,
            'response'      => (string) ($data['response'] ?? $data['text'] ?? ''),
            'provider_used' => (string) ($data['provider_used'] ?? 'backend'),
            'model_used'    => (string) ($data['model_used'] ?? $model),
            'tokens_used'   => (int) ($data['tokens_used'] ?? 0),
            'cost_usd'      => (float) ($data['cost_usd'] ?? 0),
        ];
    }

    /**
     * Fallback: walk provider_chain in priority order, picking up keys from
     * the AiProviders options (sh_ai_<provider>_key).
     */
    protected static function call_provider_chain(array $settings, string $prompt, string $model, int $max_t, float $temp, string $task): array {
        $chain = $settings['provider_chain'];
        usort($chain, function ($a, $b) { return (int) $a['priority'] <=> (int) $b['priority']; });

        $last_error = 'Không có provider nào khả dụng.';
        foreach ($chain as $c) {
            $pid = (string) $c['provider'];
            $key = (string) get_option('sh_ai_' . $pid . '_key', '');
            if ($key === '') { $last_error = 'Provider ' . $pid . ' chưa có API key.'; continue; }

            $try = self::call_provider_direct($pid, $key, $prompt, $model, $max_t, $temp, $settings);
            if ($try['success']) return $try;

            $last_error = $try['message'] ?? 'Provider thất bại.';
            if (empty($c['fallback_on_error'])) break;
        }
        return [
            'success'       => false,
            'message'       => $last_error,
            'response'      => '',
            'provider_used' => '',
            'model_used'    => $model,
            'tokens_used'   => 0,
            'cost_usd'      => 0,
        ];
    }

    protected static function call_provider_direct(string $pid, string $key, string $prompt, string $model, int $max_t, float $temp, array $settings): array {
        $timeout = max(5, (int) ($settings['timeouts']['read_ms'] / 1000));
        $args = [
            'timeout'   => $timeout,
            'sslverify' => (wp_get_environment_type() !== 'local'),
            'headers'   => ['Content-Type' => 'application/json'],
        ];

        $resolved_model = $model;

        try {
            if ($pid === 'openai' || $pid === 'groq') {
                $args['headers']['Authorization'] = 'Bearer ' . $key;
                $args['body'] = wp_json_encode([
                    'model'       => $model,
                    'messages'    => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens'  => $max_t,
                    'temperature' => $temp,
                ]);
                $url = ($pid === 'openai')
                    ? 'https://api.openai.com/v1/chat/completions'
                    : 'https://api.groq.com/openai/v1/chat/completions';
                $resp = wp_remote_post($url, $args);
                $parsed = self::parse_openai_like($resp);
            } elseif ($pid === 'anthropic') {
                $args['headers']['x-api-key'] = $key;
                $args['headers']['anthropic-version'] = '2023-06-01';
                $args['body'] = wp_json_encode([
                    'model'      => $model,
                    'max_tokens' => $max_t,
                    'messages'   => [['role' => 'user', 'content' => $prompt]],
                ]);
                $resp = wp_remote_post('https://api.anthropic.com/v1/messages', $args);
                $parsed = self::parse_anthropic($resp);
            } elseif ($pid === 'gemini') {
                $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent';
                $url = add_query_arg('key', $key, $url);
                $args['body'] = wp_json_encode([
                    'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => $max_t, 'temperature' => $temp],
                ]);
                $resp = wp_remote_post($url, $args);
                $parsed = self::parse_gemini($resp);
            } else {
                return ['success' => false, 'message' => 'Provider không hỗ trợ: ' . $pid];
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        list($text, $tokens, $err) = $parsed;
        if ($err !== null) {
            return ['success' => false, 'message' => $pid . ': ' . $err];
        }
        $cost = round(($tokens / 1000) * self::provider_cost_per_1k($pid), 6);
        return [
            'success'       => true,
            'response'      => $text,
            'provider_used' => $pid,
            'model_used'    => $resolved_model,
            'tokens_used'   => $tokens,
            'cost_usd'      => $cost,
        ];
    }

    protected static function parse_openai_like($resp) {
        if (is_wp_error($resp)) return ['', 0, $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $d    = json_decode($body, true);
        if ($code < 200 || $code >= 300) return ['', 0, self::err_from_body($d, $code)];
        $text = isset($d['choices'][0]['message']['content']) ? (string) $d['choices'][0]['message']['content'] : '';
        $tokens = isset($d['usage']['total_tokens']) ? (int) $d['usage']['total_tokens'] : 0;
        return [$text, $tokens, null];
    }

    protected static function parse_anthropic($resp) {
        if (is_wp_error($resp)) return ['', 0, $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $d    = json_decode($body, true);
        if ($code < 200 || $code >= 300) return ['', 0, self::err_from_body($d, $code)];
        $text = isset($d['content'][0]['text']) ? (string) $d['content'][0]['text'] : '';
        $in   = isset($d['usage']['input_tokens'])  ? (int) $d['usage']['input_tokens']  : 0;
        $out  = isset($d['usage']['output_tokens']) ? (int) $d['usage']['output_tokens'] : 0;
        return [$text, $in + $out, null];
    }

    protected static function parse_gemini($resp) {
        if (is_wp_error($resp)) return ['', 0, $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $d    = json_decode($body, true);
        if ($code < 200 || $code >= 300) return ['', 0, self::err_from_body($d, $code)];
        $text = isset($d['candidates'][0]['content']['parts'][0]['text']) ? (string) $d['candidates'][0]['content']['parts'][0]['text'] : '';
        $tokens = 0;
        if (isset($d['usageMetadata']['totalTokenCount'])) {
            $tokens = (int) $d['usageMetadata']['totalTokenCount'];
        } elseif (isset($d['usageMetadata']['promptTokenCount'], $d['usageMetadata']['candidatesTokenCount'])) {
            $tokens = (int) $d['usageMetadata']['promptTokenCount'] + (int) $d['usageMetadata']['candidatesTokenCount'];
        }
        return [$text, $tokens, null];
    }

    protected static function err_from_body($d, $code) {
        if (is_array($d)) {
            if (!empty($d['error']['message'])) return (string) $d['error']['message'];
            if (!empty($d['message'])) return (string) $d['message'];
            if (!empty($d['error']) && is_string($d['error'])) return $d['error'];
        }
        if ($code === 401 || $code === 403) return 'Invalid API key';
        if ($code === 429) return 'Rate limit exceeded';
        return 'HTTP ' . $code;
    }

    // -----------------------------------------------------------------
    //  Usage breakdown
    // -----------------------------------------------------------------

    public static function get_usage(\WP_REST_Request $request) {
        global $wpdb;
        $table  = $wpdb->prefix . self::TABLE;
        $days   = max(1, min(365, (int) ($request->get_param('days') ?: 30)));
        $group  = sanitize_key((string) ($request->get_param('group_by') ?: 'day'));

        $cutoff = gmdate('Y-m-d 00:00:00', strtotime("-{$days} days"));

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
             WHERE type IN ('usage', 'refund')
               AND created_at >= %s
             ORDER BY id ASC",
            $cutoff
        ), ARRAY_A);

        $totals = ['cost_usd' => 0.0, 'tokens' => 0, 'requests' => 0];
        $by_day   = [];
        $by_model = [];
        $by_task  = [];

        // Pre-fill daily bars.
        $start = strtotime(gmdate('Y-m-d', strtotime("-" . ($days - 1) . " days")));
        $end   = strtotime(gmdate('Y-m-d'));
        for ($t = $start; $t <= $end; $t += DAY_IN_SECONDS) {
            $d = gmdate('Y-m-d', $t);
            $by_day[$d] = ['date' => $d, 'cost_usd' => 0.0, 'tokens' => 0, 'requests' => 0];
        }

        foreach ((array) $rows as $r) {
            $amount = (float) $r['amount_usd'];
            $tokens = (int) $r['tokens_used'];
            $model  = (string) $r['model'];
            $sign   = (string) $r['type'] === 'refund' ? -1 : 1;

            $totals['cost_usd']  += $sign * $amount;
            $totals['tokens']    += $sign * $tokens;
            $totals['requests']++;

            $day = substr((string) $r['created_at'], 0, 10);
            if (isset($by_day[$day])) {
                $by_day[$day]['cost_usd'] += $sign * $amount;
                $by_day[$day]['tokens']   += $sign * $tokens;
                $by_day[$day]['requests']++;
            }

            if ($model !== '') {
                if (!isset($by_model[$model])) $by_model[$model] = ['model' => $model, 'requests' => 0, 'tokens' => 0, 'cost_usd' => 0];
                $by_model[$model]['requests']++;
                $by_model[$model]['tokens']   += $sign * $tokens;
                $by_model[$model]['cost_usd'] += $sign * $amount;
            }

            $meta = [];
            if (!empty($r['meta'])) {
                $decoded = json_decode((string) $r['meta'], true);
                if (is_array($decoded)) $meta = $decoded;
            }
            $task = (string) ($meta['task'] ?? 'chat');
            if (!isset($by_task[$task])) $by_task[$task] = ['task' => $task, 'requests' => 0, 'tokens' => 0, 'cost_usd' => 0];
            $by_task[$task]['requests']++;
            $by_task[$task]['tokens']   += $sign * $tokens;
            $by_task[$task]['cost_usd'] += $sign * $amount;
        }

        // Add percentage column for models.
        $total_cost = max(0.00000001, (float) $totals['cost_usd']);
        $by_model_list = array_values($by_model);
        foreach ($by_model_list as &$m) {
            $m['pct'] = round(($m['cost_usd'] / $total_cost) * 100, 2);
            $m['cost_usd'] = round((float) $m['cost_usd'], 6);
        }
        unset($m);
        usort($by_model_list, function ($a, $b) { return $b['cost_usd'] <=> $a['cost_usd']; });

        $by_task_list = array_values($by_task);
        foreach ($by_task_list as &$t) {
            $t['cost_usd'] = round((float) $t['cost_usd'], 6);
        }
        unset($t);

        $by_day_list = array_values($by_day);
        foreach ($by_day_list as &$d) {
            $d['cost_usd'] = round((float) $d['cost_usd'], 6);
        }
        unset($d);

        $avg_cost_per_request = $totals['requests'] > 0
            ? round((float) $totals['cost_usd'] / $totals['requests'], 6)
            : 0;

        return new \WP_REST_Response([
            'days'    => $days,
            'group_by' => $group,
            'totals'  => [
                'cost_usd'  => round((float) $totals['cost_usd'], 6),
                'tokens'    => (int) $totals['tokens'],
                'requests'  => (int) $totals['requests'],
                'avg_cost_per_request' => $avg_cost_per_request,
            ],
            'by_day'   => $by_day_list,
            'by_model' => $by_model_list,
            'by_task'  => $by_task_list,
        ], 200);
    }

    /**
     * Stream CSV download of usage in the requested window.
     */
    public static function export_usage_csv(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;
        $days  = max(1, min(365, (int) ($request->get_param('days') ?: 30)));

        $cutoff = gmdate('Y-m-d 00:00:00', strtotime("-{$days} days"));
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT id, created_at, type, provider, model, tokens_used, amount_usd, currency, status, method, meta
             FROM $table
             WHERE created_at >= %s
             ORDER BY id ASC",
            $cutoff
        ), ARRAY_A);

        $filename = 'pi-ai-cloud-usage-' . $days . 'd-' . gmdate('Ymd-His') . '.csv';

        if (function_exists('nocache_headers')) nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');
        // BOM for Excel UTF-8.
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, ['id', 'created_at', 'type', 'provider', 'model', 'tokens_used', 'amount_usd', 'currency', 'status', 'method', 'task']);
        foreach ((array) $rows as $r) {
            $meta = [];
            if (!empty($r['meta'])) {
                $decoded = json_decode((string) $r['meta'], true);
                if (is_array($decoded)) $meta = $decoded;
            }
            fputcsv($fp, [
                $r['id'],
                $r['created_at'],
                $r['type'],
                $r['provider'],
                $r['model'],
                $r['tokens_used'],
                $r['amount_usd'],
                $r['currency'],
                $r['status'],
                $r['method'],
                $meta['task'] ?? '',
            ]);
        }
        fclose($fp);
        exit;
    }
}

// --------------------------------------------------------------------
//  Ledger helper functions (globals — used by Pi plugins + /test route)
// --------------------------------------------------------------------

if (!function_exists('pi_ai_cloud_record_usage')) {
    /**
     * Record an AI usage event and deduct from the wallet.
     */
    function pi_ai_cloud_record_usage($provider, $model, $tokens, $cost_usd) {
        global $wpdb;
        $table    = $wpdb->prefix . Pi_Api_AiCloud::TABLE;
        $provider = sanitize_key((string) $provider);
        $model    = sanitize_text_field((string) $model);
        $tokens   = max(0, (int) $tokens);
        $cost_usd = max(0, (float) $cost_usd);

        $wpdb->insert($table, [
            'user_id'     => (int) get_current_user_id(),
            'type'        => 'usage',
            'amount_usd'  => $cost_usd,
            'currency'    => 'USD',
            'status'      => 'completed',
            'method'      => '',
            'provider'    => $provider,
            'model'       => $model,
            'tokens_used' => $tokens,
            'meta'        => wp_json_encode(['task' => 'chat']),
            'created_at'  => current_time('mysql', 1),
        ]);

        $id = (int) $wpdb->insert_id;

        $wallet = (array) get_option('pi_ai_cloud_settings', []); // just to keep call shape
        $wallet_opt = get_option('pi_ai_cloud_wallet', []);
        if (!is_array($wallet_opt)) $wallet_opt = [];
        $balance = isset($wallet_opt['balance_usd']) ? (float) $wallet_opt['balance_usd'] : 0.0;
        $balance = max(0, $balance - $cost_usd);
        $wallet_opt['balance_usd'] = $balance;
        update_option('pi_ai_cloud_wallet', $wallet_opt, false);
        return $id;
    }
}

if (!function_exists('pi_ai_cloud_record_topup')) {
    /**
     * Create a pending topup row. Returns the inserted id or 0 on failure.
     */
    function pi_ai_cloud_record_topup($user_id, $amount, $method, $note = '') {
        global $wpdb;
        $table  = $wpdb->prefix . Pi_Api_AiCloud::TABLE;
        $amount = max(0, (float) $amount);
        $method = sanitize_key((string) $method);

        $ok = $wpdb->insert($table, [
            'user_id'     => (int) $user_id,
            'type'        => 'topup',
            'amount_usd'  => $amount,
            'currency'    => 'USD',
            'status'      => 'pending',
            'method'      => $method,
            'provider'    => '',
            'model'       => '',
            'tokens_used' => 0,
            'meta'        => wp_json_encode(['note' => (string) $note]),
            'created_at'  => current_time('mysql', 1),
        ]);
        if (!$ok) return 0;
        return (int) $wpdb->insert_id;
    }
}

if (!function_exists('pi_ai_cloud_complete_topup')) {
    /**
     * Mark a pending topup as completed and add the amount to the wallet.
     * Idempotent: a second call on a completed row is a no-op.
     */
    function pi_ai_cloud_complete_topup($transaction_id) {
        global $wpdb;
        $table = $wpdb->prefix . Pi_Api_AiCloud::TABLE;
        $id    = (int) $transaction_id;
        if ($id <= 0) return false;

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
        if (!$row) return false;
        if ((string) $row['type'] !== 'topup') return false;
        if ((string) $row['status'] === 'completed') return true;

        $wpdb->update(
            $table,
            ['status' => 'completed'],
            ['id' => $id]
        );

        $amount = (float) $row['amount_usd'];
        $wallet_opt = get_option('pi_ai_cloud_wallet', []);
        if (!is_array($wallet_opt)) $wallet_opt = [];
        $balance = isset($wallet_opt['balance_usd']) ? (float) $wallet_opt['balance_usd'] : 0.0;
        $wallet_opt['balance_usd']   = $balance + $amount;
        $wallet_opt['last_topup_at'] = current_time('mysql', 1);
        update_option('pi_ai_cloud_wallet', $wallet_opt, false);

        return true;
    }
}
