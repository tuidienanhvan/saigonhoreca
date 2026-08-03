<?php
/**
 * Project Pillar — kdl-rung-thong-nui-voi
 * Section #1: hero (Pine-forest eco-tourism — full viewport, airy & green)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-kdl">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (không JS, GPU-smooth) -->
  <div class="pp-hero-kdl__bg" style="background-image:url('<?php echo sgh_img('du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca-thumbnail-project-cover.webp'); ?>');"></div>
  <div class="pp-hero-kdl__overlay" aria-hidden="true"></div>
  <div class="pp-hero-kdl__mist" aria-hidden="true"></div>

  <!-- Removed text content overlay as the background image already contains baked-in titles
<div class="pp-hero-kdl__content">
    <div class="pp-hero-kdl__emblem" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 3 L18 12 H14 L19 20 H5 L10 12 H6 Z"/>
        <line x1="12" y1="20" x2="12" y2="22"/>
      </svg>
    </div>

    <div class="pp-hero-kdl__eyebrow"><?php echo esc_html__('Tây Nguyên', 'saigonhoreca'); ?> &bull; <?php echo esc_html__('Rừng thông', 'saigonhoreca'); ?> &bull; <?php echo esc_html__('Ẩm thực bản địa', 'saigonhoreca'); ?></div>

    <h1 class="pp-hero-kdl__title">
      <?php echo esc_html__('KDL Rừng Thông Núi Voi', 'saigonhoreca'); ?>
      <span class="pp-hero-kdl__title-sub"><?php echo esc_html__('Thiên nhiên hùng vĩ — Trải nghiệm tuyệt vời', 'saigonhoreca'); ?></span>
    </h1>

    <div class="pp-hero-kdl__divider" aria-hidden="true"></div>

    <p class="pp-hero-kdl__lead"><?php echo esc_html__('Saigon Horeca và hành trình kiến tạo không gian bếp cho ẩm thực Tây Nguyên tinh tế giữa lòng rừng thông nguyên bản.', 'saigonhoreca'); ?></p>
  </div>
-->

  <div class="pp-hero-kdl__scroll" aria-hidden="true">
    <span class="pp-hero-kdl__scroll-dot"></span>
  </div>
</section>
