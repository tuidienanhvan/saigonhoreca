<?php
/**
 * Bilingual slug map (.vn Vietnamese ↔ .com English).
 *
 * Pillar slugs (brand names) giữ identical cross-domain — chỉ structural
 * prefixes đổi theo section (xem inc/config/routes.php `sgh_bilingual_slug_map()`
 * cho bảng slug thật — engine chỉ chứa LOGIC, không hardcode route/brand).
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
    /** Canonical bilingual slug table — dữ liệu thật ở inc/config/routes.php. */
    function sgh_slug_map() {
        return function_exists('sgh_bilingual_slug_map') ? sgh_bilingual_slug_map() : [];
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
    if ($host === '' || !function_exists('sgh_is_known_production_host') || !sgh_is_known_production_host($host)) {
        return $url; // .local / unknown host → untouched (dev + admin safe)
    }
    // 1) Internal links follow the visitor's domain (.com stays .com, .vn stays .vn).
    $url = preg_replace('#^(https?://)[^/]+#', '${1}' . $host, $url, 1);
    // 2) On the English (.com) site, translate the structural VI prefix → EN
    //    section slugs (see inc/config/routes.php `sgh_bilingual_slug_map()`) so
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
     *   sgh_slug('news')  → 'tin-tuc' on .vn, 'blog' on .com, 'blog' on ja
     */
    function sgh_slug($key, $lang = null) {
        $map = sgh_slug_map();
        if (!isset($map[$key])) return $key;
        if ($lang === null) {
            $lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : (sgh_is_english_site() ? 'en' : 'vi');
        }
        if (isset($map[$key][$lang])) {
            return $map[$key][$lang];
        }
        if ($lang === 'en') {
            return $map[$key][1] ?? $map[$key][0];
        }
        if ($lang === 'ja') {
            return $map[$key]['ja'] ?? $map[$key][1] ?? $map[$key][0];
        }
        return $map[$key][0];
    }
}

if (!function_exists('sgh_url')) {
    /**
     * Build URL for section in CURRENT language.
     *   sgh_url('news')          → /tin-tuc/         OR /blog/          OR /ja/blog/
     *   sgh_url('news', 'heiwa') → /tin-tuc/heiwa/   OR /blog/heiwa/    OR /ja/blog/heiwa/
     */
    function sgh_url($key, $sub = '', $lang = null) {
        if ($lang === null) {
            $lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : (sgh_is_english_site() ? 'en' : 'vi');
        }
        $slug = sgh_slug($key, $lang);
        $is_en_site = sgh_is_english_site();
        if ($lang === 'vi') {
            $prefix = $is_en_site ? '/vi' : '';
        } elseif ($lang === 'en') {
            $prefix = $is_en_site ? '' : '/en';
        } else {
            $prefix = '/' . $lang;
        }
        $path = $prefix . '/' . $slug . '/' . ($sub ? trim($sub, '/') . '/' : '');
        return home_url($path);
    }
}

if (!function_exists('sgh_translate_path')) {
    /**
     * Translate path between languages (VI ↔ EN ↔ JA).
     *   $target = true / 'en'  → /tin-tuc/foo/ → /blog/foo/
     *   $target = false / 'vi' → /blog/foo/    → /tin-tuc/foo/
     *   $target = 'ja'         → /tin-tuc/foo/ → /blog/foo/ (or ja slug)
     */
    function sgh_translate_path($path, $target = true) {
        $target_lang = is_bool($target) ? ($target ? 'en' : 'vi') : (string) $target;
        $source_lang = 'vi';
        if (preg_match('#^/(en|ja)(/|$)#', $path, $m)) {
            $source_lang = $m[1];
            $path = preg_replace('#^/(en|ja)(/|$)#', '/', $path);
        }

        $map = sgh_slug_map();
        foreach ($map as $entry) {
            $vi = $entry[0] ?? '';
            $en = $entry[1] ?? '';
            $ja = $entry['ja'] ?? $en;

            $src_slug = match ($source_lang) {
                'en' => $en,
                'ja' => $ja,
                default => $vi,
            };
            $dst_slug = match ($target_lang) {
                'en' => $en,
                'ja' => $ja,
                default => $vi,
            };

            if ($src_slug && $dst_slug && $src_slug !== $dst_slug) {
                if (preg_match('#^/' . preg_quote($src_slug, '#') . '(/|$)#', $path)) {
                    $path = preg_replace(
                        '#^/' . preg_quote($src_slug, '#') . '(/|$)#',
                        '/' . $dst_slug . '$1',
                        $path,
                        1
                    );
                    break;
                }
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
            return 'https://' . SGH_DOMAIN_VN . sgh_translate_path($path, false);
        }
        return 'https://' . SGH_DOMAIN_COM . sgh_translate_path($path, true);
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
        $vi   = 'https://' . SGH_DOMAIN_VN  . sgh_translate_path($path, false);
        $en   = 'https://' . SGH_DOMAIN_COM . sgh_translate_path($path, true);
        $ja   = 'https://' . SGH_DOMAIN_VN  . '/ja' . sgh_translate_path($path, 'ja');
        echo "\n";
        echo '<link rel="alternate" hreflang="vi" href="' . esc_url($vi) . '">' . "\n";
        echo '<link rel="alternate" hreflang="en" href="' . esc_url($en) . '">' . "\n";
        echo '<link rel="alternate" hreflang="ja" href="' . esc_url($ja) . '">' . "\n";
        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($vi) . '">' . "\n";
    }
}

// Tự động in thẻ hreflang vào wp_head thay vì gọi thủ công ở header.php
add_action('wp_head', 'sgh_hreflang_tags', 2);
