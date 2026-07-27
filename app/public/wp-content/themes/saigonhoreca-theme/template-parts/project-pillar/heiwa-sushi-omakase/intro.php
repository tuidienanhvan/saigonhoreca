<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #2: intro — editorial "Bối cảnh dự án": text column + single
 * showcase image, matching the live production layout.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hwa pp-intro-hwa">
  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
  <div class="pp-intro-hwa__grid">

  <!-- Cột trái: khối chữ bối cảnh dự án -->
  <div class="pp-intro-hwa__text">
  <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('BỐI CẢNH DỰ ÁN', 'saigonhoreca'); ?></div>
  <h2 class="pp-text-hwa__title"><?php echo esc_html__('Không Gian Nghệ Thuật Omakase Đích Thực', 'saigonhoreca'); ?></h2>
  <div class="pp-text-hwa__divider" aria-hidden="true"></div>
  <blockquote class="pp-text-hwa__quote"><?php echo esc_html__('“Nơi sự tĩnh lặng của kiến trúc Zen Nhật Bản gặp gỡ sự cầu kỳ của nghệ thuật ẩm thực Omakase đỉnh cao.”', 'saigonhoreca'); ?></blockquote>
  <div class="pp-text-hwa__body">
  <p><?php echo esc_html__('Heiwa Sushi Omakase (Số 2 Phan Văn Đáng, Quận 2, HCMC) là điểm đến đỉnh cao cho những tín đồ sành ăn yêu thích ẩm thực Nhật Bản đích thực tại Sài Gòn. Không gian nội thất của Heiwa được thiết kế tỉ mỉ, kiến tạo sự cân bằng hoàn mỹ giữa văn hóa truyền thống Wabi-Sabi và nhịp thở hiện đại đương đại.', 'saigonhoreca'); ?></p>
  <p><?php echo esc_html__('Được chủ đầu tư tin cậy lựa chọn là đơn vị thiết kế và thi công trọn gói hệ thống bếp biểu diễn cao cấp, Saigon Horeca đã quy hoạch một gian bếp mở thông minh. Nơi đây không chỉ tối ưu hóa 100% công năng vận hành khắt khe của Omakase mà còn tạo ra cầu nối tương tác trực tiếp, đánh thức trọn vẹn thị giác và cảm xúc của thực khách khi chiêm ngưỡng đầu bếp chế biến các món sashimi, sushi tinh xảo.', 'saigonhoreca'); ?></p>
  </div>
  </div>

  </div>
  </div>
</section>
