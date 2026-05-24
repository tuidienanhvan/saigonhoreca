<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #6: gallery (storage & kitchen execution)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-rf-gallery">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M35 20 L50 80 L65 20" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--top-right" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="rkf-gallery__layout">
      
      <div class="rkf-gallery__main">
        <div class="pp-glass-card-roka rkf-gallery__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="rkf-gallery__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Bảo quản & Chế biến', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-rf__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Trang thiết bị chuyên nghiệp tối tân', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>

          <div class="rkf-gallery__body">
            <p class="rkf-gallery__lead"><?php echo esc_html__('Hệ thống thiết bị nhập khẩu Nhật Bản cao cấp phối hợp hoàn mỹ cùng gia công inox Saigon Horeca.', 'saigonhoreca'); ?></p>
            
            <div class="rkf-gallery__grid">
              <div class="rkf-gallery__item">
                <strong><?php echo esc_html__('Thiết bị nhiệt nhập khẩu', 'saigonhoreca'); ?></strong>
                <p><?php echo esc_html__('Bếp Á 4 họng, máy chiên nhúng và bếp nướng Salamander cao cấp.', 'saigonhoreca'); ?></p>
              </div>
              <div class="rkf-gallery__item">
                <strong><?php echo esc_html__('Inox Saigon Horeca', 'saigonhoreca'); ?></strong>
                <p><?php echo esc_html__('Hệ thống máy hút khói, bồn rửa và kệ treo tường thiết kế chuyên biệt.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <p class="rkf-gallery__note"><?php echo esc_html__('Khu vực sơ chế trang bị máy làm đá chuyên dụng và hệ thống tủ lạnh, tủ đông Hoshizaki, cung cấp đá tinh khiết tuyệt đối để giữ lạnh và trình bày các món sashimi nghệ thuật.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <div class="rkf-gallery__media">
        <div class="pp-image-container-rf rkf-gallery__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-rf">STORAGE</div>
          <img src="<?php echo sgh_img('2024/03/Roka-fella-bep-nau.jpg'); ?>" alt="<?php echo esc_attr__('Phòng bếp nấu ăn Roka Fella', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-rf"><?php echo esc_html__('Không gian bếp nấu thực tế tối ưu hiệu năng vận hành vượt trội', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
