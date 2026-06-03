<?php
/**
 * Project Pillar — g-cup-coffee-bistro
 * Section #1: hero (Architectural Blueprint Style)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-gcb">
    <!-- Họa tiết chấm Grid Dot kỹ thuật SVG nền -->
    <div class="pp-gcb-hero-dot-grid" aria-hidden="true"></div>

    <div class="pp-hero-gcb__media" style="background-image:url('<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-quay-bar-da-trang-van-may.webp'); ?>');"></div>
    <div class="pp-hero-gcb__overlay" aria-hidden="true"></div>

    <div class="pp-ambient-glow-gcb pp-ambient-glow-gcb--center" aria-hidden="true"></div>

    <!-- Họa tiết SVG La bàn kỹ thuật / CAD xoay nhẹ tinh tế -->
    <div class="pp-gcb-hero-cad-compass" aria-hidden="true">
        <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.5">
            <circle cx="50" cy="50" r="45" stroke-dasharray="1 3"/>
            <circle cx="50" cy="50" r="35"/>
            <circle cx="50" cy="50" r="10" stroke-dasharray="2 1"/>
            <path d="M50 5v90M5 50h90M18.2 18.2l63.6 63.6M18.2 81.8l63.6-63.6"/>
            <text x="50" y="8" font-size="4" text-anchor="middle" fill="currentColor">N</text>
            <text x="50" y="96" font-size="4" text-anchor="middle" fill="currentColor">S</text>
            <text x="96" y="52" font-size="4" text-anchor="middle" fill="currentColor">E</text>
            <text x="4" y="52" font-size="4" text-anchor="middle" fill="currentColor">W</text>
        </svg>
    </div>

    <div class="pp-hero-gcb__content">
        <!-- Khung chữ sắc sảo với vạch dấu góc đo CAD -->
        <div class="pp-gcb-hero-title-wrap scroll-reveal">
            <span class="pp-gcb-cad-corner pp-gcb-cad-corner--tl"></span>
            <span class="pp-gcb-cad-corner pp-gcb-cad-corner--tr"></span>
            <span class="pp-gcb-cad-corner pp-gcb-cad-corner--bl"></span>
            <span class="pp-gcb-cad-corner pp-gcb-cad-corner--br"></span>
            
            <h1 class="pp-hero-gcb__title">
                <span class="pp-gcb-title-outline">G.CUP</span>
                <span class="pp-gcb-title-solid">COFFEE & BISTRO</span>
            </h1>
        </div>

        <p class="pp-hero-gcb__subhead scroll-reveal">
            <svg class="gcb-sparkle-inline" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L15 9L22 12L15 15L12 22L9 15L2 12L9 9L12 2Z" />
            </svg>
            <?php echo esc_html__('Bếp vận hành đồng điệu với không gian được trau chuốt đến từng chi tiết', 'saigonhoreca'); ?>
        </p>
        <p class="pp-hero-gcb__subtitle scroll-reveal"><?php echo esc_html__('Giữa lòng Metropole Thủ Thiêm – khu đô thị hiện đại và sôi động bậc nhất TP.HCM – G.Cup Coffee & Bistro chọn cho mình một hướng đi riêng: tinh tế, trau chuốt và đặt trọn tâm huyết vào từng trải nghiệm của khách hàng.', 'saigonhoreca'); ?></p>
    </div>
</section>
