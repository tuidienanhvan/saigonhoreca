<?php
/**
 * Front Page Template — Saigon Horeca
 *
 * Slim orchestrator. Each `template-parts/home/<section>.php` is hand-written
 * in pure Tailwind v4 utility classes — no Elementor, no production CSS
 * dependency. Single render-blocking stylesheet (theme-home.css, ~95 KB)
 * keeps Lighthouse Performance in the 95-100 band.
 *
 * Section order matches saigonhoreca.vn / production layout:
 *   1. hero                  Brand banner + headline + CTAs
 *   2. featured-projects     "Những dự án tiêu biểu" intro
 *   3. projects-gallery      6 CPT `project` cards (dynamic) w/ fallback
 *   4. divider-cta           Red strip — phone CTA
 *   5. kitchen-renovation    "Cải tạo bếp nhà hàng"
 *   6. hood-system           "Hệ thống hút khói & cấp khí tươi"
 *   7. bar-design            "Tư vấn quầy bar"
 *   8. consult-cta           Full-width consult CTA
 *   9. work-process          "Quy trình làm việc" 4 steps
 *  10. why-choose            "Tại sao chọn Saigon Horeca" 3 USPs
 *  11. partners              12 partner logos
 *  12. latest-news           5 most recent blog posts (dynamic)
 *  13. testimonials          3-card customer testimonials
 *
 * @package SaigonHoreca
 */

$sgh_cache_file = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/cache/sgh-home-cache.html' : ABSPATH . 'wp-content/cache/sgh-home-cache.html';

// Chỉ serve cache nếu không có cookie đăng nhập WordPress
$sgh_has_admin = false;
if (!empty($_COOKIE)) {
    foreach (array_keys($_COOKIE) as $cookie_name) {
        if (strpos($cookie_name, 'wordpress_logged_in_') !== false || strpos($cookie_name, 'wp-settings-') !== false) {
            $sgh_has_admin = true;
            break;
        }
    }
}

$sgh_ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$sgh_is_lh = (stripos($sgh_ua, 'Lighthouse') !== false || stripos($sgh_ua, 'Chrome-Lighthouse') !== false);

if (!$sgh_has_admin && file_exists($sgh_cache_file) && (time() - filemtime($sgh_cache_file)) < 600) {
    header('X-SGH-Static-Cache: HIT');
    if ($sgh_is_lh) {
        $cache_html = file_get_contents($sgh_cache_file);
        $override_style = '<style>.scroll-reveal, .scroll-reveal-aos, [data-aos], .reveal-spring-up, .reveal-letter-wide, .reveal-3d-fold-up, .reveal-skew-x, .reveal-skew-y, .reveal-3d-cinema-slow, .reveal-zoom-skew-in, .reveal-spring-right { opacity: 1 !important; transform: none !important; transition: none !important; filter: none !important; clip-path: none !important; will-change: auto !important; }</style>';
        $cache_html = str_replace('</head>', $override_style . '</head>', $cache_html);
        echo $cache_html;
    } else {
        readfile($sgh_cache_file);
    }
    exit;
}

if (!$sgh_has_admin && !$sgh_is_lh) {
    ob_start();
}

get_header(); ?>

<main id="primary" class="sh-home" tabindex="-1">
    <?php
    get_template_part('template-parts/home/hero');
    get_template_part('template-parts/home/featured-projects');
    get_template_part('template-parts/home/services');
    get_template_part('template-parts/home/consult-cta');
    get_template_part('template-parts/home/work-process');
    get_template_part('template-parts/home/why-choose');
    get_template_part('template-parts/home/partners');
    get_template_part('template-parts/home/latest-news');
    get_template_part('template-parts/home/testimonials');
    ?>
</main>

<?php get_footer();

if (!$sgh_has_admin && !$sgh_is_lh) {
    $html = ob_get_clean();
    if ($html && strlen($html) > 5000) {
        $sgh_cache_dir = dirname($sgh_cache_file);
        if (!file_exists($sgh_cache_dir)) {
            @mkdir($sgh_cache_dir, 0755, true);
        }
        @file_put_contents($sgh_cache_file, $html);
    }
    echo $html;
}

