<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #1: hero — cinematic video backdrop + staggered title entry.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-hero-sol">
    <div class="pp-hero-sol__media" aria-hidden="true">
        <iframe src="https://www.youtube.com/embed/c8aOoxqXJqI?autoplay=1&mute=1&loop=1&playlist=c8aOoxqXJqI&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="<?php echo esc_attr__('Video nền dự án SOL Kitchen &amp; Bar', 'saigonhoreca'); ?>" tabindex="-1"></iframe>
    </div>
    <div class="pp-hero-sol__overlay" aria-hidden="true"></div>
    <div class="pp-hero-sol__grid" aria-hidden="true"></div>
    <div class="pp-hero-sol__glow pp-hero-sol__glow--lt" aria-hidden="true"></div>
    <div class="pp-hero-sol__glow pp-hero-sol__glow--rb" aria-hidden="true"></div>

    <div class="pp-hero-sol__rail pp-hero-sol__rail--left" aria-hidden="true">KITCHEN RENOVATION / DISTRICT 7</div>
    <div class="pp-hero-sol__rail pp-hero-sol__rail--right" aria-hidden="true">SGH M&amp;E SYSTEM</div>

    <div class="pp-hero-sol__content">
        <div class="pp-hero-sol__subhead"><?php echo esc_html__('2023 MICHELIN Guide Vietnam', 'saigonhoreca'); ?></div>
        <h1 class="pp-hero-sol__title"><?php echo esc_html__('Renovate SOL KITCHEN &amp; BAR quận 7', 'saigonhoreca'); ?></h1>
        <div class="pp-hero-sol__divider" aria-hidden="true"></div>
        <div class="pp-hero-sol__meta" aria-label="<?php echo esc_attr__('Thông tin nổi bật dự án SOL Kitchen &amp; Bar quận 7', 'saigonhoreca'); ?>">
            <span><?php echo esc_html__('Bếp 70m²', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Chụp hút 8m', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Lò BBQ riêng', 'saigonhoreca'); ?></span>
        </div>
    </div>
</section>
