<?php
/**
 * Plugin Name:       Pi API
 * Plugin URI:        https://pi-ecosystem.com/api
 * Description:       Connect your WordPress site to Pi Dashboard. Manage SEO, leads, AI chatbot, and analytics from one place.
 * Version:           1.0.4
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

define('PI_API_VERSION', '1.0.4');
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

// PIX-1 migrate & Site Migrator core (Zone: WebZone, Workspace: MigrateWorkspace).
// Loaded explicitly for procedural Pi_Pix_* and Pi_* classes.
$pix_app = PI_API_DIR . 'includes/Modules/WebZone/MigrateWorkspace/PixCatalogApp/';
$migrator_app = PI_API_DIR . 'includes/Modules/WebZone/MigrateWorkspace/SiteMigratorApp/';

require_once $pix_app . 'class-pix-schema.php';
require_once $pix_app . 'class-pix-validator.php';
require_once $pix_app . 'class-pix-folder-writer.php';
require_once $pix_app . 'class-pix-folder-reader.php';
require_once $pix_app . 'class-pix-folder-importer.php';
require_once $pix_app . 'trait-pix-item-converter.php';
require_once $pix_app . 'class-pix-json-reader.php';
require_once $pix_app . 'class-pix-csv-reader.php';
require_once $pix_app . 'class-catalog-importer.php';

require_once $migrator_app . 'class-db-migrator.php';
require_once $migrator_app . 'class-file-sync.php';
require_once $migrator_app . 'class-site-package.php';
require_once $migrator_app . 'class-cli.php';

// Multilang (Zone: WebZone, Workspace: LocalizationWorkspace, App: MultilangApp).
//
// Nạp ở FILE SCOPE, có chủ đích, KHÔNG chuyển sang hook:
//   - Pi_Multilang_Core::init() phải đăng ký filter `home_url` trước khi WP
//     dựng query — đó là toàn bộ cơ chế định tuyến tiền tố ngôn ngữ.
//   - Pi_Multilang_Legacy_Bridge::init() phải định nghĩa các hàm sgh_* trước
//     khi functions.php của theme nạp (chúng đều bọc if (!function_exists)).
//
// Và nằm NGOÀI mọi nhánh license/auth: định tuyến URL công khai không được
// phụ thuộc vào việc backend có gọi tới được hay không. Cả hai lớp này chỉ
// đọc option cục bộ.
//
// Guard: plugin pi-multilang độc lập khai báo cùng bộ class. pi-api nạp TRƯỚC
// nó (thứ tự alphabet) nên class_exists() chưa bắt được — phải hỏi thẳng danh
// sách plugin đang bật, nếu không bật cả hai là fatal "cannot redeclare".
$pi_api_multilang_standalone = in_array(
    'pi-multilang/pi-multilang.php',
    (array) get_option('active_plugins', []),
    true
);

if (!$pi_api_multilang_standalone && !class_exists('Pi_Multilang_Core')) {
    $multilang_app = PI_API_DIR . 'includes/Modules/WebZone/LocalizationWorkspace/MultilangApp/';

    require_once $multilang_app . 'class-core.php';
    require_once $multilang_app . 'class-slugs.php';
    require_once $multilang_app . 'class-slug-batch.php';
    require_once $multilang_app . 'class-router.php';
    require_once $multilang_app . 'class-legacy-bridge.php';
    require_once $multilang_app . 'class-po-file.php';
    require_once $multilang_app . 'class-ui-strings.php';
    require_once $multilang_app . 'class-meta.php';
    require_once $multilang_app . 'class-frontend.php';
    require_once $multilang_app . 'class-seo.php';
    require_once $multilang_app . 'class-admin.php';
    require_once $multilang_app . 'class-languages-admin.php';
    require_once $multilang_app . 'class-content.php';
    require_once $multilang_app . 'class-term.php';
    require_once $multilang_app . 'class-audit.php';
    require_once $multilang_app . 'class-jobs.php';

    Pi_Multilang_Core::init();
    Pi_Multilang_Router::init();
    Pi_Multilang_Legacy_Bridge::init();

    add_action('plugins_loaded', function () {
        Pi_Multilang_Meta::init();
        Pi_Multilang_Frontend::init();
        Pi_Multilang_SEO::init();
        Pi_Multilang_Admin::init();
        Pi_Multilang_Languages_Admin::init();
        Pi_Multilang_Content::init();
        Pi_Multilang_Term::init();
    });
}

add_action('plugins_loaded', function () {
    load_plugin_textdomain('pi-api', false, dirname(plugin_basename(__FILE__)) . '/languages');

    PiApi\Core\Settings::init();
    PiApi\Core\Heartbeat::init();
    PiApi\Core\IframeRenderer::init();
    PiApi\Core\JwtAjax::init();
    PiApi\Core\AuthManager::init();
    PiApi\Core\ApiBootstrap::init();
    // Theme-agnostic edge HTML page cache. Themes opt in via the
    // `pi_api_cache_enabled` filter; this only registers the writer + flush
    // hooks. One plugin, many themes (see CacheManager docblock).
    PiApi\Core\CacheManager::init();
    // CORS so the iframe (different origin) can call WP REST with the
    // Application Password the user enters in the login form.
    PiApi\Core\CorsBridge::register();

    add_action('pi_api_report_usage', function ($tokens_in, $tokens_out, $jwt): void {
        PiApi\Core\BackendClient::reportUsage((int) $tokens_in, (int) $tokens_out, (string) $jwt);
    }, 10, 3);

    // Register WP-CLI commands for pi-api migration functions.
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        WP_CLI::add_command( 'pi-api', 'Pi_Api_CLI' );
    }
}, 5);
