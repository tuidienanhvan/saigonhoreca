<?php
namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

class BackendClient {
    private static function backendUrl(): string {
        return rtrim((string) get_option('pi_api_backend_url', PI_API_BACKEND_URL), '/');
    }

    private static function request(string $method, string $path, array $body = [], string $jwt = ''): array {
        if (defined('PI_API_MOCK_MODE') && PI_API_MOCK_MODE) {
            return self::mockResponse($path, $body);
        }

        $args = [
            'method'  => $method,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'timeout' => 15,
        ];
        if ($jwt !== '') {
            $args['headers']['Authorization'] = 'Bearer ' . $jwt;
        }
        if (!empty($body)) {
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request(self::backendUrl() . $path, $args);
        if (is_wp_error($response)) {
            return ['success' => false, 'error' => $response->get_error_message()];
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $data = json_decode((string) wp_remote_retrieve_body($response), true);
        if (!is_array($data)) {
            $data = [];
        }

        return [
            'success' => $code >= 200 && $code < 300,
            'status'  => $code,
            'data'    => $data,
            'error'   => $data['message'] ?? $data['detail'] ?? ('HTTP ' . $code),
        ];
    }

    public static function activate(string $licenseKey, string $domain): array {
        return self::request('POST', '/auth/activate', [
            'license_key'    => $licenseKey,
            'domain'         => $domain,
            'site_url'       => home_url(),
            'wp_version'     => get_bloginfo('version'),
            'plugin_version' => PI_API_VERSION,
        ]);
    }

    public static function deactivate(string $licenseKey): array {
        return self::request('POST', '/auth/deactivate', [
            'license_key' => $licenseKey,
            'domain'      => self::domain(),
        ]);
    }

    public static function heartbeat(string $licenseKey): array {
        return self::request('POST', '/auth/heartbeat', [
            'license_key'    => $licenseKey,
            'domain'         => self::domain(),
            'wp_version'     => get_bloginfo('version'),
            'plugin_version' => PI_API_VERSION,
        ]);
    }

    public static function issueJwt(string $licenseKey): array {
        return self::request('POST', '/auth/issue-jwt', [
            'license_key' => $licenseKey,
            'domain'      => self::domain(),
        ]);
    }

    public static function reportUsage(int $tokensInput, int $tokensOutput, string $jwt): array {
        return self::request('POST', '/v1/usage/report', [
            'tokens_input'  => max(0, $tokensInput),
            'tokens_output' => max(0, $tokensOutput),
            'source'        => 'wp-plugin',
            'site_url'      => home_url(),
        ], $jwt);
    }

    public static function registerSiteCredentials(string $email, string $appPass): array {
        return self::request('POST', '/auth/register-credentials', [
            'email'    => $email,
            'app_pass' => $appPass,
            'site_url' => home_url(),
            'domain'   => self::domain(),
        ]);
    }

    private static function domain(): string {
        return (string) wp_parse_url(home_url(), PHP_URL_HOST);
    }

    private static function mockResponse(string $path, array $body): array {
        $key = (string) ($body['license_key'] ?? '');
        if ($key !== 'TESTING-12345-DEMO0-KEYAA' && str_contains($path, '/activate')) {
            return ['success' => false, 'status' => 403, 'error' => 'Mock license key invalid'];
        }

        $payload = [
            'tier'       => 'pro',
            'status'     => 'active',
            'features'   => ['ai_chatbot', 'seo_audit', 'lead_pipeline', 'analytics'],
            // Tạo mock JWT hợp lệ mà jwtDecode (JS) có thể parse được.
            // Nếu dùng chuỗi 'mock.jwt.token' thì jwtDecode sẽ throw → iframe bị lỗi.
            'jwt'        => self::buildMockJwt(),
            'expires_in' => 86400,
        ];

        if (str_contains($path, '/usage/report')) {
            return ['success' => true, 'status' => 202, 'data' => ['accepted' => true]];
        }

        return ['success' => true, 'status' => 200, 'data' => $payload];
    }

    /**
     * Tạo mock JWT hợp lệ (3-part base64url) để jwtDecode (JS) parse được.
     * KHÔNG dùng cho production — chỉ khi PI_API_MOCK_MODE = true.
     */
    private static function buildMockJwt(): string {
        $header = self::base64url(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = self::base64url([
            'tier'       => 'max',
            'features'   => ['*'],
            'is_admin'   => true,
            'domain'     => 'local.dev',
            'tenant_id'  => 1,
            'exp'        => time() + 86400, // 24 giờ
        ]);
        return $header . '.' . $payload . '.mock-dev-signature';
    }

    private static function base64url(array $data): string {
        return rtrim(strtr(base64_encode((string) wp_json_encode($data)), '+/', '-_'), '=');
    }
}
