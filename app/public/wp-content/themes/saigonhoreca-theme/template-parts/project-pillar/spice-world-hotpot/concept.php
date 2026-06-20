<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #3: concept
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-swh-concept">
  <div class="pp-swh-concept__glow" aria-hidden="true"></div>
  <div class="pp-swh-concept__num-watermark" aria-hidden="true">02</div>

  <div class="pp-container-shared">
    <div class="pp-swh-concept__grid">

      <div class="pp-swh-concept__side">
        <figure class="pp-swh-media pp-image-container-shared pp-swh-concept__figure">
          <span class="pp-swh-tag">OLD WORLD &times; MODERN</span>
          <img src="<?php echo esc_url(home_url('/wp-content/uploads/spice-world-hotpot/Spice-World-Hot-Pot-03-1.jpg')); ?>" alt="<?php echo esc_attr__('Đội ngũ nhân viên Spice World Hot Pot chụp ảnh ngày khai trương', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Đội ngũ nhân viên Spice World Hot Pot Việt Nam trong ngày khai trương bên phông nền thương hiệu đỏ rực', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

      <div class="pp-swh-concept__main">
        <header class="pp-swh-concept__header">
          <div class="pp-swh-badge">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            <?php echo esc_html__('Ý tưởng thiết kế', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-swh-concept__title">
            <?php echo esc_html__('Thiết kế của nhà hàng Spice World HotPot', 'saigonhoreca'); ?>
            <span class="pp-swh-concept__title-gold"><?php echo esc_html__('Nơi Thế Giới Cổ Điển Gặp Gỡ Hiện Đại', 'saigonhoreca'); ?></span>
          </h2>
          <div class="pp-swh-divider" aria-hidden="true"></div>
        </header>

        <div class="pp-swh-concept__body">
          <p><?php echo esc_html__('Theo truyền thống, lẩu Sichuan thường được ưa chuộng vào những tháng mùa đông tại Trung Quốc để làm ấm cơ thể. Tuy nhiên, tại Việt Nam, sự phổ biến của lẩu đã vượt qua giới hạn của thời tiết, khi người Việt có thể thưởng thức lẩu bất cứ lúc nào họ mong muốn.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Nhà hàng có hai tầng với 180 chỗ ngồi và ba phòng riêng, mỗi phòng phục vụ 10 khách. Màu chủ đạo là màu đỏ kết hợp với màu đen và trắng, tạo nên một sự kết hợp giữa thế giới cũ và hiện đại.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Một nửa không gian đại diện cho thế giới hiện đại với trần nhà bằng thép màu đen, tường kính trong suốt và thiết kế nội thất tối giản trong màu đen và trắng. Nửa còn lại thể hiện thế giới cũ với tường được trang trí bằng các bức tranh của những nhân vật lịch sử, chủ yếu là màu đỏ, tạo nên một không khí hấp dẫn và bí ẩn.', 'saigonhoreca'); ?></p>
        </div>

        <dl class="pp-swh-concept__stats">
          <div class="pp-swh-concept__stat">
            <dt class="pp-swh-concept__stat-value">02</dt>
            <dd class="pp-swh-concept__stat-label"><?php echo esc_html__('Tầng phục vụ', 'saigonhoreca'); ?></dd>
          </div>
          <div class="pp-swh-concept__stat">
            <dt class="pp-swh-concept__stat-value">180</dt>
            <dd class="pp-swh-concept__stat-label"><?php echo esc_html__('Chỗ ngồi', 'saigonhoreca'); ?></dd>
          </div>
          <div class="pp-swh-concept__stat">
            <dt class="pp-swh-concept__stat-value">03</dt>
            <dd class="pp-swh-concept__stat-label"><?php echo esc_html__('Phòng riêng', 'saigonhoreca'); ?></dd>
          </div>
        </dl>
      </div>

    </div>
  </div>
</section>
