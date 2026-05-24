<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #6: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-gallery-section">
  <div class="sgh-gallery-container">
    <div class="sgh-gallery-grid">
      
      <!-- Cột trái: Offset Frame Media Showcase -->
      <div class="sgh-gallery-media">
        <div class="sgh-gallery-media__frame" aria-hidden="true"></div>
        <div class="sgh-gallery-media__wrapper">
          <img src="<?php echo sgh_img('2024/06/SGH-sold7-01.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống thiết bị bếp inox công nghiệp cao cấp tại Sol Kitchen & Bar - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        </div>
      </div>

      <!-- Cột phải: Thuyết minh & Feature List -->
      <div class="sgh-gallery-text">
        <div class="sgh-gallery-badge">
          <span class="sgh-gallery-badge__accent">//</span> <?php echo esc_html__('GIẢI PHÁP ĐỒNG BỘ', 'saigonhoreca'); ?>
        </div>
        
        <h3 class="sgh-gallery-title">
          <?php echo esc_html__('Quy Quy Hoạch & Tối Ưu Hóa Thiết Bị Bếp', 'saigonhoreca'); ?>
        </h3>
        
        <div class="sgh-gallery-body">
          <p class="sgh-gallery-paragraph">
            <?php echo esc_html__('Saigon Horeca cung cấp hệ thống tủ đông và tủ mát cao cấp, đảm bảo việc bảo quản thực phẩm luôn ở nhiệt độ tối ưu theo tiêu chuẩn vệ sinh an toàn thực phẩm HACCP. Lò nướng combi thông minh là sự kết hợp hoàn hảo giữa nướng và hấp, mang đến khả năng chế biến các món ăn chất lượng cao với tốc độ vượt trội.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-gallery-paragraph">
            <?php echo esc_html__('Đặc biệt, lò BBQ sản xuất riêng biệt mang phong cách Italia chính là điểm nhấn thẩm mỹ nghệ thuật độc đáo cho gian bếp mở của Sol Kitchen & Bar, kết hợp sự vững chãi với tính năng dễ dàng thao tác.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-gallery-paragraph">
            <?php echo esc_html__('Song song với việc lắp đặt thiết bị mới, chúng tôi đã chủ động kiểm định công năng và tái tích hợp các thiết bị sẵn có của chủ đầu tư, tối ưu hóa ngân sách mà vẫn bảo đảm chuỗi vận hành liên tục ổn định.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- High-End Technical Feature List -->
        <div class="sgh-gallery-features">
          <div class="sgh-gallery-feature-item">
            <span class="sgh-gallery-feature-icon">❖</span>
            <div class="sgh-gallery-feature-content">
              <span class="sgh-gallery-feature-title"><?php echo esc_html__('Bảo quản HACCP', 'saigonhoreca'); ?></span>
              <span class="sgh-gallery-feature-desc"><?php echo esc_html__('Hệ tủ đông/mát đa tầng', 'saigonhoreca'); ?></span>
            </div>
          </div>
          <div class="sgh-gallery-feature-item">
            <span class="sgh-gallery-feature-icon">❖</span>
            <div class="sgh-gallery-feature-content">
              <span class="sgh-gallery-feature-title"><?php echo esc_html__('Lò nướng Combi', 'saigonhoreca'); ?></span>
              <span class="sgh-gallery-feature-desc"><?php echo esc_html__('Hấp nướng thông minh', 'saigonhoreca'); ?></span>
            </div>
          </div>
          <div class="sgh-gallery-feature-item">
            <span class="sgh-gallery-feature-icon">❖</span>
            <div class="sgh-gallery-feature-content">
              <span class="sgh-gallery-feature-title"><?php echo esc_html__('Lò BBQ Custom', 'saigonhoreca'); ?></span>
              <span class="sgh-gallery-feature-desc"><?php echo esc_html__('Phong cách Ý độc quyền', 'saigonhoreca'); ?></span>
            </div>
          </div>
          <div class="sgh-gallery-feature-item">
            <span class="sgh-gallery-feature-icon">❖</span>
            <div class="sgh-gallery-feature-content">
              <span class="sgh-gallery-feature-title"><?php echo esc_html__('Tối ưu ngân sách', 'saigonhoreca'); ?></span>
              <span class="sgh-gallery-feature-desc"><?php echo esc_html__('Kiểm định và tái tích hợp', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

