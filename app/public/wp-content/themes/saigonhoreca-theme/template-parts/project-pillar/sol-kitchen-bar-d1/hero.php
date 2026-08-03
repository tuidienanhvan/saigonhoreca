<?php
/**
 * Project Pillar — sol-kitchen-bar-d1
 * Section #1: hero — bright cinematic image backdrop with the GPU-smooth
 * "Parallax Clip-Path" technique (no JS): the section clips a position:fixed bg.
 * Text sits directly on the image (NO dark overlay, NO panel behind title) per
 * owner feedback — only a LIGHT token overlay + text-shadow for legibility.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-hero-solshr">
    <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed bị section clip) -->
    <div class="pp-hero-solshr__media" style="background-image:url('<?php echo esc_url(sgh_img('sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-saigon-horeca-khong-gian-quay-bar-bistro.webp')); ?>');" aria-hidden="true">
        <iframe data-lazy-src="https://www.youtube.com/embed/145Vplgi3TA?autoplay=1&mute=1&loop=1&playlist=145Vplgi3TA&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video Sol Kitchen & Bar" tabindex="-1" aria-hidden="true"></iframe>
    </div>
    <div class="pp-hero-solshr__overlay" aria-hidden="true"></div>

    <div class="pp-hero-solshr__glow pp-hero-solshr__glow--lt" aria-hidden="true"></div>
    <div class="pp-hero-solshr__glow pp-hero-solshr__glow--rb" aria-hidden="true"></div>

    <div class="pp-hero-solshr__rail pp-hero-solshr__rail--left" aria-hidden="true">LATIN AMERICAN KITCHEN &amp; BAR</div>
    <div class="pp-hero-solshr__rail pp-hero-solshr__rail--right" aria-hidden="true">HACIENDA / SAIGON</div>

    <div class="pp-hero-solshr__content">
        <div class="pp-hero-solshr__subhead"><?php echo esc_html__('2023 MICHELIN Guide Vietnam', 'saigonhoreca'); ?></div>
        <h1 class="pp-hero-solshr__title">SOL KITCHEN &amp; BAR</h1>
        <div class="pp-hero-solshr__divider" aria-hidden="true"></div>
        <p class="pp-hero-solshr__subtitle"><?php echo esc_html__('Sol Kitchen & Bar không chỉ là một nhà hàng mà còn là nơi trải nghiệm ẩm thực một cách tinh tế được gói gọn trong bầu không khí đầy màu sắc và ngập tràn cảm hứng Mỹ Latin.', 'saigonhoreca'); ?></p>
    </div>
</section>
