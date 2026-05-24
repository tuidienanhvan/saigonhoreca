<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #5: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-hwa-specs-section">
  <div class="sgh-hwa-specs-container">
    <div class="sgh-hwa-specs-grid">
      
      <!-- Cột trái: Thuyết minh & Specs Card (Parallel Framer) -->
      <div class="sgh-hwa-specs-text">
        <div class="sgh-hwa-specs-badge">
          <span class="sgh-hwa-specs-badge__accent">//</span> <?php echo esc_html__('UY TÍN ĐỐI TÁC', 'saigonhoreca'); ?>
        </div>
        
        <h3 class="sgh-hwa-specs-title">
          <?php echo esc_html__('Sự Hợp Tác Giữa Heiwa Sushi & Saigon Horeca', 'saigonhoreca'); ?>
        </h3>
        
        <div class="sgh-hwa-specs-body">
          <p class="sgh-hwa-specs-paragraph">
            <?php echo esc_html__('Sự hợp tác chặt chẽ giữa Heiwa Sushi và Saigon Horeca là minh chứng rõ ràng cho tầm quan trọng của việc quy hoạch và thiết lập hệ thống bếp chuyên nghiệp trong thành công rực rỡ của một nhà hàng Omakase. Với kinh nghiệm chuyên môn dày dặn, Saigon Horeca đã đồng hành lựa chọn và lắp đặt các dòng thiết bị nhà bếp tốt nhất, kiến tạo nên trải nghiệm trọn vẹn và an tâm tuyệt đối.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- Technical Specs Card (Parallel Framer) -->
        <div class="sgh-hwa-specs-card">
          <h4 class="sgh-hwa-specs-card__title"><?php echo esc_html__('Thông số kỹ thuật lắp đặt', 'saigonhoreca'); ?></h4>
          <div class="sgh-hwa-specs-card__grid">
            <div class="sgh-hwa-specs-card__item">
              <span class="sgh-hwa-specs-card__label"><?php echo esc_html__('Thiết bị lạnh chính', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-specs-card__value"><?php echo esc_html__('Hoshizaki Japan', 'saigonhoreca'); ?></span>
            </div>
            <div class="sgh-hwa-specs-card__item">
              <span class="sgh-hwa-specs-card__label"><?php echo esc_html__('Hệ thống quầy bar', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-specs-card__value"><?php echo esc_html__('Gỗ tự nhiên & Kính', 'saigonhoreca'); ?></span>
            </div>
            <div class="sgh-hwa-specs-card__item">
              <span class="sgh-hwa-specs-card__label"><?php echo esc_html__('Chất liệu cơ khí', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-specs-card__value"><?php echo esc_html__('Inox 304 tiêu chuẩn', 'saigonhoreca'); ?></span>
            </div>
            <div class="sgh-hwa-specs-card__item">
              <span class="sgh-hwa-specs-card__label"><?php echo esc_html__('Tiêu chuẩn vệ sinh', 'saigonhoreca'); ?></span>
              <span class="sgh-hwa-specs-card__value"><?php echo esc_html__('Omakase Fresh Standard', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải: Offset Frame Showcase -->
      <div class="sgh-hwa-specs-media">
        <div class="sgh-hwa-specs-media__frame" aria-hidden="true"></div>
        <div class="sgh-hwa-specs-media__wrapper">
          <img src="<?php echo sgh_img('2024/02/Heiwa-Sushi-00004.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống thiết bị bếp inox công nghiệp cao cấp tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        </div>
      </div>

    </div>
  </div>
</section>

