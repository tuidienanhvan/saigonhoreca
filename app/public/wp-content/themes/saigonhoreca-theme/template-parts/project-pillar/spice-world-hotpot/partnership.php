<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #4: partnership
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-swh-partnership">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M20 20 H80 M35 20 V80 M20 80 H46 M50 20 V80 M38 80 H62 M65 20 V80 M50 80 H80" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--top-right" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-swh">
      
      <div class="pp-grid-12-swh__media--order-1 swh-partnership__side">
        <div class="pp-image-container-swh swh-partnership__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-swh">KITCHEN SYSTEM</div>
          <img src="<?php echo sgh_img('2024/02/Spice-World-Hot-Pot-02.jpg'); ?>" alt="<?php echo esc_attr__('Spice World Kitchen Design', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-swh"><?php echo esc_html__('Bố trí khu bếp công nghiệp thép không gỉ 100% bền bỉ', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-12-swh__text--order-2 swh-partnership__main">
        <div class="pp-glass-card-swh swh-partnership__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="swh-partnership__header">
            <div class="pp-badge-swh">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Hợp tác chiến lược', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-swh__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Saigon Horeca tư vấn & thi công bếp', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-swh__divider" aria-hidden="true"></div>
          </header>

          <div class="swh-partnership__body" style="font-size: 0.98rem; line-height: 1.75; color: var(--bc2);">
            <p class="swh-partnership__lead" style="font-size: 1.15rem; color: var(--bc); font-weight: 600; margin-bottom: 1rem;">
              <?php echo esc_html__('Tối ưu hóa không gian 120m² theo tiêu chuẩn quốc tế.', 'saigonhoreca'); ?>
            </p>
            <p><?php echo esc_html__('Để phục vụ món ăn chất lượng cao, Spice World Hot Pot Việt Nam đã tin tưởng vào Saigon Horeca như đơn vị thiết kế và thi công toàn bộ trang thiết bị nhà bếp.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Chúng tôi đã tính toán và phân bổ các khu vực chức năng dựa trên các điều kiện thực tế như thang máy và luồng vận hành, đảm bảo hiệu suất cao nhất cho nhà hàng.', 'saigonhoreca'); ?></p>
          </div>

          <div class="swh-partnership__stats">
            <div class="swh-partnership__stat-item">
              <span class="swh-partnership__stat-value">120m²</span>
              <span class="swh-partnership__stat-label"><?php echo esc_html__('Diện tích bếp', 'saigonhoreca'); ?></span>
            </div>
            <div class="swh-partnership__stat-item">
              <span class="swh-partnership__stat-value"><?php echo esc_html__('QUỐC TẾ', 'saigonhoreca'); ?></span>
              <span class="swh-partnership__stat-label"><?php echo esc_html__('Tiêu chuẩn', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
