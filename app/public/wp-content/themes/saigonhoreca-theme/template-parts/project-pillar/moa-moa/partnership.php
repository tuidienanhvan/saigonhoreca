<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #4: partnership — đối tác chiến lược + dashboard chỉ số + ảnh xếp lớp.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-moa-partnership-section">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="pp-moa-partnership-section__bg" style="background-image:url('<?php echo sgh_img('moa-moa/moa-moa-khong-gian-an-trong-nha-nhin-pho-quan-1.webp'); ?>');"></div>
  <div class="pp-moa-partnership-section__overlay" aria-hidden="true"></div>
  
  <div class="pp-moa-partnership-section__content">
    <div class="pp-moa-partnership-card">
      
      <!-- Tiêu đề và divider phong cách tạp chí -->
      <div class="pp-moa-partnership-card__header">
        <span class="pp-text-section__divider pp-text-section__divider--center" aria-hidden="true"></span>
        <h2 class="pp-text-section__title">
          <?php echo esc_html__('Hệ thống hút khói – xử lý mùi giữa phố đi bộ', 'saigonhoreca'); ?>
        </h2>
      </div>
      
      <!-- PHẦN 1: Tầm Nhìn & Ý Tưởng Khởi Đầu (2 Cột thoáng đãng) -->
      <div class="pp-moa-partnership-row-top">
        <div class="pp-moa-partnership-col-left">
          <div class="pp-moa-partnership-quote">
            <span class="pp-moa-quote-icon" aria-hidden="true">“</span>
            <p class="pp-moa-quote-text">
              <?php echo esc_html__('Với mô hình bếp mở và vị trí đắc địa ngay khu phố đi bộ Nguyễn Huệ, bài toán lớn nhất của Moa Moa không chỉ là nấu ngon, mà còn là kiểm soát mùi và khói.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
        <div class="pp-moa-partnership-col-right">
          <p class="pp-moa-partnership-lead">
            <?php echo esc_html__('Saigon Horeca đã cung cấp và lắp đặt hệ thống chụp hút và quạt hút chuyên dụng, được tính toán kỹ lưỡng theo công suất thiết bị và đặc thù bếp Âu – bếp nướng than củi.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
      <!-- PHẦN 2: Hiện Thực Hóa Chuyên Môn (Dạng cột catalog tạp chí kiến trúc) -->
      <div class="pp-moa-partnership-catalog">
        <div class="pp-moa-catalog-item">
          <div class="pp-moa-catalog-meta">
            <span class="pp-moa-catalog-num">01</span>
            <span class="pp-moa-catalog-tag">Tiêu Chuẩn Vận Hành</span>
          </div>
          <p class="pp-moa-catalog-text">
            <?php echo esc_html__('Hệ thống này đảm bảo không gian bếp luôn thông thoáng, không ảnh hưởng đến trải nghiệm thực khách, đồng thời tuân thủ các tiêu chuẩn an toàn – vận hành trong khu vực trung tâm đông đúc.', 'saigonhoreca'); ?>
          </p>
        </div>
        
        <div class="pp-moa-partnership-catalog-stats">
          <div class="pp-moa-partnership-stat-item">
            <span class="pp-moa-partnership-stat-value">OPEN</span>
            <span class="pp-moa-partnership-stat-label"><?php echo esc_html__('Bếp mở Italian kitchen', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-moa-partnership-stat-item">
            <span class="pp-moa-partnership-stat-value">Q.1</span>
            <span class="pp-moa-partnership-stat-label"><?php echo esc_html__('40E Ngô Đức Kế, Bến Nghé', 'saigonhoreca'); ?></span>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>
