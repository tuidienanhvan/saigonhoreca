<?php
/**
 * Pi API Themes Endpoints
 *
 * GET    /pi/v1/themes                       — list installed themes
 * POST   /pi/v1/themes/{slug}/activate
 * DELETE /pi/v1/themes/{slug}                — uninstall
 * GET    /pi/v1/themes/{slug}/customize      — color preset, custom CSS
 * PUT    /pi/v1/themes/{slug}/customize      — update preset/CSS
 * GET    /pi/v1/themes/presets               — available color presets
 *
 * `slug` here = the theme stylesheet directory (e.g. "saigonhouse-theme").
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Themes {

    /** Default presets available to users. The 29-slot --pi-* token map
     *  that the dashboard webapp consumes is provided per-preset.            */
    private static $color_presets = [
        'saigonhouse' => [
            'label'   => 'SaigonHouse (mặc định)',
            'tokens' => [
                '--pi-primary'    => '#0F4C81',
                '--pi-primary-2'  => '#13609B',
                '--pi-secondary'  => '#F2A03D',
                '--pi-bg'         => '#0B0F14',
                '--pi-bg-card'    => '#121821',
                '--pi-bg-alt'     => '#1A2330',
                '--pi-text'       => '#E6EDF3',
                '--pi-text-2'     => '#9CA8B6',
                '--pi-text-3'     => '#5E6B7A',
                '--pi-text-muted' => '#7A8694',
                '--pi-border'     => '#27313F',
                '--pi-border-light' => '#1F2935',
                '--pi-success'    => '#22C55E',
                '--pi-warning'    => '#F59E0B',
                '--pi-danger'     => '#EF4444',
                '--pi-info'       => '#3B82F6',
                '--pi-on-brand'   => '#FFFFFF',
                '--pi-link'       => '#60A5FA',
                '--pi-link-hover' => '#93C5FD',
                '--pi-shadow'     => 'rgba(0,0,0,0.45)',
                '--pi-overlay'    => 'rgba(11,15,20,0.85)',
                '--pi-glass'      => 'rgba(18,24,33,0.6)',
                '--pi-glass-2'    => 'rgba(26,35,48,0.5)',
                '--pi-ring'       => 'rgba(15,76,129,0.45)',
                '--pi-radius'     => '12px',
                '--pi-radius-sm'  => '8px',
                '--pi-radius-lg'  => '16px',
                '--pi-spacing'    => '1rem',
                '--pi-font'       => "'Inter', system-ui, sans-serif",
            ],
        ],
        'pi-store' => [
            'label'  => 'Pi Store (purple)',
            'tokens' => [
                '--pi-primary'    => '#7C3AED',
                '--pi-primary-2'  => '#9555FF',
                '--pi-secondary'  => '#EAB308',
                '--pi-bg'         => '#0E0B1B',
                '--pi-bg-card'    => '#181029',
                '--pi-bg-alt'     => '#21183A',
                '--pi-text'       => '#F4F0FB',
                '--pi-text-2'     => '#B0A3CB',
                '--pi-text-3'     => '#675980',
                '--pi-text-muted' => '#8C7FAA',
                '--pi-border'     => '#33274D',
                '--pi-border-light' => '#251D3B',
                '--pi-success'    => '#22C55E',
                '--pi-warning'    => '#F59E0B',
                '--pi-danger'     => '#F43F5E',
                '--pi-info'       => '#38BDF8',
                '--pi-on-brand'   => '#FFFFFF',
                '--pi-link'       => '#C4B5FD',
                '--pi-link-hover' => '#DDD6FE',
                '--pi-shadow'     => 'rgba(0,0,0,0.5)',
                '--pi-overlay'    => 'rgba(14,11,27,0.9)',
                '--pi-glass'      => 'rgba(24,16,41,0.6)',
                '--pi-glass-2'    => 'rgba(33,24,58,0.5)',
                '--pi-ring'       => 'rgba(124,58,237,0.45)',
                '--pi-radius'     => '12px',
                '--pi-radius-sm'  => '8px',
                '--pi-radius-lg'  => '16px',
                '--pi-spacing'    => '1rem',
                '--pi-font'       => "'Inter', system-ui, sans-serif",
            ],
        ],
        'auto' => [
            'label'  => 'Tự động (theo OS)',
            'tokens' => [], // empty = follow OS prefers-color-scheme
        ],
    ];

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/themes', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_themes'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/themes/presets', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_presets'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/themes/(?P<slug>[^/]+)/activate', [
            'methods'  => 'POST',
            'callback' => [self::class, 'activate_theme'],
            'permission_callback' => $admin,
            'args' => ['slug' => ['type' => 'string']],
        ]);
        register_rest_route($ns, '/themes/(?P<slug>[^/]+)/customize', [
            ['methods' => 'GET', 'callback' => [self::class, 'get_customize'], 'permission_callback' => $admin, 'args' => ['slug' => ['type' => 'string']]],
            ['methods' => 'PUT', 'callback' => [self::class, 'update_customize'], 'permission_callback' => $admin, 'args' => ['slug' => ['type' => 'string']]],
        ]);
        register_rest_route($ns, '/themes/(?P<slug>[^/]+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'uninstall_theme'],
            'permission_callback' => $admin,
            'args' => ['slug' => ['type' => 'string']],
        ]);
    }

    /* ─── GET /themes ─────────────────────────────────────────────── */

    public static function list_themes() {
        $themes = wp_get_themes(['allowed' => null]);
        $active_slug = get_stylesheet();
        $list = [];
        foreach ($themes as $slug => $t) {
            $screenshot = $t->get_screenshot();
            $list[] = [
                'slug'        => $slug,
                'name'        => $t->get('Name'),
                'description' => wp_strip_all_tags($t->get('Description')),
                'version'     => $t->get('Version'),
                'author'      => wp_strip_all_tags($t->get('Author')),
                'theme_uri'   => $t->get('ThemeURI'),
                'screenshot'  => $screenshot ?: '',
                'is_active'   => ($slug === $active_slug),
                'tags'        => $t->get('Tags'),
            ];
        }
        return new \WP_REST_Response([
            'themes'      => $list,
            'active_slug' => $active_slug,
            'total'       => count($list),
        ], 200);
    }

    /* ─── GET /themes/presets ─────────────────────────────────────── */

    public static function list_presets() {
        $out = [];
        foreach (self::$color_presets as $key => $p) {
            $out[] = [
                'slug'   => $key,
                'label'  => $p['label'],
                'tokens' => $p['tokens'],
            ];
        }
        return new \WP_REST_Response(['presets' => $out], 200);
    }

    /* ─── POST /themes/{slug}/activate ────────────────────────────── */

    public static function activate_theme(\WP_REST_Request $request) {
        $slug = (string) $request['slug'];
        $themes = wp_get_themes();
        if (!isset($themes[$slug])) {
            return new \WP_REST_Response(['message' => 'Theme không tồn tại: ' . $slug], 404);
        }
        switch_theme($slug);
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã kích hoạt theme: ' . $themes[$slug]->get('Name'),
            'slug'    => $slug,
        ], 200);
    }

    /* ─── DELETE /themes/{slug} ───────────────────────────────────── */

    public static function uninstall_theme(\WP_REST_Request $request) {
        if (!function_exists('delete_theme')) require_once ABSPATH . 'wp-admin/includes/theme.php';
        $slug = (string) $request['slug'];
        $themes = wp_get_themes();
        if (!isset($themes[$slug])) {
            return new \WP_REST_Response(['message' => 'Theme không tồn tại: ' . $slug], 404);
        }
        if ($slug === get_stylesheet()) {
            return new \WP_REST_Response(['message' => 'Không thể xoá theme đang được kích hoạt.'], 400);
        }
        $result = delete_theme($slug);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['message' => $result->get_error_message()], 500);
        }
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã gỡ theme: ' . $themes[$slug]->get('Name'),
            'slug'    => $slug,
        ], 200);
    }

    /* ─── GET /themes/{slug}/customize ────────────────────────────── */

    public static function get_customize(\WP_REST_Request $request) {
        $slug = (string) $request['slug'];
        $option_key = 'pi_theme_customize__' . $slug;
        $stored = get_option($option_key, []);
        $preset = $stored['preset'] ?? 'saigonhouse';
        $custom_tokens = $stored['custom_tokens'] ?? [];

        // Custom CSS — stored on the WP custom-css post (per WP core).
        $custom_css = '';
        if (function_exists('wp_get_custom_css')) {
            $custom_css = wp_get_custom_css($slug);
        }

        return new \WP_REST_Response([
            'slug'           => $slug,
            'preset'         => $preset,
            'preset_tokens'  => self::$color_presets[$preset]['tokens'] ?? [],
            'custom_tokens'  => $custom_tokens,
            'custom_css'     => $custom_css,
            'available_presets' => array_keys(self::$color_presets),
        ], 200);
    }

    /* ─── PUT /themes/{slug}/customize ────────────────────────────── */

    public static function update_customize(\WP_REST_Request $request) {
        $slug = (string) $request['slug'];
        $params = $request->get_json_params();
        if (!is_array($params)) $params = [];

        $option_key = 'pi_theme_customize__' . $slug;
        $stored = get_option($option_key, []);
        if (!is_array($stored)) $stored = [];

        if (isset($params['preset'])) {
            $preset = sanitize_key($params['preset']);
            if (isset(self::$color_presets[$preset]) || $preset === 'custom') {
                $stored['preset'] = $preset;
            }
        }
        if (isset($params['custom_tokens']) && is_array($params['custom_tokens'])) {
            // Whitelist: only --pi-* keys, sanitize values.
            $clean = [];
            foreach ($params['custom_tokens'] as $k => $v) {
                if (strpos($k, '--pi-') !== 0) continue;
                $clean[$k] = wp_strip_all_tags((string) $v);
            }
            $stored['custom_tokens'] = $clean;
        }
        update_option($option_key, $stored, false);

        // Save custom CSS via WP core.
        if (isset($params['custom_css']) && function_exists('wp_update_custom_css_post')) {
            $css = (string) $params['custom_css'];
            wp_update_custom_css_post($css, ['stylesheet' => $slug]);
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã lưu tuỳ chỉnh giao diện.',
            'slug'    => $slug,
            'stored'  => $stored,
        ], 200);
    }
}
