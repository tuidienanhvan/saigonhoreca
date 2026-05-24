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
