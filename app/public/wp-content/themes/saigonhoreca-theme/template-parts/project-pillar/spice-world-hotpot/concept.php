<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #3: concept
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt pp-swh-concept">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M30 20 H70 M42 20 V80 M30 80 H54 M58 20 V80 M46 80 H70" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--bottom-left" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-swh">
      
      <div class="pp-grid-12-swh__text--cols-5 swh-concept__main">
        <span class="pp-text-swh__divider" aria-hidden="true"></span>
        <h2 class="pp-text-swh__title">
          <?php echo esc_html__('Thiết kế của nhà hàng Spice World HotPot', 'saigonhoreca'); ?>
          <span class="pp-text-swh__title-gold"><?php echo esc_html__('Nơi Thế Giới Cổ Điển Gặp Gỡ Hiện Đại', 'saigonhoreca'); ?></span>
        </h2>
        
        <div class="pp-text-swh__body">
          <p><?php echo esc_html__('Theo truyền thống, lẩu Sichuan thường được ưa chuộng vào những tháng mùa đông tại Trung Quốc để làm ấm cơ thể. Tuy nhiên, tại Việt Nam, sự phổ biến của lẩu đã vượt qua giới hạn của thời tiết, khi người Việt có thể thưởng thức lẩu bất cứ lúc nào họ mong muốn.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Nhà hàng có hai tầng với 180 chỗ ngồi và ba phòng riêng, mỗi phòng phục vụ 10 khách. Màu chủ đạo là màu đỏ kết hợp với màu đen và trắng, tạo nên một sự kết hợp giữa thế giới cũ và hiện đại.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Một nửa không gian đại diện cho thế giới hiện đại với trần nhà bằng thép màu đen, tường kính trong suốt và thiết kế nội thất tối giản trong màu đen và trắng. Nửa còn lại thể hiện thế giới cũ với tường được trang trí bằng các bức tranh của những nhân vật lịch sử, chủ yếu là màu đỏ, tạo nên một không khí hấp dẫn và bí ẩn.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-grid-12-swh__media--cols-7 swh-concept__side">
        <div class="pp-image-container-swh swh-concept__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-swh">STUDIO</div>
          <img src="<?php echo home_url('/wp-content/uploads/2024/02/Spice-World-Hot-Pot-03-1.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-swh"><?php echo esc_html__('Không gian bếp mở thiết kế tinh gọn theo tiêu chuẩn Saigon Horeca', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
