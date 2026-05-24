<?php
/**
 * Template Name: Gioi Thieu
 * 
 * Company: Công Ty Cổ Phần Xây Dựng Saigon House
 * Layout: Modular Template Parts
 */

get_header();
?>

<!-- 1. Hero Section -->
<div data-aos="fade-up" data-aos-delay="40">
<?php get_template_part('template-parts/about/hero'); ?>
</div>

<!-- 2. About Us (Story) -->
<div data-aos="fade-up" data-aos-delay="80">
<?php get_template_part('template-parts/about/story'); ?>
</div>

<!-- 3. Core Values -->
<div data-aos="fade-up" data-aos-delay="120">
<?php get_template_part('template-parts/about/values'); ?>
</div>

<!-- 4. Introduction (Why Choose Us - Restored Part) -->
<div data-aos="fade-up" data-aos-delay="160">
<?php get_template_part('template-parts/about/introduction'); ?>
</div>

<!-- CTA Footer -->
<div data-aos="fade-up" data-aos-delay="200">
<?php get_template_part('template-parts/about/cta'); ?>
</div>

<?php get_footer(); ?>
