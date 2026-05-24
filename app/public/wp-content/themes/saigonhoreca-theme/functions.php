<?php
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

// Disable WP UPDATE CHECKS only — không chặn Plugin Install / Search API.
// 1. Return empty data for update transients (prevents stale check trigger)
add_filter('pre_site_transient_update_plugins', function() { return (object) ['last_checked' => time(), 'response' => [], 'translations' => [], 'no_update' => []]; });
add_filter('pre_site_transient_update_themes',  function() { return (object) ['last_checked' => time(), 'response' => [], 'translations' => [], 'no_update' => []]; });
add_filter('pre_site_transient_update_core',    function() { return (object) ['last_checked' => time(), 'version_checked' => get_bloginfo('version'), 'updates' => []]; });
// 2. Block CHỈ các endpoint update-check (không chặn plugin install / browse / download)
add_filter('pre_http_request', function($pre, $args, $url) {
    $block_patterns = [
        '/core/version-check/',      // Core update check
        '/plugins/update-check/',    // Plugin update check
        '/themes/update-check/',     // Theme update check
        '/core/credits/',            // Credits ping (not essential)
    ];
    foreach ($block_patterns as $needle) {
        if (stripos($url, $needle) !== false) {
            return ['response' => ['code' => 200], 'body' => ''];
        }
    }
    return $pre;
}, 1, 3);
// 3. Remove scheduled cron events at earliest hook
add_action('init', function() {
    remove_action('wp_version_check', 'wp_version_check');
    remove_action('wp_update_plugins', 'wp_update_plugins');
    remove_action('wp_update_themes', 'wp_update_themes');
    remove_action('wp_maybe_auto_update', 'wp_maybe_auto_update');
}, 1);

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
require_once get_template_directory() . '/inc/core/class-sidebar-walker.php';

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

// Pi Color System: Saigonhoreca palette now lives in pi-dashboard-v2/includes/themes/saigonhouse.php.
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

// Config (brand-specific " theme only)
require $inc . 'config/constants.php';       // SGH_SITE_NAME, FB_APP_ID, etc.
require $inc . 'config/contact-info.php';    // Company phone, email, address
require $inc . 'config/site-structure.php';  // Pages, categories, menu definitions
require $inc . 'core/perf-config.php';       // Perf constants + filter helpers
require $inc . 'core/img-helper.php';        // sgh_img() — env-aware uploads URL (local mirror vs prod)
require $inc . 'core/slug-map.php';          // Bilingual slug map (vi ↔ en) + counterpart URL switcher
require $inc . 'core/pillar-routes.php';     // Auto-route /du-an/<slug>/ to page-pillar-*.php (BEM rebuild)
if (function_exists('sgh_is_local_static_mirror_enabled') && sgh_is_local_static_mirror_enabled()) {
    require $inc . 'core/static-mirror.php'; // Local/dev scraped HTML fallback only.
}
require $inc . 'core/cpt-registration.php';  // CPT `project`+`product` + taxonomies (Phase 2 data layer)

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
// PI PLUGINS " Installed in wp-content/plugins/ and activated via WP Admin
// ============================================================================
// Required plugins:
//   pi-dashboard     " Core framework (must be active)
//   pi-ai-provider   " 25 AI providers + failover (required by SEO Bot + Chatbot)
//   pi-performance   " Cache, PWA, image optimizer
//   pi-analytics     " Visit tracking, post views
//   pi-leads         " CRM, contact form, webhooks
//   pi-chatbot       " AI chatbot widget
//   pi-seo           " Meta tags, schema, sitemap, SEO audit
//
// Show admin notice only on Dashboard + Plugins pages, not everywhere.
add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;

    // Only show on Dashboard Home and Plugins pages
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen) return;
    $allowed_screens = ['dashboard', 'plugins'];
    if (!in_array($screen->id, $allowed_screens, true)) return;

    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $required = [
        'pi-dashboard/pi-dashboard.php'       => 'Pi Dashboard',
        'pi-ai-provider/pi-ai-provider.php'   => 'Pi AI Provider',
        'pi-seo/pi-seo.php'                   => 'Pi SEO',
        'pi-chatbot/pi-chatbot.php'           => 'Pi Chatbot',
        'pi-leads/pi-leads.php'               => 'Pi Leads',
        'pi-analytics/pi-analytics.php'       => 'Pi Analytics',
        'pi-performance/pi-performance.php'   => 'Pi Performance',
    ];
    $missing = [];
    foreach ($required as $slug => $name) {
        if (!is_plugin_active($slug)) $missing[] = $name;
    }
    if (!empty($missing)) {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>SaigonHoreca Theme:</strong> Chưa kích hoạt plugin: <strong>'
            . esc_html(implode(', ', $missing)) . '</strong>. '
            . '<a href="' . esc_url(admin_url('plugins.php')) . '">Vào Plugins → Activate</a>.</p></div>';
    }
});

// ============================================================================
// THEME MODULES -- Brand-specific, not in plugins
// ============================================================================

require $inc . 'core/enqueue.php';           // Frontend assets (JS, CSS, fonts)
require $inc . 'core/critical-css.php';      // Inline theme.css + dequeue external (T-014 restore)
require $inc . 'core/auto-setup.php';        // Theme activation (front page, menu)
require $inc . 'core/svg-icons.php';         // Inline SVG icons
require $inc . 'core/section-images.php';    // Media library helper
require $inc . 'core/breadcrumbs.php';       // Breadcrumbs renderer
require $inc . 'core/cache-helpers.php';     // Theme cache helpers
require $inc . 'core/pi-seo-resolver.php';   // Pi-managed SEO meta/options resolver
require $inc . 'core/seo-meta-description.php'; // Phase 1: Auto meta description (120-155 chars)
require $inc . 'core/seo-robots.php';           // Phase 1: Force index,follow on critical category URLs
require $inc . 'core/seo-schema.php';           // Phase 2: JSON-LD (LocalBusiness, WebSite, Product)
require $inc . 'core/seo-robots-txt.php';       // Phase 3: robots.txt + pagination noindex
require $inc . 'core/seo-performance.php';      // Phase 4: img attrs, resource hints, strip emoji/oembed
require $inc . 'core/webp-conversion.php';      // WebP sibling generation + runtime serve (Accept-aware)
require $inc . 'core/redirect-manager.php';  // Redirect frontend handler
require $inc . 'core/website-features.php';  // Shortcodes (calculator, gallery, etc.)

// Navigation walkers
require_once $inc . 'core/class-mobile-walker.php';
require_once $inc . 'core/class-desktop-walker.php';

// Customizer (theme color, footer, etc.)
require $inc . 'customizer/init.php';

// Admin modules (brand-specific — not in plugins)
// Also load for REST API requests so /pi/v1/* endpoints can access SGH_Dashboard, sync functions, etc.
$is_rest_api = (defined('REST_REQUEST') && REST_REQUEST) 
    || (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], 'wp-json/pi/'));

if (is_admin() || wp_doing_ajax() || $is_rest_api) {
    require $inc . 'admin/front-page-meta.php';      // Homepage section metabox
    require $inc . 'admin/dashboard-class.php';      // SGH_Dashboard class (SGH-specific stats)
    require $inc . 'admin/dashboard-menu-def.php';   // sgh_dashboard_get_menu_definition() " brand-specific menu tree
    require $inc . 'admin/sync/sync-setup.php';      // Favicons, menu sync
    require $inc . 'admin/sync/sync-deploy.php';     // .htaccess, robots.txt deploy
    require $inc . 'admin/sync/sync-data.php';       // DB-to-JSON export, WebP convert
    require $inc . 'admin/notifications.php';        // Admin notices
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


/**
 * Force HTTPS on every URL WordPress generates.
 *
 * `siteurl` and `home` options in the DB still hold `http://` because
 * the site was created over plain HTTP. Filtering them in PHP is safer
 * than a DB migration — every call to `home_url()`, `site_url()`,
 * `wp_get_attachment_url()` etc. picks up the HTTPS scheme without
 * needing a one-shot search-replace.
 *
 * T-020: required for Lighthouse Best Practices `is-on-https` audit.
 */
$sgh_force_https_url = static function($url) {
    if (is_string($url) && strpos($url, 'http://') === 0) {
        return 'https://' . substr($url, 7);
    }
    return $url;
};
add_filter('option_siteurl', $sgh_force_https_url);
add_filter('option_home', $sgh_force_https_url);
add_filter('wp_get_attachment_url', $sgh_force_https_url);
add_filter('wp_get_attachment_image_src', static function($image) use ($sgh_force_https_url) {
    if (is_array($image) && !empty($image[0])) {
        $image[0] = $sgh_force_https_url($image[0]);
    }
    return $image;
});
add_filter('wp_calculate_image_srcset', static function($sources) use ($sgh_force_https_url) {
    if (is_array($sources)) {
        foreach ($sources as &$s) {
            if (isset($s['url'])) {
                $s['url'] = $sgh_force_https_url($s['url']);
            }
        }
    }
    return $sources;
});

/**
 * Final-output HTTPS rewrite. Catches any HTTP URLs that slip through
 * the option/attachment filters (menu hrefs, permalinks, post_content
 * with hardcoded URLs, etc.). Runs after the response body is fully
 * rendered but before it leaves the server.
 *
 * Hooked late (priority 999) on `template_redirect` to register an
 * `ob_start` callback that survives until WP closes the buffer.
 */
add_action('template_redirect', static function() {
    $is_https = is_ssl()
        || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

    if (!$is_https || is_admin() || wp_doing_ajax() || defined('REST_REQUEST')) {
        return;
    }
    ob_start(static function($buffer) {
        return str_replace('http://saigonhoreca.local', 'https://saigonhoreca.local', (string) $buffer);
    });
}, 999);

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

/**
 * Posts with featured images appear first in archives
 */
add_filter('posts_join', function ($join, $query) {
    if (is_admin() || !$query->is_main_query()) return $join;
    if ($query->is_home() || $query->is_archive() || $query->is_tag() || $query->is_category()) {
        global $wpdb;
        if (strpos($join, 'AS mt_thumb') === false) {
            $join .= " LEFT JOIN {$wpdb->postmeta} AS mt_thumb ON ({$wpdb->posts}.ID = mt_thumb.post_id AND mt_thumb.meta_key = '_thumbnail_id') ";
        }
    }
    return $join;
}, 10, 2);

add_filter('posts_orderby', function ($orderby, $query) {
    if (is_admin() || !$query->is_main_query()) return $orderby;
    if ($query->is_home() || $query->is_archive() || $query->is_tag() || $query->is_category()) {
        global $wpdb;
        $orderby = "IF(mt_thumb.meta_value IS NOT NULL, 1, 0) DESC, {$wpdb->posts}.post_date DESC";
    }
    return $orderby;
}, 10, 2);

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if ($query->is_archive() || $query->is_category() || $query->is_tag() || $query->is_search() || $query->is_home()) {
        $query->set('posts_per_page', 9);
    }
});




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

require $inc . 'core/markdown-filters.php';
require $inc . 'tools/reimport-products.php';   // Admin-only ?sgh_reimport_products=1
require $inc . 'tools/clean-elementor-posts.php'; // Admin-only ?sgh_clean_elementor=1
require $inc . 'tools/recrawl-posts.php';        // Admin-only ?sgh_recrawl_posts=1

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

/**
 * ── Best Practices: Security Headers ────────────────────────
 * Implements HSTS, CSP, X-Frame-Options, etc.
 * Target: 100/100 in Lighthouse Best Practices.
 */
add_action('send_headers', function() {
    if (is_admin()) return;

    $is_https = is_ssl()
        || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

    // HSTS — only valid on HTTPS (browsers ignore on HTTP; sending it
    // anyway causes a console warning on local HTTP dev).
    if ($is_https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    // X-Frame-Options — prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // X-Content-Type-Options — prevent MIME sniffing
    header('X-Content-Type-Options: nosniff');

    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions Policy
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()');

    // Cross-Origin-Opener-Policy — browsers ignore on non-HTTPS and emit
    // a console warning ("untrustworthy origin"). Only send on HTTPS.
    if ($is_https) {
        header('Cross-Origin-Opener-Policy: same-origin');
    }

    // Content Security Policy — tuned for saigonhoreca's Elementor +
    // Astra + WooCommerce + GA4 stack served via the static mirror.
    // The saigonhouse parent theme had a tight saigonhouse-plugin CSP
    // that broke runtime here (9× font-src violations, GA collect blocks,
    // WooCommerce webpack chunks refused). Relaxed scopes:
    //   - font-src: + https: (FontAwesome, Astra subsets, third-party icon sets)
    //   - script-src: + https: (Astra + Elementor + Woo chunks under same host)
    //   - connect-src: + https://*.google.com https://*.analytics.google.com
    //     (GA4 routes traffic through analytics.google.com and www.google.com)
    //   - style-src: + https: (Astra inline + Elementor)
    //   - img-src: blob: https: (already permissive)
    // Future hardening (production): replace with nonced CSP per real plugin
    // set; static mirror is dev-only so trade some strictness for working UI.
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: blob:",
        "script-src-elem 'self' 'unsafe-inline' https: blob:",
        "style-src 'self' 'unsafe-inline' https:",
        "font-src 'self' data: https:",
        "img-src 'self' data: blob: https:",
        "media-src 'self' https:",
        "connect-src 'self' https: wss:",
        "frame-src 'self' https:",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self' https:",
        "frame-ancestors 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
});

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

/**
 * ─────────────────────────────────────────────────────────────────────
 * Static HTML Cache — ported from saigonhouse-theme.
 *
 * Companion to the Apache rewrite in `.htaccess` (block "4. STATIC HTML
 * CACHE"). The .htaccess rule serves `wp-content/cache/sgh-static/{path}/
 * index.html` directly when the file exists, bypassing PHP entirely →
 * TTFB drops from ~300-500 ms (full PHP boot) to ~50 ms (Apache static
 * file serve). This callback POPULATES that file on cache-miss so the
 * second visit can hit the rewrite.
 *
 * Cache key = REQUEST_URI (e.g. `/` → `sgh-static/index.html`,
 * `/lien-he/` → `sgh-static/lien-he/index.html`).
 *
 * Cacheable when ALL hold:
 *   - GET request + status 200
 *   - Not admin / AJAX / REST / CLI / cron
 *   - Not a logged-in / commenter / postpass cookie
 *   - No query string
 *   - Output is a real HTML page (≥ 1 KB)
 *
 * Invalidated by save_post, comment_post, switch_theme, wp_update_nav_menu,
 * and selected option updates.
 * ─────────────────────────────────────────────────────────────────────
 */
if (!function_exists('sgh_static_cache_dir')) {
    function sgh_static_cache_dir(): string {
        return WP_CONTENT_DIR . '/cache/sgh-static';
    }
}

if (!function_exists('sgh_static_cache_path_for_uri')) {
    function sgh_static_cache_path_for_uri(string $uri): string {
        $uri = strtok($uri, '?');
        if ($uri === false) $uri = '/';
        $uri = trim($uri, '/');
        // Sanitize: only allow [a-zA-Z0-9/_-], reject everything else
        // (cache poisoning guard — REQUEST_URI is user-controlled).
        if ($uri !== '' && !preg_match('#^[a-zA-Z0-9/_-]+$#', $uri)) return '';
        return sgh_static_cache_dir() . ($uri === '' ? '/index.html' : '/' . $uri . '/index.html');
    }
}

if (!function_exists('sgh_static_cache_is_cacheable')) {
    function sgh_static_cache_is_cacheable(): bool {
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

add_action('template_redirect', static function() {
    error_log('[CACHE] template_redirect fired, uri='.$_SERVER['REQUEST_URI']);if (!sgh_static_cache_is_cacheable()) { error_log('[CACHE] not cacheable'); return; }
    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    $path = sgh_static_cache_path_for_uri($uri);
    if ($path === '') return;
    ob_start(static function($buffer) use ($path) {
        if (http_response_code() !== 200) return $buffer;
        if (strlen($buffer) < 1024) return $buffer;
        if (strpos($buffer, '<title>Error') !== false) return $buffer;
        $dir = dirname($path);
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        @file_put_contents($path, $buffer, LOCK_EX);
        return $buffer;
    });
}, 2);

if (!function_exists('sgh_static_cache_flush')) {
    function sgh_static_cache_flush(): void {
        $dir = sgh_static_cache_dir();
        if (!is_dir($dir)) return;
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            if ($f->isFile()) @unlink($f->getPathname());
            elseif ($f->isDir()) @rmdir($f->getPathname());
        }
    }
}
add_action('save_post', 'sgh_static_cache_flush');
add_action('comment_post', 'sgh_static_cache_flush');
add_action('switch_theme', 'sgh_static_cache_flush');
add_action('wp_update_nav_menu', 'sgh_static_cache_flush');
add_action('updated_option', static function($option) {
    if (in_array($option, ['siteurl', 'home', 'blogname', 'blogdescription', 'permalink_structure', 'page_on_front', 'page_for_posts'], true)) {
        sgh_static_cache_flush();
    }
});

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
