<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-rkf">
  <div class="pp-hero-rkf__media" style="background-image:url('<?php echo sgh_img('roka-fella/roka-fella-thumbnail-project-cover.jpg'); ?>');"></div>
  <div class="pp-hero-rkf__overlay" aria-hidden="true"></div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--center" aria-hidden="true"></div>

  <div class="pp-hero-rkf__content">
    <div class="pp-hero-rkf__star" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
      </svg>
    </div>

    <div class="pp-hero-rkf__subtitle">Omakase &bull; Sushi &bull; Vinyl Bar</div>
    <h1 class="pp-hero-rkf__title">
      Roka Fella
      <span class="pp-hero-rkf__title-sub"><?php echo esc_html__('Tinh hoa ẩm thực Nhật Bản', 'saigonhoreca'); ?></span>
    </h1>
    <div class="pp-hero-rkf__divider" aria-hidden="true"></div>
  </div>
</section>
