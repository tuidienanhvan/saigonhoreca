<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #8: cta (Gộp Ảnh và Khối Chữ làm một khối thống nhất, loại bỏ ảnh rời phía dưới)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-hwa-cta-section">
  <div class="sgh-hwa-cta-container">
    
    <!-- High-End Integrated Split CTA Card -->
    <div class="sgh-hwa-cta-card">
      
      <!-- Cột trái: Khối chữ thuyết minh -->
      <div class="sgh-hwa-cta-content">
        <div class="sgh-hwa-cta-badge">
          <span>//</span> <?php echo esc_html__('ĐỒNG HÀNH PHÁT TRIỂN', 'saigonhoreca'); ?>
        </div>
        
        <h2 class="sgh-hwa-cta-title">
          <?php echo esc_html__('Kiến Tạo Không Gian Bếp Omakase Đẳng Cấp Cho Riêng Bạn', 'saigonhoreca'); ?>
        </h2>
        
        <p class="sgh-hwa-cta-desc">
          <?php echo esc_html__('Bạn đang lên kế hoạch cho một dự án nhà hàng Omakase Nhật Bản sang trọng hay một không gian ẩm thực đỉnh cao? Hãy để Saigon Horeca đồng hành biến ý tưởng của bạn thành một thực tại vận hành hoàn mỹ nhất.', 'saigonhoreca'); ?>
        </p>
        
        <!-- Shimmer Button -->
        <div class="sgh-hwa-cta-btn-wrapper">
          <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" class="sgh-hwa-cta-btn">
            <?php echo esc_html__('Liên Hệ Tư Vấn Ngay', 'saigonhoreca'); ?>
          </a>
        </div>
      </div>

      <!-- Cột phải: Hình ảnh tích hợp trực tiếp ôm khít trong card -->
      <div class="sgh-hwa-cta-media">
        <div class="sgh-hwa-cta-media__wrapper">
          <img src="<?php echo sgh_img('2024/01/SGH-Heiwasushi-1.webp'); ?>" alt="<?php echo esc_attr__('Toàn cảnh không gian quầy bar và bếp Omakase đẳng cấp tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="900" height="600">
        </div>
      </div>

    </div>

  </div>
</section>
