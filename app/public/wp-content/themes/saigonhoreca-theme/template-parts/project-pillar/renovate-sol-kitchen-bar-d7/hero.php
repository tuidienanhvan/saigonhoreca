<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-d7
 * Section #1: hero — full-bleed kitchen backdrop rendered with the GPU-smooth
 * "Parallax Clip-Path" technique (position:fixed __bg confined by clip-path on the
 * section). Title sits directly on the bright image (light token overlay only — no
 * dark scrim / no panel behind text; legibility via text-shadow).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-hero-solq7">
    <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed) -->
    <div class="pp-hero-solq7__media" style="background-image:url('<?php echo sgh_img('renovate-sol-kitchen-bar-quan-7/sol-kitchen-bar-q7-bep-inox-hoan-thien-chup-hut-mui.webp'); ?>');" aria-hidden="true">
        <iframe data-lazy-src="https://www.youtube.com/embed/c8aOoxqXJqI?autoplay=1&mute=1&loop=1&playlist=c8aOoxqXJqI&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video Sol Kitchen & Bar Quan 7" tabindex="-1" aria-hidden="true"></iframe>
    </div>
    <div class="pp-hero-solq7__overlay" aria-hidden="true"></div>
    <div class="pp-hero-solq7__grid" aria-hidden="true"></div>

    <div class="pp-hero-solq7__glow pp-hero-solq7__glow--lt" aria-hidden="true"></div>
    <div class="pp-hero-solq7__glow pp-hero-solq7__glow--rb" aria-hidden="true"></div>
    <div class="pp-hero-solq7__rail pp-hero-solq7__rail--left" aria-hidden="true">RENOVATION / DISTRICT 7</div>
    <div class="pp-hero-solq7__rail pp-hero-solq7__rail--right" aria-hidden="true">KITCHEN LINE / SGH</div>

    <div class="pp-hero-solq7__content">
        <div class="pp-hero-solq7__badge">
            <span class="pp-hero-solq7__badge-tag"><?php echo esc_html__('PROJECT PILLAR', 'saigonhoreca'); ?></span>
            <span class="pp-hero-solq7__badge-sep" aria-hidden="true">&bull;</span>
            <span class="pp-hero-solq7__badge-tag"><?php echo esc_html__('RENOVATION', 'saigonhoreca'); ?></span>
        </div>

        <h1 class="pp-hero-solq7__title">
            <span class="pp-hero-solq7__title-top"><?php echo esc_html__('SOL KITCHEN & BAR', 'saigonhoreca'); ?></span>
            <span class="pp-hero-solq7__title-sub"><?php echo esc_html__('Cải tạo không gian bếp & nhà hàng chuyên nghiệp tại Quận 7', 'saigonhoreca'); ?></span>
        </h1>

        <div class="pp-hero-solq7__divider" aria-hidden="true"></div>

        <div class="pp-hero-solq7__award">
            <span class="pp-hero-solq7__award-star" aria-hidden="true">&#9733;</span>
            <span class="pp-hero-solq7__award-text"><?php echo esc_html__('Featured in 2023 MICHELIN Guide Vietnam', 'saigonhoreca'); ?></span>
            <span class="pp-hero-solq7__award-star" aria-hidden="true">&#9733;</span>
        </div>
    </div>

    <div class="pp-hero-solq7__scroll" aria-hidden="true">
        <span class="pp-hero-solq7__scroll-line"></span>
    </div>
</section>
