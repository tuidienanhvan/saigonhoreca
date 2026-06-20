<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #1: hero — full-viewport cinematic intro.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-moa">
  <!-- Thẻ con gánh hiệu ứng Parallax: position:absolute + background-attachment:fixed → ảnh ghim theo viewport, mượt, không cần JS -->
  <div class="pp-hero-moa__bg" style="background-image:url('<?php echo sgh_img('moa-moa/moa-moa-khong-gian-an-trong-nha-nhin-pho-quan-1.webp'); ?>');" role="img" aria-label="<?php echo esc_attr__('Không gian Moa Moa Pasta Club nhìn ra trung tâm Quận 1', 'saigonhoreca'); ?>"></div>
  <div class="pp-hero-moa__overlay" aria-hidden="true"></div>

  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--center" aria-hidden="true"></div>

  <div class="pp-hero-moa__content">
    <div class="pp-hero-moa__emblem" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
        <path d="M5 3c0 4 3 6 7 6s7-2 7 6c0 4-3 6-7 6s-7-2-7-6" stroke-linecap="round"/>
        <path d="M12 9v12" stroke-linecap="round"/>
      </svg>
    </div>

    <div class="pp-hero-moa__subtitle">Pasta Club &bull; Italiano Autentico &bull; Quận 1</div>
    <h1 class="pp-hero-moa__title">
      Moa Moa
      <span class="pp-hero-moa__title-sub"><?php echo esc_html__('Tinh thần ẩm thực Ý trong một gian bếp thủ công', 'saigonhoreca'); ?></span>
    </h1>
    <div class="pp-hero-moa__divider" aria-hidden="true"></div>
  </div>

  <a href="#moa-intro" class="pp-hero-moa__scroll" aria-label="<?php echo esc_attr__('Cuộn xuống', 'saigonhoreca'); ?>">
    <svg class="pp-hero-moa__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" aria-hidden="true">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </a>
</section>
