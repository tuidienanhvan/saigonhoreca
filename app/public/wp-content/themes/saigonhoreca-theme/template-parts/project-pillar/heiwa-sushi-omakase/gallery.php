<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #6: gallery (Hệ thống thiết bị lạnh & cơ khí inox)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-hwa-gallery-section">
  <div class="sgh-hwa-gallery-container">
    <div class="sgh-hwa-gallery-grid">
      
      <!-- Cột trái: Offset Frame Showcase ảnh inox/thiết bị -->
      <div class="sgh-hwa-gallery-media">
        <div class="sgh-hwa-gallery-media__frame" aria-hidden="true"></div>
        <div class="sgh-hwa-gallery-media__wrapper">
          <img src="<?php echo sgh_img('2024/02/heiwasushi2-1.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống thiết bị tủ đông Hoshizaki và bàn inox cao cấp - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        </div>
      </div>

      <!-- Cột phải: Thuyết minh & Lưới tính năng thiết bị bếp -->
      <div class="sgh-hwa-gallery-text">
        <div class="sgh-hwa-gallery-badge">
          <span class="sgh-hwa-gallery-badge__accent">//</span> <?php echo esc_html__('KỸ THUẬT VẬN HÀNH', 'saigonhoreca'); ?>
        </div>
        
        <h3 class="sgh-hwa-gallery-title">
          <?php echo esc_html__('Hệ Thống Thiết Bị Hoshizaki & Cơ Khí Inox Cao Cấp', 'saigonhoreca'); ?>
        </h3>
        
        <div class="sgh-hwa-gallery-body">
          <p class="sgh-hwa-gallery-paragraph">
            <?php echo esc_html__('Trái tim vận hành của bếp Heiwa Sushi nằm ở các giải pháp thiết bị lạnh chất lượng cao từ thương hiệu Hoshizaki Nhật Bản. Tủ đông và tủ mát bảo quản nguyên liệu tươi sống được tính toán vị trí lắp đặt chính xác, tối ưu hóa tối đa lối đi và tầm với của đầu bếp trong giờ cao điểm.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-hwa-gallery-paragraph">
            <?php echo esc_html__('Hệ thống giá treo tường, chậu rửa, bàn inox 304 cao cấp do Saigon Horeca gia công sản xuất riêng cho dự án đều đạt tiêu chuẩn cơ khí hoàn mỹ. Mỗi góc chấn ép, vết hàn khí trơ đều được xử lý mài bóng mịn, triệt tiêu khe hở tích tụ vi khuẩn để đảm bảo tiêu chí Omakase Fresh khắt khe.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- Technical Features Grid -->
        <div class="sgh-hwa-gallery-features">
          
          <div class="sgh-hwa-gallery-feature-item">
            <div class="sgh-hwa-gallery-feature-icon">✦</div>
            <div class="sgh-hwa-gallery-feature-content">
              <span class="sgh-hwa-gallery-feature-title"><?php echo esc_html__('Tủ Lạnh Hoshizaki', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-gallery-feature-desc"><?php echo esc_html__('Bảo quản hải sản sashimi ở nhiệt độ tối ưu nhất.', 'saigonhoreca'); ?></span>
            </div>
          </div>
          
          <div class="sgh-hwa-gallery-feature-item">
            <div class="sgh-hwa-gallery-feature-icon">✦</div>
            <div class="sgh-hwa-gallery-feature-content">
              <span class="sgh-hwa-gallery-feature-title"><?php echo esc_html__('Inox 304 Chuẩn Sạch', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-gallery-feature-desc"><?php echo esc_html__('Kháng khuẩn tuyệt đối, chống gỉ sét và dễ vệ sinh.', 'saigonhoreca'); ?></span>
            </div>
          </div>

          <div class="sgh-hwa-gallery-feature-item">
            <div class="sgh-hwa-gallery-feature-icon">✦</div>
            <div class="sgh-hwa-gallery-feature-content">
              <span class="sgh-hwa-gallery-feature-title"><?php echo esc_html__('Giá Treo Tiện Lợi', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-gallery-feature-desc"><?php echo esc_html__('Tối ưu hóa không gian, tăng diện tích thao tác thực tế.', 'saigonhoreca'); ?></span>
            </div>
          </div>

          <div class="sgh-hwa-gallery-feature-item">
            <div class="sgh-hwa-gallery-feature-icon">✦</div>
            <div class="sgh-hwa-gallery-feature-content">
              <span class="sgh-hwa-gallery-feature-title"><?php echo esc_html__('Hệ Chậu Rửa Ergonomics', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-gallery-feature-desc"><?php echo esc_html__('Thiết kế sâu lòng thông minh ngăn văng nước bẩn ra ngoài.', 'saigonhoreca'); ?></span>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>
