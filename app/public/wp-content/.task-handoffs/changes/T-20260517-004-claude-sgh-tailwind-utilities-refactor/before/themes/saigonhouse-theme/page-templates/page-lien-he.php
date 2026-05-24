<?php
/**
 * Template Name: Liên Hệ Premium
 * 
 * @package SaigonHouse
 */

get_header();
?>

<!-- 1. Hero Section -->
<div data-aos="fade-up" data-aos-delay="40">
<?php get_template_part('template-parts/contact/hero'); ?>
</div>

<!-- 2. Contact Form Section -->
<div data-aos="fade-up" data-aos-delay="100">
<?php get_template_part('template-parts/contact/form'); ?>
</div>

<!-- 3. Map Section -->
<div data-aos="fade-up" data-aos-delay="160">
<?php get_template_part('template-parts/contact/map'); ?>
</div>

<?php get_footer();
