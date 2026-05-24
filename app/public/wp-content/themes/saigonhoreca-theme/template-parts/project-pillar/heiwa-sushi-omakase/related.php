<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #7: related (Thi công hoàn thiện & Triết lý chi tiết)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-hwa-related-section">
  <div class="sgh-hwa-related-container">
    <div class="sgh-hwa-related-grid">
      
      <!-- Cột trái: Thuyết minh quy trình thi công & Quote thiền ngâm -->
      <div class="sgh-hwa-related-text">
        <div class="sgh-hwa-related-badge">
          <span class="sgh-hwa-related-badge__accent">//</span> <?php echo esc_html__('HOÀN THIỆN THỰC TẾ', 'saigonhoreca'); ?>
        </div>
        
        <h3 class="sgh-hwa-related-title">
          <?php echo esc_html__('Kiến Tạo Chuẩn Xác Từng Chi Tiết Cơ Khí', 'saigonhoreca'); ?>
        </h3>
        
        <div class="sgh-hwa-related-body">
          <p class="sgh-hwa-related-paragraph">
            <?php echo esc_html__('Tại Heiwa Sushi, sự hoàn mỹ không chỉ xuất hiện trên các đĩa thức ăn tuyệt hảo của người nghệ sĩ Omakase, mà được đảm bảo xuyên suốt từ cốt lõi hạ tầng kỹ thuật. Saigon Horeca cam kết thi công thực tế bám sát 100% bản vẽ kỹ thuật kỹ lưỡng nhất.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-hwa-related-paragraph">
            <?php echo esc_html__('Quy trình lắp ráp thiết bị, căn chỉnh độ phẳng mặt bàn inox, tối ưu hóa các đường đi ống thoát sàn và hệ thống hút mùi đều được giám sát khắt khe bởi các kỹ sư dày dặn kinh nghiệm, mang đến một gian bếp an toàn, hiệu năng vượt trội.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- Zen Blockquote Card -->
        <div class="sgh-hwa-related-quote">
          <p>
            <?php echo esc_html__('“Sự tinh tế không nằm ở những gì hào nhoáng lộ thiên, mà ẩn sâu trong sự hoàn hảo của những chi tiết cơ khí âm thầm nâng bước người đầu bếp sáng tạo nghệ thuật.”', 'saigonhoreca'); ?>
          </p>
          <span class="sgh-hwa-related-quote__author"><?php echo esc_html__('Đội Ngũ Kỹ Sư Saigon Horeca', 'saigonhoreca'); ?></span>
        </div>
      </div>

      <!-- Cột phải: Staggered Triad Grid (Collage 3 ảnh thi công) -->
      <div class="sgh-hwa-related-collage">
        
        <!-- Ảnh sau cùng bên trái (lên trên) -->
        <div class="sgh-hwa-collage-item sgh-hwa-collage-item--top">
          <img src="<?php echo sgh_img('2024/02/heiwasushi3-1.jpg'); ?>" alt="<?php echo esc_attr__('Thi công quầy bar gỗ tự nhiên cao cấp tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
        </div>
        
        <!-- Ảnh chính giữa trung tâm -->
        <div class="sgh-hwa-collage-item sgh-hwa-collage-item--main">
          <img src="<?php echo sgh_img('2024/02/heiwasushi-04.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống quầy Omakase hoàn thiện thực tế cực kỳ cao cấp - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="900" height="650">
        </div>
        
        <!-- Ảnh trên cùng bên phải (xuống dưới) -->
        <div class="sgh-hwa-collage-item sgh-hwa-collage-item--bottom">
          <img src="<?php echo sgh_img('2024/02/Heiwa-Sushi-00004.jpg'); ?>" alt="<?php echo esc_attr__('Kiểm thử thiết bị bếp inox công nghiệp trước ca vận hành - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
        </div>

      </div>

    </div>
  </div>
</section>
