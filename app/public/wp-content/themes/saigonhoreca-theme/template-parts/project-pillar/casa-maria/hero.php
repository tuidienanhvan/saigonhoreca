<?php
/**
 * Project Pillar — casa-maria
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-cm">
  <div class="pp-hero-cm__media" style="background-image: url('<?php echo sgh_img('casa-maria/casa-maria-thumbnail-project-cover.jpg'); ?>');"></div>
  <div class="pp-hero-cm__overlay" aria-hidden="true"></div>
  <div class="pp-hero-cm__content">
    <h1 class="pp-hero-cm__title"><?php echo esc_html__('Casa Maria', 'saigonhoreca'); ?></h1>
    <p class="pp-hero-cm__subhead"><?php echo esc_html__('Khi không gian Tapas & Wine cần một căn bếp "biết kể chuyện"', 'saigonhoreca'); ?></p>
  </div>
  <div class="pp-hero-cm__scroll-indicator">
    <span class="pp-hero-cm__scroll-text"><?php echo esc_html__('Khám phá dự án', 'saigonhoreca'); ?></span>
    <svg class="pp-hero-cm__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>
