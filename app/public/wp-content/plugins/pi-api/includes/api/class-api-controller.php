<?php
/**
 * Pi REST API Controller - Main Loader
 * This class initializes all modular endpoints and registers routes.
 * 
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Controller {

    private static $instance = null;
    private $namespace = 'pi/v1';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // Load all endpoint modules
        $this->load_modules();

        // Initialize hooks
        add_action('rest_api_init', [$this, 'register_routes']);
        add_filter('rest_pre_serve_request', [$this, 'handle_cors']);
    }

    private function load_modules() {
        $dir = __DIR__ . '/endpoints/';
        
        // Load files from root of endpoints/
        $root_files = glob($dir . 'class-*.php');
        if ($root_files) {
            foreach ($root_files as $file) {
                require_once $file;
            }
        }

        // Load files from subdirectories (Domain-based) — 1 cấp: endpoints/{workspace}/class-*.php
        $sub_files = glob($dir . '*/class-*.php');
        if ($sub_files) {
            foreach ($sub_files as $file) {
                require_once $file;
            }
        }

        // Load 2 cấp: endpoints/{workspace}/{app}/class-*.php
        // glob() không đệ quy nên phải khai báo riêng — thiếu dòng này thì mọi
        // route trong App con sẽ 404 âm thầm (class không bao giờ được require).
        // Vẫn giữ sau khi thêm tầng zone: `_shared/{app}/` nằm ở đúng độ sâu này.
        $app_files = glob($dir . '*/*/class-*.php');
        if ($app_files) {
            foreach ($app_files as $file) {
                require_once $file;
            }
        }

        // Load 3 cấp (Zone→Workspace→App): endpoints/{zone}_zone/{workspace}/{app}/class-*.php
        // Thêm 2026-08-02 khi bổ sung tầng zone cho khớp taxonomy frontend.
        // Route vẫn đăng ký theo TÊN CLASS trong register_routes() nên đổi vị
        // trí file không đổi URL — nhưng thiếu dòng glob này thì class không
        // bao giờ được require và toàn bộ route trong zone 404 âm thầm.
        $zone_files = glob($dir . '*/*/*/class-*.php');
        if ($zone_files) {
            foreach ($zone_files as $file) {
                require_once $file;
            }
        }
    }

    public function handle_cors($value) {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (!$origin) return $value;

        $host = parse_url($origin, PHP_URL_HOST) ?: '';

        // Chỉ cho phép localhost (dev) và ĐÚNG domain Pi Dashboard đã cấu hình
        // (PI_API_DASHBOARD_URL, override được qua wp-config.php).
        // TRƯỚC ĐÂY: str_ends_with($host, '.vercel.app') tin tưởng TOÀN BỘ
        // nền tảng công cộng Vercel — ai cũng deploy free app ở *.vercel.app,
        // và origin đó được cấp Access-Control-Allow-Credentials:true cho mọi
        // route REST của plugin. Thu hẹp về đúng 1 domain đã biết trước.
        $dashboard_host = defined('PI_API_DASHBOARD_URL')
            ? (parse_url(PI_API_DASHBOARD_URL, PHP_URL_HOST) ?: '')
            : '';
        $allowed = ($host === 'localhost' || $host === '127.0.0.1'
            || ($dashboard_host !== '' && $host === $dashboard_host));

        if ($allowed) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
            header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce, X-Requested-With, X-Pi-Client');
            header('Access-Control-Allow-Credentials: true');

            // Handle preflight
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                status_header(200);
                exit;
            }
        }
        return $value;
    }

    public function register_routes() {
        $admin_callback = [$this, 'is_admin'];

        // Define groups and their implementing classes
        $groups = [
            'overview'    => 'Pi_Api_Overview',
            'system'      => 'Pi_Api_System',
            'content'     => 'Pi_Api_Content',
            'migrate'      => 'Pi_Api_Migrate',
            'seo'         => 'Pi_Api_Seo',
            'seo_redirects' => 'Pi_Api_SeoRedirects',
            'seo_schema'   => 'Pi_Api_SeoSchema',
            'seo_sitemaps' => 'Pi_Api_SeoSitemaps',
            'seo_tools'    => 'Pi_Api_SeoTools',
            'seo_health'   => 'Pi_Api_SeoHealth',
            'seo_gsc'      => 'Pi_Api_SeoGsc',
            'seo_og_image' => 'Pi_Api_SeoOgImage',
            'seo_indexing' => 'Pi_Api_SeoIndexing',
            'seo_ai_bot'   => 'Pi_Api_SeoAiBot',
            'seo_internal_links' => 'Pi_Api_SeoInternalLinks',
            'seo_llm_txt'        => 'Pi_Api_SeoLlmTxt',
            'seo_robots_txt'     => 'Pi_Api_SeoRobotsTxt',
            'analytics'   => 'Pi_Api_Analytics',
            'leads'       => 'Pi_Api_Leads',
            'media'       => 'Pi_Api_Media',
            'categories'  => 'Pi_Api_Categories',
            'menu'        => 'Pi_Api_Menu',
            'comments'    => 'Pi_Api_Comments',
            'chat'        => 'Pi_Api_Chat',
            'chatbot'     => 'Pi_Api_Chatbot',
            'tools'       => 'Pi_Api_Tools',
            'performance' => 'Pi_Api_Performance',
            'performance_pro' => 'Pi_Api_PerformancePro',
            'cache'       => 'Pi_Api_Cache',
            'ai'          => 'Pi_Api_Ai',
            'ai_cloud'    => 'Pi_Api_AiCloud',
            'webhooks'    => 'Pi_Api_Webhooks',
            'search'      => 'Pi_Api_Search',
            'forms'       => 'Pi_Api_Forms',
            'email_templates' => 'Pi_Api_EmailTemplates',
            'email_campaigns' => 'Pi_Api_EmailCampaigns',
            'plugins'     => 'Pi_Api_Plugins',
            'themes'      => 'Pi_Api_Themes',
            'cron'        => 'Pi_Api_Cron',
            'db_explorer' => 'Pi_Api_DbExplorer',
            'backup'      => 'Pi_Api_Backup',
            'logs'        => 'Pi_Api_Logs',
            'sites'       => 'Pi_Api_Sites',
            'license'     => 'Pi_Api_License',
            'api_keys'    => 'Pi_Api_ApiKeys',
            'users'       => 'Pi_Api_Users',
            'audit_log'   => 'Pi_Api_AuditLog',
            'help'        => 'Pi_Api_Help',
        ];

        foreach ($groups as $group => $class) {
            if (class_exists($class) && method_exists($class, 'register_routes')) {
                $class::register_routes($this->namespace, $admin_callback);
            }
        }
    }

    public function is_admin() {
        return current_user_can('manage_options');
    }
}
