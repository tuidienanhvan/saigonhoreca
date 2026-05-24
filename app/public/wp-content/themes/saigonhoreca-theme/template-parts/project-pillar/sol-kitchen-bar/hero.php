<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-skb">
    <div class="pp-hero-skb__media">
        <iframe src="https://www.youtube.com/embed/c8aOoxqXJqI?autoplay=1&mute=1&loop=1&playlist=c8aOoxqXJqI&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video" tabindex="-1"></iframe>
    </div>
    <div class="pp-hero-skb__overlay" aria-hidden="true"></div>
    <div class="pp-hero-skb__content">
        <h1 class="pp-hero-skb__title"><?php echo esc_html__('Renovate SOL KITCHEN &amp; BAR quận 7', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-skb__subtitle"><?php echo esc_html__('2023 MICHELIN Guide Vietnam', 'saigonhoreca'); ?></p>
    </div>
</section>
