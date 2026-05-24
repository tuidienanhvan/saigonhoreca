<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-swh">
    <!-- Background Image Zooming -->
    <div class="pp-hero-swh__media" style="background-image:url('<?php echo sgh_img('2024/01/SGH-Spice-World-1.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    
    <!-- Dark Cinematic Overlay -->
    <div class="pp-hero-swh__overlay" aria-hidden="true"></div>
    
    <!-- Ambient Glow (Red/Orange Sichuan Fire) -->
    <div class="pp-ambient-glow-swh pp-ambient-glow-swh--center" aria-hidden="true"></div>
    
    <!-- Content Card -->
    <div class="pp-hero-swh__content">
        <!-- L-Brackets Corners Decoration -->
        <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
        <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
        <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
        <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
        
        <!-- Gold Star Icon -->
        <div class="pp-hero-swh__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
        </div>
        
        <h1 class="pp-hero-swh__title">SPICE WORLD HOT POT</h1>
        <p class="pp-hero-swh__subhead"><?php echo esc_html__('Thương Hiệu Lẩu Đỉnh Cao', 'saigonhoreca'); ?></p>
    </div>
</section>
