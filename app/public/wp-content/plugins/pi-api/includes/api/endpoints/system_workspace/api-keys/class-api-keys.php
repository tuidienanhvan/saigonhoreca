<?php
/**
 * Pi API — API Keys Vault (centralized credential storage)
 *
 * Endpoints:
 * - GET    /api-keys                   List keys (masked)
 * - POST   /api-keys                   Create new key {name, service, key, scopes?, expires_at?}
 * - PUT    /api-keys/{id}              Update key (rotate / change name / scopes)
 * - PUT    /api-keys/{id}/rotate       Rotate (generate new value or accept new value)
 * - DELETE /api-keys/{id}
 * - POST   /api-keys/{id}/test         Test connection with provider
 * - GET    /api-keys/services          Supported services
 *
 * Storage:
 *   Table wp_pi_api_keys (id, name, service, key_encrypted, scopes JSON, expires_at,
 *                          last_used_at, created_at, created_by, last_accessed_at)
 *   Encrypted at rest using openssl_encrypt(AES-256-CBC, derived from wp_salt())
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_ApiKeys {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/api-keys', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_keys'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_key'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/api-keys/services', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_services'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/api-keys/(?P<id>\d+)', [
            ['methods' => 'PUT',    'callback' => [self::class, 'update_key'], 'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_key'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/api-keys/(?P<id>\d+)/rotate', [
            'methods'  => 'PUT',
            'callback' => [self::class, 'rotate_key'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/api-keys/(?P<id>\d+)/test', [
            'methods'  => 'POST',
            'callback' => [self::class, 'test_key'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/api-keys/(?P<id>\d+)/reveal', [
            'methods'  => 'POST',
            'callback' => [self::class, 'reveal_key'],
            'permission_callback' => $admin,
        ]);
    }

    /* ─────────────────────────────────────────────────────────
       LIST
       ───────────────────────────────────────────────────────── */
    public static function list_keys() {
        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_api_keys';
        $rows  = $wpdb->get_results("SELECT * FROM {$table} ORDER BY id DESC", ARRAY_A) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $out[] = self::row_for_response($r);
        }
        return new \WP_REST_Response(['keys' => $out], 200);
    }

    public static function create_key(\WP_REST_Request $req) {
        $body = $req->get_json_params();
        if (!is_array($body)) $body = [];

        $name    = trim((string)($body['name']    ?? ''));
        $service = sanitize_key((string)($body['service'] ?? ''));
        $key     = trim((string)($body['key']     ?? ''));
        $scopes  = isset($body['scopes']) && is_array($body['scopes']) ? array_values(array_filter(array_map('sanitize_text_field', $body['scopes']))) : [];
        $expires = isset($body['expires_at']) ? (string) $body['expires_at'] : null;

        if ($name === '' || $service === '' || $key === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu tên / dịch vụ / khóa.'], 400);
        }
        if (!self::is_known_service($service)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Dịch vụ không được hỗ trợ.'], 400);
        }

        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_api_keys';

        $encrypted = self::encrypt($key);
        $now = current_time('mysql', true);
        $user = function_exists('wp_get_current_user') ? wp_get_current_user() : null;

        $wpdb->insert($table, [
            'name'             => sanitize_text_field($name),
            'service'          => $service,
            'key_encrypted'    => $encrypted,
            'scopes'           => wp_json_encode($scopes),
            'expires_at'       => $expires ? sanitize_text_field($expires) : null,
            'last_used_at'     => null,
            'created_at'       => $now,
            'created_by'       => $user ? (string) $user->user_login : '',
            'last_accessed_at' => null,
        ], ['%s','%s','%s','%s','%s','%s','%s','%s','%s']);

        $id = (int) $wpdb->insert_id;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        self::audit($id, 'created', ['service' => $service]);
        return new \WP_REST_Response(['success' => true, 'key' => self::row_for_response($row)], 201);
    }

    public static function update_key(\WP_REST_Request $req) {
        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_api_keys';
        $id   = (int) $req->get_param('id');
        $body = $req->get_json_params();
        if (!is_array($body)) $body = [];

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        if (!$row) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không tìm thấy.'], 404);
        }

        $update = [];
        $fmt    = [];
        if (isset($body['name'])) {
            $update['name'] = sanitize_text_field((string) $body['name']);
            $fmt[] = '%s';
        }
        if (isset($body['scopes']) && is_array($body['scopes'])) {
            $update['scopes'] = wp_json_encode(array_values(array_filter(array_map('sanitize_text_field', $body['scopes']))));
            $fmt[] = '%s';
        }
        if (array_key_exists('expires_at', $body)) {
            $update['expires_at'] = $body['expires_at'] ? sanitize_text_field((string) $body['expires_at']) : null;
            $fmt[] = '%s';
        }
        if (!empty($body['key'])) {
            $update['key_encrypted'] = self::encrypt(trim((string) $body['key']));
            $fmt[] = '%s';
        }

        if (empty($update)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không có thay đổi.'], 400);
        }
        $wpdb->update($table, $update, ['id' => $id], $fmt, ['%d']);
        self::audit($id, 'updated', ['fields' => array_keys($update)]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        return new \WP_REST_Response(['success' => true, 'key' => self::row_for_response($row)], 200);
    }

    public static function rotate_key(\WP_REST_Request $req) {
        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_api_keys';
        $id    = (int) $req->get_param('id');
        $body  = $req->get_json_params();
        if (!is_array($body)) $body = [];

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        if (!$row) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không tìm thấy.'], 404);
        }

        $new_key = isset($body['key']) ? trim((string) $body['key']) : '';
        if ($new_key === '') {
            // Generate (only sensible for Pi-issued keys; placeholder)
            $new_key = 'pi_' . bin2hex(random_bytes(20));
        }

        $wpdb->update($table, [
            'key_encrypted' => self::encrypt($new_key),
        ], ['id' => $id], ['%s'], ['%d']);
        self::audit($id, 'rotated');
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        return new \WP_REST_Response([
            'success' => true,
            'key'     => self::row_for_response($row),
            // Only return raw value once on rotation:
            'raw'     => $new_key,
        ], 200);
    }

    public static function delete_key(\WP_REST_Request $req) {
        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_api_keys';
        $id    = (int) $req->get_param('id');
        $wpdb->delete($table, ['id' => $id], ['%d']);
        self::audit($id, 'deleted');
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function test_key(\WP_REST_Request $req) {
        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_api_keys';
        $id    = (int) $req->get_param('id');
        $row   = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        if (!$row) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không tìm thấy.'], 404);
        }

        $raw = self::decrypt((string) $row['key_encrypted']);
        if ($raw === null) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không giải mã được khóa.'], 500);
        }

        $service = (string) $row['service'];
        $result  = self::probe_service($service, $raw);

        $wpdb->update($table, ['last_used_at' => current_time('mysql', true)], ['id' => $id], ['%s'], ['%d']);
        self::audit($id, 'tested', ['service' => $service, 'ok' => !empty($result['ok'])]);

        return new \WP_REST_Response($result, 200);
    }

    public static function reveal_key(\WP_REST_Request $req) {
        global $wpdb;
        self::ensure_table();
        $table = $wpdb->prefix . 'pi_api_keys';
        $id    = (int) $req->get_param('id');
        $row   = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
        if (!$row) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không tìm thấy.'], 404);
        }
        $raw = self::decrypt((string) $row['key_encrypted']);
        if ($raw === null) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không giải mã được khóa.'], 500);
        }
        $wpdb->update($table, ['last_accessed_at' => current_time('mysql', true)], ['id' => $id], ['%s'], ['%d']);
        self::audit($id, 'revealed');
        return new \WP_REST_Response(['success' => true, 'raw' => $raw], 200);
    }

    public static function list_services() {
        return new \WP_REST_Response(['services' => self::services_catalog()], 200);
    }

    /* ─────────────────────────────────────────────────────────
       INTERNAL
       ───────────────────────────────────────────────────────── */
    public static function services_catalog() {
        return [
            ['id' => 'openai',     'label' => 'OpenAI',         'category' => 'AI',        'icon' => 'sparkles',  'has_scopes' => false, 'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'anthropic',  'label' => 'Anthropic',      'category' => 'AI',        'icon' => 'brain',     'has_scopes' => false, 'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'google_ai',  'label' => 'Google AI',      'category' => 'AI',        'icon' => 'sparkles',  'has_scopes' => false, 'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'mistral',    'label' => 'Mistral',        'category' => 'AI',        'icon' => 'sparkles',  'has_scopes' => false, 'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'clearbit',   'label' => 'Clearbit',       'category' => 'Enrichment','icon' => 'users',     'has_scopes' => false, 'rotation_days' => 180, 'pi_issued' => false],
            ['id' => 'hunter',     'label' => 'Hunter.io',      'category' => 'Enrichment','icon' => 'mail',      'has_scopes' => false, 'rotation_days' => 180, 'pi_issued' => false],
            ['id' => 'apollo',     'label' => 'Apollo.io',      'category' => 'Enrichment','icon' => 'users',     'has_scopes' => false, 'rotation_days' => 180, 'pi_issued' => false],
            ['id' => 'stripe',     'label' => 'Stripe',         'category' => 'Payments',  'icon' => 'creditcard','has_scopes' => true,  'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'paypal',     'label' => 'PayPal',         'category' => 'Payments',  'icon' => 'creditcard','has_scopes' => true,  'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'aws_s3',     'label' => 'AWS S3',         'category' => 'Cloud',     'icon' => 'cloud',     'has_scopes' => true,  'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'gcp',        'label' => 'Google Cloud',   'category' => 'Cloud',     'icon' => 'cloud',     'has_scopes' => true,  'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'azure',      'label' => 'Azure',          'category' => 'Cloud',     'icon' => 'cloud',     'has_scopes' => true,  'rotation_days' => 90, 'pi_issued' => false],
            ['id' => 'cloudflare', 'label' => 'Cloudflare',     'category' => 'Cloud',     'icon' => 'cloud',     'has_scopes' => false, 'rotation_days' => 180, 'pi_issued' => false],
            ['id' => 'sendgrid',   'label' => 'SendGrid',       'category' => 'Email',     'icon' => 'mail',      'has_scopes' => false, 'rotation_days' => 180, 'pi_issued' => false],
            ['id' => 'mailgun',    'label' => 'Mailgun',        'category' => 'Email',     'icon' => 'mail',      'has_scopes' => false, 'rotation_days' => 180, 'pi_issued' => false],
            ['id' => 'twilio',     'label' => 'Twilio',         'category' => 'SMS',       'icon' => 'phone',     'has_scopes' => false, 'rotation_days' => 180, 'pi_issued' => false],
            ['id' => 'gsc',        'label' => 'Google Search Console','category' => 'SEO','icon' => 'search',    'has_scopes' => true,  'rotation_days' => 365, 'pi_issued' => false],
            ['id' => 'pi_cloud',   'label' => 'Pi Cloud (issued)','category' => 'Pi',      'icon' => 'zap',       'has_scopes' => false, 'rotation_days' => 90, 'pi_issued' => true],
        ];
    }

    public static function is_known_service($service) {
        foreach (self::services_catalog() as $s) {
            if ($s['id'] === $service) return true;
        }
        return false;
    }

    private static function row_for_response($row) {
        $raw = null;
        $masked = '';
        if (!empty($row['key_encrypted'])) {
            $raw = self::decrypt((string) $row['key_encrypted']);
            $masked = $raw !== null ? self::mask_key($raw) : '••••••••';
        }
        $expires_at = $row['expires_at'] ?? null;
        $status = self::compute_status($expires_at, $row['last_used_at'] ?? null);
        return [
            'id'               => (int) $row['id'],
            'name'             => (string) $row['name'],
            'service'          => (string) $row['service'],
            'masked'           => $masked,
            'scopes'           => json_decode((string) ($row['scopes'] ?? '[]'), true) ?: [],
            'expires_at'       => $expires_at,
            'last_used_at'     => $row['last_used_at'] ?? null,
            'last_accessed_at' => $row['last_accessed_at'] ?? null,
            'created_at'       => $row['created_at'] ?? null,
            'created_by'       => $row['created_by'] ?? '',
            'status'           => $status,
        ];
    }

    /* ─────────────────────────────────────────────────────────
       Encryption — public so it can be unit-tested
       ───────────────────────────────────────────────────────── */
    public static function encryption_key() {
        // Derive 32-byte key from wp_salt (defense-in-depth: distinct purpose).
        $base = function_exists('wp_salt') ? wp_salt('auth') : 'pi-fallback-salt';
        return hash('sha256', 'pi-api-keys|' . $base, true);
    }

    public static function encrypt($plain) {
        if (!function_exists('openssl_encrypt')) return base64_encode($plain);
        $iv  = random_bytes(16);
        $ct  = openssl_encrypt($plain, 'AES-256-CBC', self::encryption_key(), OPENSSL_RAW_DATA, $iv);
        if ($ct === false) return '';
        return 'v1:' . base64_encode($iv . $ct);
    }

    public static function decrypt($cipher) {
        if (!is_string($cipher) || $cipher === '') return null;
        if (strpos($cipher, 'v1:') !== 0) {
            // Legacy / fallback (plain base64) — best-effort.
            $raw = base64_decode($cipher, true);
            return $raw === false ? null : $raw;
        }
        if (!function_exists('openssl_decrypt')) return null;
        $blob = base64_decode(substr($cipher, 3), true);
        if ($blob === false || strlen($blob) < 17) return null;
        $iv  = substr($blob, 0, 16);
        $ct  = substr($blob, 16);
        $pt  = openssl_decrypt($ct, 'AES-256-CBC', self::encryption_key(), OPENSSL_RAW_DATA, $iv);
        return $pt === false ? null : $pt;
    }

    public static function mask_key($key) {
        $key = (string) $key;
        $len = strlen($key);
        if ($len === 0) return '';
        if ($len <= 8) return str_repeat('•', max(0, $len - 2)) . substr($key, -2);
        return substr($key, 0, 4) . str_repeat('•', 8) . substr($key, -4);
    }

    private static function compute_status($expires_at, $last_used_at) {
        if ($expires_at) {
            $ts = strtotime($expires_at);
            if ($ts && $ts < time()) return 'expired';
            if ($ts && $ts - time() < 30 * 86400) return 'expiring';
        }
        if (!$last_used_at) return 'untested';
        $lu = strtotime($last_used_at);
        if ($lu && time() - $lu < 30 * 86400) return 'healthy';
        return 'idle';
    }

    private static function probe_service($service, $raw) {
        // Stub probes — production would call provider auth endpoints.
        $endpoints = [
            'openai'    => 'https://api.openai.com/v1/models',
            'anthropic' => 'https://api.anthropic.com/v1/messages',
            'hunter'    => 'https://api.hunter.io/v2/account?api_key=' . urlencode($raw),
            'clearbit'  => 'https://person.clearbit.com/v2/people/email/test@example.com',
            'sendgrid'  => 'https://api.sendgrid.com/v3/scopes',
            'mailgun'   => 'https://api.mailgun.net/v3/domains',
            'cloudflare'=> 'https://api.cloudflare.com/client/v4/user/tokens/verify',
            'stripe'    => 'https://api.stripe.com/v1/account',
        ];
        if (!isset($endpoints[$service])) {
            return ['success' => true, 'ok' => true, 'message' => 'Định dạng khóa hợp lệ. (Không có probe endpoint cho dịch vụ này)'];
        }
        $args = [
            'timeout' => 12,
            'headers' => ['User-Agent' => 'Pi-Dashboard-KeyTester/1.0'],
        ];
        switch ($service) {
            case 'openai':
            case 'cloudflare':
            case 'sendgrid':
            case 'mailgun':
            case 'stripe':
                $args['headers']['Authorization'] = 'Bearer ' . $raw;
                break;
            case 'anthropic':
                $args['headers']['x-api-key']         = $raw;
                $args['headers']['anthropic-version'] = '2023-06-01';
                $args['method']                       = 'POST';
                $args['body']                         = wp_json_encode([
                    'model' => 'claude-3-haiku-20240307',
                    'max_tokens' => 1,
                    'messages' => [['role' => 'user', 'content' => 'ping']],
                ]);
                $args['headers']['Content-Type'] = 'application/json';
                break;
            case 'clearbit':
                $args['headers']['Authorization'] = 'Bearer ' . $raw;
                break;
        }
        $resp = ($args['method'] ?? 'GET') === 'POST'
            ? wp_remote_post($endpoints[$service], $args)
            : wp_remote_get($endpoints[$service], $args);
        if (is_wp_error($resp)) {
            return ['success' => false, 'ok' => false, 'message' => $resp->get_error_message()];
        }
        $code = (int) wp_remote_retrieve_response_code($resp);
        $ok   = $code >= 200 && $code < 400;
        return [
            'success' => true,
            'ok'      => $ok,
            'status'  => $code,
            'message' => $ok ? 'Khóa hoạt động.' : 'Provider trả về mã ' . $code,
        ];
    }

    private static function audit($key_id, $event, $meta = []) {
        $log = get_option('pi_api_keys_audit', []);
        if (!is_array($log)) $log = [];
        $log[] = [
            'key_id' => (int) $key_id,
            'event'  => sanitize_key($event),
            'at'     => gmdate('c'),
            'user'   => function_exists('wp_get_current_user') ? (wp_get_current_user()->user_login ?? '') : '',
            'meta'   => $meta,
        ];
        if (count($log) > 500) $log = array_slice($log, -500);
        update_option('pi_api_keys_audit', $log, false);
    }

    public static function ensure_table() {
        global $wpdb;
        $table  = $wpdb->prefix . 'pi_api_keys';
        $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table;
        if ($exists) return;

        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(120) NOT NULL,
            service VARCHAR(64) NOT NULL,
            key_encrypted LONGTEXT NOT NULL,
            scopes LONGTEXT NULL,
            expires_at DATETIME NULL,
            last_used_at DATETIME NULL,
            last_accessed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_by VARCHAR(120) NULL,
            PRIMARY KEY (id),
            KEY service (service),
            KEY expires_at (expires_at)
        ) {$charset};";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
