<?php
if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if (!is_plugin_active('pi-multilang/pi-multilang.php')) {
    activate_plugin('pi-multilang/pi-multilang.php');
}
/**
 * Saigon Horeca Theme Functions
 *
 * Slim loader " all logic lives in inc/ modules.
 *
 * Naming convention:
 *   sgh_  " canonical prefix for all new functions (admin + public)
 *   sh_   " legacy prefix (public-facing helpers, kept for compatibility)
 *   saigonhouse_ " legacy prefix (theme setup functions, kept for compatibility)
 *
 * @package SaigonHoreca
 */

// 0. AUTOLOADER removed " all PSR-4 classes moved to plugins (pi-*)
// Each Pi plugin has its own autoloader in its bootstrap file.

// Polyfill mbstring functions if extension not loaded
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($str, $encoding = 'UTF-8') { return strtolower($str); }
}
if (!function_exists('mb_strpos')) {
    function mb_strpos($haystack, $needle, $offset = 0, $encoding = 'UTF-8') { return strpos($haystack, $needle, $offset); }
}
if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $length = null, $encoding = 'UTF-8') {
        return $length === null ? substr($str, $start) : substr($str, $start, $length);
    }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($str, $encoding = 'UTF-8') { return strlen($str); }
}

require get_template_directory() . '/inc/engine/update-blocker.php';

/**
 * ── Performance: Disable Emojis & oEmbed ────────────────────
 * Removes unused CSS and JS from frontend (saves ~20 KiB + 1 request)
 */
add_action('init', function() {
    // Emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', function($plugins) { return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : []; });
    add_filter('wp_resource_hints', function($hints, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            $hints = array_diff($hints, ['//s.w.org']);
        }
        return $hints;
    }, 10, 2);

    // oEmbed
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('embed_oembed_discover', '__return_false');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
}, 1);

// ============================================================================
// 1. THEME SETUP
// ============================================================================

function saigonhouse_setup() {
    // Load textdomain for i18n (bilingual VN/EN support)
    load_theme_textdomain('saigonhoreca', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo', [
        'height' => 80, 'width' => 200, 'flex-height' => true, 'flex-width' => true,
    ]);
    add_theme_support('site-icon');
    add_editor_style('assets/css/utilities.css');
    register_nav_menus([
        'primary'      => __('Primary Menu', 'saigonhoreca'),
        'sidebar_menu' => __('Sidebar — Tiện Ích Nổi Bật', 'saigonhoreca'),
    ]);

    // T-013: Custom image sizes matching real display widths.
    // Lighthouse warned about 768x576 served when displayed at 412x309 →
    // 51KB wasted bandwidth per image. These sizes feed into wp_get_attachment_image()
    // srcset so the browser picks the closest match for the viewport.
    add_image_size('sgh-card-sm', 480, 360, false);   // mobile project cards (~412px display)
    add_image_size('sgh-card',    640, 480, false);   // tablet / retina mobile (~640px)
    add_image_size('sgh-card-lg', 960, 720, false);   // desktop card 2x (~480px display)
    add_image_size('sgh-square',  480, 480, true);    // square thumbnails (avatars, icons)
}
add_action('after_setup_theme', 'saigonhouse_setup');

/**
 * Register widget areas used across the theme.
 *
 * Sidebar area (1):
 *   saigonhouse-sidebar — single, page, category, archive, tag, search.
 *
 * Footer areas (4):
 *   saigonhouse-footer-1 … -4 — four equal columns in footer widget row.
 *   Fallback: if ALL four are empty, the legacy hardcoded footer grid
 *   (company-info / quick-links / contact-info) is rendered instead.
 */
function saigonhouse_widgets_init() {
    // ── Sidebar (shared across single + page + archive templates) ──
    register_sidebar([
        'name'          => __('Sidebar — Widget Area', 'saigonhoreca'),
        'id'            => 'saigonhouse-sidebar',
        'description'   => __('Hiển thị trên bài viết, trang, category, tag, search. Đặt giữa danh sách CTA và khối Liên Hệ.', 'saigonhoreca'),
        'before_widget' => '<div id="%1$s" class="sh-sidebar__widget sh-sidebar__widget--wp %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sh-sidebar__widget-title"><span class="sh-sidebar__title-bar"></span>',
        'after_title'   => '</h3>',
    ]);

    // ── Footer columns (4) ──
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar([
            'name'          => sprintf(__('Footer Cột %d', 'saigonhoreca'), $i),
            'id'            => 'saigonhouse-footer-' . $i,
            'description'   => sprintf(__('Cột %d trong 4 cột footer. Để trống cả 4 cột → hiển thị footer mặc định (Giới thiệu / Liên kết / Liên hệ).', 'saigonhoreca'), $i),
            'before_widget' => '<div id="%1$s" class="sh-footer__widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="sh-footer__widget-title">',
            'after_title'   => '</h4>',
        ]);
    }
}
add_action('widgets_init', 'saigonhouse_widgets_init');

/**
 * Returns true if ANY of the 4 footer widget columns has a widget.
 * Used by footer.php to decide between widget layout vs legacy hardcode fallback.
 */
function sh_footer_has_widgets(): bool {
    for ($i = 1; $i <= 4; $i++) {
        if (is_active_sidebar('saigonhouse-footer-' . $i)) return true;
    }
    return false;
}

// Load the Sidebar Menu Walker (depends on sh_icon())
require_once get_template_directory() . '/inc/engine/class-sidebar-walker.php';

/**
 * Auto-enforce permalink structure on theme activation.
 * Fires ONCE when the theme is switched to. Also runs if the structure is missing.
 * Defensive: catches any server-side restriction (e.g. read-only .htaccess).
 */

if (!function_exists('saigonhouse_enforce_permalink_structure')) {
function saigonhouse_enforce_permalink_structure() {
    try {
        // saigonhoreca.vn uses clean `/<slug>/` permalinks for blog posts
        // (no date prefix), matching production. Without this, WP defaults
        // to date-prefixed permalinks and 301-redirects clean URLs onto them,
        // which breaks every imported post URL.
        $canonical = '/%postname%/';
        $current = get_option('permalink_structure');
        if ($current === $canonical) return;

        // Cheap path: just update the option. flush_rules writes .htaccess which
        // can fail on locked-down hosting — avoid that in the auto-enforce path.
        update_option('permalink_structure', $canonical);

        global $wp_rewrite;
        if (!empty($wp_rewrite) && is_object($wp_rewrite) && method_exists($wp_rewrite, 'set_permalink_structure')) {
            $wp_rewrite->set_permalink_structure($canonical);
            // flush_rules without writing .htaccess (second arg false)
            if (method_exists($wp_rewrite, 'flush_rules')) {
                $wp_rewrite->flush_rules(false);
            }
        }
    } catch (\Throwable $e) {
        // never fatal the site for permalink auto-correction
        error_log('[SaigonHoreca] enforce_permalink_structure failed: ' . $e->getMessage());
    }
}
}
add_action('after_switch_theme', 'saigonhouse_enforce_permalink_structure');
// Fallback: enforce on admin_init if structure still wrong (only in admin context)
add_action('admin_init', function () {
    if (get_option('permalink_structure') !== '/%postname%/') {
        saigonhouse_enforce_permalink_structure();
    }
});

// Pi Color System: bảng màu Saigonhoreca nằm trong assets/css/core/_tokens.css của chính theme.
// (2026-08-02: bỏ tham chiếu pi-dashboard-v2 — plugin đó chưa từng được cài trên site này.)
// The old inc/config/pi-colors.php filter was removed (duplicated plugin theme).

// Page Section Registry: editable sections for each page template
require_once __DIR__ . '/inc/config/section-registry.php';

// Allow comments without login
add_filter('pre_option_comment_registration', '__return_zero');

// Tắt Gutenberg Block Editor → dùng Classic Editor
add_filter('use_block_editor_for_post', '__return_false');
add_filter('use_block_editor_for_post_type', '__return_false');

/**
 * Force register page templates in subdirectories.
 *
 * Saigonhoreca page templates orchestrate template-parts/{about,contact}/*
 * — each section extracted from the production scrape. Each template
 * triggers its own per-route Tailwind v4 bundle via inc/core/enqueue.php:
 *   page-gioi-thieu.php → theme-about.css
 *   page-lien-he.php    → theme-contact.css
 *
 * Saigonhouse construction page templates (Báo Giá Kiến Trúc, Bảng Giá,
 * Portfolio Kiến Trúc, Dự Toán) do not apply to a horeca business and
 * are intentionally absent.
 */
add_filter('theme_page_templates', function ($templates) {
    $templates['page-templates/page-gioi-thieu.php'] = 'Giới Thiệu (Về Saigon Horeca)';
    $templates['page-templates/page-lien-he.php']    = 'Liên Hệ';
    return $templates;
});

// ============================================================================
// 2. INCLUDES
// ============================================================================

$inc = get_template_directory() . '/inc/';

// ─────────────────────────────────────────────────────────────────────────
// THỨ TỰ NẠP CÓ RÀNG BUỘC THẬT — đảo dòng là site vỡ, và KHÔNG có test nào
// bắt được. Ràng buộc đã kiểm chứng (2026-08-03) ghi ngay tại dòng, dạng
// "⟵ CẦN <file>". Dòng không ghi gì là độc lập, đổi chỗ được.
//
// Quy tắc chung: config/ nạp TRƯỚC engine/. config/ chỉ khai hằng số và bảng
// dữ liệu, không hook WP; engine/ đọc các bảng đó. Thêm file mới thì giữ đúng
// hai tầng này — đừng để engine/ phải biết dữ liệu riêng của site
// (scripts/build.mjs có lint ENGINE PURITY canh điều đó).
// ─────────────────────────────────────────────────────────────────────────

// Config (brand-specific " theme only)
require $inc . 'config/constants.php';       // SGH_SITE_NAME, FB_APP_ID, etc.
require $inc . 'config/domains.php';         // Production hostnames (.vn/.com)
require $inc . 'config/contact-info.php';    // Company phone, email, address
require $inc . 'config/site-structure.php';  // Pages, categories, menu definitions
require $inc . 'config/routes.php';          // Route → CSS bundle + hero-preload map
                                             //   ⟵ CẦN domains.php: sgh_is_known_production_host()
                                             //   Cung cấp: sgh_bilingual_slug_map(), sgh_route_css_rules(),
                                             //   sgh_project_consolidation_redirects(), sgh_project_slug_aliases()
require $inc . 'config/fonts.php';           // Font families + color-token palette
require $inc . 'engine/perf-config.php';       // Perf constants + filter helpers
require $inc . 'engine/img-helper.php';        // sgh_img() — env-aware uploads URL (local mirror vs prod)
                                               //   ⟵ CẦN domains.php + perf-config.php
require $inc . 'site/project-cover.php';      // project cover resolver — cached map + fallback chain (never-black)
                                              //   ⟵ CẦN img-helper.php (sgh_img)
require $inc . 'engine/bot-optimize.php';      // sgh_is_bot() — bots/Lighthouse get light render (no fixed parallax)
require $inc . 'engine/slug-map.php';          // Bilingual slug map (vi ↔ en) + counterpart URL switcher
                                               //   ⟵ CẦN routes.php: sgh_bilingual_slug_map()
                                               //   Cung cấp: sgh_slug(), sgh_url(), filter 'locale'
require $inc . 'engine/pillar-routes.php';     // Auto-route /du-an/<slug>/ to single-project/<slug>.php
                                               //   ⟵ CẦN slug-map.php (sgh_slug) — bắt URI theo slug
                                               //      ngôn ngữ hiện tại; nạp trước thì route ảo hỏng.
                                               //   ⟵ CẦN routes.php: sgh_project_consolidation_redirects()
require $inc . 'engine/cpt-registration.php';  // CPT `project`+`product` + taxonomies (Phase 2 data layer)
                                               //   ⟵ CẦN slug-map.php — has_archive/rewrite lấy slug từ sgh_slug()
require $inc . 'engine/media-routing.php';      // Route new uploads: product->products/<slug>/, post->blog/<slug>/
                                                //   ⟵ CẦN cpt-registration.php — phân nhánh theo post_type

add_filter('template_include', function ($template) {
    if (is_front_page()) {
        $front_page = get_template_directory() . '/front-page.php';
        if (is_readable($front_page)) {
            return $front_page;
        }
    }

    return $template;
}, 100);

// WebP pipeline toggle. The conversion module also reads this legacy filter name.
add_filter('sh_enable_webp_autoconvert', '__return_true');
add_filter('sgh_webp_enabled', '__return_true');

/**
 * ── Performance: combined `the_content` rewrite — single regex pass ──
 *
 * Was 2 separate `the_content` filters (one for loading="lazy", one for alt
 * text below at line ~465), each running its own preg_replace_callback. On
 * archive pages with 10 posts this meant 20 regex passes. Combined into a
 * single pass that handles both transforms.
 *
 * - `loading="lazy"`: skip the FIRST image (LCP candidate), lazy the rest.
 * - `alt="..."`: only on singular pages, fall back to post title.
 *
 * NOTE: the static $count is intentionally module-scope so that on archive
 * pages only the very first <img> across all listed posts is eager.
 */
add_filter('the_content', function ($content) {
    if (empty($content)) return $content;

    $singular = is_singular();
    $title    = $singular ? get_the_title() : '';
    static $imgCount = 0;

    return preg_replace_callback('/<img([^>]+)>/i', function ($matches) use ($singular, $title, &$imgCount) {
        $tag = $matches[0];

        // 1. loading="lazy" — skip first image on the page (LCP)
        if (strpos($tag, 'loading=') === false) {
            $imgCount++;
            if ($imgCount > 1) {
                $tag = str_replace('<img', '<img loading="lazy"', $tag);
            }
        }

        // 2. alt="..." — singular only, use post title as fallback
        if ($singular && $title !== '') {
            if (preg_match('/\salt=[\'"][^\'"]+[\'"]/i', $tag)) {
                // Already has non-empty alt — leave it.
            } elseif (preg_match('/\salt=[\'"][\'"]/i', $tag)) {
                $tag = preg_replace('/\salt=[\'"][\'"]/i', ' alt="' . esc_attr($title) . '"', $tag);
            } else {
                $tag = str_replace('<img', '<img alt="' . esc_attr($title) . '"', $tag);
            }
        }

        return $tag;
    }, $content);
}, 999);

/**
 * Legacy transient cleanup from the removed first-generation WebP mapper.
 *
 * Current behavior lives in `inc/core/webp-conversion.php`:
 * - generate `.webp` siblings for uploads
 * - keep original file URLs intact in the database
 * - swap to `.webp` at runtime only when the sibling exists
 *
 * This block only clears old transients once.
 */
add_action('init', function () {
    if (get_option('sh_webp_legacy_cleared')) return;
    delete_transient('sh_webp_map');
    update_option('sh_webp_legacy_cleared', 1, false);
}, 999);


// ============================================================================
// THEME MODULES -- Brand-specific, not in plugins
// ============================================================================

require $inc . 'engine/live-search.php';       // AJAX autocomplete cho ô tìm kiếm (header + archive-product)
require $inc . 'engine/enqueue.php';           // Frontend assets (JS, CSS, fonts)
require $inc . 'engine/critical-css.php';      // Inline theme.css + dequeue external (T-014 restore)
require $inc . 'site/auto-setup.php';        // Theme activation (front page, menu)
require $inc . 'engine/svg-icons.php';         // Inline SVG icons
require $inc . 'site/section-images.php';    // Media library helper
require $inc . 'engine/breadcrumbs.php';       // Breadcrumbs renderer
require $inc . 'engine/excerpt-resolver.php';     // Smart Excerpt & Sapo Fallback Module
require $inc . 'engine/cache-helpers.php';     // Theme cache helpers
require $inc . 'site/pi-seo-resolver.php';   // Pi-managed SEO meta/options resolver
require $inc . 'engine/seo-meta.php';           // Saigon Horeca SEO Meta & Social Tags Module
require $inc . 'engine/seo-robots.php';         // Saigon Horeca SEO Robots & Canonical Module
require $inc . 'engine/seo-schema.php';         // Saigon Horeca JSON-LD Structured Data Schema Module
require $inc . 'engine/seo-performance.php';    // Saigon Horeca SEO Performance & Security Module
require $inc . 'engine/webp-conversion.php';      // WebP sibling generation + runtime serve (Accept-aware)
require $inc . 'engine/redirect-manager.php';  // Redirect frontend handler

// Navigation walkers
require_once $inc . 'engine/class-mobile-walker.php';
require_once $inc . 'engine/class-desktop-walker.php';

// Customizer (theme color, footer, etc.)
require $inc . 'customizer/init.php';

// Admin modules (brand-specific — not in plugins)
// Also load for REST API requests so /pi/v1/* endpoints can access SGH_Dashboard, sync functions, etc.
$is_rest_api = (defined('REST_REQUEST') && REST_REQUEST) 
    || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'wp-json/pi/') !== false);

if (is_admin() || wp_doing_ajax() || $is_rest_api) {
    // Guarded so a missing optional module never fatals wp-admin / ajax / REST
    // (a bare require of an absent file 500s every /wp-json/pi/* call + wp-admin).
    foreach ([
        'site/admin/front-page-meta.php',     // Homepage section metabox
        'site/admin/dashboard-class.php',     // SGH_Dashboard class (SGH-specific stats)
        'site/admin/dashboard-menu-def.php',  // sgh_dashboard_get_menu_definition() — brand menu tree
        'site/admin/sync/sync-setup.php',     // Favicons, menu sync
        'site/admin/sync/sync-deploy.php',    // .htaccess, robots.txt deploy
        'site/admin/sync/sync-data.php',      // DB-to-JSON export, WebP convert
        'site/admin/notifications.php',       // Admin notices (optional — may be absent)
        'site/tools/import-gdocs.php',        // wp-admin: Posts > Import Google Docs (paste link → draft "tin tức")
    ] as $sgh_admin_mod) {
        $sgh_admin_path = $inc . $sgh_admin_mod;
        if (file_exists($sgh_admin_path)) {
            require $sgh_admin_path;
        }
    }
}

add_filter('pi_api_overview_stats', function ($stats) {
    if (class_exists('SGH_Dashboard')) {
        return SGH_Dashboard::get_instance()->get_stats();
    }
    return $stats;
});

add_filter('pi_api_theme_og_image_exists', function ($exists) {
    return $exists || file_exists(get_template_directory() . '/assets/images/og-default.webp');
});

add_filter('pi_api_deploy_action_result', function ($result, $action) {
    if (class_exists('SGH_Dashboard')) {
        return SGH_Dashboard::get_instance()->process_action($action);
    }
    return $result;
}, 10, 2);

// ============================================================================
// 3. UTILITIES
// ============================================================================

/**
 * Enforce site title
 */
add_action('init', function () {
    if (get_option('blogname') !== SGH_SITE_NAME) {
        update_option('blogname', SGH_SITE_NAME);
        update_option('blogdescription', SGH_SITE_TAGLINE);
    }
});



/**
 * Ensure Publish metabox is always visible
 */
add_action('admin_init', function () {
    if (!$uid = get_current_user_id()) return;
    if (!get_option('page_on_front')) return;
    $hidden = get_user_meta($uid, 'metaboxhidden_page', true);
    if (is_array($hidden) && in_array('submitdiv', $hidden)) {
        update_user_meta($uid, 'metaboxhidden_page', array_diff($hidden, ['submitdiv']));
    }
});

/**
 * Post views " handled by inc/core/post-views.php (AJAX-based)
 * Old cookie-based counter removed. Meta key: _sh_post_views
 */


// Force HTTPS on every URL WordPress generates — moved to inc/engine/https-rewrite.php.
require get_template_directory() . '/inc/engine/https-rewrite.php';

/**
 * Redirect /saigon-admin †' WP Admin dashboard
 */
add_action('template_redirect', function () {
    // Force HTTPS on every environment (local + production). LocalWP
    // ships a trusted self-signed cert, so HTTPS works in dev too.
    if (
        !is_admin()
        && !wp_doing_ajax()
        && !defined('REST_REQUEST')
    ) {
        $is_https = is_ssl()
            || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

        if (!$is_https && !headers_sent()) {
            $uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/';
            $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
            wp_safe_redirect('https://' . $host . $uri, 301);
            exit;
        }
    }

    $req = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    if (strpos($req, '/saigon-admin') !== false) {
        wp_redirect(admin_url('admin.php?page=sgh-dashboard'));
        exit;
    }
    // /mau-nha/ †' redirect to category thiet-ke (page is empty, category has posts)
    if (is_page('mau-nha')) {
        $term = get_term_by('slug', 'thiet-ke', 'category');
        if ($term) {
            wp_redirect(get_term_link($term), 301);
            exit;
        }
    }
});

// ============================================================================
// 4. QUERY MODIFICATIONS
// ============================================================================

// Query modifications (featured-image-first ordering, posts_per_page) — moved to inc/engine/query-mods.php.
require $inc . 'engine/query-mods.php';




// ============================================================================
// 5. CONTENT FILTERS
// ============================================================================

// Redirect /tin-tuc/ → /category/tin-tuc/ (đồng bộ layout với category archive)
add_action('template_redirect', function () {
    $uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
    $uri = '/' . trim((string) $uri, '/') . '/';
    if ($uri === '/tin-tuc/' || strpos($uri, '/tin-tuc/page/') === 0) {
        $target = str_replace('/tin-tuc/', '/category/tin-tuc/', $uri);
        wp_safe_redirect(home_url($target), 301);
        exit;
    }
}, 1);

require $inc . 'engine/markdown-filters.php';
require $inc . 'site/tools/clean-elementor-posts.php'; // Admin-only ?sgh_clean_elementor=1
require $inc . 'site/tools/convert-webp.php';         // Admin-only ?sgh_convert_webp=1 (PNG/JPG/JPEG → WebP)
require $inc . 'site/tools/seed-content.php';          // Admin-only ?sgh_seed=1 (seed 30 dự án + fix template trang Về/Liên hệ)
// import-content / reimport-products / recrawl-posts / upload-zip đã chuyển
// sang plugin pi-migrate (2026-07-27) — không phải logic theme.

// ============================================================================
// 7. SEO HOOKS
// ============================================================================

/**
 * Provide Facebook App ID for Open Graph
 * Note: You can replace '966242223397117' with your actual FB App ID
 */
add_filter('sh_fb_app_id', function() {
    return SGH_FB_APP_ID;
});

/*
 * Auto alt text for images — merged into the combined `the_content` filter
 * above (search "combined `the_content` rewrite"). Removed standalone filter
 * to avoid running a second preg_replace_callback over the same content.
 */

/**
 * Auto alt for attachment images
 */
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment) {
    if (empty($attr['alt'])) {
        $attr['alt'] = get_the_title() ? wp_strip_all_tags(get_the_title()) : get_the_title($attachment->ID);
    }
    return $attr;
}, 10, 3);

/**
 * Global post thumbnail fallback.
 *
 * Returns ['url' => string, 'id' => int] where id > 0 means the image
 * has valid WP attachment metadata (safe to use with wp_get_attachment_image()).
 * If metadata is broken (e.g. WebP width=1), id is reset to 0 so callers
 * fall back to a simple <img src> tag instead.
 */
function saigonhouse_get_post_thumbnail_data($post_id = null, $size = 'large') {
    if (!$post_id) $post_id = get_the_ID();

    $image_url = '';
    $image_id  = 0;

    // 1. Featured image
    if (has_post_thumbnail($post_id)) {
        $image_id  = (int) get_post_thumbnail_id($post_id);
        $image_url = wp_get_attachment_image_url($image_id, $size);
    }

    // 2. First image in post content (fallback)
    if (empty($image_url)) {
        $post = get_post($post_id);
        if ($post) {
            $content = $post->post_content;
            // Ưu tiên img tag có src thật
            if (preg_match('/<img[^>]+(?:src|data-src)=[\'"]([^\'"]+\.(?:jpg|jpeg|png|webp|gif))[\'"][^>]*>/i', $content, $m)) {
                $image_url = $m[1];
            // Fallback: Gutenberg block wp:image
            } elseif (preg_match('/wp:image\s+\{"id":(\d+)/i', $content, $m)) {
                $att_id  = (int) $m[1];
                $att_url = wp_get_attachment_image_url($att_id, $size);
                if ($att_url) { $image_url = $att_url; $image_id = $att_id; }
            }
            // Tự động gán Featured Image nếu tìm được ảnh trong content
            if (!empty($image_url)) {
                if (!$image_id) $image_id = (int) attachment_url_to_postid($image_url);
                if ($image_id > 0) set_post_thumbnail($post_id, $image_id);
            }
        }
    }

    // 3. Validate metadata " fix WebP width=1 height=1 bug
    //    Khi WP không đọc được metadata ảnh WebP, nó gán width=1 height=1.
    //    Nếu dùng wp_get_attachment_image() với ID này, ảnh render ra 1x1 pixel.
    //    Reset ID về 0 để post-card.php dùng <img src> trực tiếp thay thế.
    if ($image_id > 0) {
        $meta = wp_get_attachment_metadata($image_id);
        if (empty($meta['width']) || (int) $meta['width'] <= 1) {
            // Metadata hỏng †' giữ URL nhưng bỏ ID
            if (empty($image_url)) {
                $image_url = wp_get_attachment_url($image_id);
            }
            $image_id = 0;
        }
    }

    // 4. Placeholder (không có ảnh nào)
    if (empty($image_url)) {
        $image_url = get_template_directory_uri() . '/assets/images/placeholder.svg';
        $image_id  = 0;
    }

    return ['url' => $image_url, 'id' => $image_id];
}

// Security headers (HSTS, CSP, X-Frame-Options...) — moved to inc/engine/security-headers.php.
require $inc . 'engine/security-headers.php';

/**
 * ── Smart Author Redirect ────────────────────────────────────
 * Bắt các request truy cập trực tiếp vào '/author' hoặc '/author/' (lỗi 404 mặc định của WP)
 * và tự động redirect 301 về trang tác giả chính của website (ID = 1, admin)
 * để tránh trang 404 xấu xí cho người dùng.
 */
add_action('template_redirect', function() {
    if (is_404()) {
        $request_uri = $_SERVER['REQUEST_URI'];
        // Chuẩn hóa request uri, ví dụ: /author/ hoặc /author
        $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
        
        if ($path === 'author') {
            // Lấy ID của admin đầu tiên có bài viết, mặc định là 1
            $author_id = 1; 
            $author_url = get_author_posts_url($author_id);
            if ($author_url) {
                wp_redirect($author_url, 301);
                exit;
            }
        }
    }
});

// Static HTML Cache: logic đầy đủ (dir/path/is_cacheable/flush + hook
// template_redirect/save_post/comment_post/switch_theme/updated_option) đã
// nằm trong inc/engine/cache-helpers.php — block này TỪNG bị trùng lặp ở
// đây (dead code do function_exists() guard, cache-helpers.php load trước
// nên luôn thắng) VÀ 2 add_action('template_redirect') cùng priority 2 từ
// 2 nơi từng lồng ob_start() vào nhau. Đã dọn 07/2026: merge fix gzip-decode
// (từng chỉ có ở bản dead-code này) vào bản đang chạy thật trong
// cache-helpers.php, rồi xoá hẳn block trùng ở đây.

/**
 * Heading-order fix for /gioi-thieu/ legacy WP page content.
 *
 * The post content authored in WP editor uses <h5> directly after <h1>,
 * skipping h2/h3/h4 (axe `heading-order` fail). Filter promotes them to
 * <h2> on render so screen-reader hierarchy is correct without forcing
 * the user to edit legacy content.
 */
add_filter('the_content', static function ($content) {
    if (!is_page('gioi-thieu')) return $content;
    return strtr($content, [
        '<h1>'  => '<h2 class="sh-legacy-h1">',
        '</h1>' => '</h2>',
        '<h5>'  => '<h2 class="sh-legacy-h5">',
        '</h5>' => '</h2>',
        '<h6>'  => '<h3 class="sh-legacy-h6">',
        '</h6>' => '</h3>',
    ]);
}, 20);
