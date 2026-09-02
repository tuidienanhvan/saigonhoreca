<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_SEO {
    public static function init() {
        add_action('wp_head', [__CLASS__, 'inject_tags'], 1);

        // Theme tự xuất hreflang riêng (inc/engine/slug-map.php:141, hook ở
        // :169) — hardcode 2 ngôn ngữ và trỏ thẳng vào tên miền production. Trên
        // local đo được 4 thẻ hreflang trên một trang, 3 trong đó sai. Hàm có
        // TÊN nên remove_action() gỡ được mà không phải sửa inc/engine/.
        //
        // CHỈ gỡ khi host này chạy theo tiền tố: production vẫn giữ nguyên thẻ
        // của theme, từng byte.
        add_action('after_setup_theme', function () {
            if (Pi_Multilang_Core::host_uses_prefixes()) {
                remove_action('wp_head', 'sgh_hreflang_tags', 2);
            }
        }, 99);
        
        // WP Core Sitemaps
        add_filter('wp_sitemaps_posts_query_args', [__CLASS__, 'filter_sitemap_query'], 10, 2);
        
        // Remove native WP canonical to prevent conflicts, we output our own
        remove_action('wp_head', 'rel_canonical');
        
        // Phân tách cache sitemap thật của theme theo ngôn ngữ
        add_filter('pre_transient_sh_sitemap_xml', [__CLASS__, 'read_sitemap_cache']);
        add_action('set_transient_sh_sitemap_xml', [__CLASS__, 'write_sitemap_cache'], 10, 3);
        add_filter('pre_transient_sh_sitemap_xml_gz', [__CLASS__, 'read_sitemap_cache_gz']);
        add_action('set_transient_sh_sitemap_xml_gz', [__CLASS__, 'write_sitemap_cache_gz'], 10, 3);
    }

    /**
     * Canonical + hreflang, dựng từ cấu hình ngôn ngữ.
     *
     * Bản cũ chỉ chạy trên singular CÓ nhóm dịch, và hardcode 'en' làm
     * x-default. Giờ hreflang chạy trên mọi trang index được, còn x-default lấy
     * theo ngôn ngữ mặc định trong config (ngôn ngữ có tiền tố rỗng) — đổi mặc
     * định trong trang cài đặt là x-default đổi theo, không phải sửa code.
     */
    public static function inject_tags() {
        if (is_admin() || is_404() || is_search() || is_feed()) return;

        // ⚠️ 2026-08-03 — BỎ phần phát canonical ở đây.
        //
        // Plugin gỡ core `rel_canonical` (dòng 25) rồi tự phát cho singular.
        // Nhưng theme (inc/engine/seo-robots.php) cũng phát cho front-page, và
        // trang chủ dạng page vừa là front-page vừa là singular => TRANG CHỦ CÓ
        // 2 THẺ CANONICAL. Google gặp nhiều canonical có thể bỏ qua hết.
        //
        // Nay theme là nguồn DUY NHẤT của canonical (đã bổ sung nhánh singular).
        // Vẫn giữ remove_action('wp_head','rel_canonical') ở trên — nếu trả core
        // lại thì core + theme sẽ thành 2 thẻ.
        // Plugin chỉ còn lo hreflang, đúng phần việc của nó.

        if (!class_exists('Pi_Multilang_Frontend')) return;

        $config = Pi_Multilang_Core::get_language_config();
        if (count($config) < 2) return;

        $host    = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
        $default = null;

        foreach ($config as $code => $c) {
            if (isset($c['host']) && $c['host'] === $host && trim($c['path_prefix'], '/') === '') {
                $default = $code;
                break;
            }
        }

        foreach ($config as $code => $c) {
            $url = Pi_Multilang_Frontend::url_in_language($code);
            if ($url === null) continue;   // không có bản dịch -> không bịa thẻ
            printf('<link rel="alternate" hreflang="%s" href="%s" />' . "\n",
                esc_attr($c['hreflang']), esc_url($url));
            if ($code === $default) {
                printf('<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url($url));
            }
        }
    }

    /**
     * Sitemap chỉ liệt kê nội dung của ngôn ngữ đang xem. Dùng ĐÚNG quy tắc
     * của Pi_Multilang_Frontend::filter_query() — khớp ngôn ngữ hiện tại HOẶC
     * chưa gắn thẻ — để sitemap không mâu thuẫn với thứ site thật sự phục vụ.
     * Bản cũ hardcode 'vi' là ngôn ngữ duy nhất được thấy bài chưa gắn thẻ.
     */
    public static function filter_sitemap_query($args, $post_type = '') {
        if (!defined('PI_CURRENT_LANGUAGE')) return $args;

        $args['meta_query'] = [
            'relation' => 'OR',
            ['key' => 'pi_language', 'value' => PI_CURRENT_LANGUAGE, 'compare' => '='],
            ['key' => 'pi_language', 'compare' => 'NOT EXISTS'],
        ];
        return $args;
    }

    // --- XỬ LÝ CACHE SITEMAP THEO NGÔN NGỮ ---

    public static function read_sitemap_cache($pre) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ')) return $pre;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ', true);
        $val = get_transient('sh_sitemap_xml_' . PI_CURRENT_LANGUAGE);
        return $val === false ? false : $val; // Nếu false, WP sẽ gọi generator
    }

    public static function write_sitemap_cache($value, $expiration, $transient) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE')) return;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE', true);
        
        set_transient('sh_sitemap_xml_' . PI_CURRENT_LANGUAGE, $value, $expiration);
        // Ngay sau khi core set sh_sitemap_xml, ta xoá nó đi để tránh dùng chung
        delete_option('_transient_sh_sitemap_xml');
        delete_option('_transient_timeout_sh_sitemap_xml');
    }

    public static function read_sitemap_cache_gz($pre) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ_GZ')) return $pre;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ_GZ', true);
        $val = get_transient('sh_sitemap_xml_gz_' . PI_CURRENT_LANGUAGE);
        return $val === false ? false : $val;
    }

    public static function write_sitemap_cache_gz($value, $expiration, $transient) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE_GZ')) return;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE_GZ', true);
        
        set_transient('sh_sitemap_xml_gz_' . PI_CURRENT_LANGUAGE, $value, $expiration);
        delete_option('_transient_sh_sitemap_xml_gz');
        delete_option('_transient_timeout_sh_sitemap_xml_gz');
    }
}
