<?php
/**
 * Project Pillar — tales-by-chapter
 * Section #4: partnership — "Chương 03" Tầng 3 Lab Bar, creative R&D space.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tbc pp-section-tbc--alt pp-partnership-tbc">
  <div class="pp-chapter-mark-tbc" aria-hidden="true">03</div>
  <div class="pp-ambient-glow-tbc pp-ambient-glow-tbc--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-partnership-tbc__grid">

      <div class="pp-partnership-tbc__content">
        <div class="pp-partnership-tbc__card">
          <header>
            <div class="pp-badge-tbc">
              <span class="pp-badge-tbc__num">Chương 03</span>
              <?php echo esc_html__('Tầng 3 · Lab Bar', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-tbc__title"><?php echo esc_html__('Nơi những chương mới được viết', 'saigonhoreca'); ?></h2>
            <span class="pp-text-tbc__divider" aria-hidden="true"></span>
          </header>

          <p class="pp-partnership-tbc__lead"><?php echo esc_html__('Tầng 3 là không gian riêng dành cho sự sáng tạo – nơi sáng tạo không có giới hạn.', 'saigonhoreca'); ?></p>

          <div class="pp-text-tbc__body">
            <p><?php echo esc_html__('Đây là nơi các bartender thử nghiệm, phát triển cocktail mới và khẳng định phong cách của TBC. Saigon Horeca đã cung cấp hệ thống thiết bị bar chuyên dụng, bao gồm tủ mát bar, máy làm đá viên và hệ thống cấp thoát nước tinh gọn. Layout được tính toán để bartender có thể di chuyển nhanh, thao tác thuận tay và dễ dàng kiểm soát nguyên liệu.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Mỗi chi tiết trong lab bar đều được Saigon Horeca thiết kế để hỗ trợ cho sự sáng tạo, không áp đặt khuôn khổ.', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-partnership-tbc__stats">
            <div class="pp-partnership-tbc__stat">
              <span class="pp-partnership-tbc__stat-value">R&amp;D</span>
              <span class="pp-partnership-tbc__stat-label"><?php echo esc_html__('Phòng lab phát triển cocktail', 'saigonhoreca'); ?></span>
            </div>
            <div class="pp-partnership-tbc__stat">
              <span class="pp-partnership-tbc__stat-value"><?php echo esc_html__('Linh hoạt', 'saigonhoreca'); ?></span>
              <span class="pp-partnership-tbc__stat-label"><?php echo esc_html__('Layout không áp đặt khuôn khổ', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="pp-partnership-tbc__media">
        <div class="pp-partnership-tbc__media-main pp-image-container-shared">
          <img src="<?php echo sgh_img('tales-by-chapter/tales-by-chapter-lab-bar-quay-da-cam-thach.jpg'); ?>" alt="<?php echo esc_attr__('Quầy bar mặt đá cẩm thạch có khách ngồi, phía sau là bếp mở', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Quầy bar mặt đá cẩm thạch sáng đèn – nơi khách ngồi tại quầy, dõi theo từng thao tác trước gian bếp mở', 'saigonhoreca'); ?></div>
        </div>
        <div class="pp-partnership-tbc__media-float pp-image-container-shared">
          <img src="<?php echo sgh_img('tales-by-chapter/tales-by-chapter-mon-trang-mieng-keo-bong.jpg'); ?>" alt="<?php echo esc_attr__('Sáng tạo đồ uống và tráng miệng tại lab bar Tales', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Thành quả sáng tạo từ lab bar – nơi viết nên những chương mới', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
