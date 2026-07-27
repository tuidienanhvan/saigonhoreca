<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #4: partnership
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-swh-partner">
  <div class="pp-swh-partner__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-swh-partner__grid">

      <div class="pp-swh-partner__main">
        <header class="pp-swh-partner__header">
          <div class="pp-swh-badge">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            <?php echo esc_html__('Đối tác', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-swh-partner__title">
            <?php echo esc_html__('Saigon Horeca & Spice World', 'saigonhoreca'); ?>
            <span class="pp-swh-partner__title-gold"><?php echo esc_html__('Hợp Tác Chiến Lược', 'saigonhoreca'); ?></span>
          </h2>
          <div class="pp-swh-divider" aria-hidden="true"></div>
        </header>

        <div class="pp-swh-glass-card pp-swh-partner__card">
          <p class="pp-swh-partner__lead"><?php echo esc_html__('Saigon Horeca tự hào là đối tác chiến lược của Spice World Hotpot tại Việt Nam.', 'saigonhoreca'); ?></p>
          <div class="pp-swh-partner__body">
            <p><?php echo esc_html__('Chúng tôi đã cung cấp giải pháp bếp toàn diện, từ tư vấn thiết kế đến thi công và bảo trì.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Với kinh nghiệm hơn 10 năm trong lĩnh vực bếp công nghiệp, Saigon Horeca hiểu rõ nhu cầu đặc thù của nhà hàng lẩu và đã tạo ra một hệ thống bếp hiệu quả, an toàn và thẩm mỹ.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Sự hợp tác này không chỉ mang lại giải pháp kỹ thuật mà còn góp phần nâng cao trải nghiệm ẩm thực cho thực khách tại Spice World Hotpot.', 'saigonhoreca'); ?></p>
          </div>

          <dl class="pp-swh-partner__stats">
            <div class="pp-swh-partner__stat">
              <dt class="pp-swh-partner__stat-value">100%</dt>
              <dd class="pp-swh-partner__stat-label"><?php echo esc_html__('Giải pháp bếp toàn diện', 'saigonhoreca'); ?></dd>
            </div>
            <div class="pp-swh-partner__stat">
              <dt class="pp-swh-partner__stat-value">10+</dt>
              <dd class="pp-swh-partner__stat-label"><?php echo esc_html__('Năm kinh nghiệm bếp công nghiệp', 'saigonhoreca'); ?></dd>
            </div>
          </dl>
        </div>
      </div>

      <div class="pp-swh-partner__side">
        <figure class="pp-swh-media pp-image-container-shared pp-swh-partner__figure">
          <span class="pp-swh-tag">PARTNERSHIP</span>
          <img src="<?php echo esc_url(sgh_img('spice-world-hotpot/spice-world-hotpot-mat-tien-nha-hang-ban-dem.jpeg')); ?>" alt="<?php echo esc_attr__('Mặt tiền nhà hàng Spice World Hotpot sáng đèn về đêm', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Mặt tiền nhà hàng Spice World Hotpot rực sáng đèn lồng đỏ trong ngày khai trương tại Quận 5', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

    </div>
  </div>
</section>
