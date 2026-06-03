<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #3: concept
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-rf pp-rf-concept">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M20 20 H50 M35 20 V80 M20 80 H50 M50 20 H80 M65 20 V80 M50 80 H80" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-concept-watermark-rf" aria-hidden="true">02</div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-rf">
      
      <div class="pp-grid-12-rf__media--cols-7 rkf-concept__side">
        <div class="pp-image-container-shared rkf-concept__image-container">
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('roka-fella/roka-fella-goc-quay-bar-nhat-ban.jpg'); ?>" alt="<?php echo esc_attr__('Roka Fella Omakase Counter', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Đầu bếp sushi đang trực tiếp trình diễn và phục vụ thực khách tại quầy gỗ sồi Omakase', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-12-rf__text--cols-5 rkf-concept__main">
        <div class="pp-glass-card-roka rkf-concept__glass-card">
          
          <header class="rkf-concept__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Phong cách ẩm thực', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-rf__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Trải nghiệm Omakase đỉnh cao', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>

          <div class="rkf-concept__body" style="font-size: 0.98rem; line-height: 1.8; color: var(--bc2);">
            <p><?php echo esc_html__('Omakase xuất phát từ động từ "Makasu," có nghĩa là "tin tưởng." Omakase trong tiếng Nhật mang thông điệp "tôi tin tưởng", tượng trưng cho việc thực khách đặt trọn sự an tâm vào bàn tay tài ba của người đầu bếp.', 'saigonhoreca'); ?></p>
            <p style="margin-bottom: 0;"><?php echo esc_html__('Ngồi tại Roka Fella, bạn chỉ cần thư giãn, thưởng lãm từng lát cắt nghệ thuật đầy điệu nghệ và đón nhận những kiệt tác ẩm thực tươi ngon được chế tác tỉ mỉ.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
