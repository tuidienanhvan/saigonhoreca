<?php
/**
 * Project Pillar — vien-duong-lao-nam-an
 * Section #4 (route) — partnership: Nam An & Saigon Horeca.
 * Glassmorphic statement over a Parallax Clip-Path kitchen backdrop (GPU-smooth, no JS).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-nan">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed bị clip-path của section giam lại) -->
  <div class="pp-section-bg-nan__bg" style="background-image:url('<?php echo esc_url(sgh_img('vien-duong-lao-nam-an/nam-an-toan-canh-san-vuon.webp')); ?>');" aria-hidden="true"></div>
  <div class="pp-section-bg-nan__overlay" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-section-bg-nan__content">
      <span class="pp-badge-nan">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
          <path d="M8 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 0a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM2 21a6 6 0 0 1 12 0m-2 0a6 6 0 0 1 10 0" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <?php echo esc_html__('Hợp tác', 'saigonhoreca'); ?>
      </span>

      <h2 class="pp-section-bg-nan__title"><?php echo esc_html__('Nam An & Saigon Horeca – Sự gặp nhau của hai chuẩn mực', 'saigonhoreca'); ?></h2>
      <span class="pp-text-nan__divider pp-text-nan__divider--center" aria-hidden="true"></span>

      <div class="pp-section-bg-nan__body">
        <p><?php echo esc_html__('Dự án Nam An là minh chứng rõ nét cho cách Saigon Horeca đồng hành cùng chủ đầu tư từ triết lý đến giải pháp. Khi Nam An đặt ra yêu cầu cao về chất lượng bữa ăn và sự an toàn cho người cao tuổi, Saigon Horeca đáp lại bằng một hệ thống bếp được thiết kế chỉn chu, vận hành bền bỉ và hướng đến giá trị lâu dài.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Nam An – Nursing Home Kitchen không chỉ là một dự án bếp công nghiệp, mà là nơi kỹ thuật, vận hành và sự thấu cảm cùng gặp nhau – đúng với tinh thần mà Saigon Horeca luôn theo đuổi trong mỗi công trình.', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-section-bg-nan__stats">
        <div class="pp-section-bg-nan__stat">
          <strong><?php echo esc_html__('Kỹ thuật', 'saigonhoreca'); ?></strong>
          <span><?php echo esc_html__('Thiết kế chỉn chu, vận hành bền bỉ', 'saigonhoreca'); ?></span>
        </div>
        <div class="pp-section-bg-nan__stat">
          <strong><?php echo esc_html__('Thấu cảm', 'saigonhoreca'); ?></strong>
          <span><?php echo esc_html__('Hướng đến giá trị lâu dài', 'saigonhoreca'); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>
