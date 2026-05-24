<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #5: specs
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-rf-specs">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M25 20 H45 M35 20 V80 M25 80 H45" stroke-linecap="round"/>
      <path d="M55 20 L67 80 L79 20" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--bottom-left" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-rf">
      
      <div class="pp-grid-12-rf__media--cols-7 rkf-specs__side">
        <div class="pp-image-container-rf rkf-specs__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-rf">BLUEPRINT</div>
          <img src="<?php echo sgh_img('2020/12/roka-bv-bo-tri-thiet-bi.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ bố trí thiết bị Roka Fella', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-rf"><?php echo esc_html__('Bản vẽ kỹ thuật chi tiết bố trí thiết bị bếp Roka Fella', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-12-rf__text--cols-5 rkf-specs__main">
        <div class="pp-glass-card-roka rkf-specs__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="rkf-specs__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Thiết kế công năng', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-rf__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Phòng bếp & Quầy Sushi Roka Fella', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>

          <div class="rkf-specs__body">
            <div class="rkf-specs__feature">
              <div class="rkf-specs__icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M20 7h-9m3 4H5m16 4h-9m6 4H8" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="rkf-specs__feature-content">
                <h4><?php echo esc_html__('Tối ưu hóa không gian 4m²', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Khu vực bếp siêu nhỏ được Saigon Horeca bố trí tỉ mỉ, khoa học. Từng thiết bị được tính toán chuẩn xác để tối ưu hóa khí động học, lưu thông gió mang lại sự mát mẻ, thoải mái cho đầu bếp.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="rkf-specs__feature">
              <div class="rkf-specs__icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                  <line x1="8" y1="21" x2="16" y2="21" stroke-linecap="round"/>
                  <line x1="12" y1="17" x2="12" y2="21" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="rkf-specs__feature-content">
                <h4><?php echo esc_html__('Quầy Sushi bảo quản tối tân', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Được trang bị hệ thống tủ lạnh, tủ đông Hoshizaki hiện đại để giữ trọn độ tươi ngon tuyệt mỹ của cá, tôm, hải sản cao cấp, sẵn sàng phục vụ các màn trình diễn Omakase điệu nghệ.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
