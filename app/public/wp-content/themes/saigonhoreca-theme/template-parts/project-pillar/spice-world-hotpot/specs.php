<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #5: specs
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-swh-specs">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M25 20 H45 M35 20 V80 M25 80 H45" stroke-linecap="round"/>
      <path d="M55 20 L67 80 L79 20" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--bottom-left" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-swh">
      
      <div class="pp-grid-12-swh__media--cols-7 swh-specs__side">
        <div class="pp-image-container-swh swh-specs__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-swh">BLUEPRINT</div>
          <img src="<?php echo sgh_img('2024/02/swh-ban-ve-tong-the-khu-bep.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ kỹ thuật tổng thể khu bếp', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-swh"><?php echo esc_html__('Bản vẽ kỹ thuật tổng thể khu bếp 120m²', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-12-swh__text--cols-5 swh-specs__main">
        <div class="pp-glass-card-swh swh-specs__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="swh-specs__header">
            <div class="pp-badge-swh">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Thiết kế công năng', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-swh__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Ý tưởng thiết kế bếp công nghiệp', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-swh__divider" aria-hidden="true"></div>
          </header>

          <div class="swh-specs__body">
            <div class="swh-specs__feature">
              <div class="swh-specs__icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M20 7h-9m3 4H5m16 4h-9m6 4H8" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="swh-specs__feature-content">
                <h4><?php echo esc_html__('Khu vực sơ chế & Vệ sinh', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Được thiết kế với hệ thống bồn rửa kép, bàn rửa và kệ tủ thép không gỉ 100% từ Saigon Horeca. Luồng vận hành được tính toán tối ưu, đảm bảo an toàn vệ sinh thực phẩm tuyệt đối.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="swh-specs__feature">
              <div class="swh-specs__icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                  <line x1="8" y1="21" x2="16" y2="21" stroke-linecap="round"/>
                  <line x1="12" y1="17" x2="12" y2="21" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="swh-specs__feature-content">
                <h4><?php echo esc_html__('Giải pháp thông gió tối ưu', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Do đặc thù lẩu phục vụ tại bàn, bếp được tối ưu với hệ thống hút khói đơn nhưng hiệu quả, đảm bảo thông thoáng mà vẫn tiết kiệm đáng kể chi phí đầu tư ban đầu.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
