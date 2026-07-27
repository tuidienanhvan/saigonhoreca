<?php
/**
 * Pi API License Endpoints
 *
 * Endpoints:
 * - GET  /license/status
 * - GET  /license
 * - POST /license/activate
 * - POST /license/deactivate
 * - POST /auth/issue-jwt
 * - POST /license/transfer
 * - GET  /license/features
 * - GET  /license/usage
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pi_Api_License {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/license/status', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_license_status'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/license', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_license_status'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/license/activate', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'activate'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/license/deactivate', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'deactivate'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/auth/issue-jwt', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'issue_jwt'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/license/transfer', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'transfer'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/license/features', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_features'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/license/usage', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_usage'],
            'permission_callback' => $admin,
        ]);

        // NOTE: /license/generate intentionally NOT registered.
        // License creation requires admin user JWT (CurrentAdmin); pi-api only
        // holds tenant/license JWT, so the call would be rejected by backend.
        // Operators must use pi-store-admin webapp instead.
    }

    public static function get_license_status() {
        $key = \PiApi\Settings::getLicenseKey();
        $tier = \PiApi\Settings::getTier();
        $status = (string) get_option('pi_api_status', $key !== '' ? 'active' : 'inactive');

        return new \WP_REST_Response([
            'active'         => \PiApi\Settings::isActive(),
            'status'         => $status,
            'tier'           => $tier,
            'features'       => \PiApi\Settings::getFeatures(),
            'token_quota'    => \PiApi\Settings::getTokenQuota(),
            'masked_key'     => \PiApi\Settings::maskKey($key),
            'has_key'        => $key !== '',
            'jwt_expires_at' => (int) get_option('pi_api_jwt_expires_at', 0),
            'activated_at'   => (int) get_option('pi_api_activated_at', 0),
            'sites_used'     => self::sites_used(),
            'sites_max'      => self::sites_max_for_tier($tier),
            'audit'          => array_slice(self::read_audit(), -20),
        ], 200);
    }

    public static function activate(\WP_REST_Request $req) {
        $body = $req->get_json_params();
        if (!is_array($body)) {
            $body = [];
        }

        $key = isset($body['license_key']) ? (string) $body['license_key'] : (string) ($body['key'] ?? '');
        $key = sanitize_text_field(trim($key));

        if ($key === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Thiếu khóa license.',
            ], 400);
        }

        $resp = \PiApi\BackendClient::activate($key, (string) wp_parse_url(home_url(), PHP_URL_HOST));
        if (empty($resp['success'])) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => (string) ($resp['error'] ?? 'Không kích hoạt được license.'),
            ], 400);
        }

        \PiApi\Settings::storeActivation($key, (array) ($resp['data'] ?? []));
        $tier = \PiApi\Settings::getTier();
        self::audit('activated', ['tier' => $tier]);

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Kích hoạt license thành công.',
            'tier'    => $tier,
            'status'  => self::get_license_status()->get_data(),
        ], 200);
    }

    public static function deactivate() {
        $key = \PiApi\Settings::getLicenseKey();
        if ($key === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Không có license đang kích hoạt.',
            ], 400);
        }

        \PiApi\BackendClient::deactivate($key);
        $previous_tier = \PiApi\Settings::getTier();
        \PiApi\Settings::clearActivation();
        self::audit('deactivated', ['previous_tier' => $previous_tier]);

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã hủy kích hoạt.',
            'status'  => self::get_license_status()->get_data(),
        ], 200);
    }

    public static function issue_jwt() {
        $key = \PiApi\Settings::getLicenseKey();
        if ($key === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Chưa có license đang kích hoạt.',
            ], 400);
        }

        $resp = \PiApi\BackendClient::issueJwt($key);
        if (empty($resp['success'])) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => (string) ($resp['error'] ?? 'Không thể cấp JWT mới.'),
            ], 400);
        }

        $data = (array) ($resp['data'] ?? []);
        $jwt = (string) ($data['jwt'] ?? '');
        $expires_in = max(60, (int) ($data['expires_in'] ?? 900));
        $expires_at = time() + $expires_in;

        update_option('pi_api_jwt', $jwt, false);
        update_option('pi_api_jwt_expires_at', $expires_at, false);

        return new \WP_REST_Response([
            'success'    => true,
            'jwt'        => $jwt,
            'expires_at' => $expires_at,
        ], 200);
    }

    public static function transfer(\WP_REST_Request $req) {
        $body = $req->get_json_params();
        if (!is_array($body)) {
            $body = [];
        }

        $target = isset($body['target_site_url']) ? esc_url_raw((string) $body['target_site_url']) : '';
        if ($target === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Thiếu URL site đích.',
            ], 400);
        }

        if (\PiApi\Settings::getLicenseKey() === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Không có license để chuyển.',
            ], 400);
        }

        self::audit('transferred', ['target' => $target]);

        return new \WP_REST_Response([
            'success'        => true,
            'message'        => 'Yêu cầu chuyển license đã ghi nhận.',
            'target'         => $target,
            'transfer_token' => wp_generate_password(24, false, false),
        ], 200);
    }

    public static function get_features() {
        return new \WP_REST_Response([
            'tiers'    => self::all_tiers(),
            'features' => self::feature_matrix(),
        ], 200);
    }

    public static function get_usage() {
        $tier = \PiApi\Settings::getTier();
        $limits = self::limits_for_tier($tier);

        $ai_used = (int) get_option('pi_ai_cloud_tokens_used_month', 0);
        $storage_mb = (int) get_option('pi_storage_used_mb', 0);
        $api_calls = (int) get_option('pi_api_calls_month', 0);

        return new \WP_REST_Response([
            'tier'   => $tier,
            'limits' => $limits,
            'usage'  => [
                'ai_tokens'  => ['used' => $ai_used, 'max' => $limits['ai_tokens']],
                'storage_mb' => ['used' => $storage_mb, 'max' => $limits['storage_mb']],
                'sites'      => ['used' => self::sites_used(), 'max' => $limits['sites']],
                'api_calls'  => ['used' => $api_calls, 'max' => $limits['api_calls']],
            ],
            'thresholds' => ['warn' => 80, 'critical' => 90],
        ], 200);
    }

    private static function sites_used() {
        $legacy = get_option('pi_license_data', []);
        return is_array($legacy) ? (int) ($legacy['sites_used'] ?? 1) : 1;
    }

    private static function read_audit() {
        $audit = get_option('pi_license_audit', []);
        return is_array($audit) ? $audit : [];
    }

    private static function audit($event, $meta = []) {
        $log = self::read_audit();
        $log[] = [
            'event' => sanitize_key($event),
            'at'    => gmdate('c'),
            'user'  => function_exists('wp_get_current_user') ? (wp_get_current_user()->user_login ?? '') : '',
            'meta'  => is_array($meta) ? $meta : [],
        ];

        if (count($log) > 200) {
            $log = array_slice($log, -200);
        }

        update_option('pi_license_audit', $log, false);
    }

    private static function all_tiers() {
        return [
            ['id' => 'free', 'label' => 'Free', 'price_vnd' => 0, 'color' => '#94a3b8'],
            ['id' => 'pro', 'label' => 'Pro', 'price_vnd' => 1990000, 'color' => '#10b981'],
            ['id' => 'max', 'label' => 'Max', 'price_vnd' => 4990000, 'color' => '#f59e0b'],
            ['id' => 'enterprise', 'label' => 'Enterprise', 'price_vnd' => null, 'color' => '#8b5cf6'],
        ];
    }

    private static function sites_max_for_tier($tier) {
        $limits = self::limits_for_tier($tier);
        return $limits['sites'];
    }

    private static function limits_for_tier($tier) {
        switch ($tier) {
            case 'enterprise':
                return ['sites' => -1, 'ai_tokens' => -1, 'storage_mb' => -1, 'api_calls' => -1];
            case 'max':
                return ['sites' => 25, 'ai_tokens' => 500000, 'storage_mb' => 51200, 'api_calls' => 500000];
            case 'pro':
                return ['sites' => 5, 'ai_tokens' => 100000, 'storage_mb' => 10240, 'api_calls' => 100000];
            default:
                return ['sites' => 1, 'ai_tokens' => 5000, 'storage_mb' => 1024, 'api_calls' => 5000];
        }
    }

    public static function feature_matrix() {
        $row = function ($id, $label, $group, $free, $pro, $max) {
            return ['id' => $id, 'label' => $label, 'group' => $group, 'free' => $free, 'pro' => $pro, 'max' => $max];
        };

        return [
            $row('dashboard', 'Tổng quan dashboard', 'Cốt lõi', true, true, true),
            $row('content_mgmt', 'Quản lý bài viết / trang', 'Cốt lõi', true, true, true),
            $row('media_library', 'Thư viện media', 'Cốt lõi', true, true, true),
            $row('comments', 'Quản lý bình luận', 'Cốt lõi', true, true, true),
            $row('categories', 'Phân loại / Menu', 'Cốt lõi', true, true, true),
            $row('leads_basic', 'Leads cơ bản', 'Khách hàng', true, true, true),
            $row('leads_pipeline', 'Pipeline khách hàng', 'Khách hàng', false, true, true),
            $row('leads_scoring', 'Chấm điểm leads bằng AI', 'Khách hàng', false, true, true),
            $row('leads_enrichment', 'Lead enrichment', 'Khách hàng', false, true, true),
            $row('seo_basic', 'SEO cơ bản', 'SEO', true, true, true),
            $row('seo_audit', 'SEO Audit', 'SEO', true, true, true),
            $row('seo_schema', 'JSON-LD Schema', 'SEO', false, true, true),
            $row('seo_redirects', 'Redirects và 404 monitor', 'SEO', false, true, true),
            $row('seo_ai_bot', 'AI SEO Bot', 'SEO', false, true, true),
            $row('seo_internal_links', 'Gợi ý internal link', 'SEO', false, true, true),
            $row('seo_og_image', 'OG Image generator', 'SEO', false, true, true),
            $row('seo_indexing', 'Instant Indexing API', 'SEO', false, true, true),
            $row('seo_llm_txt', 'LLM.txt cho AI crawler', 'SEO', false, true, true),
            $row('seo_gsc', 'Search Console integration', 'SEO', false, true, true),
            $row('ai_chatbot', 'Chatbot cơ bản', 'AI', true, true, true),
            $row('ai_cloud', 'AI Cloud Gateway', 'AI', false, true, true),
            $row('ai_kb', 'Chatbot KB nâng cao', 'AI', false, true, true),
            $row('ai_unlimited', 'AI tokens không giới hạn', 'AI', false, false, true),
            $row('forms_basic', 'Form builder cơ bản', 'Marketing', true, true, true),
            $row('email_templates', 'Email templates', 'Marketing', false, true, true),
            $row('email_campaigns', 'Email campaigns', 'Marketing', false, true, true),
            $row('funnels', 'Funnel builder', 'Marketing', false, true, true),
            $row('webhooks', 'Webhooks ngoài', 'Marketing', false, true, true),
            $row('perf_basic', 'Hiệu suất cơ bản', 'Hiệu suất', true, true, true),
            $row('perf_pro', 'Performance PRO', 'Hiệu suất', false, true, true),
            $row('perf_cdn', 'CDN rewriter', 'Hiệu suất', false, true, true),
            $row('perf_pwa', 'PWA và offline', 'Hiệu suất', false, true, true),
            $row('perf_nano_cache', 'Nano Cache page', 'Hiệu suất', false, true, true),
            $row('system_health', 'System health', 'Hệ thống', true, true, true),
            $row('cron_scheduler', 'Cron scheduler', 'Hệ thống', false, true, true),
            $row('plugin_manager', 'Plugin manager', 'Hệ thống', false, true, true),
            $row('theme_manager', 'Theme manager', 'Hệ thống', false, true, true),
            $row('db_explorer', 'DB Explorer read-only', 'Hệ thống', false, true, true),
            $row('backup_local', 'Backup local', 'Hệ thống', false, true, true),
            $row('backup_s3', 'Backup S3 / cloud', 'Hệ thống', false, true, true),
            $row('multi_site', 'Quản lý multi-site', 'Max', false, false, true),
            $row('white_label', 'White label', 'Max', false, false, true),
            $row('priority_support', 'Hỗ trợ ưu tiên 24/7', 'Max', false, false, true),
            $row('client_reports', 'Báo cáo khách hàng', 'Max', false, false, true),
            $row('unlimited_keys', 'Không giới hạn API keys', 'Max', false, false, true),
        ];
    }

    public static function features_for_tier($tier) {
        if ($tier === 'enterprise') {
            $features = [];
            foreach (self::feature_matrix() as $item) {
                $features[$item['id']] = true;
            }
            return $features;
        }

        $map = [];
        foreach (self::feature_matrix() as $item) {
            $map[$item['id']] = (bool) ($item[$tier] ?? false);
        }
        return $map;
    }
}
