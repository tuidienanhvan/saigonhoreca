<?php
/**
 * Project Pillar — du-nam-an-an
 * Section #1: hero — Dự Nam An An (Nursing Home Kitchen).
 * Full-viewport poster + Parallax Clip-Path (GPU-smooth, no JS) + caring-precision motif.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-nan">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed bị clip-path của section giam lại) -->
  <div class="pp-hero-nan__bg" style="background-image:url('<?php echo esc_url(sgh_img('du-nam-an-an/nam-an-bep-tu-cong-nghiep-inox.webp')); ?>');" role="img" aria-label="<?php echo esc_attr__('Gian bếp Nursing Home Kitchen dự án Nam An An', 'saigonhoreca'); ?>"></div>
  <div class="pp-hero-nan__overlay" aria-hidden="true"></div>
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--center" aria-hidden="true"></div>

  <div class="pp-hero-nan__content">
    <div class="pp-hero-nan__mark" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M12 21s-7.5-4.35-7.5-9.75A4.25 4.25 0 0 1 12 8.5a4.25 4.25 0 0 1 7.5 2.75C19.5 16.65 12 21 12 21Z" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <p class="pp-hero-nan__subhead"><?php echo esc_html__('Nursing Home Kitchen', 'saigonhoreca'); ?></p>
    <h1 class="pp-hero-nan__title">
      <?php echo esc_html__('Dự Nam An An', 'saigonhoreca'); ?>
      <span class="pp-hero-nan__title-sub"><?php echo esc_html__('Khi gian bếp trở thành nền tảng của sự chăm sóc tinh tế', 'saigonhoreca'); ?></span>
    </h1>
    <div class="pp-hero-nan__divider" aria-hidden="true"></div>
  </div>

  <div class="pp-hero-nan__scroll" aria-hidden="true">
    <svg class="pp-hero-nan__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>
