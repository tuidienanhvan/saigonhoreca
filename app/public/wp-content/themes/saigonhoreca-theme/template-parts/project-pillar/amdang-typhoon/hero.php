<?php
/**
 * Project Pillar — amdang-typhoon
 * Section #1: hero (Cinematic Centered Layout)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-hero-adt">
    <!-- Nền ảnh từ website production -->
    <div class="pp-hero-adt__media" style="background-image:url('<?php echo sgh_img('amdang-typhoon/amdang-typhoon-phong-vip-ban-tron-gieng-troi.jpg'); ?>');"></div>
    <div class="pp-hero-adt__overlay" aria-hidden="true"></div>
    
    <div class="pp-hero-adt__content">
        <!-- Project Metadata Badge -->
        <div class="pp-hero-adt__meta-badge">
            <span class="pp-hero-adt__meta-line"></span>
            <span class="pp-hero-adt__meta-text">PROJECT PILLAR • FOOD & BEVERAGE SYSTEM</span>
            <span class="pp-hero-adt__meta-line"></span>
        </div>

        <!-- Tiêu đề chính lớn sang trọng -->
        <h1 class="pp-hero-adt__title">
            <span class="pp-hero-adt__title-top">AMDANG TYPHOON</span>
            <span class="pp-hero-adt__title-sub">Ẩm Thực Thái - Hoa Đạt Chuẩn Michelin Guide</span>
        </h1>

        <!-- Michelin Award Badge -->
        <div class="pp-hero-adt__award">
            <span class="pp-hero-adt__award-icon">★</span>
            <span class="pp-hero-adt__award-text">Featured in 2021 MICHELIN Guide Vietnam</span>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="pp-hero-adt__scroll">
        <span class="pp-hero-adt__scroll-text">SCROLL TO EXPLORE</span>
        <span class="pp-hero-adt__scroll-bar">
            <span class="pp-hero-adt__scroll-dot"></span>
        </span>
    </div>
</section>

