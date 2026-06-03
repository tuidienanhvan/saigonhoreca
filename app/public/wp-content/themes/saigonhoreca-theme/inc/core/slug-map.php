<?php
/**
 * Bilingual slug map (.vn Vietnamese ↔ .com English).
 *
 * Pillar slugs (brand names) giữ identical cross-domain — chỉ structural
 * prefixes đổi: /du-an/ ↔ /projects/, /lien-he/ ↔ /contact/, etc.
 *
 * Strategy:
 *   - Site language detect via .com (English) vs .vn/.local (Vietnamese).
 *   - sgh_url() builds URL in current language.
 *   - sgh_translate_path() converts path EN ↔ VI for counterpart switcher.
 *   - sgh_counterpart_url() returns same-page URL on opposite-language site.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_slug_map')) {
    /**
     * Canonical bilingual slug table.
     * Each entry: section_key => [vi_slug, en_slug]
     */
    function sgh_slug_map() {
        return [
            'projects_index'  => ['du-an',                                          'projects'],
            'about'           => ['ve-saigon-horeca',                               'about'],
            'products_index'  => ['san-pham',                                       'products'],
            'products_cat'    => ['danh-muc-san-pham',                              'product-category'],
            'news'            => ['tin-tuc',                                        'blog'],
            'contact'         => ['lien-he',                                        'contact'],
            'consult_kitchen' => ['tu-van-thiet-ke-bep-nha-hang-bep-cong-nghiep',   'kitchen-consulting'],
            'consult_bar'     => ['cung-cap-thiet-bi-tu-van-thiet-ke-cho-quay-bar', 'bar-equipment'],
        ];
    }
}

if (!function_exists('sgh_is_english_site')) {
    function sgh_is_english_site() {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        return substr($host, -4) === '.com';
    }
}

// Auto-switch locale: .com → en_US, .vn/.local → vi
add_filter('locale', function ($locale) {
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    return substr($host, -4) === '.com' ? 'en_US' : 'vi';
});

if (!function_exists('sgh_slug')) {
    /**
     * Get section slug for CURRENT site language.
     *   sgh_slug('projects_index')  → 'du-an' on .vn, 'projects' on .com
     */
    function sgh_slug($key) {
        $map = sgh_slug_map();
        if (!isset($map[$key])) return $key;
        return sgh_is_english_site() ? $map[$key][1] : $map[$key][0];
    }
}

if (!function_exists('sgh_url')) {
    /**
     * Build URL for section in CURRENT language.
     *   sgh_url('projects_index')          → /du-an/         OR /projects/
     *   sgh_url('projects_index', 'heiwa') → /du-an/heiwa/   OR /projects/heiwa/
     */
    function sgh_url($key, $sub = '') {
        $slug = sgh_slug($key);
        $path = '/' . $slug . '/' . ($sub ? trim($sub, '/') . '/' : '');
        return home_url($path);
    }
}

if (!function_exists('sgh_translate_path')) {
    /**
     * Translate path from one language to the other.
     *   $to_english = true  → /du-an/foo/   → /projects/foo/
     *   $to_english = false → /projects/foo/ → /du-an/foo/
     */
    function sgh_translate_path($path, $to_english) {
        $map = sgh_slug_map();
        foreach ($map as $entry) {
            list($vi, $en) = $entry;
            if ($to_english) {
                $path = preg_replace(
                    '#^/' . preg_quote($vi, '#') . '(/|$)#',
                    '/' . $en . '$1',
                    $path
                );
            } else {
                $path = preg_replace(
                    '#^/' . preg_quote($en, '#') . '(/|$)#',
                    '/' . $vi . '$1',
                    $path
                );
            }
        }
        return $path;
    }
}

if (!function_exists('sgh_counterpart_url')) {
    /**
     * URL of current page on the OPPOSITE-language site.
     * Used by header lang-switcher button + <link rel="alternate"> hreflang.
     */
    function sgh_counterpart_url() {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $path = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';

        if (substr($host, -4) === '.com') {
            return 'https://saigonhoreca.vn' . sgh_translate_path($path, false);
        }
        return 'https://saigonhoreca.com' . sgh_translate_path($path, true);
    }
}

if (!function_exists('sgh_hreflang_tags')) {
    /**
     * Emit <link rel="alternate" hreflang> tags for SEO.
     * Call from header.php inside <head>.
     */
    function sgh_hreflang_tags() {
        $path = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
        $vi   = 'https://saigonhoreca.vn'  . sgh_translate_path($path, false);
        $en   = 'https://saigonhoreca.com' . sgh_translate_path($path, true);
        echo "\n";
        echo '<link rel="alternate" hreflang="vi" href="' . esc_url($vi) . '">' . "\n";
        echo '<link rel="alternate" hreflang="en" href="' . esc_url($en) . '">' . "\n";
        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($vi) . '">' . "\n";
    }
}

// Tự động in thẻ hreflang vào wp_head thay vì gọi thủ công ở header.php
add_action('wp_head', 'sgh_hreflang_tags', 2);
