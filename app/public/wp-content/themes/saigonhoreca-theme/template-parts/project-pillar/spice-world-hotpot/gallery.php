<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #6: gallery (storage & service)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-swh-gallery">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M35 20 L50 80 L65 20" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--top-right" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="swh-gallery__layout">
      
      <div class="swh-gallery__main">
        <div class="pp-glass-card-swh swh-gallery__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="swh-gallery__header">
            <div class="pp-badge-swh">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Lưu trữ & Phục vụ', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-swh__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Hệ thống bảo quản thực phẩm tươi sống', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-swh__divider" aria-hidden="true"></div>
          </header>

          <div class="swh-gallery__body">
            <p class="swh-gallery__lead"><?php echo esc_html__('Dung tích lưu trữ gần 3000 lít với thiết bị nhập khẩu từ Malaysia & Nhật Bản.', 'saigonhoreca'); ?></p>
            
            <div class="swh-gallery__grid">
              <div class="swh-gallery__item">
                <strong><?php echo esc_html__('Bảo quản Berjayja', 'saigonhoreca'); ?></strong>
                <p><?php echo esc_html__('3 tủ lạnh đứng cánh kính giúp quan sát nguyên liệu dễ dàng.', 'saigonhoreca'); ?></p>
              </div>
              <div class="swh-gallery__item">
                <strong><?php echo esc_html__('Bảo quản Hoshizaki', 'saigonhoreca'); ?></strong>
                <p><?php echo esc_html__('2 tủ lạnh nằm tối ưu cho việc chuẩn bị thịt bò Barbie & hải sản.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <p class="swh-gallery__note"><?php echo esc_html__('Hệ thống kệ thép không gỉ nhiều tầng và 6 tủ lạnh trượt đảm bảo dịch vụ hiệu quả, đáp ứng khối lượng đồ dùng lớn đặc thù của ẩm thực lẩu.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <div class="swh-gallery__media">
        <div class="pp-image-container-swh swh-gallery__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-swh">STORAGE</div>
          <img src="<?php echo sgh_img('2024/02/swh-bv-khu-tru-dong-cat-thit-e1632327853637.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ khu trữ đông', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-swh"><?php echo esc_html__('Khu vực cắt thịt & Trữ đông tối ưu công năng', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
