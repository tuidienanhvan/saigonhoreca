<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
$video_id = 'nC3dS_Yvch8';
$origin = rawurlencode(home_url('/'));
$embed_url = sprintf(
    'https://www.youtube-nocookie.com/embed/%1$s?autoplay=1&mute=1&loop=1&playlist=%1$s&controls=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi&autohide=1&enablejsapi=0&origin=%2$s',
    $video_id,
    $origin
);
?>
<section class="pp-hero-lb scroll-reveal">
    <div class="pp-hero-lb__media">
        <iframe src="<?php echo esc_url($embed_url); ?>" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video" tabindex="-1" aria-hidden="true"></iframe>
    </div>
    <div class="pp-hero-lb__overlay" aria-hidden="true"></div>
    <div class="pp-hero-lb__ambient-glow" aria-hidden="true"></div>
    
    <!-- SVG Bounding Box/Crosshair góc trang trí -->
    <div class="pp-hero-lb__crosshairs" aria-hidden="true">
        <svg class="pp-hero-lb__crosshair pp-hero-lb__crosshair--tl" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5"><line x1="0" y1="0" x2="24" y2="0"/><line x1="0" y1="0" x2="0" y2="24"/><circle cx="0" cy="0" r="4"/></svg>
        <svg class="pp-hero-lb__crosshair pp-hero-lb__crosshair--tr" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5"><line x1="0" y1="0" x2="24" y2="0"/><line x1="24" y1="0" x2="24" y2="24"/><circle cx="24" cy="0" r="4"/></svg>
        <svg class="pp-hero-lb__crosshair pp-hero-lb__crosshair--bl" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5"><line x1="0" y1="24" x2="24" y2="24"/><line x1="0" y1="0" x2="0" y2="24"/><circle cx="0" cy="24" r="4"/></svg>
        <svg class="pp-hero-lb__crosshair pp-hero-lb__crosshair--br" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5"><line x1="0" y1="24" x2="24" y2="24"/><line x1="24" y1="0" x2="24" y2="24"/><circle cx="24" cy="24" r="4"/></svg>
    </div>
    
    <!-- SVG Họa tiết lưới kỹ thuật AutoCAD siêu mảnh hai bên cánh -->
    <svg class="pp-hero-lb__svg-grid pp-hero-lb__svg-grid--left" viewBox="0 0 100 800" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <line x1="50" y1="0" x2="50" y2="800" stroke="rgba(245, 166, 35, 0.04)" stroke-width="0.5"/>
        <line x1="10" y1="0" x2="10" y2="800" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.5" stroke-dasharray="2 4"/>
        <line x1="90" y1="0" x2="90" y2="800" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.5" stroke-dasharray="2 4"/>
    </svg>
    <svg class="pp-hero-lb__svg-grid pp-hero-lb__svg-grid--right" viewBox="0 0 100 800" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <line x1="50" y1="0" x2="50" y2="800" stroke="rgba(245, 166, 35, 0.04)" stroke-width="0.5"/>
        <line x1="10" y1="0" x2="10" y2="800" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.5" stroke-dasharray="2 4"/>
        <line x1="90" y1="0" x2="90" y2="800" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.5" stroke-dasharray="2 4"/>
    </svg>

    <div class="pp-container-shared pp-hero-lb__container">
        <div class="pp-hero-lb__content">
            <span class="pp-hero-lb__star-decor" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                    <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
                </svg>
            </span>
            <span class="pp-hero-lb__subhead"><?php echo esc_html__('Vibrant vibe · Tasty food · Dynamic wine', 'saigonhoreca'); ?></span>
            <h1 class="pp-hero-lb__title"><?php echo esc_html__('Little Bear Thảo Điền', 'saigonhoreca'); ?></h1>
            <div class="pp-hero-lb__divider" aria-hidden="true"></div>
            <p class="pp-hero-lb__subtitle"><?php echo esc_html__('Một thiên đường F&B nhỏ nhưng có nhịp vận hành rõ nét, nơi những viên gạch nung ấm áp, hương vị tinh khiết và tiếng cười rộn rã hòa quyện thành một bản giao hương ẩm thực tinh tế giữa lòng Thảo Điền.', 'saigonhoreca'); ?></p>
        </div>
    </div>
    <!-- Scroll Down Indicator -->
    <div class="pp-hero-lb__scroll-down" aria-hidden="true">
        <span class="pp-hero-lb__scroll-mouse">
            <span class="pp-hero-lb__scroll-wheel"></span>
        </span>
        <span class="pp-hero-lb__scroll-text"><?php echo esc_html__('Cuộn xuống', 'saigonhoreca'); ?></span>
    </div>
</section>
