<?php
/**
 * Plugin Name:       Pi API
 * Plugin URI:        https://pi-ecosystem.com/api
 * Description:       Connect your WordPress site to Pi Dashboard. Manage SEO, leads, AI chatbot, and analytics from one place.
 * Version:           1.0.2
 * Requires at least: 6.0
 * Requires PHP:      8.3
 * Author:            Pi Ecosystem
 * Author URI:        https://pi-ecosystem.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pi-api
 * Domain Path:       /languages
 *
 * @package Pi\Api
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PI_API_VERSION', '1.0.2');
define('PI_API_FILE', __FILE__);
define('PI_API_DIR', plugin_dir_path(__FILE__));
define('PI_API_URL', plugin_dir_url(__FILE__));

// REST API backend (FastAPI on Railway). Used by BackendClient for
// /auth/activate, /auth/issue-jwt, /auth/heartbeat, /v1/tiers/spec, etc.
// Override in wp-config.php once a branded API custom domain (e.g.
// api.pi-ecosystem.com) is set up + routed to Railway.
if (!defined('PI_API_BACKEND_URL')) {
    define('PI_API_BACKEND_URL', 'https://pi-backend.up.railway.app');
}

// Dashboard webapp (Vite SPA on Vercel). Used by IframeRenderer to load
// the embedded admin UI inside an iframe. Separate from BACKEND_URL because
// the API and webapp deploy to different platforms (Railway vs Vercel) and
// will likely live on different subdomains long-term (api.* vs app.*).
// Override in wp-config.php to use a branded custom domain
// (e.g. app.pi-ecosystem.com).
if (!defined('PI_API_DASHBOARD_URL')) {
    define('PI_API_DASHBOARD_URL', 'https://pi-dashboard-wordpress.vercel.app');
}

if (!defined('PI_API_MOCK_MODE')) {
    define('PI_API_MOCK_MODE', false);
}

spl_autoload_register(function (string $class): void {
    $prefix = 'PiApi\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = PI_API_DIR . 'includes/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

add_action('plugins_loaded', function () {
    load_plugin_textdomain('pi-api', false, dirname(plugin_basename(__FILE__)) . '/languages');

    PiApi\Settings::init();
    PiApi\Heartbeat::init();
    PiApi\IframeRenderer::init();
    PiApi\JwtAjax::init();
    PiApi\AuthManager::init();
    PiApi\ApiBootstrap::init();
    // CORS so the iframe (different origin) can call WP REST with the
    // Application Password the user enters in the login form.
    PiApi\CorsBridge::register();

    add_action('pi_api_report_usage', function ($tokens_in, $tokens_out, $jwt): void {
        PiApi\BackendClient::reportUsage((int) $tokens_in, (int) $tokens_out, (string) $jwt);
    }, 10, 3);
}, 5);
