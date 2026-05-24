<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #8: cta (Gộp Ảnh và Khối Chữ làm một khối thống nhất, loại bỏ ảnh rời phía dưới)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-cta-section">
  <div class="sgh-cta-container">
    
    <!-- High-End Integrated Split CTA Card -->
    <div class="sgh-cta-card">
      
      <!-- Cột trái: Khối chữ thuyết minh -->
      <div class="sgh-cta-content">
        <span class="sgh-cta-badge"><?php echo esc_html__('KẾT NỐI & KHỞI TẠO', 'saigonhoreca'); ?></span>
        <h2 class="sgh-cta-title"><?php echo esc_html__('Khởi Tạo Không Gian Bếp Đẳng Cấp Cho Dự Án Của Bạn', 'saigonhoreca'); ?></h2>
        
        <div class="sgh-cta-body">
          <p class="sgh-cta-paragraph">
            <?php echo esc_html__('Với kinh nghiệm và uy tín đã được khẳng định hơn 10 năm trong lĩnh vực thiết bị bếp công nghiệp & quầy bar, Saigon Horeca tự hào là đối tác đáng tin cậy đồng hành cùng nhiều nhà hàng và quán Bar đẳng cấp quốc tế như Bambino, Loco Complex, Yuzu Omakase, Unic Bar, và Sol Kitchen & Bar.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-cta-paragraph">
            <?php echo esc_html__('Hãy để lại thông tin liên hệ nếu quý khách đang tìm kiếm một đơn vị tư vấn, thiết kế và thi công hệ thống bếp chuyên nghiệp. Saigon Horeca sẽ chủ động liên hệ, tiến hành khảo sát thực tế tại công trình để lắng nghe yêu cầu và đề xuất giải pháp tối ưu nhất - vừa tiết kiệm tối đa chi phí đầu tư, vừa bảo đảm hệ thống vận hành trơn tru và bền bỉ tuyệt đối.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-cta-paragraph--accent">
            <?php echo esc_html__('Saigon Horeca | Kitchen Equipment & Bar Solutions.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- Action Button có hiệu ứng trượt sáng -->
        <div class="sgh-cta-btn-wrapper">
          <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" class="sgh-cta-btn">
            <?php echo esc_html__('Liên Hệ Tư Vấn Ngay', 'saigonhoreca'); ?>
          </a>
        </div>
      </div>

      <!-- Cột phải: Hình ảnh tích hợp trực tiếp ôm khít trong card -->
      <div class="sgh-cta-media">
        <div class="sgh-cta-media__wrapper">
          <img src="<?php echo sgh_img('2024/06/sol-sgh.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp công nghiệp hoàn thiện đẳng cấp tại Sol Kitchen & Bar - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="900" height="600">
        </div>
      </div>

    </div>

  </div>
</section>
