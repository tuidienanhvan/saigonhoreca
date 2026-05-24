<?php
/**
 * Front Page Template
 *
 * Slim orchestrator using template parts.
 *
 * @package SaigonHouse
 */

get_header(); ?>

<main id="primary" class="sh-home">
    <?php
    // 1. Hero Carousel
    get_template_part('template-parts/home/hero-carousel');

    // 2. Service Links
    get_template_part('template-parts/home/service-links');

    // 3. Villa Designs
    get_template_part('template-parts/home/villa-designs');

    // 4. Townhouse Designs
    get_template_part('template-parts/home/townhouse-designs');

    // 5. Work Process
    get_template_part('template-parts/home/work-process');

    // 6. Construction Diary (YouTube Videos)
    get_template_part('template-parts/home/testimonials');

    // 7. Latest News
    get_template_part('template-parts/home/latest-news');

    // 8. Featured Projects (Marquee) — cuối trang
    get_template_part('template-parts/home/featured-projects');
    ?>
</main>

<?php
// Customer Reviews — tạm ẩn
// get_template_part('template-parts/home/customer-reviews');

get_footer();
