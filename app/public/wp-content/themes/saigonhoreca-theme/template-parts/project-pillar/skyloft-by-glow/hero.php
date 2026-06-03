<?php
/**
 * Project Pillar — skyloft-by-glow
 * Section #1: hero — Neon rooftop bar with Ken-Burns skyline.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-sky">
    <div class="pp-hero-sky__media" style="background-image:url('<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-quay-bar-rooftop-dem.webp'); ?>');" aria-hidden="true" role="img" aria-label="<?php echo esc_attr__('Không gian quầy bar rooftop Skyloft by Glow rực rỡ sắc màu về đêm', 'saigonhoreca'); ?>"></div>
    <div class="pp-hero-sky__overlay" aria-hidden="true"></div>
    <div class="pp-hero-sky__grid" aria-hidden="true"></div>

    <div class="pp-hero-sky__glow pp-hero-sky__glow--lt" aria-hidden="true"></div>
    <div class="pp-hero-sky__glow pp-hero-sky__glow--rb" aria-hidden="true"></div>
    <div class="pp-hero-sky__rail pp-hero-sky__rail--left" aria-hidden="true"><?php echo esc_html__('ROOFTOP BAR / QUẬN 1', 'saigonhoreca'); ?></div>
    <div class="pp-hero-sky__rail pp-hero-sky__rail--right" aria-hidden="true"><?php echo esc_html__('HỆ THỐNG QUẦY BAR / SGH', 'saigonhoreca'); ?></div>

    <div class="pp-hero-sky__content">
        <span class="pp-hero-sky__subhead"><?php echo esc_html__('Rooftop • Quầy Bar • Skyline', 'saigonhoreca'); ?></span>
        <h1 class="pp-hero-sky__title"><?php echo esc_html__('Skyloft by Glow', 'saigonhoreca'); ?></h1>
        <div class="pp-hero-sky__divider" aria-hidden="true"></div>
        <div class="pp-hero-sky__meta" aria-label="<?php echo esc_attr__('Thông tin nổi bật dự án Skyloft by Glow', 'saigonhoreca'); ?>">
            <span><?php echo esc_html__('President Place', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Tầm nhìn 360°', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Quầy bar thiết kế riêng', 'saigonhoreca'); ?></span>
        </div>
    </div>
</section>

