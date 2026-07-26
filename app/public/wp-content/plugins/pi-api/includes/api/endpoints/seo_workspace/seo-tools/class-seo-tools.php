<?php
/**
 * Pi API SEO Tools Endpoints — small GET/POST pairs per tool.
 *
 * Routes (each supports GET + POST):
 *   /pi/v1/seo/tools/breadcrumb         → sgh_seo_breadcrumb
 *   /pi/v1/seo/tools/html-sitemap       → sgh_seo_html_sitemap
 *   /pi/v1/seo/tools/toc                → sgh_seo_toc
 *   /pi/v1/seo/tools/robots-txt         → physical /robots.txt at site root
 *   /pi/v1/seo/tools/image-seo          → sgh_seo_image_seo
 *   /pi/v1/seo/tools/primary-category   → sgh_seo_primary_category
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoTools {

    const OPT_BREADCRUMB   = 'sgh_seo_breadcrumb';
    const OPT_HTML_SITEMAP = 'sgh_seo_html_sitemap';
    const OPT_TOC          = 'sgh_seo_toc';
    const OPT_IMAGE_SEO    = 'sgh_seo_image_seo';
    const OPT_PRIMARY_CAT  = 'sgh_seo_primary_category';

    public static function register_routes($ns, $admin) {
        $tools = ['breadcrumb', 'html-sitemap', 'toc', 'robots-txt', 'image-seo', 'primary-category'];
        foreach ($tools as $slug) {
            $method_get  = 'get_' . str_replace('-', '_', $slug);
            $method_post = 'post_' . str_replace('-', '_', $slug);
            register_rest_route($ns, '/seo/tools/' . $slug, [
                ['methods' => 'GET',  'callback' => [self::class, $method_get],  'permission_callback' => $admin],
                ['methods' => 'POST', 'callback' => [self::class, $method_post], 'permission_callback' => $admin],
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // BREADCRUMB
    // ──────────────────────────────────────────────────────────────────────
    public static function get_breadcrumb() {
        $opt = (array) get_option(self::OPT_BREADCRUMB, []);
        return new \WP_REST_Response(self::merge_defaults_breadcrumb($opt), 200);
    }

    public static function post_breadcrumb(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $clean = [
            'enabled'      => !empty($body['enabled']),
            'separator'    => sanitize_text_field((string) ($body['separator']    ?? '›')),
            'home_text'    => sanitize_text_field((string) ($body['home_text']    ?? 'Trang chủ')),
            'show_current' => !empty($body['show_current']),
        ];
        update_option(self::OPT_BREADCRUMB, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    protected static function merge_defaults_breadcrumb(array $opt): array {
        return array_merge([
            'enabled'      => false,
            'separator'    => '›',
            'home_text'    => 'Trang chủ',
            'show_current' => true,
        ], $opt);
    }

    // ──────────────────────────────────────────────────────────────────────
    // HTML SITEMAP
    // ──────────────────────────────────────────────────────────────────────
    public static function get_html_sitemap() {
        $opt = (array) get_option(self::OPT_HTML_SITEMAP, []);
        $data = self::merge_defaults_html_sitemap($opt);

        // Also return the available post_types for UI selection
        $pts = [];
        foreach (get_post_types(['public' => true], 'objects') as $pt) {
            if ($pt->name === 'attachment') continue;
            $pts[] = ['slug' => $pt->name, 'label' => $pt->labels->name];
        }
        $data['available_post_types'] = $pts;
        return new \WP_REST_Response($data, 200);
    }

    public static function post_html_sitemap(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $clean = [
            'post_types' => isset($body['post_types']) && is_array($body['post_types'])
                ? array_values(array_filter(array_map('sanitize_key', $body['post_types'])))
                : ['post', 'page'],
            'depth'   => max(1, min(5, (int) ($body['depth']   ?? 2))),
            'columns' => max(1, min(6, (int) ($body['columns'] ?? 2))),
        ];
        update_option(self::OPT_HTML_SITEMAP, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    protected static function merge_defaults_html_sitemap(array $opt): array {
        return array_merge([
            'post_types' => ['post', 'page'],
            'depth'      => 2,
            'columns'    => 2,
        ], $opt);
    }

    // ──────────────────────────────────────────────────────────────────────
    // TABLE OF CONTENTS
    // ──────────────────────────────────────────────────────────────────────
    public static function get_toc() {
        $opt = (array) get_option(self::OPT_TOC, []);
        return new \WP_REST_Response(self::merge_defaults_toc($opt), 200);
    }

    public static function post_toc(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $position = in_array(($body['position'] ?? ''), ['before_content', 'after_first_h2', 'sidebar'], true)
            ? $body['position'] : 'after_first_h2';

        $clean = [
            'enabled' => !empty($body['enabled']),
            'min_h2'  => max(1, min(10, (int) ($body['min_h2'] ?? 2))),
            'position'=> $position,
            'sticky'  => !empty($body['sticky']),
        ];
        update_option(self::OPT_TOC, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    protected static function merge_defaults_toc(array $opt): array {
        return array_merge([
            'enabled' => false,
            'min_h2'  => 2,
            'position'=> 'after_first_h2',
            'sticky'  => false,
        ], $opt);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ROBOTS.TXT (physical file)
    // ──────────────────────────────────────────────────────────────────────
    public static function get_robots_txt() {
        $path = self::robots_path();
        $exists = file_exists($path);
        $content = $exists ? (string) file_get_contents($path) : '';
        $writable = $exists ? is_writable($path) : is_writable(dirname($path));

        return new \WP_REST_Response([
            'content'   => $content,
            'exists'    => $exists,
            'writable'  => $writable,
            'path'      => wp_normalize_path($path),
            'default'   => self::default_robots_body(),
            'url'       => home_url('/robots.txt'),
        ], 200);
    }

    public static function post_robots_txt(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $content = (string) ($body['content'] ?? '');

        // Strip any HTML tags, normalise line endings, cap size.
        $content = wp_check_invalid_utf8($content);
        $content = preg_replace('/<[^>]+>/', '', $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        if (strlen($content) > 32768) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Nội dung quá dài (>32KB)'], 400);
        }

        $path = self::robots_path();
        $dir  = dirname($path);
        if (!is_writable($dir) && !is_writable($path)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Không ghi được vào ' . wp_normalize_path($path),
            ], 500);
        }

        $result = @file_put_contents($path, $content, LOCK_EX);
        if ($result === false) {
            return new \WP_REST_Response(['success' => false, 'message' => 'file_put_contents thất bại'], 500);
        }

        return new \WP_REST_Response([
            'success' => true,
            'bytes'   => (int) $result,
            'path'    => wp_normalize_path($path),
        ], 200);
    }

    protected static function robots_path(): string {
        // Prefer the WP install root; fall back to ABSPATH.
        $root = defined('ABSPATH') ? ABSPATH : getcwd();
        // If we're in wp-content/themes/... the project root is ABSPATH; ABSPATH ends with trailing slash.
        return rtrim(wp_normalize_path($root), '/') . '/robots.txt';
    }

    protected static function default_robots_body(): string {
        $public = (int) get_option('blog_public');
        if ($public === 0) {
            return "User-agent: *\nDisallow: /\n";
        }
        return "User-agent: *\n" .
               "Disallow: /wp-admin/\n" .
               "Allow: /wp-admin/admin-ajax.php\n\n" .
               "Sitemap: " . esc_url_raw(home_url('/sitemap.xml')) . "\n";
    }

    // ──────────────────────────────────────────────────────────────────────
    // IMAGE SEO
    // ──────────────────────────────────────────────────────────────────────
    public static function get_image_seo() {
        $opt = (array) get_option(self::OPT_IMAGE_SEO, []);
        return new \WP_REST_Response(self::merge_defaults_image_seo($opt), 200);
    }

    public static function post_image_seo(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $clean = [
            'auto_alt'     => !empty($body['auto_alt']),
            'alt_template' => sanitize_text_field((string) ($body['alt_template'] ?? '%filename% - %sitename%')),
            'auto_title'   => !empty($body['auto_title']),
        ];
        update_option(self::OPT_IMAGE_SEO, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    protected static function merge_defaults_image_seo(array $opt): array {
        return array_merge([
            'auto_alt'     => false,
            'alt_template' => '%filename% - %sitename%',
            'auto_title'   => false,
        ], $opt);
    }

    // ──────────────────────────────────────────────────────────────────────
    // PRIMARY CATEGORY
    // ──────────────────────────────────────────────────────────────────────
    public static function get_primary_category() {
        $opt = (array) get_option(self::OPT_PRIMARY_CAT, []);
        return new \WP_REST_Response(self::merge_defaults_primary_cat($opt), 200);
    }

    public static function post_primary_category(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $clean = [
            'enabled' => !empty($body['enabled']),
        ];
        update_option(self::OPT_PRIMARY_CAT, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    protected static function merge_defaults_primary_cat(array $opt): array {
        return array_merge([
            'enabled' => false,
        ], $opt);
    }
}
