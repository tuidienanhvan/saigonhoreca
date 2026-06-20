<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #1: hero — bright project image with GPU-smooth Parallax Clip-Path
 * (position:fixed bg confined by clip-path on the section). Title sits directly
 * on the image (NO dark overlay/panel) per owner feedback — only a LIGHT token
 * gradient overlay + text-shadow for legibility.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-hero-sol">
    <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed) -->
    <div class="pp-hero-sol__media" aria-hidden="true" style="background-image:url('<?php echo esc_url(sgh_img('sol-kitchen-bar/sol-kitchen-bar-chop-hut-mui-inox-khac-logo.webp')); ?>');"></div>
    <div class="pp-hero-sol__overlay" aria-hidden="true"></div>

    <div class="pp-hero-sol__glow pp-hero-sol__glow--lt" aria-hidden="true"></div>
    <div class="pp-hero-sol__glow pp-hero-sol__glow--rb" aria-hidden="true"></div>

    <div class="pp-hero-sol__rail pp-hero-sol__rail--left" aria-hidden="true">KITCHEN RENOVATION / DISTRICT 7</div>
    <div class="pp-hero-sol__rail pp-hero-sol__rail--right" aria-hidden="true">SGH M&amp;E SYSTEM</div>

    <div class="pp-hero-sol__content">
        <div class="pp-hero-sol__subhead"><?php echo esc_html__('2023 MICHELIN Guide Vietnam', 'saigonhoreca'); ?></div>
        <h1 class="pp-hero-sol__title"><?php echo esc_html__('Renovate SOL KITCHEN & BAR quận 7', 'saigonhoreca'); ?></h1>
        <div class="pp-hero-sol__divider" aria-hidden="true"></div>
        <div class="pp-hero-sol__meta" aria-label="<?php echo esc_attr__('Thông tin nổi bật dự án SOL Kitchen & Bar quận 7', 'saigonhoreca'); ?>">
            <span><?php echo esc_html__('Bếp 70m²', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Chụp hút 8m', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Lò BBQ riêng', 'saigonhoreca'); ?></span>
        </div>
    </div>
</section>
