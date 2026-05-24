<?php
/**
 * Scripts & Styles Enqueue
 * All frontend asset loading in one place.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * Aggressive Defer: Defer ALL non-admin scripts (saves ~379 KiB Render Blocking)
 */
add_filter('script_loader_tag', function ($tag, $handle) {
    if (is_admin()) return $tag;
    
    // Eager load only critical libraries if absolutely necessary
    $eager = ['jquery-core']; 
    if (in_array($handle, $eager)) return $tag;

    // Add defer to all other scripts
    if (strpos($tag, ' defer') === false && strpos($tag, ' async') === false) {
        return str_replace(' src=', ' defer src=', $tag);
    }
    return $tag;
}, 20, 2);

/**
 * True when current page uses any `page-pillar-*.php` template.
 * Used to route the project CSS bundle for all 12 pillar pages.
 */
function sgh_is_pillar_page_template() {
    $tpl = get_page_template_slug();
    return is_string($tpl) && strpos($tpl, 'page-templates/page-project-') === 0;
}

/**
 * Cached filemtime accessor to reduce Disk I/O
 */
function sh_get_asset_version($file_path) {
    if (wp_get_environment_type() === 'local') {
        return file_exists($file_path) ? filemtime($file_path) : '1.0';
    }
    
    // In production, use transient to cache filemtime for 24 hours
    static $versions = null;
    if ($versions === null) {
        $versions = get_transient('sh_asset_versions');
        if (!is_array($versions)) $versions = [];
    }

    $md5_path = md5($file_path);
    if (!isset($versions[$md5_path])) {
        $versions[$md5_path] = file_exists($file_path) ? filemtime($file_path) : '1.0';
        set_transient('sh_asset_versions', $versions, 24 * HOUR_IN_SECONDS);
    }

    return $versions[$md5_path];
}

function sgh_theme_css_bundle($dir = null) {
    $dir = $dir ?: get_template_directory();
    $rel = '/assets/css/dist/theme.css';

    if (is_front_page()) {
        $rel = '/assets/css/dist/theme-home.css';
    } elseif (is_page_template('page-templates/page-gioi-thieu.php') || is_page('ve-saigon-horeca') || is_page('gioi-thieu')) {
        $rel = '/assets/css/dist/theme-about.css';
    } elseif (is_page_template('page-templates/page-lien-he.php') || is_page('lien-he')) {
        $rel = '/assets/css/dist/theme-contact.css';
    } elseif (is_singular('product')) {
        $rel = '/assets/css/dist/theme-product.css';
    } elseif (
        is_singular('project')
        || is_page_template('page-templates/page-project.php')
        || sgh_is_pillar_page_template()
    ) {
        $rel = '/assets/css/dist/theme-project.css';
    } elseif (is_singular('post')) {
        $rel = '/assets/css/dist/theme-single.css';
    } elseif (is_post_type_archive('product') || is_tax('product_category') || is_tax('product_brand')) {
        $rel = '/assets/css/dist/theme-archive-product.css';
    } elseif (is_post_type_archive('project') || is_tax('project_category')) {
        $rel = '/assets/css/dist/theme-archive-project.css';
    } elseif (is_archive() || is_home() || is_search() || is_404()) {
        $rel = '/assets/css/dist/theme-archive.css';
    }

    $path = $dir . $rel;
    if (!file_exists($path)) {
        $rel = '/assets/css/dist/theme.css';
        $path = $dir . $rel;
    }

    return ['path' => $path, 'rel' => $rel];
}

function sgh_is_native_theme_surface() {
    return is_front_page()
        || is_page_template('page-templates/page-gioi-thieu.php')
        || is_page_template('page-templates/page-lien-he.php')
        || is_page_template('page-templates/page-project.php')
        || sgh_is_pillar_page_template()
        || is_singular('project')
        || is_post_type_archive('project')
        || is_tax('project_category')
        || is_post_type_archive('product')
        || is_tax('product_category')
        || is_tax('product_brand');
}

/**
 * Enqueue all frontend scripts & styles
 */
add_action('wp_enqueue_scripts', function () {
    $uri = get_template_directory_uri();
    $dir = get_template_directory();


    // Theme CSS — split between home-page and rest-of-site bundles.
    // theme.css = full bundle (all template-parts CSS imports) for pages
    //   that use any combination of about/contact/pricing/single/archive
    //   components.
    // theme-home.css = lean home-only bundle (-56 %, 418 KB → 182 KB raw)
    //   built from _imports-home.css which omits the pricing × 15,
    //   contact × 3, about × 5, etc. files the front page never touches.
    //   Mobile Slow-4G FCP is throughput-bound on this render-blocking
    //   stylesheet, so trimming the byte count is the highest-ROI win.
    $theme_bundle = sgh_theme_css_bundle($dir);
    wp_enqueue_style('theme-tokens', $uri . $theme_bundle['rel'], [], sh_get_asset_version($theme_bundle['path']));

    // Enqueue Google Fonts (Be Vietnam Pro & Lexend) for high-end typography
    if (defined('SGH_GOOGLE_FONTS_URL') && !empty(SGH_GOOGLE_FONTS_URL)) {
        wp_enqueue_style('google-fonts-sgh', SGH_GOOGLE_FONTS_URL, [], null);
    }

    // Native pages must not pull legacy Elementor/static-mirror styles.
    // Elementor/Astra assets can still exist in uploads or old theme folders
    // on production, but this theme no longer depends on them.

    // Animate-on-scroll was removed entirely to avoid "empty middle"
    // scroll-jank. The legacy CSS/JS files and inline attributes have
    // been stripped, so no script or stylesheet target remains.

    // T-020: prefer minified `assets/js/dist/<name>.js` when present
    // (produced by `npx esbuild --minify`). Source `.js` stays as the
    // dev/maintenance copy. Falls back to source if dist missing so
    // local-dev workflow without a fresh build still works.
    if (!function_exists('sgh_js_path')) {
        function sgh_js_path($name, $dir, $uri) {
            $dist = $dir . '/assets/js/dist/' . $name;
            if (file_exists($dist)) {
                return [$uri . '/assets/js/dist/' . $name, sh_get_asset_version($dist)];
            }
            return [$uri . '/assets/js/' . $name, sh_get_asset_version($dir . '/assets/js/' . $name)];
        }
    }

    // JS — deferred via filter above
    list($mm_url, $mm_ver) = sgh_js_path('main-modern.js', $dir, $uri);
    wp_enqueue_script('main-modern', $mm_url, [], $mm_ver, true);

    if (is_front_page()) {
        list($hc_url, $hc_ver) = sgh_js_path('hero-carousel.js', $dir, $uri);
        wp_enqueue_script('hero-carousel', $hc_url, [], $hc_ver, true);
    }
    if (is_singular('post')) {
        list($se_url, $se_ver) = sgh_js_path('single-enhancements.js', $dir, $uri);
        wp_enqueue_script('sh-single-enhancements', $se_url, [], $se_ver, true);
    }
    if (is_page_template('page-templates/page-du-toan.php')) {
        list($cl_url, $cl_ver) = sgh_js_path('calculator.js', $dir, $uri);
        wp_enqueue_script('calculator-script', $cl_url, [], $cl_ver, true);
    }
    if (is_page_template('page-templates/page-portfolio.php')) {
        list($gl_url, $gl_ver) = sgh_js_path('gallery.js', $dir, $uri);
        wp_enqueue_script('sh-gallery', $gl_url, [], $gl_ver, true);
    }

    // Page-template CSS — only files that are NOT bundled via _imports.css.
    // The `about/*.css`, `contact/*.css`, `pricing/hero.css`,
    // `turnkey-work-items.css`, `faq-premium.css`, and `components/page-hero.css`
    // are already imported into the full `theme.css` bundle (see _imports.css),
    // so re-enqueueing them as standalone <link>s downloads the rules twice
    // and triggers cascade-debt warnings on review. Only the two page-template
    // entry files (page-bang-gia.css, page-lien-he.css) live outside _imports
    // and stay conditionally enqueued.
    if (is_page_template('page-templates/page-lien-he.php') || is_page('lien-he')) {
        list($cf_url, $cf_ver) = sgh_js_path('contact-form.js', $dir, $uri);
        wp_enqueue_script('contact-form-js', $cf_url, [], $cf_ver, true);
    }
    list($tt_url, $tt_ver) = sgh_js_path('theme-toggle.js', $dir, $uri);
    wp_enqueue_script('theme-toggle', $tt_url, [], $tt_ver, true);

    // Pillar pages giữ scroll behavior bình thường (match production) —
    // không load parallax JS. Nếu muốn re-enable sau, restore block này

    $gtm_container = function_exists('sh_get_gtm_container_id') ? sh_get_gtm_container_id() : SGH_GTM_CONTAINER_ID;
    if (!empty($gtm_container)) {
        list($gt_url, $gt_ver) = sgh_js_path('gtm-lazy.js', $dir, $uri);
        wp_enqueue_script('sh-gtm-lazy', $gt_url, [], $gt_ver, true);
        wp_add_inline_script('sh-gtm-lazy', 'window.SH_GTM=' . wp_json_encode([
            'id'        => $gtm_container,
            'defer'     => function_exists('sh_should_defer_third_party_tracking') ? sh_should_defer_third_party_tracking() : true,
            'timeoutMs' => 3500,
        ]) . ';', 'before');
    }

    // JS data
    wp_localize_script('main-modern', 'SH_DATA', [
        'ajax_url'   => admin_url('admin-ajax.php'),
        'home_url'   => home_url(),
        'theme_uri'  => $uri,
        'nonce'      => wp_create_nonce('sh_portfolio_nonce'),
        'rest_nonce' => wp_create_nonce('wp_rest'),
        'is_admin'   => current_user_can('manage_options'),
    ]);

    // Inject crucial Elementor/Astra global color CSS variables so dark overlay works perfectly on project pages.
    // Activate Be Vietnam Pro & Lexend fonts for luxury corporate aesthetic (Option B)
    wp_add_inline_style('theme-tokens', '
        :root {
            --font-body: "Be Vietnam Pro", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            --font-heading: "Lexend", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            --font-serif: "Lora", Georgia, serif;
            --ast-global-color-0: #f9c349;
            --ast-global-color-1: #ffb100;
            --ast-global-color-2: #000000;
            --ast-global-color-3: #242525;
            --ast-global-color-4: #f9fafb;
            --ast-global-color-5: #ffffff;
            --ast-global-color-6: #e2e8f0;
            --ast-global-color-7: #cbd5e1;
            --ast-global-color-8: #94a3b8;
            --e-global-color-astglobalcolor0: #f9c349;
            --e-global-color-astglobalcolor1: #ffb100;
            --e-global-color-astglobalcolor2: #000000;
            --e-global-color-astglobalcolor3: #242525;
            --e-global-color-astglobalcolor4: #f9fafb;
            --e-global-color-astglobalcolor5: #ffffff;
            --e-global-color-astglobalcolor6: #e2e8f0;
            --e-global-color-astglobalcolor7: #cbd5e1;
            --e-global-color-astglobalcolor8: #94a3b8;
        }
        body { font-family: var(--font-body); }
        h1, h2, h3, h4, h5, h6, .font-sans { font-family: var(--font-heading) !important; }
        .e-font-icon-svg { display: inline-block; width: 1em; height: 1em; fill: currentColor; }
    ');

    // ── Performance: aggressive dequeue unused core assets ──────
    $unused_styles = ['wp-block-library', 'wp-block-library-theme', 'wc-blocks-style', 'global-styles', 'classic-theme-styles'];
    foreach ($unused_styles as $style) {
        wp_dequeue_style($style);
        wp_deregister_style($style);
    }

    // Remove WP global styles inline CSS (prevents unwanted a { text-decoration: underline })
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

    // Dequeue unused scripts
    if (function_exists('wp_dequeue_script')) {
        wp_dequeue_script('wp-embed');
        wp_deregister_script('wp-embed');
    }
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
});

add_action('wp_enqueue_scripts', function () {
    if (is_admin() || !sgh_is_native_theme_surface()) {
        return;
    }

    $legacy_styles = [
        'astra-theme-css',
        'astra-addon-css',
        'elementor-frontend',
        'elementor-post-9',
        'elementor-post-90',
        'elementor-post-bambino',
        'elementor-post-grand-marble',
        'widget-heading',
        'widget-divider',
        'widget-image',
        'widget-counter',
        'widget-icon-list',
        'widget-icon-box',
        'widget-image-carousel',
        'widget-star-rating',
        'swiper',
        'e-swiper',
        'e-animation-grow',
        'elementor-gf-montserrat',
        'elementor-gf-roboto',
    ];

    foreach ($legacy_styles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }

    $legacy_scripts = [
        'astra-theme-js',
        'astra-addon-js',
        'elementor-webpack-runtime',
        'elementor-frontend-modules',
        'elementor-frontend',
        'jquery-numerator',
        'swiper',
    ];

    foreach ($legacy_scripts as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}, 100);

// Disable jQuery Migrate on frontend (modern jQuery 3+ doesn't need it)
add_action('wp_default_scripts', function ($scripts) {
    if (is_admin() || !is_object($scripts) || empty($scripts->registered)) return;
    if (!isset($scripts->registered['jquery']) || !is_object($scripts->registered['jquery'])) return;
    $jquery = $scripts->registered['jquery'];
    if (isset($jquery->deps) && is_array($jquery->deps)) {
        $jquery->deps = array_values(array_diff($jquery->deps, ['jquery-migrate']));
    }
});

/**
 * Preload hero image for LCP optimization.
 *
 * T-018: emit `imagesrcset` + `imagesizes` so the browser preloads the
 * SAME resolution it will render. Without `imagesrcset`, Lighthouse
 * preload-LCP-candidate warns "preloaded image not used" because the
 * <img> picks a different srcset entry than the preload URL.
 */
add_action('wp_head', function () {
    if (is_admin()) return;

    $uri = get_template_directory_uri();
    $preload_img = '';

    if (is_front_page()) {
        $cache_key = 'sh_hero_preload_v2';
        $cached = get_transient($cache_key);

        if ($cached === false) {
            $hero_id = 0;
            $hero_bg_url = '';
            $front_page_id = (int) get_option('page_on_front');

            if ($front_page_id > 0) {
                $raw_bg = get_post_meta($front_page_id, '_pi_hero_1_bg', true);
                if (is_numeric($raw_bg)) {
                    $hero_id = (int) $raw_bg;
                    $hero_bg_url = wp_get_attachment_image_url($hero_id, 'full');
                } elseif (!empty($raw_bg)) {
                    $hero_bg_url = (string) $raw_bg;
                }
            }

            if (empty($hero_bg_url)) {
                $q = new WP_Query([
                    'post_type' => 'post', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC',
                    'meta_query' => [['key' => '_thumbnail_id', 'compare' => 'EXISTS']],
                    'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false,
                ]);
                if ($q->have_posts()) {
                    $q->the_post();
                    $hero_id = (int) get_post_thumbnail_id(get_the_ID());
                    if ($hero_id > 0) {
                        $hero_bg_url = wp_get_attachment_image_url($hero_id, 'full');
                    }
                    wp_reset_postdata();
                }
            }

            if (empty($hero_bg_url)) {
                $hero_bg_url = get_template_directory_uri() . '/assets/images/placeholder.svg';
            }

            $hero_srcset = '';
            if ($hero_id > 0) {
                $hero_srcset = (string) wp_get_attachment_image_srcset($hero_id, 'full');
            }

            $cached = ['url' => $hero_bg_url, 'srcset' => $hero_srcset];
            set_transient($cache_key, $cached, DAY_IN_SECONDS);
        }

        $hero_bg_url = html_entity_decode((string) ($cached['url'] ?? ''), ENT_QUOTES, 'UTF-8');
        $hero_srcset = html_entity_decode((string) ($cached['srcset'] ?? ''), ENT_QUOTES, 'UTF-8');

        if ($hero_srcset !== '') {
            printf(
                '<link rel="preload" as="image" href="%1$s" imagesrcset="%2$s" imagesizes="100vw" fetchpriority="high">' . "\n",
                esc_url($hero_bg_url),
                esc_attr($hero_srcset)
            );
        } else {
            printf(
                '<link rel="preload" as="image" href="%1$s" imagesizes="100vw" fetchpriority="high">' . "\n",
                esc_url($hero_bg_url)
            );
        }
        return;
    } elseif (is_page_template('page-templates/page-gioi-thieu.php') || is_page('ve-saigon-horeca') || is_page('gioi-thieu')) {
        $preload_img = sgh_img('2023/12/bia-bep-cong-nghiep-inox.webp');
    } elseif (is_page_template('page-templates/page-lien-he.php') || is_page('lien-he')) {
        $preload_img = sgh_img('2023/12/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp');
    } elseif (is_post_type_archive('project') || is_tax('project_category') || is_page('du-an')) {
        $preload_img = sgh_img('2024/06/Sol0D7-05.webp');
    }

    if (!empty($preload_img)) {
        printf(
            '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
            esc_url($preload_img)
        );
    }
}, 2);

// Bust old hero preload cache once on theme upgrade (T-018 changed cache key).
add_action('save_post', function($post_id) {
    if ($post_id == get_option('page_on_front')) {
        delete_transient('sh_hero_preload_url'); // legacy key
        delete_transient('sh_hero_preload_v2');
    }
});

// Clear hero cache when featured image attachment is updated
add_filter('wp_update_attachment_metadata', function($data, $id) {
    $front_id = (int) get_option('page_on_front');
    if ($front_id > 0 && (int) get_post_thumbnail_id($front_id) === $id) {
        delete_transient('sh_hero_preload_url'); // legacy
        delete_transient('sh_hero_preload_v2');
    }
    return $data;
}, 10, 2);

/**
 * Optimize Google Fonts loading by making it non-blocking.
 * Replaces rel="stylesheet" with asynchronous preload loading.
 */
add_filter('style_loader_tag', function ($tag, $handle) {
    if (is_admin()) return $tag;
    if ($handle === 'google-fonts-sgh') {
        return str_replace(
            "rel='stylesheet'",
            "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"",
            $tag
        );
    }
    return $tag;
}, 10, 2);
