<?php
/**
 * Project Pillar - amdang-typhoon
 * Section #8: cta (Showroom Center Call-to-Action)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-at-cta">
  <!-- High-end technical background elements -->
  <div class="pp-cta-grid-pattern" aria-hidden="true"></div>
  <div class="pp-cta-ambient-glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-cta-split pp-cta-center-layout">
      
      <!-- Center Column: Copy & Interactive Specs Showcase -->
      <div class="pp-cta-copy">
        <div class="pp-badge-at-cta">
          <span class="pp-badge-accent-at-cta">//</span> <?php echo esc_html('ĐỒNG HÀNH THIẾT KẾ'); ?>
        </div>

        <h2 class="pp-title-at-cta">
          <?php echo 'Kiến Tạo Không Gian <span class="pp-highlight-gold">Bếp Chuẩn Michelin</span> Cho Dự Án F&B Của Bạn'; ?>
        </h2>

        <div class="pp-desc-at-cta">
          <p class="pp-desc-paragraph">
            <?php echo esc_html('Hơn 3/4 hệ thống thiết bị bếp nướng lò than đặc thù và quầy mì di động của Amdang Typhoon đều được Saigon Horeca thiết kế và gia công sản xuất trực tiếp theo tiêu chuẩn cơ khí inox cao cấp nhất.'); ?>
          </p>
          <p class="pp-desc-paragraph">
            <?php echo esc_html('Bạn đang có ý tưởng cho một dự án nhà hàng sang trọng hay mô hình F&B độc đáo đạt tiêu chuẩn quốc tế? Hãy để Saigon Horeca đồng hành cùng bạn trên con đường kiến tạo.'); ?>
          </p>
        </div>

        <!-- Interactive Specs Horizontal Grid -->
        <div class="pp-cta-specs-list pp-cta-specs-grid">
          <div class="pp-cta-spec-item">
            <span class="pp-cta-spec-num">01</span>
            <div class="pp-cta-spec-content">
              <strong><?php echo esc_html('QUY HOẠCH TỐI ƯU'); ?></strong>
              <span><?php echo esc_html('Bản vẽ mặt bằng 2D/3D chi tiết, tối ưu luồng di chuyển một chiều chuẩn F&B.'); ?></span>
            </div>
          </div>
          
          <div class="pp-cta-spec-item">
            <span class="pp-cta-spec-num">02</span>
            <div class="pp-cta-spec-content">
              <strong><?php echo esc_html('SẢN XUẤT ĐỘC BẢN'); ?></strong>
              <span><?php echo esc_html('Sản xuất trực tiếp thiết bị inox SUS304 cao cấp theo module thực đơn chuyên biệt.'); ?></span>
            </div>
          </div>

          <div class="pp-cta-spec-item">
            <span class="pp-cta-spec-num">03</span>
            <div class="pp-cta-spec-content">
              <strong>HỆ THỐNG AIRFLOW</strong>
              <span><?php echo esc_html('Tính toán thông gió, hút khói bếp lò than áp lực cao, khử mùi triệt để.'); ?></span>
            </div>
          </div>
        </div>

        <div class="pp-btn-wrapper-at-cta">
          <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" class="pp-btn-at-cta">
            <span class="pp-btn-at-cta-text"><?php echo esc_html('LIÊN HỆ TƯ VẤN NGAY'); ?></span>
            <svg class="pp-btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>
