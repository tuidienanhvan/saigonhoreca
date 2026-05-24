<?php
/**
 * Project Pillar — du-nam-an-an
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-nan">
    <div class="pp-hero-nan__media" style="background-image:url('<?php echo sgh_img('2025/01/sheh-fung-5.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-nan__overlay" aria-hidden="true"></div>
    <div class="pp-hero-nan__content">
        <h1 class="pp-hero-nan__title">Dự Nam An An</h1>
        <p class="pp-hero-nan__subhead"><?php echo esc_html__('Khi gian bếp trở thành nền tảng của sự chăm sóc tinh tế', 'saigonhoreca'); ?></p>
    </div>
</section>
