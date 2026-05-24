<?php
/**
 * Project Pillar — mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-mcs">
    <div class="pp-hero-mcs__media">
        <iframe src="https://www.youtube.com/embed/38Dr2BO7xzE?autoplay=1&mute=1&loop=1&playlist=38Dr2BO7xzE&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video" tabindex="-1"></iframe>
    </div>
    <div class="pp-hero-mcs__overlay" aria-hidden="true"></div>
    <div class="pp-hero-mcs__content">
        <h1 class="pp-hero-mcs__title"><?php echo esc_html__('Mùa Craft Sake', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-mcs__subhead"><?php echo esc_html__('Nhà máy làm rượu sake thủ công đầu tiên tại Việt Nam', 'saigonhoreca'); ?></p>
        <p class="pp-hero-mcs__subtitle"><?php echo esc_html__('Brewing Fresh Sake for the people', 'saigonhoreca'); ?></p>
    </div>
</section>
