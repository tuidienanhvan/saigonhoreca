<?php
/**
 * Centralized Cache Management
 *
 * Single place to flush all theme transients and caches (including Transient Cache and Static HTML Cache).
 * Replaces scattered wp_cache_flush() and delete_transient() calls.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * All transient keys used by the theme.
 *
 * @return string[] Transient keys (some use patterns with post IDs).
 */
function sgh_get_theme_transient_keys() {
    return [
        'sgh_dashboard_stats',
        'sgh_hero_image',
        'sgh_hero_image_url',
        'sh_youtube_videos_v2',
        'sh_sitemap_xml',
        'sh_sitemap_xml_gz',
    ];
}

/**
 * Flush all theme-specific transients, WP object cache, and Static HTML Cache.
 *
 * Call this instead of wp_cache_flush() + individual delete_transient() calls.
 *
 * @param bool $flush_wp_cache Also call wp_cache_flush() (default true).
 * @return int Number of transients actually deleted (0 if nothing was stored).
 */
function sgh_flush_theme_caches($flush_wp_cache = true) {
    $deleted = 0;
    foreach (sgh_get_theme_transient_keys() as $key) {
        // delete_transient returns true only if the transient existed + was deleted
        if (delete_transient($key)) {
            $deleted++;
        }
    }

    if ($flush_wp_cache) {
        wp_cache_flush();
    }

    // Flush Static HTML Cache
    sgh_static_cache_flush();

    return $deleted;
}

/**
 * Invalidate caches on content changes.
 * Hooked to save_post, delete_post, wp_update_nav_menu.
 */
function sgh_invalidate_content_cache($post_id = 0) {
    // Skip auto-saves and revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if ($post_id && wp_is_post_revision($post_id)) return;

    delete_transient('sgh_dashboard_stats');
    delete_transient('sh_sitemap_xml');
    delete_transient('sh_sitemap_xml_gz');

    // Flush Static HTML Cache on any content change
    sgh_static_cache_flush();
}

add_action('save_post', 'sgh_invalidate_content_cache');
add_action('delete_post', 'sgh_invalidate_content_cache');
add_action('wp_update_nav_menu', function () {
    sgh_invalidate_content_cache(0);
});

/* ============================================================================
 * STATIC HTML CACHE SYSTEM
 * ============================================================================ */

if (!function_exists('sgh_static_cache_dir')) {
    /**
     * Get the directory path for static HTML cache.
     *
     * @return string
     */
    function sgh_static_cache_dir(): string {
        return WP_CONTENT_DIR . '/cache/sgh-static';
    }
}

if (!function_exists('sgh_static_cache_path_for_uri')) {
    /**
     * Get the physical file path for a given URI.
     *
     * @param string $uri
     * @return string
     */
    function sgh_static_cache_path_for_uri(string $uri): string {
        $uri = strtok($uri, '?');
        if ($uri === false) $uri = '/';
        $uri = trim($uri, '/');
        // Sanitize: only allow [a-zA-Z0-9/_-], reject everything else
        // (cache poisoning guard — REQUEST_URI is user-controlled).
        if ($uri !== '' && !preg_match('#^[a-zA-Z0-9/_-]+$#', $uri)) return '';

        // ⚠️ 2026-08-02 — khoá cache PHẢI gồm locale.
        //
        // .vn và .com dùng CHUNG một bản cài; inc/engine/slug-map.php:34 đổi
        // locale theo domain (.com -> en_US, còn lại -> vi). Bản cũ chỉ lấy URI
        // làm khoá, nên cùng một đường dẫn trên hai domain ghi và đọc CÙNG một
        // file. Domain nào render trước thì domain kia nhận sai ngôn ngữ — đã
        // tái hiện: cache do host vi ghi, khách host en nhận <html lang="vi">
        // và chuỗi skip-link tiếng Việt thay vì bản tiếng Anh.
        //
        // Dùng get_locale() thay vì tự đọc HTTP_HOST: locale mới là thứ QUYẾT
        // ĐỊNH nội dung render. Domain nào rồi cũng quy về locale, và nếu sau
        // này thêm cách đổi ngôn ngữ khác (cookie, path /en/) thì khoá vẫn đúng
        // mà không phải sửa lại chỗ này.
        $locale = function_exists('get_locale') ? get_locale() : 'vi';
        $locale = preg_replace('#[^a-zA-Z0-9_-]#', '', (string) $locale);
        if ($locale === '') $locale = 'vi';

        return sgh_static_cache_dir() . '/' . $locale
            . ($uri === '' ? '/index.html' : '/' . $uri . '/index.html');
    }
}

if (!function_exists('sgh_static_cache_is_cacheable')) {
    /**
     * Check if the current request is cacheable.
     *
     * @return bool
     */
    function sgh_static_cache_is_cacheable(): bool {
        // Bots get a UA-specific render (no fixed parallax — inc/core/bot-optimize.php).
        // NEVER cache a bot response: a UA-agnostic static cache would otherwise serve
        // that bot (no-parallax) HTML to humans. Bots always render fresh instead.
        if (function_exists('sgh_is_bot') && sgh_is_bot()) return false;
        if (defined('DOING_AJAX') && DOING_AJAX) return false;
        if (defined('DOING_CRON') && DOING_CRON) return false;
        if (defined('REST_REQUEST') && REST_REQUEST) return false;
        if (defined('WP_CLI') && WP_CLI) return false;
        if (is_admin()) return false;
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'GET') return false;
        if (!empty($_GET)) return false;
        if (is_user_logged_in()) return false;
        if (is_preview() || (function_exists('is_customize_preview') && is_customize_preview())) return false;
        if (!empty($_SERVER['HTTP_COOKIE'])) {
            $cookie = (string) $_SERVER['HTTP_COOKIE'];
            if (stripos($cookie, 'wordpress_logged_in_') !== false) return false;
            if (stripos($cookie, 'wp-postpass_') !== false) return false;
            if (stripos($cookie, 'comment_author_') !== false) return false;
        }
        return true;
    }
}

if (!function_exists('sgh_static_cache_flush')) {
    /**
     * Flush all static HTML cache files and directories.
     *
     * Bao gồm cả `sgh-home-cache.html` — cache trang chủ RIÊNG (front-page.php,
     * TTL 10 phút), sống ngoài thư mục sgh-static/ nên trước đây hàm này (và
     * endpoint /flush-cache dùng nó) không hề đụng tới, khiến sửa code xong
     * vẫn thấy bản trang chủ cũ tối đa 10 phút sau đó.
     */
    function sgh_static_cache_flush(): void {
        // ⚠️ 2026-08-03 — phải xoá MỌI biến thể sgh-home-cache*.html.
        //
        // front-page.php:36 cho plugin đổi tên file cache qua filter
        // `sgh_home_cache_file` để tách theo ngôn ngữ (pi-multilang tạo
        // sgh-home-cache-vi.html). Nhưng hàm flush này lại xoá tên CỨNG
        // `sgh-home-cache.html` — không khớp file thật, nên "Xoá cache" chưa
        // bao giờ xoá được cache trang chủ. Đã tái hiện: sửa og:image xong, các
        // trang archive đổi ngay còn trang chủ vẫn giữ ảnh cũ, vì nó đọc từ
        // file sgh-home-cache-<locale>.html mà flush không đụng tới.
        //
        // Dùng glob thay vì gọi lại filter: mỗi ngôn ngữ một file, mà lúc flush
        // không biết đang ở ngôn ngữ nào — phải quét hết.
        foreach ((array) glob(WP_CONTENT_DIR . '/cache/sgh-home-cache*.html') as $home_cache) {
            if (is_file($home_cache)) @unlink($home_cache);
        }

        $dir = sgh_static_cache_dir();
        if (!is_dir($dir)) return;
        try {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($it as $f) {
                if ($f->isFile()) {
                    @unlink($f->getPathname());
                } elseif ($f->isDir()) {
                    @rmdir($f->getPathname());
                }
            }
        } catch (\Throwable $e) {
            error_log('[SaigonHoreca] sgh_static_cache_flush failed: ' . $e->getMessage());
        }
    }
}

// Hook template_redirect to start output buffering for cache generation
add_action('template_redirect', static function() {
    if (!sgh_static_cache_is_cacheable()) return;
    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    $path = sgh_static_cache_path_for_uri($uri);
    if ($path === '') return;
    ob_start(static function($buffer) use ($path) {
        if (http_response_code() !== 200) return $buffer;
        if (strlen($buffer) < 1024) return $buffer;
        if (strpos($buffer, '<title>Error') !== false) return $buffer;

        // Kiểm tra xem buffer có bị nén gzip (1f 8b) không — nếu ghi thẳng buffer
        // đã gzip vào file .html tĩnh, Apache/Nginx sẽ serve nó không kèm header
        // Content-Encoding: gzip, khiến trình duyệt tải file về thay vì hiển thị.
        $is_gzip = (strlen($buffer) >= 2 && ord($buffer[0]) === 0x1f && ord($buffer[1]) === 0x8b);
        $data_to_write = $buffer;
        if ($is_gzip) {
            $decompressed = @gzdecode($buffer);
            if ($decompressed !== false) {
                $data_to_write = $decompressed;
            } else {
                return $buffer; // Không thể giải nén thì không cache
            }
        }

        $dir = dirname($path);
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        @file_put_contents($path, $data_to_write, LOCK_EX);
        return $buffer;
    });
}, 2);

// Hook additional actions to flush static cache
add_action('comment_post', 'sgh_static_cache_flush');
add_action('switch_theme', 'sgh_static_cache_flush');
add_action('updated_option', static function($option) {
    $watched_options = [
        'siteurl', 
        'home', 
        'blogname', 
        'blogdescription', 
        'permalink_structure', 
        'page_on_front', 
        'page_for_posts'
    ];
    if (in_array($option, $watched_options, true)) {
        sgh_static_cache_flush();
    }
});

/**
 * Telemetry nhẹ: ghi nhận khi 1 project slug rơi vào fallback CSS 1.13MB
 * (theme-project.css) thay vì bundle riêng — thường do publish trước khi
 * chạy `npm run build`. Ghi tối đa 1 lần/slug/ngày (transient) để tránh
 * write amplification trên option DB. Xem admin notice ở dashboard nếu
 * muốn hiện cảnh báo — hiện tại chỉ ghi vào option để tra cứu qua WP-CLI/DB.
 */
if (!function_exists('sgh_log_project_bundle_fallback')) {
    function sgh_log_project_bundle_fallback($slug) {
        if (empty($slug)) return;
        $lock_key = 'sgh_fallback_logged_' . md5($slug);
        if (get_transient($lock_key)) return;
        set_transient($lock_key, 1, DAY_IN_SECONDS);

        $log = get_option('sgh_project_bundle_fallback_log', []);
        if (!is_array($log)) $log = [];
        $log[$slug] = [
            'count'    => ($log[$slug]['count'] ?? 0) + 1,
            'last_seen'=> current_time('mysql'),
        ];
        // Giới hạn 100 slug gần nhất để option không phình
        if (count($log) > 100) $log = array_slice($log, -100, null, true);
        update_option('sgh_project_bundle_fallback_log', $log, false);
    }
}
