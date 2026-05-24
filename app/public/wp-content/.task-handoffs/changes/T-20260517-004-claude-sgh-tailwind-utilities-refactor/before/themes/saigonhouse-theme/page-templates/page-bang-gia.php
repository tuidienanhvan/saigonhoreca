<?php
/**
 * Template Name: Bảng Giá
 * 
 * The template for displaying the Pricing page (Bảng Giá).
 * 
 * @package SaigonHouse
 */

get_header(); ?>

<main id="primary" class="site-main" data-aos="fade-up">

    <?php
    // 1. Hero Section
    get_template_part('template-parts/pricing/hero');

    // 2. Pricing Tables Content
    get_template_part('template-parts/pricing/content');

    // 3. Call to Action
    get_template_part('template-parts/pricing/cta');
    ?>

</main><!-- #primary -->

<?php
get_footer();
