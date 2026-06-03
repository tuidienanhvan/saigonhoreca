<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #4: bg_section
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-lb">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="pp-section-bg-lb__bg" style="background-image:url('<?php echo sgh_img('little-bear-thao-dien/little-bear-thao-dien-doi-tac-van-hanh-bep.jpg'); ?>');"></div>
  <div class="pp-section-bg-lb__overlay" aria-hidden="true"></div>

  <!-- SVG Họa tiết Bánh răng Cơ khí quay chậm -->
  <svg class="pp-lb-gears-decor" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <g class="pp-lb-gear pp-lb-gear--1" transform-origin="30 30">
      <path d="M35 30 a5 5 0 1 1 -10 0 a5 5 0 1 1 10 0" fill="none" stroke="rgba(245, 166, 35, 0.03)" stroke-width="2" stroke-dasharray="2 3"/>
      <circle cx="30" cy="30" r="15" fill="none" stroke="rgba(245, 166, 35, 0.02)" stroke-width="1"/>
      <circle cx="30" cy="30" r="18" fill="none" stroke="rgba(245, 166, 35, 0.015)" stroke-width="2" stroke-dasharray="4 4"/>
    </g>
    <g class="pp-lb-gear pp-lb-gear--2" transform-origin="65 50">
      <path d="M75 50 a10 10 0 1 1 -20 0 a10 10 0 1 1 20 0" fill="none" stroke="rgba(245, 166, 35, 0.03)" stroke-width="1.5" stroke-dasharray="3 4"/>
      <circle cx="65" cy="50" r="22" fill="none" stroke="rgba(245, 166, 35, 0.02)" stroke-width="1"/>
      <circle cx="65" cy="50" r="26" fill="none" stroke="rgba(245, 166, 35, 0.015)" stroke-width="3" stroke-dasharray="6 6"/>
    </g>
  </svg>

  <!-- SVG Họa tiết lưới tọa độ blueprint định vị thiết bị mờ ảo -->
  <svg class="pp-lb-partnership-svg-decor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <line x1="10" y1="0" x2="10" y2="100" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.25" stroke-dasharray="2 2"/>
    <line x1="90" y1="0" x2="90" y2="100" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.25" stroke-dasharray="2 2"/>
    <circle cx="50" cy="50" r="12" fill="none" stroke="rgba(245, 166, 35, 0.02)" stroke-width="0.25"/>
    <circle cx="50" cy="50" r="1" fill="rgba(245, 166, 35, 0.04)"/>
  </svg>

  <div class="pp-container-shared">
    <!-- Bọc scroll-reveal riêng để tránh áp transform lên section cha làm hỏng position: fixed của background -->
    <div class="pp-partnership-lb__wrapper scroll-reveal">
      
      <div class="pp-partnership-lb__header">
        <span class="pp-text-lb__divider pp-text-lb__divider--center" aria-hidden="true"></span>
        <h2 class="pp-text-lb__title"><?php echo esc_html__('Thách thức & Giải pháp Thiết bị', 'saigonhoreca'); ?></h2>
        <p class="pp-partnership-lb__lead"><?php echo esc_html__('Trong không gian bếp hình vuông hạn chế của Little Bear, Saigon Horeca đã khéo léo bố trí một bản giao hưởng của chức năng và phong cách.', 'saigonhoreca'); ?></p>
      </div>

      <!-- Mảng lưới giới thiệu các dòng thiết bị nhập khẩu cao cấp -->
      <div class="pp-partnership-lb__grid">
        
        <div class="pp-partnership-card-lb">
          <div class="pp-partnership-card-lb__header">
            <span class="pp-partnership-card-lb__brand">Rational</span>
            <h3 class="pp-partnership-card-lb__title"><?php echo esc_html__('Lò hấp nướng đa năng', 'saigonhoreca'); ?></h3>
          </div>
          <p class="pp-partnership-card-lb__text"><?php echo esc_html__('Tích hợp giải pháp nướng, hấp thông minh giúp đầu bếp rảnh tay, kiểm soát chính xác mức độ chín và giữ nguyên độ ẩm ngọt tự nhiên của món ăn.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-partnership-card-lb">
          <div class="pp-partnership-card-lb__header">
            <span class="pp-partnership-card-lb__brand">Hoshizaki</span>
            <h3 class="pp-partnership-card-lb__title"><?php echo esc_html__('Bảo quản & Làm đá', 'saigonhoreca'); ?></h3>
          </div>
          <p class="pp-partnership-card-lb__text"><?php echo esc_html__('Hệ thống tủ mát và bàn đông mát under-counter duy trì nhiệt độ bảo quản chuẩn mực, lưu trữ nguyên liệu tươi sạch hoàn hảo suốt cả ngày.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-partnership-card-lb">
          <div class="pp-partnership-card-lb__header">
            <span class="pp-partnership-card-lb__brand">Fujimak</span>
            <h3 class="pp-partnership-card-lb__title"><?php echo esc_html__('Thiết bị bếp nóng', 'saigonhoreca'); ?></h3>
          </div>
          <p class="pp-partnership-card-lb__text"><?php echo esc_html__('Hệ thống bếp phẳng hồng ngoại công suất cao cùng chảo thép đen carbon tay cầm cong, đáp ứng cường độ nấu nướng nhanh cho tối tối cao điểm.', 'saigonhoreca'); ?></p>
        </div>

      </div>

      <div class="pp-partnership-lb__footer">
        <p><?php echo esc_html__('Quy định nhà bếp được tuân thủ nghiêm ngặt nhưng không làm hạn chế sự sáng tạo ẩm thực, nhờ hệ thống thông gió được thiết kế tỉ mỉ, lưu trữ tối ưu và bố cục tiện dụng hỗ trợ các đầu bếp. Bếp Little Bear không chỉ là nhu cầu vận hành; đó là minh chứng cho cam kết tạo ra những không gian nuôi dưỡng sự xuất sắc về ẩm thực.', 'saigonhoreca'); ?></p>
      </div>

    </div>
  </div>
</section>
