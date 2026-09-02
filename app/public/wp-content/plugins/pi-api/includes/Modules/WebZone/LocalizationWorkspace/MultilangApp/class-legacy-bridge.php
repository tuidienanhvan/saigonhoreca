<?php
if (!defined('ABSPATH')) exit;

/**
 * Cầu nối tới theme saigonhoreca-theme.
 *
 * Theme quyết định ngôn ngữ bằng TÊN MIỀN (`inc/engine/slug-map.php`), có từ
 * thời site chỉ có 2 domain. Cơ chế tiền tố đường dẫn không lật được các hàm
 * đó, mà `inc/engine/` thì KHÔNG ĐƯỢC SỬA (lint gate "ENGINE PURITY: sạch ✓").
 *
 * Lối vào hợp lệ: mọi hàm ngôn ngữ trong `inc/engine/` đều bọc
 * `if (!function_exists(...))`, và plugin nạp TRƯỚC `functions.php` của theme.
 * Định nghĩa trước ở đây thì bản của theme không bao giờ được nạp — đây là cơ
 * chế mở rộng mà chính các file đó được viết ra để dùng, không phải mẹo lách.
 *
 * File này phải được require ở FILE SCOPE của plugin, không phải trong hook.
 */
class Pi_Multilang_Legacy_Bridge {

    public static function init() {
        // Cache trang chủ (front-page.php) là MỘT file dùng chung, không phân
        // biệt ngôn ngữ, và bỏ qua query string nên ?nc= không vượt được. Tách
        // theo locale, nếu không thì ngôn ngữ nào render trước sẽ được phục vụ
        // cho tất cả trong 600s. Đã tái hiện được lỗi này trước khi sửa.
        add_filter('sgh_home_cache_file', [__CLASS__, 'home_cache_file_per_locale']);

        // Xoá mọi biến thể khi theme yêu cầu flush — sgh_static_cache_flush()
        // nằm trong inc/engine/ và chỉ biết tên file không hậu tố.
        add_action('sgh_after_flush_cache', [__CLASS__, 'flush_home_caches']);
        add_action('save_post',             [__CLASS__, 'flush_home_caches']);
        add_action('switch_theme',          [__CLASS__, 'flush_home_caches']);
    }

    public static function home_cache_file_per_locale($file) {
        $locale = function_exists('get_locale') ? get_locale() : '';
        $locale = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $locale);
        if ($locale === '') return $file;
        return preg_replace('/\.html$/', '-' . $locale . '.html', $file);
    }

    public static function flush_home_caches() {
        if (!defined('WP_CONTENT_DIR')) return;
        foreach ((array) glob(WP_CONTENT_DIR . '/cache/sgh-home-cache*.html') as $f) {
            if (is_file($f)) @unlink($f);
        }
    }
}

/**
 * Theme dùng hàm này để chọn slug tiếng Việt hay tiếng Anh
 * (`sgh_slug()`, `sgh_url()`), và để khai báo ngôn ngữ trong schema
 * (`inc/engine/seo-schema.php:245`). Bản gốc chỉ xét hậu tố `.com` của host,
 * nên trên `saigonhoreca.local/en/` nó luôn trả false.
 *
 * Bản này giữ nguyên hành vi theo tên miền cho production, và bổ sung nhánh
 * theo ngôn ngữ đang hoạt động để chế độ tiền tố dùng được.
 */
if (!function_exists('sgh_is_english_site')) {
    function sgh_is_english_site() {
        $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
        if (substr($host, -4) === '.com') return true;
        return defined('PI_CURRENT_LANGUAGE') && PI_CURRENT_LANGUAGE === 'en';
    }
}

/**
 * Ngôn ngữ đang hoạt động, dùng chung cho 3 hàm slug bên dưới.
 * Tách riêng để không lặp lại điều kiện ở ba chỗ.
 */
if (!function_exists('pi_ml_active_lang')) {
    function pi_ml_active_lang() {
        if (defined('PI_CURRENT_LANGUAGE')) return PI_CURRENT_LANGUAGE;
        return function_exists('sgh_is_english_site') && sgh_is_english_site() ? 'en' : 'vi';
    }
}

/**
 * Slug của một đoạn cấu trúc theo NGÔN NGỮ ĐANG XEM.
 *
 * Bản theme (inc/engine/slug-map.php:77) chỉ biết 2 ngôn ngữ: `$map[$key][0]`
 * hay `[1]` tuỳ `sgh_is_english_site()`. Bản này tra bảng alias nên số ngôn ngữ
 * không giới hạn, và tiếng Nhật rơi về slug gốc cho tới khi có alias.
 *
 * ⚠️ KHÔNG được làm `$map[$key][0]` động theo ngôn ngữ. Cột [0] nuôi
 * `register_post_type(rewrite => slug)` (inc/engine/cpt-registration.php:33-34);
 * trả khác nhau giữa các request thì rewrite rule trong DB lệch với slug đang
 * đăng ký và TOÀN BỘ /du-an/* sẽ 404. Cột [0] là hằng, chỉ hàm này được động.
 */
if (!function_exists('sgh_slug')) {
    function sgh_slug($key) {
        $map = function_exists('sgh_slug_map') ? sgh_slug_map()
             : (function_exists('sgh_bilingual_slug_map') ? sgh_bilingual_slug_map() : []);
        if (!isset($map[$key][0])) return $key;

        $canonical = $map[$key][0];
        if (!class_exists('Pi_Multilang_Slugs') || !Pi_Multilang_Slugs::flag('slug_aliases')) {
            // Cờ tắt: giữ nguyên hành vi cũ của theme, từng byte.
            return (function_exists('sgh_is_english_site') && sgh_is_english_site() && isset($map[$key][1]))
                ? $map[$key][1] : $canonical;
        }
        return Pi_Multilang_Slugs::resolve_out($canonical, pi_ml_active_lang(), 'route');
    }
}

/**
 * URL của một đoạn cấu trúc trong ngôn ngữ đang xem.
 * Tiền tố ngôn ngữ do filter `home_url` (class-core.php:70) lo, không lo ở đây.
 */
if (!function_exists('sgh_url')) {
    function sgh_url($key, $sub = '') {
        $slug = sgh_slug($key);
        $path = '/' . $slug . '/' . ($sub !== '' ? trim((string) $sub, '/') . '/' : '');
        return home_url($path);
    }
}

/**
 * Dịch path giữa hai ngôn ngữ.
 *
 * GIỮ NGUYÊN chữ ký boolean của bản theme (inc/engine/slug-map.php:103) vì có 8
 * điểm gọi nằm trong inc/engine/ — thư mục không được sửa. Boolean chỉ phủ được
 * 2 ngôn ngữ; ngôn ngữ thứ 3 trở đi phải gọi thẳng
 * Pi_Multilang_Slugs::translate_path_to_lang().
 */
if (!function_exists('sgh_translate_path')) {
    function sgh_translate_path($path, $to_english) {
        if (!class_exists('Pi_Multilang_Slugs') || !Pi_Multilang_Slugs::flag('slug_aliases')) {
            return $path;   // cờ tắt: không đụng gì
        }
        $lang = $to_english ? 'en' : 'vi';
        if (!$to_english && class_exists('Pi_Multilang_Core')) {
            // "không phải tiếng Anh" nghĩa là ngôn ngữ mặc định của host này,
            // không phải lúc nào cũng là 'vi'.
            foreach (Pi_Multilang_Core::get_language_config() as $code => $c) {
                if (trim((string) $c['path_prefix'], '/') === '') { $lang = $code; break; }
            }
        }
        return Pi_Multilang_Slugs::translate_path_to_lang($path, $lang);
    }
}

/**
 * Khoá cache tĩnh theo HOST thay vì theo locale.
 *
 * SỰ CỐ ĐÃ ĐO 2026-08-29: `/vi/` trả 200 kèm `X-SGH-Static-Cache: HIT` và KHÔNG
 * có `X-Powered-By` — tức Apache phục vụ file trước khi PHP chạy, nên lệnh 301
 * `/vi/` -> `/` không bao giờ nổ.
 *
 * Nguyên nhân là hai đầu lệch nhau:
 *   - Bộ GHI (inc/engine/cache-helpers.php:101) khoá `sgh-static/{locale}/{uri}/index.html`
 *   - Bộ ĐỌC (app/public/.htaccess) map thẳng `sgh-static/{REQUEST_URI}/index.html`
 * Locale `vi` trùng đúng tiền tố ngôn ngữ `/vi/`, nên khách vào `/vi/` nhận
 * trang chủ tiếng Việt đã cache. Hệ quả rộng hơn: mọi URL đã cache đều bỏ qua
 * tầng chuyển hướng và tầng phân giải slug của phân hệ đa ngôn ngữ.
 *
 * Dùng HOST làm khoá vì đó là thứ DUY NHẤT cả hai đầu cùng tính được: Apache có
 * %{HTTP_HOST} mà không cần gọi PHP. Ngôn ngữ vốn đã nằm trong tiền tố URI ở chế
 * độ tiền tố (/en/, /ja/) và nằm trong host ở chế độ hai tên miền, nên
 * (host + uri) là khoá đầy đủ — không mất khả năng phân tách mà locale đang cho.
 *
 * Tên thư mục host không bao giờ đụng đoạn URL: guard sanitize bên dưới loại mọi
 * ký tự ngoài [a-zA-Z0-9/_-], mà host luôn chứa dấu chấm.
 *
 * ⚠️ Sửa hàm này thì PHẢI sửa khối "STATIC HTML CACHE" trong app/public/.htaccess
 * cùng lúc. Lệch một đầu là cache chết âm thầm (miss 100%) hoặc tệ hơn, phục vụ
 * nhầm nội dung như sự cố ở trên.
 */
if (!function_exists('sgh_static_cache_path_for_uri')) {
    function sgh_static_cache_path_for_uri(string $uri): string {
        $uri = strtok($uri, '?');
        if ($uri === false) $uri = '/';
        $uri = trim($uri, '/');

        // Giữ nguyên guard chống cache poisoning của bản gốc: REQUEST_URI do
        // người dùng điều khiển, chỉ nhận ký tự an toàn cho đường dẫn tệp.
        if ($uri !== '' && !preg_match('#^[a-zA-Z0-9/_-]+$#', $uri)) return '';

        $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
        $host = preg_replace('#[^a-z0-9.\-]#', '', $host);
        if ($host === '') return '';   // không xác định được host thì KHÔNG cache

        $dir = function_exists('sgh_static_cache_dir')
            ? sgh_static_cache_dir()
            : WP_CONTENT_DIR . '/cache/sgh-static';

        return $dir . '/' . $host . ($uri === '' ? '/index.html' : '/' . $uri . '/index.html');
    }
}
