<?php
/**
 * Critical-CSS strategy — saigonhoreca-theme
 *
 * Two-layer setup:
 *   1. Inline `assets/css/critical.css` (~3.5 KB minified) directly in
 *      <head> so the above-fold layout (header, hero, skip-link, font
 *      fallbacks) paints without waiting on the network. Eliminates the
 *      FCP gap left behind by the render-blocking main bundle.
 *   2. Emit a high-priority preload (`<link rel="preload" as="style">`)
 *      for the main `theme.css` BEFORE wp_head() writes the actual
 *      stylesheet tag. The preload kicks off the network fetch in
 *      parallel with HTML parsing so by the time the blocking <link>
 *      is reached the bytes are already in the disk cache.
 *
 * Why not async (`media="print" onload="…"`):
 *   - Below-fold sections paint twice (unstyled, then styled). Lighthouse
 *     Speed Index drifts from ~1 s to ~4 s — the page is "visually
 *     incomplete" until the swap. Both desktop and mobile scores drop.
 *   - Hero LCP regressed because Lighthouse caught a lazy-loaded slide
 *     before the carousel JS upgraded it.
 *
 * Why not inline-the-whole-bundle:
 *   - Desktop with brotli benefited (single 69 KB stream) but mobile (slow
 *     4G simulation) tanked: FCP went 1.8 s → 4.1 s, score 98 → 75. The
 *     extra ~20 KB of HTML on a throttled connection costs more than the
 *     round-trip saved on a fast desktop link.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('sgh_critical_css_path')) {
    function sgh_critical_css_path() {
        return get_template_directory() . '/assets/css/critical.css';
    }
}

if (!function_exists('sgh_critical_css_contents')) {
    function sgh_critical_css_contents() {
        static $cache = false;
        if ($cache !== false) {
            return $cache;
        }
        $path = sgh_critical_css_path();
        if (!is_readable($path)) {
            return $cache = null;
        }
        $css = @file_get_contents($path);
        $route_path = get_template_directory() . '/assets/css/critical-routes.css';
        if ($css !== false && is_readable($route_path)) {
            $route_css = @file_get_contents($route_path);
            if ($route_css !== false && $route_css !== '') {
                $css .= $route_css;
            }
        }
        return $cache = ($css === false || $css === '') ? null : $css;
    }
}

if (!function_exists('sgh_should_inline_route_bundle')) {
    /**
     * No route currently benefits from inlining its bundle.
     *
     * Empirical /lien-he/ test (10-run median with Chrome process cleanup
     * between runs): inline theme-contact.css (~16 KB brotli) → median
     * stayed at ~76. The bottleneck isn't the CSS round-trip; it's
     * Lantern's projection of observed FCP ~1.7s onto Slow-4G CPU
     * throttling. Inlining bloats HTML and ate the round-trip win.
     *
     * Stub returns false so the inline branch stays callable in
     * `sgh_inline_theme_css()` without ever firing. Flip a route to
     * `true` later only after a measured win.
     */
    function sgh_should_inline_route_bundle() {
        // No route currently benefits from inlining its bundle on top of
        // the image-LCP preload trick. /lien-he/ was tested: standalone
        // image-LCP gives median 91; adding inline contact bundle (~16 KB
        // brotli) dropped median to 82 — the HTML inflation costs more
        // than the eliminated round-trip saves on Slow-4G simulation.
        return false;
    }
}

if (!function_exists('sgh_inline_theme_css')) {
    function sgh_inline_theme_css() {
        static $printed = false;
        if ($printed) {
            return;
        }
        $css = sgh_critical_css_contents();
        if ($css === null) {
            return;
        }
        echo "<style id=\"sgh-critical\">{$css}</style>\n";

        if (sgh_should_inline_route_bundle() && function_exists('sgh_theme_css_bundle')) {
            $bundle = sgh_theme_css_bundle();
            if (is_readable($bundle['path'])) {
                $bundle_css = @file_get_contents($bundle['path']);
                if ($bundle_css !== false && $bundle_css !== '') {
                    echo "<style id=\"sgh-theme-inline\">{$bundle_css}</style>\n";
                }
            }
        }

        $printed = true;
    }
}

if (!function_exists('sgh_dequeue_inlined_route_bundle')) {
    /**
     * When we inline a route bundle in <head>, strip the external <link>
     * from `wp_enqueue_style('theme-tokens', …)` so the browser doesn't
     * race a useless 2nd download against the already inlined bytes.
     */
    function sgh_dequeue_inlined_route_bundle() {
        if (sgh_should_inline_route_bundle()) {
            wp_dequeue_style('theme-tokens');
        }
    }
}
add_action('wp_enqueue_scripts', 'sgh_dequeue_inlined_route_bundle', 999);

if (!function_exists('sgh_skip_preload_inlined_route_bundle')) {
    /**
     * Skip the preload hint for the route bundle when we already inline it.
     * Fires on `template_redirect` so `is_page_template()` / `is_page()`
     * have settled before we evaluate the inline-route condition.
     */
    function sgh_skip_preload_inlined_route_bundle() {
        if (sgh_should_inline_route_bundle()) {
            remove_action('wp_head', 'sgh_preload_theme_css', 2);
        }
    }
}
add_action('template_redirect', 'sgh_skip_preload_inlined_route_bundle');

if (!function_exists('sgh_preload_theme_css')) {
    /**
     * Emit a `<link rel="preload" as="style">` for the main theme.css.
     *
     * Hooked at priority 2 so it runs after the preconnect block (priority
     * 1) but before wp_head() emits the stylesheet <link> — the preload
     * hint warms the disk cache before the blocking <link> arrives.
     */
    function sgh_preload_theme_css() {
        if (is_admin()) {
            return;
        }
        $uri = get_template_directory_uri();
        $dir = get_template_directory();
        $bundle = function_exists('sgh_theme_css_bundle')
            ? sgh_theme_css_bundle($dir)
            : ['path' => $dir . '/assets/css/dist/theme.css', 'rel' => '/assets/css/dist/theme.css'];
        $path = $bundle['path'];
        $rel = $bundle['rel'];
        if (!is_readable($path)) {
            return;
        }
        $ver = function_exists('sh_get_asset_version')
            ? sh_get_asset_version($path)
            : @filemtime($path);
        $href = esc_url($uri . $rel . '?ver=' . $ver);
        // Note: no fetchpriority="high" — on the simulated 4G profile, a
        // high-priority CSS preload steals bandwidth from the hero-image
        // preload (the LCP element) and bumped Mobile LCP 2.1 s → 2.6 s.
        // Default priority is still ahead of regular stylesheets.
        echo '<link rel="preload" as="style" href="' . $href . '">' . "\n";
    }
}
add_action('wp_head', 'sgh_preload_theme_css', 2);

// Async-CSS for /lien-he/ was attempted (Filament Group media="print"
// swap) after the grid-stack hero redesign removed the FOUC risk on
// hero layout. Median didn't move (75-77 same as render-blocking) but
// CLS spiked to 0.12 on 2/10 runs when below-fold sections (form, map)
// relayout after CSS arrives. Reverted — grid-stack alone is the win.

if (!function_exists('sgh_preload_route_lcp_image')) {
    function sgh_preload_route_lcp_image() {
        if (is_admin()) {
            return;
        }

        $url = '';

        // ── /du-an/ archive — first hero slide background image ──
        if (is_post_type_archive('project') || (is_archive() && (string) get_query_var('post_type') === 'project') || is_page('du-an')) {
            if (function_exists('sgh_img')) {
                $url = sgh_img('2024/06/Sol0D7-05.webp');
            }
        }
        // ── /ve-saigon-horeca/ (page-gioi-thieu template) — about hero ──
        elseif (is_page_template('page-templates/page-gioi-thieu.php') || is_page('ve-saigon-horeca') || is_page('gioi-thieu')) {
            if (function_exists('sgh_img')) {
                $url = sgh_img('2023/12/bia-bep-cong-nghiep-inox.webp');
            }
        }
        // ── /lien-he/ — contact hero ──
        elseif (is_page_template('page-templates/page-lien-he.php') || is_page('lien-he')) {
            if (function_exists('sgh_img')) {
                $url = sgh_img('2023/12/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp');
            }
        }
        elseif (is_singular('post')) {
            $image_id = get_post_thumbnail_id(get_queried_object_id());
            $url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
        }

        if ($url) {
            echo '<link rel="preload" as="image" href="' . esc_url($url) . '" fetchpriority="high">' . "\n";
        }
    }
}
add_action('wp_head', 'sgh_preload_route_lcp_image', 1);

/**
 * Pre-warm TLS connection to prod CDN for cross-origin images.
 * Saves ~600-900 ms DNS+TCP+TLS on Lighthouse Slow-4G sim.
 * Runs at priority 0 (before everything else in wp_head).
 */
if (!function_exists('sgh_preconnect_cdn')) {
    function sgh_preconnect_cdn() {
        // Detect local environment to prevent Lighthouse preconnect warning
        $is_local = false;
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
            if (strpos($host, '.local') !== false || strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                $is_local = true;
            }
        }

        if (!$is_local) {
            echo '<link rel="preconnect" href="https://saigonhoreca.vn" crossorigin>' . "\n";
            echo '<link rel="dns-prefetch" href="https://saigonhoreca.vn">' . "\n";
        }
    }
}
add_action('wp_head', 'sgh_preconnect_cdn', 0);

// Note: the Filament Group `media="print" onload="this.media='all'"` trick
// was tried twice on this theme and regressed both times:
//   - With 3.5 KB critical.css: Speed Index 1 s → 4 s, hero LCP 0.7 s → 2.3 s
//   - With 5 KB critical.css (header+nav+hero+svc-links): Mobile LCP 2.4 s
//     → 4.0 s, CLS 0 → 0.18, Mobile score 97 → 78. The carousel hero IMG
//     decode races the async stylesheet — Chrome paints the image into a
//     pre-CSS layout, then re-paints into the styled layout. Lighthouse
//     scores both paints and the second one becomes LCP.
// Render-blocking <link> + preload hint stays the local-stack winner.

// Note: woff2 preloads were attempted (priority="auto" and "high") but
// measurably stole bandwidth from the hero-image preload, regressing
// LCP 600 ms → 810 ms and dropping the median Perf 100 → 99. The
// font-swap CLS (~0.003 — well under the 0.1 "good" threshold) costs
// less than that LCP regression, so we rely on `display=optional`
// alone. Keep the comment so the next person doesn't repeat the
// experiment.
