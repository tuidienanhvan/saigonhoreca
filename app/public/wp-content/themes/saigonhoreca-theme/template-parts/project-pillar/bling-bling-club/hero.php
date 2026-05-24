<?php
/**
 * Project Pillar — bling-bling-club
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-bbc">
    <div class="pp-hero-bbc__media">
        <iframe src="https://www.youtube.com/embed/JrvIMIpnddI?autoplay=1&mute=1&loop=1&playlist=JrvIMIpnddI&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video" tabindex="-1"></iframe>
    </div>
    <div class="pp-hero-bbc__overlay" aria-hidden="true"></div>
    <div class="pp-hero-bbc__content">
        <h1 class="pp-hero-bbc__title"><?php echo esc_html__('BLING BLING CLUB', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-bbc__subhead"><?php echo esc_html__('"Refresh your senses, Remix your night"', 'saigonhoreca'); ?></p>
        <p class="pp-hero-bbc__subtitle"><?php echo esc_html__('Bling Bling: Sang trọng – Hiện đại - Năng động', 'saigonhoreca'); ?></p>
    </div>
</section>
