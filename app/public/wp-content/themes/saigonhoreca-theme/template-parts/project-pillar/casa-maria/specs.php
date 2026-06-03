<?php
/**
 * Project Pillar — casa-maria
 * Section #5: specs — Hệ thống hút mùi & bản vẽ kỹ thuật
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-cm pp-section-cm--alt pp-specs-cm">
  <div class="pp-container-shared">

    <!-- Lưới blueprint chạy ngầm mờ ảo ở nền -->
    <svg class="pp-specs-bg-blueprint" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; opacity: 0.04; z-index: 1;">
      <defs>
        <pattern id="specs-grid" width="40" height="40" patternUnits="userSpaceOnUse">
          <path d="M 40 0 L 0 0 0 40" fill="none" stroke="var(--gold)" stroke-width="0.5"/>
          <path d="M 20 0 L 20 40 M 0 20 L 40 20" fill="none" stroke="var(--gold)" stroke-width="0.2" stroke-dasharray="2 2"/>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#specs-grid)" />
    </svg>

    <!-- Block 1: Khối thông tin biên tập tạp chí (Editorial Split Header) -->
    <div class="pp-specs-editorial-header">
      <div class="pp-specs-editorial-title">
        <div class="pp-badge-cm">
          <svg class="pp-badge-icon-cm" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="14" height="14">
            <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
          </svg>
          <?php echo esc_html__('Hút mùi bếp mở', 'saigonhoreca'); ?>
        </div>
        <h2 class="pp-text-cm__title" style="margin-top: 1rem !important; margin-bottom: 0 !important;">
          <?php echo esc_html__('Hút mùi cho bếp mở Casa Maria', 'saigonhoreca'); ?>
        </h2>
      </div>
      
      <div class="pp-specs-editorial-desc">
        <p class="pp-specs-lead-p-cm">
          <?php echo esc_html__('Bếp mở cần xử lý mùi ngay tại nguồn, đủ êm để không phá vỡ trải nghiệm tapas & wine của khách.', 'saigonhoreca'); ?>
        </p>
        <ul class="pp-specs-list-cm" style="margin-top: 1.5rem;">
          <li>
            <strong><?php echo esc_html__('Hút tại nguồn:', 'saigonhoreca'); ?></strong>
            <?php echo esc_html__('Giữ khói mùi không lan ra khu khách.', 'saigonhoreca'); ?>
          </li>
          <li>
            <strong><?php echo esc_html__('Gọn với kiến trúc:', 'saigonhoreca'); ?></strong>
            <?php echo esc_html__('Chụp hút và tuyến ống hòa vào tổng thể bếp mở.', 'saigonhoreca'); ?>
          </li>
          <li>
            <strong><?php echo esc_html__('Vận hành êm:', 'saigonhoreca'); ?></strong>
            <?php echo esc_html__('Giảm tiếng ồn và giữ không khí thư thái cho wine bar.', 'saigonhoreca'); ?>
          </li>
        </ul>
      </div>
    </div>

    <!-- Block 2: Bàn trình diễn ảnh Kỷ hà kỹ sư (Architectural Canvas) -->
    <div class="pp-specs-blueprint-canvas">
      
      <!-- Góc trang trí SVG thước đo đo đạc thực tế (Architectural Scale Overlay) -->
      <svg class="pp-specs-svg-scale" viewBox="0 0 100 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="display: block; width: 150px; height: 15px; opacity: 0.25; margin-bottom: 2rem;">
        <line x1="0" y1="5" x2="100" y2="5" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="0" y1="0" x2="0" y2="10" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="10" y1="2" x2="10" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="20" y1="2" x2="20" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="30" y1="2" x2="30" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="40" y1="2" x2="40" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="50" y1="0" x2="50" y2="10" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="60" y1="2" x2="60" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="70" y1="2" x2="70" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="80" y1="2" x2="80" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="90" y1="2" x2="90" y2="8" stroke="var(--gold)" stroke-width="0.5"/>
        <line x1="100" y1="0" x2="100" y2="10" stroke="var(--gold)" stroke-width="0.5"/>
      </svg>

      <div class="pp-specs-canvas-grid">
        
        <!-- Khung ảnh lớn thứ nhất -->
        <div class="pp-specs-canvas-item pp-specs-canvas-item--primary">
          <div class="pp-specs-photo-frame">
            <!-- Khung góc SVG sang trọng -->
            <svg class="pp-specs-photo-decor" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: -5px; left: -5px; width: calc(100% + 10px); height: calc(100% + 10px); pointer-events: none; z-index: 5;">
              <path d="M 0,20 L 0,0 L 20,0" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
              <path d="M 80,0 L 100,0 L 100,20" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
              <path d="M 100,80 L 100,100 L 80,100" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
              <path d="M 20,100 L 0,100 L 0,80" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
            </svg>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('casa-maria/casa-maria-he-thong-bep-nau-au-inox.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp nấu và chụp hút mùi Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Hệ thống chụp hút mùi chuyên dụng và bếp nấu Âu inox đồng bộ', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Khung ảnh lớn thứ hai -->
        <div class="pp-specs-canvas-item pp-specs-canvas-item--secondary">
          <div class="pp-specs-photo-frame">
            <!-- Khung góc SVG sang trọng -->
            <svg class="pp-specs-photo-decor" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: -5px; left: -5px; width: calc(100% + 10px); height: calc(100% + 10px); pointer-events: none; z-index: 5;">
              <path d="M 0,20 L 0,0 L 20,0" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
              <path d="M 80,0 L 100,0 L 100,20" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
              <path d="M 100,80 L 100,100 L 80,100" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
              <path d="M 20,100 L 0,100 L 0,80" stroke="var(--gold)" stroke-width="1.5" fill="none" opacity="0.3"/>
            </svg>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('casa-maria/casa-maria-thiet-bi-bep-au-griddle-tu-lanh.jpg'); ?>" alt="<?php echo esc_attr__('Chi tiết thiết bị bếp Âu Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Chi tiết thiết bị bếp Âu và griddle inox cao cấp lắp đặt hoàn thiện', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="pp-specs-canvas-footer">
        <p class="pp-specs-footer-p-cm">
          <?php echo esc_html__('Giải pháp kỹ thuật tinh tế này giúp Casa Maria giữ được sự cân bằng hoàn hảo giữa khu bếp hoạt động liên tục và không gian wine bar đầy thư thái.', 'saigonhoreca'); ?>
        </p>
      </div>

    </div>

  </div>
</section>
