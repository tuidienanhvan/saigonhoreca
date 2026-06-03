<?php
/**
 * Project Pillar â€” du-nam-an-an
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero">
    <div class="pp-hero__media" style="background-image:url('<?php echo sgh_img('2025/01/sheh-fung-5.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero__overlay" aria-hidden="true"></div>
    <div class="pp-hero__content">
        <h1 class="pp-hero__title">Dá»± Nam An An</h1>
        <p class="pp-hero__subhead"><?php echo esc_html__('Khi gian báº¿p trá»Ÿ thÃ nh ná»n táº£ng cá»§a sá»± chÄƒm sÃ³c tinh táº¿', 'saigonhoreca'); ?></p>
    </div>
</section>

