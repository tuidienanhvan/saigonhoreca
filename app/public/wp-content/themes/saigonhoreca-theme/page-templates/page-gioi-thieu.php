<?php
/**
 * Template Name: Giới Thiệu (Về Saigon Horeca)
 *
 * Page template for the "Về Saigon Horeca" page (slug `ve-saigon-horeca`).
 * Orchestrates template-parts/about/* — sections extracted directly from
 * the production saigonhoreca.vn /ve-saigon-horeca/ scrape.
 *
 * Apply via wp-admin → Page → Page Attributes → Template → "Giới Thiệu",
 * or programmatically by setting post_meta `_wp_page_template` to
 * `page-templates/page-gioi-thieu.php`.
 *
 * Saigonhouse-theme architectural parity: same orchestrator-of-template-parts
 * pattern, per-route Tailwind v4 bundle loaded via theme-about.css.
 *
 * @package SaigonHoreca
 */

get_header(); ?>

<main id="primary" class="sh-about" tabindex="-1">
    <?php
    // 1. Hero / brand banner
    get_template_part('template-parts/about/hero');

    // 2. Introduction — Giới thiệu Saigon Horeca
    get_template_part('template-parts/about/introduction');

    // 3. Partners — Đối tác của Saigon Horeca
    get_template_part('template-parts/about/partners');

    // 4. Values — giá trị + cách tiếp cận
    get_template_part('template-parts/about/values');

    // 5. Testimonials — Nhận xét từ khách hàng
    get_template_part('template-parts/about/testimonials');

    // 6. CTA — Final call to action
    get_template_part('template-parts/about/cta');
    ?>
</main>

<?php get_footer();
