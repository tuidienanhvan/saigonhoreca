<?php
/**
 * Pi API Plugins Endpoints
 *
 * GET    /pi/v1/plugins                — list installed plugins (Pi vs 3rd party)
 * POST   /pi/v1/plugins/{slug}/activate
 * POST   /pi/v1/plugins/{slug}/deactivate
 * POST   /pi/v1/plugins/{slug}/update  — trigger WP update
 * DELETE /pi/v1/plugins/{slug}         — uninstall
 * GET    /pi/v1/plugins/conflicts      — known conflict detection
 * GET    /pi/v1/plugins/available      — Pi plugins available to install
 *
 * Slug here is the plugin file path (urlencoded), e.g. "pi-seo%2Fpi-seo.php".
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Plugins {

    /** Hardcoded conflict pairs (slug fragment → competing slug fragment / display name). */
    private static $known_conflicts = [
        ['pi-seo',         'wordpress-seo/wp-seo.php', 'Yoast SEO',         'pi'],
        ['pi-seo',         'seo-by-rank-math/rank-math.php', 'Rank Math SEO', 'pi'],
        ['pi-seo',         'all-in-one-seo-pack/all_in_one_seo_pack.php', 'All in One SEO', 'pi'],
        ['pi-performance', 'wp-rocket/wp-rocket.php',       'WP Rocket',     'pi'],
        ['pi-performance', 'w3-total-cache/w3-total-cache.php', 'W3 Total Cache', 'pi'],
        ['pi-performance', 'wp-super-cache/wp-cache.php',    'WP Super Cache', 'pi'],
        ['pi-leads',       'contact-form-7/wp-contact-form-7.php', 'Contact Form 7', 'either'],
        ['pi-chatbot',     'tidio-live-chat/tidio-form.php', 'Tidio Live Chat', 'either'],
    ];

    /** Pi plugin "store" catalog (mock/static for now). */
    private static $pi_store_catalog = [
        ['slug' => 'pi-seo',         'name' => 'Pi SEO',         'description' => 'SEO meta, schema, audit, sitemap, robots.txt cho WordPress.', 'version' => '2.0.0', 'icon' => 'search'],
        ['slug' => 'pi-chatbot',     'name' => 'Pi Chatbot',     'description' => 'AI chatbot với phát hiện ý định, đa nhà cung cấp.',           'version' => '2.0.0', 'icon' => 'message-square'],
        ['slug' => 'pi-leads',       'name' => 'Pi Leads',       'description' => 'Form liên hệ, pipeline khách hàng, webhook.',              'version' => '2.0.0', 'icon' => 'users'],
        ['slug' => 'pi-analytics',   'name' => 'Pi Analytics',   'description' => 'Theo dõi page view, dashboard analytics nội bộ.',           'version' => '2.0.0', 'icon' => 'bar-chart-3'],
        ['slug' => 'pi-performance', 'name' => 'Pi Performance', 'description' => 'NanoCache, optimizer, PWA, image WebP.',                    'version' => '2.0.0', 'icon' => 'zap'],
        ['slug' => 'pi-ai-provider', 'name' => 'Pi AI Provider', 'description' => 'Định tuyến đa nhà cung cấp AI (key local).',               'version' => '2.0.0', 'icon' => 'brain'],
        ['slug' => 'pi-dashboard',   'name' => 'Pi Dashboard',   'description' => 'Khung quản trị Pi, đồng bộ site, tabs.',                  'version' => '2.0.0', 'icon' => 'layout-dashboard'],
    ];

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/plugins', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_plugins'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/plugins/conflicts', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_conflicts'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/plugins/available', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_available'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/plugins/(?P<slug>[^/]+)/activate', [
            'methods'  => 'POST',
            'callback' => [self::class, 'activate_plugin'],
            'permission_callback' => $admin,
            'args' => ['slug' => ['type' => 'string']],
        ]);
        register_rest_route($ns, '/plugins/(?P<slug>[^/]+)/deactivate', [
            'methods'  => 'POST',
            'callback' => [self::class, 'deactivate_plugin'],
            'permission_callback' => $admin,
            'args' => ['slug' => ['type' => 'string']],
        ]);
        register_rest_route($ns, '/plugins/(?P<slug>[^/]+)/update', [
            'methods'  => 'POST',
            'callback' => [self::class, 'update_plugin'],
            'permission_callback' => $admin,
            'args' => ['slug' => ['type' => 'string']],
        ]);
        register_rest_route($ns, '/plugins/(?P<slug>[^/]+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'uninstall_plugin'],
            'permission_callback' => $admin,
            'args' => ['slug' => ['type' => 'string']],
        ]);
    }

    /* ─── Helpers ─────────────────────────────────────────────────── */

    private static function ensure_plugin_helpers() {
        if (!function_exists('get_plugins'))       require_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!function_exists('activate_plugin'))   require_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!function_exists('request_filesystem_credentials')) require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    private static function decode_slug($raw) {
        // The route param is already URL-decoded by WP; keep as defensive guard.
        return urldecode((string) $raw);
    }

    private static function is_pi_plugin($file, $data) {
        // "pi-{anything}/..." dir or "Pi " prefix in name → Pi plugin.
        if (strpos($file, 'pi-') === 0) return true;
        $name = $data['Name'] ?? '';
        return (strpos($name, 'Pi ') === 0) || (strpos($name, 'Pi-') === 0);
    }

    /* ─── GET /plugins ────────────────────────────────────────────── */

    public static function list_plugins() {
        self::ensure_plugin_helpers();
        $all = get_plugins();
        $active = (array) get_option('active_plugins', []);

        // Pull update info if available.
        $updates = [];
        $upd_obj = get_site_transient('update_plugins');
        if ($upd_obj && !empty($upd_obj->response)) $updates = $upd_obj->response;

        $list = [];
        foreach ($all as $file => $data) {
            $is_pi = self::is_pi_plugin($file, $data);
            $update = isset($updates[$file]) ? [
                'new_version' => $updates[$file]->new_version ?? null,
                'package'     => $updates[$file]->package ?? null,
            ] : null;
            $list[] = [
                'file'        => $file,
                'slug'        => $file,
                'name'        => $data['Name'] ?? $file,
                'description' => $data['Description'] ?? '',
                'version'     => $data['Version'] ?? '0.0.0',
                'author'      => wp_strip_all_tags($data['Author'] ?? ''),
                'plugin_uri'  => $data['PluginURI'] ?? '',
                'is_active'   => in_array($file, $active, true),
                'is_pi'       => (bool) $is_pi,
                'update_available' => (bool) $update,
                'update'      => $update,
            ];
        }

        return new \WP_REST_Response([
            'plugins' => $list,
            'total'   => count($list),
            'active'  => count($active),
        ], 200);
    }

    /* ─── GET /plugins/available ──────────────────────────────────── */

    public static function list_available(\WP_REST_Request $request) {
        $source = $request->get_param('source') ?: 'pi-store';
        if ($source !== 'pi-store') {
            return new \WP_REST_Response(['plugins' => [], 'source' => $source], 200);
        }

        self::ensure_plugin_helpers();
        $installed = get_plugins();

        // Mark which catalog items are already installed.
        $items = [];
        foreach (self::$pi_store_catalog as $p) {
            $is_installed = false;
            foreach (array_keys($installed) as $file) {
                if (strpos($file, $p['slug'] . '/') === 0) { $is_installed = true; break; }
            }
            $items[] = array_merge($p, ['is_installed' => $is_installed]);
        }
        return new \WP_REST_Response(['plugins' => $items, 'source' => $source], 200);
    }

    /* ─── GET /plugins/conflicts ──────────────────────────────────── */

    public static function get_conflicts() {
        self::ensure_plugin_helpers();
        $all = get_plugins();
        $active = (array) get_option('active_plugins', []);
        $conflicts = [];

        foreach (self::$known_conflicts as $pair) {
            list($pi_fragment, $other_file, $other_name, $recommend) = $pair;

            $pi_active = false;
            $pi_file   = null;
            foreach ($active as $f) {
                if (strpos($f, $pi_fragment . '/') === 0) { $pi_active = true; $pi_file = $f; break; }
            }
            $other_active = in_array($other_file, $active, true);

            if ($pi_active && $other_active) {
                $conflicts[] = [
                    'id'             => md5($pi_fragment . '|' . $other_file),
                    'pi_plugin'      => ['file' => $pi_file, 'name' => $all[$pi_file]['Name'] ?? $pi_fragment],
                    'other_plugin'   => ['file' => $other_file, 'name' => $other_name],
                    'recommendation' => $recommend === 'pi' ? 'Tắt ' . $other_name : 'Chọn 1 trong 2',
                    'severity'       => 'high',
                    'reason'         => 'Cả hai plugin cùng quản lý chức năng tương tự — có thể gây xung đột hook và meta.',
                ];
            }
        }

        return new \WP_REST_Response(['conflicts' => $conflicts, 'count' => count($conflicts)], 200);
    }

    /* ─── POST /plugins/{slug}/activate ───────────────────────────── */

    public static function activate_plugin(\WP_REST_Request $request) {
        self::ensure_plugin_helpers();
        $slug = self::decode_slug($request['slug']);
        $all  = get_plugins();
        if (!isset($all[$slug])) {
            return new \WP_REST_Response(['message' => 'Plugin không tồn tại: ' . $slug], 404);
        }
        $result = activate_plugin($slug, '', false, true);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['message' => $result->get_error_message()], 500);
        }
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã kích hoạt: ' . ($all[$slug]['Name'] ?? $slug),
            'slug'    => $slug,
        ], 200);
    }

    /* ─── POST /plugins/{slug}/deactivate ─────────────────────────── */

    public static function deactivate_plugin(\WP_REST_Request $request) {
        self::ensure_plugin_helpers();
        $slug = self::decode_slug($request['slug']);
        $all  = get_plugins();
        if (!isset($all[$slug])) {
            return new \WP_REST_Response(['message' => 'Plugin không tồn tại: ' . $slug], 404);
        }
        deactivate_plugins([$slug], false, false);
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã tắt: ' . ($all[$slug]['Name'] ?? $slug),
            'slug'    => $slug,
        ], 200);
    }

    /* ─── POST /plugins/{slug}/update ─────────────────────────────── */

    public static function update_plugin(\WP_REST_Request $request) {
        self::ensure_plugin_helpers();
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';

        $slug = self::decode_slug($request['slug']);
        $all  = get_plugins();
        if (!isset($all[$slug])) {
            return new \WP_REST_Response(['message' => 'Plugin không tồn tại: ' . $slug], 404);
        }

        // Refresh transient.
        wp_update_plugins();
        $upd = get_site_transient('update_plugins');
        if (empty($upd->response[$slug])) {
            return new \WP_REST_Response(['message' => 'Không có cập nhật cho plugin này.'], 400);
        }

        $skin = new \Automatic_Upgrader_Skin();
        $upgrader = new \Plugin_Upgrader($skin);
        $ok = $upgrader->upgrade($slug);
        if (is_wp_error($ok) || $ok === false) {
            $msg = is_wp_error($ok) ? $ok->get_error_message() : 'Cập nhật thất bại.';
            return new \WP_REST_Response(['message' => $msg], 500);
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã cập nhật: ' . ($all[$slug]['Name'] ?? $slug),
            'slug'    => $slug,
        ], 200);
    }

    /* ─── DELETE /plugins/{slug} ──────────────────────────────────── */

    public static function uninstall_plugin(\WP_REST_Request $request) {
        self::ensure_plugin_helpers();
        $slug = self::decode_slug($request['slug']);
        $all  = get_plugins();
        if (!isset($all[$slug])) {
            return new \WP_REST_Response(['message' => 'Plugin không tồn tại: ' . $slug], 404);
        }
        $active = (array) get_option('active_plugins', []);
        if (in_array($slug, $active, true)) {
            deactivate_plugins([$slug], false, false);
        }
        $result = delete_plugins([$slug]);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['message' => $result->get_error_message()], 500);
        }
        if ($result === false) {
            return new \WP_REST_Response(['message' => 'Không thể xoá plugin (có thể do quyền filesystem).'], 500);
        }
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã gỡ cài đặt: ' . ($all[$slug]['Name'] ?? $slug),
            'slug'    => $slug,
        ], 200);
    }
}
