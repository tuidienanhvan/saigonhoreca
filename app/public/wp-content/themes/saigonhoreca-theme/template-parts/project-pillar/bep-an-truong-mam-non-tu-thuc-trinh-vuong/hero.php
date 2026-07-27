<?php
/**
 * Project Pillar — bep-an-truong-mam-non-tu-thuc-trinh-vuong
 * Section #1: hero (Safe-Kitchen full-viewport, mint/pastel, friendly soft frame)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-tvg">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (background position:fixed, không JS) -->
  <div class="pp-hero-tvg__bg" style="background-image:url('<?php echo sgh_img('bep-an-truong-mam-non-tu-thuc-trinh-vuong/bep-an-truong-mam-non-tu-thuc-trinh-vuong-thumbnail-project-cover.webp'); ?>');"></div>
  <div class="pp-hero-tvg__overlay" aria-hidden="true"></div>

  <div class="pp-hero-tvg__glow pp-hero-tvg__glow--mint" aria-hidden="true"></div>
  <div class="pp-hero-tvg__glow pp-hero-tvg__glow--warm" aria-hidden="true"></div>

  <!-- Removed text content overlay as the background image already contains baked-in titles
<div class="pp-hero-tvg__content">
    <div class="pp-hero-tvg__badge" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
        <path d="M5 11a7 7 0 0 1 14 0v1H5v-1Z" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M4 12h16M9 16h6" stroke-linecap="round"/>
        <path d="M12 4V2" stroke-linecap="round"/>
      </svg>
      <?php echo esc_html__('Bếp học đường an toàn', 'saigonhoreca'); ?>
    </div>

    <div class="pp-hero-tvg__eyebrow"><?php echo esc_html__('Một bếp · 500 suất ăn · 0 phút trễ', 'saigonhoreca'); ?></div>

    <h1 class="pp-hero-tvg__title">
      <?php echo esc_html__('Trường Mầm Non Trinh Vương', 'saigonhoreca'); ?>
      <span class="pp-hero-tvg__title-sub"><?php echo esc_html__('Bếp công nghiệp một chiều — chuẩn an toàn vệ sinh thực phẩm', 'saigonhoreca'); ?></span>
    </h1>

    <p class="pp-hero-tvg__lead">
      <?php echo esc_html__('500 suất ăn, 0 phút trễ — đó không chỉ là con số thể hiện hiệu suất vận hành của bếp ăn Trường Mầm Non Tư Thục Trinh Vương, mà còn là minh chứng cho sự tính toán tỉ mỉ trong từng chi tiết thiết kế mà Saigon Horeca (SGH) đã thực hiện.', 'saigonhoreca'); ?>
    </p>

    <div class="pp-hero-tvg__stats">
      <div class="pp-hero-tvg__stat">
        <span class="pp-hero-tvg__stat-value">150<small>m²</small></span>
        <span class="pp-hero-tvg__stat-label"><?php echo esc_html__('Diện tích bếp', 'saigonhoreca'); ?></span>
      </div>
      <div class="pp-hero-tvg__stat">
        <span class="pp-hero-tvg__stat-value">500</span>
        <span class="pp-hero-tvg__stat-label"><?php echo esc_html__('Suất ăn cùng lúc', 'saigonhoreca'); ?></span>
      </div>
      <div class="pp-hero-tvg__stat">
        <span class="pp-hero-tvg__stat-value">3–7</span>
        <span class="pp-hero-tvg__stat-label"><?php echo esc_html__('Độ tuổi của bé', 'saigonhoreca'); ?></span>
      </div>
    </div>
  </div>
-->

  <div class="pp-hero-tvg__scroll" aria-hidden="true">
    <svg class="pp-hero-tvg__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="18" height="18">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>
