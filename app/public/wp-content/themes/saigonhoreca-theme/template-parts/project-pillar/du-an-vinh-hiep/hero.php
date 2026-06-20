<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #1: hero — Vĩnh Hiệp Coffee Lab (Industrial Stainless Atelier)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-vhp">
  <!-- Thẻ con gánh hiệu ứng Parallax (position:absolute + background-attachment:fixed, mobile fallback: scroll) -->
  <div class="pp-hero-vhp__bg" style="background-image:url('<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-bep-xao-tu-cong-nghiep-inox.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-vhp__overlay" aria-hidden="true"></div>

  <!-- Lưới kỹ thuật blueprint chạy ngầm -->
  <div class="pp-hero-vhp__grid" aria-hidden="true"></div>
  <div class="pp-ambient-glow-vhp pp-ambient-glow-vhp--corner" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-hero-vhp__content">
      <div class="pp-hero-vhp__eyebrow">
        <span class="pp-hero-vhp__eyebrow-dot" aria-hidden="true"></span>
        <?php echo esc_html__('Coffee Lab · Nhà máy cà phê xuất khẩu', 'saigonhoreca'); ?>
      </div>

      <h1 class="pp-hero-vhp__title">
        <?php echo esc_html__('Dự án Vĩnh Hiệp', 'saigonhoreca'); ?>
        <span class="pp-hero-vhp__title-sub"><?php echo esc_html__('Coffee Lab', 'saigonhoreca'); ?></span>
      </h1>

      <div class="pp-hero-vhp__divider" aria-hidden="true"></div>

      <p class="pp-hero-vhp__subhead">
        <?php echo esc_html__('Khi không gian bếp và cà phê cùng kể một câu chuyện về chuẩn mực xuất khẩu', 'saigonhoreca'); ?>
      </p>

      <div class="pp-hero-vhp__meta" aria-hidden="true">
        <span class="pp-hero-vhp__meta-item">Gia Lai · Việt Nam</span>
        <span class="pp-hero-vhp__meta-sep">/</span>
        <span class="pp-hero-vhp__meta-item">Xuất khẩu cà phê nhân số 1 · 2023–2024</span>
        <span class="pp-hero-vhp__meta-sep">/</span>
        <span class="pp-hero-vhp__meta-item">Saigon Horeca</span>
      </div>
    </div>
  </div>

  <div class="pp-hero-vhp__scroll" aria-hidden="true">
    <svg class="pp-hero-vhp__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>
