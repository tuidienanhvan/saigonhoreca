<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #7: related (execution summary)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-rf-related">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M25 20 L37 80 L49 20" stroke-linecap="round"/>
      <path d="M59 20 H79 M69 20 V80 M59 80 H79" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--bottom-left" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-rf">
      
      <div class="pp-grid-12-rf__text--cols-5 rkf-related__main">
        <div class="pp-glass-card-roka rkf-related__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="rkf-related__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Bàn giao hoàn thiện', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-rf__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Cam kết dịch vụ đẳng cấp quốc tế', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>

          <div class="rkf-related__body" style="font-size: 0.98rem; line-height: 1.8; color: var(--bc2);">
            <p><?php echo esc_html__('Roka Fella không chỉ thu hút thực khách bởi ẩm thực tuyệt đỉnh và âm nhạc vinyl tinh tế, mà còn bởi không gian sang trọng được chăm chút tỉ mỉ từ những chi tiết nhỏ nhất.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Sự thành công của dự án đã củng cố niềm tin sâu sắc của HypeAsia dành cho Saigon Horeca, mở đường cho những sự hợp tác tầm cỡ tiếp theo, tiêu biểu là dự án Godmother 2 sau này.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-rf__media--cols-7 rkf-related__side">
        <div class="pp-image-container-rf rkf-related__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-rf">EXECUTION</div>
          <img src="<?php echo sgh_img('2024/01/Roka-Fella.webp'); ?>" alt="<?php echo esc_attr__('Roka Fella Final Execution', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-rf"><?php echo esc_html__('Toàn cảnh không gian quầy bar vinyl sang trọng tầng thượng Roka Fella', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
