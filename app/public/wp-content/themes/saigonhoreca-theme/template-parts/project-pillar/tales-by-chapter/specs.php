<?php
/**
 * Project Pillar — tales-by-chapter
 * Section #5: specs — the thesis: "Khi giải pháp đứng sau triết lý".
 * Three-space feature trio drawn from the real bar / kitchen / lab content.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tbc pp-specs-tbc">
  <div class="pp-specs-tbc__grid-bg" aria-hidden="true"></div>
  <div class="pp-ambient-glow-tbc pp-ambient-glow-tbc--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <header class="pp-specs-tbc__header">
      <div class="pp-badge-tbc">
        <span class="pp-badge-tbc__num">Lời kết</span>
        <?php echo esc_html__('Triết lý kỹ thuật', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-text-tbc__title pp-specs-tbc__title"><?php echo esc_html__('Khi giải pháp đứng sau triết lý', 'saigonhoreca'); ?></h2>
      <span class="pp-text-tbc__divider pp-text-tbc__divider--center" aria-hidden="true"></span>
      <p class="pp-specs-tbc__intro"><?php echo esc_html__('Trong dự án Tales by Chapter, Saigon Horeca chọn lùi lại phía sau để làm điểm tựa kỹ thuật vững chắc. Chúng tôi tập trung vào giá trị: tạo nên một hệ thống đủ vững để một triết lý nhân văn có thể vận hành lâu dài.', 'saigonhoreca'); ?></p>
    </header>

    <div class="pp-specs-tbc__features">
      <article class="pp-specs-tbc__feature">
        <span class="pp-specs-tbc__feature-num" aria-hidden="true">01</span>
        <div class="pp-specs-tbc__feature-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
          </svg>
        </div>
        <h3><?php echo esc_html__('Quầy bar tầng 1', 'saigonhoreca'); ?></h3>
        <p><?php echo esc_html__('Đủ công năng để phục vụ trọn vẹn trải nghiệm, gọn gàng cho quy mô 10–30 khách – nơi thực khách chậm lại và cảm nhận tinh thần nhà hàng.', 'saigonhoreca'); ?></p>
      </article>

      <article class="pp-specs-tbc__feature">
        <span class="pp-specs-tbc__feature-num" aria-hidden="true">02</span>
        <div class="pp-specs-tbc__feature-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 3v6a4 4 0 0 0 8 0V3M12 13v8M8 21h8"/>
          </svg>
        </div>
        <h3><?php echo esc_html__('Gian bếp tầng 2', 'saigonhoreca'); ?></h3>
        <p><?php echo esc_html__('Xưởng sáng tạo zero waste với hệ thống hút khói, khử mùi chuẩn xác – thiết bị âm thầm hỗ trợ để tận dụng trọn vẹn từng nguyên liệu.', 'saigonhoreca'); ?></p>
      </article>

      <article class="pp-specs-tbc__feature">
        <span class="pp-specs-tbc__feature-num" aria-hidden="true">03</span>
        <div class="pp-specs-tbc__feature-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 3h6M10 3v6l-4 9a2 2 0 0 0 2 3h8a2 2 0 0 0 2-3l-4-9V3M7 14h10"/>
          </svg>
        </div>
        <h3><?php echo esc_html__('Phòng lab tầng 3', 'saigonhoreca'); ?></h3>
        <p><?php echo esc_html__('Layout linh hoạt cho thử nghiệm và phát triển cocktail mới – mỗi chi tiết hỗ trợ sáng tạo mà không áp đặt khuôn khổ.', 'saigonhoreca'); ?></p>
      </article>
    </div>

    <div class="pp-specs-tbc__quote">
      <span class="pp-specs-tbc__quote-rule" aria-hidden="true"></span>
      <blockquote>
        <?php echo esc_html__('Mỗi giải pháp đều được đặt ra từ một câu hỏi: điều này có thực sự phục vụ cho tinh thần zero waste và sự sáng tạo hay không?', 'saigonhoreca'); ?>
      </blockquote>
      <p class="pp-specs-tbc__quote-foot"><?php echo esc_html__('Và khi mọi thứ được đặt đúng chỗ, câu chuyện của Tales by Chapter có thể được kể một cách trọn vẹn – không chỉ trên bàn ăn, mà cả trong cách nhà hàng vận hành mỗi ngày.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
