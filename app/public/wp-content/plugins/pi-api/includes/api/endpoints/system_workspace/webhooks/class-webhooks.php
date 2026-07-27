<?php
/**
 * Pi API Webhooks Endpoints
 *
 * Core endpoints:
 * - GET  /webhooks                              List all webhook configs
 * - POST /webhooks                              Save webhook config blob
 *
 * Tester endpoints (Phase 2 task C):
 * - POST /webhooks/{id}/test-fire               Send test request, return full response
 * - GET  /webhooks/{id}/recent-deliveries       Recent deliveries
 * - POST /webhooks/{id}/retry/{delivery_id}     Retry a failed delivery
 * - GET  /webhooks/test-payloads                Template payloads
 *
 * Database table:
 *   wp_pi_webhook_deliveries (id, webhook_id, payload JSON, request_headers JSON,
 *     response_status, response_body, response_headers JSON, latency_ms, status,
 *     attempt_count, created_at)
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Webhooks {

    /* ═══════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════ */
    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/webhooks', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_webhooks'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_webhooks'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/webhooks/test-payloads', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_test_payloads'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/webhooks/(?P<id>[A-Za-z0-9_\-]+)/test-fire', [
            'methods'  => 'POST',
            'callback' => [self::class, 'test_fire'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/webhooks/(?P<id>[A-Za-z0-9_\-]+)/recent-deliveries', [
            'methods'  => 'GET',
            'callback' => [self::class, 'recent_deliveries'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/webhooks/(?P<id>[A-Za-z0-9_\-]+)/retry/(?P<delivery_id>\d+)', [
            'methods'  => 'POST',
            'callback' => [self::class, 'retry_delivery'],
            'permission_callback' => $admin,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════
       CORE — list / save
       ═══════════════════════════════════════════════════════════════ */
    public static function get_webhooks() {
        return new \WP_REST_Response([
            // Legacy field, kept for back-compat
            'leads_webhook'      => get_option('pi_leads_webhook', ''),
            // Webhook URLs used by the dashboard
            'webhook_new_post'   => get_option('pi_webhook_new_post', ''),
            'webhook_new_lead'   => get_option('pi_webhook_new_lead', get_option('pi_leads_webhook', '')),
            'webhook_secret'     => get_option('pi_webhook_secret', ''),
            'telegram_bot_token' => get_option('pi_webhook_telegram_bot_token', ''),
            'telegram_chat_id'   => get_option('pi_webhook_telegram_chat_id', ''),
            'zalo_access_token'  => get_option('pi_webhook_zalo_access_token', ''),
            // Resolved list used by tester (id, name, url)
            'webhooks'           => self::list_webhooks(),
        ], 200);
    }

    public static function save_webhooks(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (!is_array($p)) $p = [];

        if (isset($p['leads_webhook']))      update_option('pi_leads_webhook', esc_url_raw($p['leads_webhook']));
        if (isset($p['webhook_new_post']))   update_option('pi_webhook_new_post', esc_url_raw($p['webhook_new_post']));
        if (isset($p['webhook_new_lead'])) {
            update_option('pi_webhook_new_lead', esc_url_raw($p['webhook_new_lead']));
            update_option('pi_leads_webhook', esc_url_raw($p['webhook_new_lead']));
        }
        if (isset($p['webhook_secret']))     update_option('pi_webhook_secret', sanitize_text_field((string) $p['webhook_secret']));
        if (isset($p['telegram_bot_token'])) update_option('pi_webhook_telegram_bot_token', sanitize_text_field((string) $p['telegram_bot_token']));
        if (isset($p['telegram_chat_id']))   update_option('pi_webhook_telegram_chat_id', sanitize_text_field((string) $p['telegram_chat_id']));
        if (isset($p['zalo_access_token']))  update_option('pi_webhook_zalo_access_token', sanitize_text_field((string) $p['zalo_access_token']));

        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       TESTER — test-fire / deliveries / retry / payloads
       ═══════════════════════════════════════════════════════════════ */
    public static function get_test_payloads() {
        return new \WP_REST_Response([
            'payloads' => [
                [
                    'id'          => 'lead_created',
                    'name'        => 'Khách hàng mới (lead_created)',
                    'description' => 'Mẫu khi có khách hàng mới qua form liên hệ',
                    'payload'     => [
                        'event'      => 'lead.created',
                        'lead_id'    => 12345,
                        'name'       => 'Nguyễn Văn A',
                        'email'      => 'a@example.com',
                        'phone'      => '0901234567',
                        'message'    => 'Tôi cần báo giá thiết kế nhà 3 tầng.',
                        'source'     => 'contact_form',
                        'created_at' => gmdate('c'),
                    ],
                ],
                [
                    'id'          => 'form_submitted',
                    'name'        => 'Form gửi đi (form_submitted)',
                    'description' => 'Mẫu khi form CF7 / Gravity Forms được gửi',
                    'payload'     => [
                        'event'      => 'form.submitted',
                        'form_id'    => 'cf7_42',
                        'fields'     => [
                            'your-name'    => 'Trần Thị B',
                            'your-email'   => 'b@example.com',
                            'your-message' => 'Yêu cầu tư vấn',
                        ],
                        'submitted_at' => gmdate('c'),
                    ],
                ],
                [
                    'id'          => 'post_published',
                    'name'        => 'Bài viết mới (post_published)',
                    'description' => 'Mẫu khi có bài viết được xuất bản',
                    'payload'     => [
                        'event'   => 'post.published',
                        'post_id' => 1024,
                        'title'   => 'Mẫu nhà phố 3 tầng đẹp năm 2026',
                        'url'     => home_url('/sample-post/'),
                        'author'  => 'admin',
                        'date'    => gmdate('c'),
                    ],
                ],
                [
                    'id'          => 'comment_added',
                    'name'        => 'Bình luận mới (comment_added)',
                    'description' => 'Mẫu khi có bình luận mới',
                    'payload'     => [
                        'event'      => 'comment.added',
                        'comment_id' => 88,
                        'post_id'    => 1024,
                        'author'     => 'Khách',
                        'content'    => 'Bài viết rất hữu ích!',
                        'created_at' => gmdate('c'),
                    ],
                ],
                [
                    'id'          => 'ping',
                    'name'        => 'Ping rỗng (ping)',
                    'description' => 'Mẫu ping đơn giản để test endpoint',
                    'payload'     => [
                        'event' => 'ping',
                        'time'  => gmdate('c'),
                    ],
                ],
            ],
        ], 200);
    }

    public static function test_fire(\WP_REST_Request $request) {
        $id = (string) $request->get_param('id');
        $p  = $request->get_json_params();
        if (!is_array($p)) $p = [];

        // Resolve URL: explicit override in body, or look up by id
        $override_url = isset($p['url']) ? esc_url_raw((string) $p['url']) : '';
        $method = strtoupper((string) ($p['method'] ?? 'POST'));
        if (!in_array($method, ['POST', 'GET', 'PUT', 'PATCH', 'DELETE'], true)) $method = 'POST';

        $url = $override_url !== '' ? $override_url : self::resolve_webhook_url($id);
        if ($url === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Webhook URL trống hoặc không tìm thấy'], 400);
        }

        $payload  = isset($p['payload']) ? $p['payload'] : new \stdClass();
        $headers  = isset($p['headers']) && is_array($p['headers']) ? $p['headers'] : [];

        $default_headers = [
            'Content-Type' => 'application/json',
            'User-Agent'   => 'Pi-Dashboard-Webhook-Tester/1.0',
        ];
        $secret = (string) get_option('pi_webhook_secret', '');
        $body_json = wp_json_encode($payload);
        if ($secret !== '') {
            $default_headers['X-Pi-Signature'] = 'sha256=' . hash_hmac('sha256', $body_json, $secret);
        }
        $merged_headers = array_merge($default_headers, $headers);

        $args = [
            'method'    => $method,
            'timeout'   => 25,
            'sslverify' => (wp_get_environment_type() !== 'local'),
            'headers'   => $merged_headers,
        ];
        if ($method !== 'GET') {
            $args['body'] = $body_json;
        }

        $start = microtime(true);
        $resp  = wp_remote_request($url, $args);
        $latency_ms = (int) round((microtime(true) - $start) * 1000);

        if (is_wp_error($resp)) {
            $delivery = self::record_delivery([
                'webhook_id'       => $id,
                'payload'          => $payload,
                'request_headers'  => $merged_headers,
                'response_status'  => 0,
                'response_body'    => $resp->get_error_message(),
                'response_headers' => [],
                'latency_ms'       => $latency_ms,
                'status'           => 'error',
            ]);
            return new \WP_REST_Response([
                'success'         => false,
                'message'         => $resp->get_error_message(),
                'latency_ms'      => $latency_ms,
                'delivery_id'     => $delivery,
            ], 200);
        }

        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = (string) wp_remote_retrieve_body($resp);
        $resp_headers = wp_remote_retrieve_headers($resp);
        $resp_headers_arr = is_object($resp_headers) && method_exists($resp_headers, 'getAll') ? $resp_headers->getAll() :
                            (is_array($resp_headers) ? $resp_headers : []);

        $cookies = [];
        $resp_cookies = wp_remote_retrieve_cookies($resp);
        if (is_array($resp_cookies)) {
            foreach ($resp_cookies as $c) {
                $cookies[] = [
                    'name'  => is_object($c) ? $c->name : ($c['name'] ?? ''),
                    'value' => is_object($c) ? $c->value : ($c['value'] ?? ''),
                ];
            }
        }

        $status = ($code >= 200 && $code < 300) ? 'success' : 'failed';
        $delivery = self::record_delivery([
            'webhook_id'       => $id,
            'payload'          => $payload,
            'request_headers'  => $merged_headers,
            'response_status'  => $code,
            'response_body'    => $body,
            'response_headers' => $resp_headers_arr,
            'latency_ms'       => $latency_ms,
            'status'           => $status,
        ]);

        return new \WP_REST_Response([
            'success'          => $status === 'success',
            'status'           => $code,
            'latency_ms'       => $latency_ms,
            'response_headers' => $resp_headers_arr,
            'response_body'    => $body,
            'cookies'          => $cookies,
            'delivery_id'      => $delivery,
        ], 200);
    }

    public static function recent_deliveries(\WP_REST_Request $request) {
        global $wpdb;
        $id    = (string) $request->get_param('id');
        $limit = max(1, min(200, (int) ($request->get_param('limit') ?: 20)));
        self::ensure_table();

        $table = $wpdb->prefix . 'pi_webhook_deliveries';
        $rows  = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, webhook_id, response_status, latency_ms, status, attempt_count, created_at
                 FROM {$table}
                 WHERE webhook_id = %s
                 ORDER BY id DESC
                 LIMIT %d",
                $id,
                $limit
            ),
            ARRAY_A
        );
        return new \WP_REST_Response(['deliveries' => $rows ?: []], 200);
    }

    public static function retry_delivery(\WP_REST_Request $request) {
        global $wpdb;
        $id  = (string) $request->get_param('id');
        $did = (int)    $request->get_param('delivery_id');
        self::ensure_table();

        $table = $wpdb->prefix . 'pi_webhook_deliveries';
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d AND webhook_id = %s", $did, $id),
            ARRAY_A
        );
        if (!$row) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không tìm thấy delivery'], 404);
        }

        // Re-fire with stored payload + headers
        $payload = json_decode((string) $row['payload'], true);
        $headers = json_decode((string) $row['request_headers'], true);
        $url = self::resolve_webhook_url($id);
        if ($url === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Webhook URL trống'], 400);
        }

        $start = microtime(true);
        $resp = wp_remote_post($url, [
            'timeout'   => 25,
            'sslverify' => (wp_get_environment_type() !== 'local'),
            'headers'   => is_array($headers) ? $headers : [],
            'body'      => wp_json_encode($payload),
        ]);
        $latency_ms = (int) round((microtime(true) - $start) * 1000);
        $code = is_wp_error($resp) ? 0 : (int) wp_remote_retrieve_response_code($resp);
        $body = is_wp_error($resp) ? $resp->get_error_message() : (string) wp_remote_retrieve_body($resp);
        $status = ($code >= 200 && $code < 300) ? 'success' : 'failed';

        $wpdb->update(
            $table,
            [
                'response_status' => $code,
                'response_body'   => $body,
                'latency_ms'      => $latency_ms,
                'status'          => $status,
                'attempt_count'   => ((int) $row['attempt_count']) + 1,
            ],
            ['id' => $did],
            ['%d', '%s', '%d', '%s', '%d'],
            ['%d']
        );
        return new \WP_REST_Response([
            'success'    => $status === 'success',
            'status'     => $code,
            'latency_ms' => $latency_ms,
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       INTERNAL HELPERS
       ═══════════════════════════════════════════════════════════════ */
    private static function list_webhooks() {
        return [
            ['id' => 'new_post', 'name' => 'Bài viết mới',     'url' => (string) get_option('pi_webhook_new_post', '')],
            ['id' => 'new_lead', 'name' => 'Khách hàng mới',   'url' => (string) get_option('pi_webhook_new_lead', get_option('pi_leads_webhook', ''))],
        ];
    }

    private static function resolve_webhook_url($id) {
        switch ($id) {
            case 'new_post':
                return (string) get_option('pi_webhook_new_post', '');
            case 'new_lead':
                return (string) get_option('pi_webhook_new_lead', get_option('pi_leads_webhook', ''));
            case 'leads':
                return (string) get_option('pi_leads_webhook', '');
            default:
                // Fallback: treat id as a direct URL ref option
                $custom = get_option('pi_webhook_' . sanitize_key($id), '');
                return is_string($custom) ? $custom : '';
        }
    }

    private static function ensure_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_webhook_deliveries';
        $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table;
        if ($exists) return;

        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            webhook_id VARCHAR(64) NOT NULL,
            payload LONGTEXT NULL,
            request_headers LONGTEXT NULL,
            response_status INT NOT NULL DEFAULT 0,
            response_body LONGTEXT NULL,
            response_headers LONGTEXT NULL,
            latency_ms INT NOT NULL DEFAULT 0,
            status VARCHAR(16) NOT NULL DEFAULT 'pending',
            attempt_count INT NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY webhook_id (webhook_id),
            KEY created_at (created_at)
        ) {$charset};";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    private static function record_delivery($args) {
        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_webhook_deliveries';
        $wpdb->insert($table, [
            'webhook_id'       => sanitize_text_field((string) ($args['webhook_id'] ?? '')),
            'payload'          => wp_json_encode($args['payload'] ?? null),
            'request_headers'  => wp_json_encode($args['request_headers'] ?? []),
            'response_status'  => (int) ($args['response_status'] ?? 0),
            'response_body'    => (string) ($args['response_body'] ?? ''),
            'response_headers' => wp_json_encode($args['response_headers'] ?? []),
            'latency_ms'       => (int) ($args['latency_ms'] ?? 0),
            'status'           => sanitize_text_field((string) ($args['status'] ?? 'pending')),
            'attempt_count'    => 1,
        ]);
        return (int) $wpdb->insert_id;
    }
}
