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

// Multi-domain home_url: internal links follow the domain the visitor is ON.
// .vn ↔ .com share one install but home_url is fixed in DB — without this, links
// rendered on .com would point at .vn (wrong domain). Scoped to the known SGH
// production domains; front-end only; .local / unknown hosts pass through unchanged
// so local dev + admin/REST/cron are never affected.
add_filter('home_url', function ($url, $path) {
    if (is_admin()
        || (defined('REST_REQUEST') && REST_REQUEST)
        || (defined('DOING_CRON') && DOING_CRON)
        || (defined('WP_CLI') && WP_CLI)) {
        return $url;
    }
    $host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    if ($host === '' || !preg_match('/^(www\.)?saigonhoreca\.(vn|com)$/', $host)) {
        return $url; // .local / unknown host → untouched (dev + admin safe)
    }
    // 1) Internal links follow the visitor's domain (.com stays .com, .vn stays .vn).
    $url = preg_replace('#^(https?://)[^/]+#', '${1}' . $host, $url, 1);
    // 2) On the English (.com) site, translate the structural VI prefix → EN
    //    (/du-an/→/projects/, /lien-he/→/contact/, /san-pham/→/products/, …) so
    //    EVERY home_url()/sgh_url() call is correct without per-link edits.
    //    Idempotent on already-EN paths; only the FIRST path segment is mapped.
    if (substr($host, -4) === '.com' && function_exists('sgh_translate_path')
        && is_string($path) && isset($path[0]) && $path[0] === '/') {
        $tp = sgh_translate_path($path, true);
        if ($tp !== $path && preg_match('#^(https?://[^/]+)#', $url, $mm)) {
            $qpos = strpos($url, '?');
            $url  = $mm[1] . $tp . ($qpos !== false ? substr($url, $qpos) : '');
        }
    }
    return $url;
}, 20, 2);

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
        // Only emit cross-language alternates where a clean reciprocal counterpart can
        // exist — otherwise hreflang points at 404 / non-reciprocal URLs (Google flags it).
        if (is_admin() || is_feed() || is_404() || is_search() || is_attachment()) return;
        if (max((int) get_query_var('paged'), (int) get_query_var('page')) >= 2) return; // paginated
        if (is_singular() && function_exists('sgh_pi_get_post_seo')) {                    // noindex singular
            $o = get_queried_object();
            if ($o && !empty($o->ID)) {
                $seo = sgh_pi_get_post_seo($o->ID);
                if (!empty($seo['noindex'])) return;
            }
        }
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
