<?php
namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renders the Pi Dashboard iframe inside WP admin.
 *
 * Architecture:
 *   - Dashboard webapp lives at PI_API_DASHBOARD_URL (default: pi-dashboard-wordpress.vercel.app).
 *   - This class registers the admin menu, enqueues a thin bridge script,
 *     and renders an <iframe> that loads the webapp with a short-lived JWT.
 *   - JWT lifecycle: Settings::ensureValidJwt() is the single source of
 *     truth — used both at first render (renderPage) and on session
 *     refresh (JwtAjax::handle, triggered by postMessage from the iframe).
 *   - License-inactive sites are redirected to the activation form, not
 *     rendered with a placeholder JWT.
 *   - A development-only mock JWT is generated locally (devMockJwt) ONLY
 *     when WP_DEBUG or PI_API_DEV_MODE is enabled, so a dev can iterate
 *     on the iframe shell without a live backend or license.
 */
class IframeRenderer {
    public const MENU_SLUG = 'pi-api-dashboard';
    public const SCREEN_ID = 'toplevel_page_pi-api-dashboard';

    public static function init(): void {
        add_action('admin_menu', [self::class, 'registerMenu']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAssets']);
        add_action('admin_head', [self::class, 'injectHeadMeta']);
    }

    public static function registerMenu(): void {
        add_menu_page(
            __('Pi Dashboard', 'pi-api'),
            __('Pi Dashboard', 'pi-api'),
            'manage_options',
            self::MENU_SLUG,
            [self::class, 'renderPage'],
            'dashicons-chart-area',
            55
        );
    }

    /**
     * Inject <head> meta only on the Pi Dashboard page:
     *   1. preconnect to backend for faster TLS handshake
     *   2. CSP frame-src whitelisting the backend origin
     */
    public static function injectHeadMeta(): void {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== self::SCREEN_ID) {
            return;
        }
        $origin = self::backendOrigin();
        if ($origin === '') {
            return;
        }
        printf('<link rel="preconnect" href="%s" crossorigin>' . "\n", esc_url($origin));
        printf(
            '<meta http-equiv="Content-Security-Policy" content="frame-src %s;">' . "\n",
            esc_attr($origin)
        );
    }

    public static function enqueueAssets(string $hook): void {
        if ($hook !== self::SCREEN_ID) {
            return;
        }

        wp_enqueue_style('pi-api-iframe', PI_API_URL . 'assets/css/iframe.css', [], PI_API_VERSION);
        wp_enqueue_script('pi-api-iframe-bridge', PI_API_URL . 'assets/js/iframe-bridge.js', [], PI_API_VERSION, true);

        wp_localize_script('pi-api-iframe-bridge', 'PiApiIframe', [
            'backendOrigin' => self::backendOrigin(),
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'refreshAction' => JwtAjax::ACTION,
            'nonce'         => wp_create_nonce(JwtAjax::NONCE),
            'siteUrl'       => home_url(),
            'wpVersion'     => get_bloginfo('version'),
            'loaderTimeout' => (int) apply_filters('pi_api_iframe_loader_timeout', 8000),
            'isDevMode'     => self::isDevMode(),
        ]);
    }

    public static function renderPage(): void {
        // License gate: send unactivated sites to the activation form. Dev
        // mode bypasses so local iteration on the iframe shell still works.
        if (!Settings::isActive() && !self::isDevMode()) {
            include PI_API_DIR . 'views/license-page.php';
            return;
        }

        $jwt_result     = Settings::ensureValidJwt();
        $backend_origin = self::backendOrigin();

        // Production: if JWT issuance fails, render the error view. NEVER
        // fall back to a self-signed mock — that would grant tier=max,
        // is_admin=true on a live site.
        if (!empty($jwt_result['error']) && !self::isDevMode()) {
            $error_code = (string) $jwt_result['error'];
            include PI_API_DIR . 'views/iframe-error-page.php';
            return;
        }

        $jwt        = (string) $jwt_result['jwt'];
        $expires_in = (int) $jwt_result['expires_in'];

        // Dev fallback: only when WP_DEBUG / PI_API_DEV_MODE is true.
        if ($jwt === '' && self::isDevMode()) {
            $jwt        = self::devMockJwt();
            $expires_in = 86400;
        }

        $iframe_url = self::buildIframeUrl($jwt);

        include PI_API_DIR . 'views/iframe-page.php';
    }

    /**
     * Public so views and other plugin components can resolve the backend
     * origin without leaking private state via PHP's `self::` resolution
     * inside included template files.
     */
    public static function backendOrigin(): string {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $parts  = wp_parse_url(PI_API_DASHBOARD_URL);
        $scheme = (string) ($parts['scheme'] ?? 'https');
        $host   = (string) ($parts['host'] ?? '');
        $port   = isset($parts['port']) ? ':' . (int) $parts['port'] : '';
        $cached = $host !== '' ? $scheme . '://' . $host . $port : '';
        return $cached;
    }

    /**
     * Build iframe URL. JWT travels as a query param because cross-origin
     * iframes cannot receive a body. `siteUrl` is intentionally NOT in the
     * query — the bridge script forwards it via postMessage after the
     * webapp signals `pi-api/ready`, keeping it out of access logs and
     * referrer headers.
     */
    private static function buildIframeUrl(string $jwt): string {
        return add_query_arg([
            't'      => $jwt,
            'iframe' => '1',
        ], PI_API_DASHBOARD_URL);
    }

    private static function isDevMode(): bool {
        return (defined('PI_API_DEV_MODE') && PI_API_DEV_MODE)
            || (defined('WP_DEBUG') && WP_DEBUG);
    }

    /**
     * Generate a local mock JWT for dev-only use. Guarded by isDevMode().
     * Never invoked in production code paths.
     */
    private static function devMockJwt(): string {
        $header  = self::b64url(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = self::b64url([
            'tier'      => 'max',
            'features'  => ['*'],
            'is_admin'  => true,
            'domain'    => 'local.dev',
            'tenant_id' => 1,
            'exp'       => time() + 86400,
        ]);
        return $header . '.' . $payload . '.dev-mock-signature';
    }

    private static function b64url(array $data): string {
        return rtrim(strtr(base64_encode((string) wp_json_encode($data)), '+/', '-_'), '=');
    }
}
