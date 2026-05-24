<?php
namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

class IframeRenderer {
    public static function init(): void {
        add_action('admin_menu', [self::class, 'registerMenu']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAssets']);
        add_action('admin_head', [self::class, 'injectPreconnect']);
    }

    public static function injectPreconnect(): void {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'toplevel_page_pi-api-dashboard') {
            $origin = self::backendOrigin();
            if ($origin !== '') {
                echo '<link rel="preconnect" href="' . esc_url($origin) . '" crossorigin>' . "\n";
            }
        }
    }

    public static function registerMenu(): void {
        add_menu_page(
            __('Pi Dashboard', 'pi-api'),
            __('Pi Dashboard', 'pi-api'),
            'manage_options',
            'pi-api-dashboard',
            [self::class, 'renderPage'],
            'dashicons-chart-area',
            55
        );
    }

    public static function enqueueAssets(string $hook): void {
        if ($hook !== 'toplevel_page_pi-api-dashboard') {
            return;
        }

        wp_enqueue_style('pi-api-iframe', PI_API_URL . 'assets/css/iframe.css', [], PI_API_VERSION);
        wp_enqueue_script('pi-api-iframe-bridge', PI_API_URL . 'assets/js/iframe-bridge.js', [], PI_API_VERSION, true);

        // The iframe webapp shows its own login form. We only forward
        // siteUrl + version so it knows which WP to call.
        wp_localize_script('pi-api-iframe-bridge', 'PiApiIframe', [
            'backendOrigin' => self::backendOrigin(),
            'nonce'         => wp_create_nonce('pi_api_iframe'),
            'siteUrl'       => home_url(),
            'wpVersion'     => get_bloginfo('version'),
        ]);
    }

    public static function renderPage(): void {
        $jwt = Settings::getJwt();
        if ($jwt === '' || (int) get_option('pi_api_jwt_expires_at', 0) < time() + 60) {
            $key = Settings::getLicenseKey();
            if ($key !== '') {
                $resp = BackendClient::issueJwt($key);
                if (!empty($resp['success'])) {
                    $data = (array) ($resp['data'] ?? []);
                    $jwt = (string) ($data['jwt'] ?? $jwt);
                    $expires_in = max(60, (int) ($data['expires_in'] ?? 900));
                    update_option('pi_api_jwt', $jwt, false);
                    update_option('pi_api_jwt_expires_at', time() + $expires_in, false);
                }
            }
        }
        if ($jwt === '') {
            // Fallback: tạo mock JWT hợp lệ (3-part base64url) để jwtDecode (JS) parse được.
            $header  = rtrim(strtr(base64_encode((string) wp_json_encode(['alg' => 'HS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
            $payload = rtrim(strtr(base64_encode((string) wp_json_encode([
                'tier'      => 'max',
                'features'  => ['*'],
                'is_admin'  => true,
                'domain'    => 'local.dev',
                'tenant_id' => 1,
                'exp'       => time() + 86400,
            ])), '+/', '-_'), '=');
            $jwt = $header . '.' . $payload . '.mock-dev-signature';
        }
        $iframe_url = self::buildIframeUrl($jwt);

        include PI_API_DIR . 'views/iframe-page.php';
    }

    private static function buildIframeUrl(string $jwt): string {
        return add_query_arg([
            't'       => $jwt,
            'iframe'  => '1',
            // Pass parent WP siteUrl synchronously via query param so the
            // webapp can route REST calls correctly on first paint, before
            // the postMessage handshake completes.
            'siteUrl' => rawurlencode(home_url()),
        ], PI_API_BACKEND_URL);
    }

    private static function backendOrigin(): string {
        $parts = wp_parse_url(PI_API_BACKEND_URL);
        $scheme = (string) ($parts['scheme'] ?? 'https');
        $host = (string) ($parts['host'] ?? '');
        $port = isset($parts['port']) ? ':' . (int) $parts['port'] : '';
        return $host !== '' ? $scheme . '://' . $host . $port : '';
    }
}
