<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-hwa-intro-section">
  <div class="sgh-hwa-intro-container">
    <div class="sgh-hwa-intro-row">
      
      <!-- CỘT TRÁI: TEXT & CONTEXT -->
      <div class="sgh-hwa-intro-col sgh-hwa-intro-col--text">
        <div class="sgh-hwa-intro-badge">
          <span class="sgh-hwa-intro-badge__accent">//</span>
          <?php echo esc_html__('BỐI CẢNH DỰ ÁN', 'saigonhoreca'); ?>
        </div>
        
        <h2 class="sgh-hwa-intro-title">
          <?php echo esc_html__('Không Gian Nghệ Thuật Omakase Đích Thực', 'saigonhoreca'); ?>
        </h2>
        
        <blockquote class="sgh-hwa-intro-quote">
          <?php echo esc_html__('“Nơi sự tĩnh lặng của kiến trúc Zen Nhật Bản gặp gỡ sự cầu kỳ của nghệ thuật ẩm thực Omakase đỉnh cao.”', 'saigonhoreca'); ?>
        </blockquote>
        
        <div class="sgh-hwa-intro-body">
          <p class="sgh-hwa-intro-paragraph">
            <?php echo esc_html__('Heiwa Sushi Omakase (Số 2 Phan Văn Đáng, Quận 2, HCMC) là điểm đến đỉnh cao cho những tín đồ sành ăn yêu thích ẩm thực Nhật Bản đích thực tại Sài Gòn. Không gian nội thất của Heiwa được thiết kế tỉ mỉ, kiến tạo sự cân bằng hoàn mỹ giữa văn hóa truyền thống Wabi-Sabi và nhịp thở hiện đại đương đại.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-hwa-intro-paragraph">
            <?php echo esc_html__('Được chủ đầu tư tin cậy lựa chọn là đơn vị thiết kế và thi công trọn gói hệ thống bếp biểu diễn cao cấp, Saigon Horeca đã quy hoạch một gian bếp mở thông minh. Nơi đây không chỉ tối ưu hóa 100% công năng vận hành khắt khe của Omakase mà còn tạo ra cầu nối tương tác trực tiếp, đánh thức trọn vẹn thị giác và cảm xúc của thực khách khi chiêm ngưỡng đầu bếp chế biến các món sashimi, sushi tinh xảo.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
      <!-- CỘT PHẢI: 3D PERSPECTIVE SAFARI MOCKUP -->
      <div class="sgh-hwa-intro-col sgh-hwa-intro-col--media">
        <div class="sgh-hwa-intro-mockup-wrapper">
          <div class="sgh-hwa-intro-ambient-glow" aria-hidden="true"></div>
          
          <div class="sgh-hwa-intro-browser">
            <div class="sgh-hwa-intro-browser__header">
              <div class="sgh-hwa-intro-browser__buttons">
                <span class="sgh-hwa-intro-browser__btn sgh-hwa-intro-browser__btn--red"></span>
                <span class="sgh-hwa-intro-browser__btn sgh-hwa-intro-browser__btn--yellow"></span>
                <span class="sgh-hwa-intro-browser__btn sgh-hwa-intro-browser__btn--green"></span>
              </div>
              <div class="sgh-hwa-intro-browser__address" aria-hidden="true">
                <span class="sgh-hwa-intro-browser__lock">🔒</span> heiwasushi.vn/omakase-gallery
              </div>
            </div>
            
            <div class="sgh-hwa-intro-browser__viewport">
              <img src="<?php echo sgh_img('2024/02/heiwasushi-04.jpg'); ?>" alt="<?php echo esc_attr__('Không gian bếp biểu diễn tương tác tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
            </div>
          </div>
          
        </div>
      </div>
      
    </div>
  </div>
</section>

