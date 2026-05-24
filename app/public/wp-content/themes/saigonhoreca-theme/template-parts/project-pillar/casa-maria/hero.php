<?php
/**
 * Project Pillar — casa-maria
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-cma">
    <div class="pp-hero-cma__media" style="background-image:url('<?php echo sgh_img('2025/01/sheh-fung-5.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-cma__overlay" aria-hidden="true"></div>
    <div class="pp-hero-cma__content">
        <h1 class="pp-hero-cma__title"><?php echo esc_html__('Casa Maria', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-cma__subhead"><?php echo esc_html__('Khi không gian Tapas & Wine cần một căn bếp "biết kể chuyện"', 'saigonhoreca'); ?></p>
    </div>
</section>
