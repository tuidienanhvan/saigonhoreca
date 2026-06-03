<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #4: partnership
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-rf pp-rf-partnership">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M15 20 H45 M25 20 V80 M15 80 H45 M40 20 H70 M50 20 V80 M40 80 H70 M65 20 H95 M75 20 V80 M65 80 H95" stroke-linecap="round"/>
    </svg>
  </div>

  <!-- Họa tiết sóng biển Nhật Bản (Seigaiha) chìm nghệ thuật -->
  <div class="pp-watermark-waves-rf" aria-hidden="true">
    <svg viewBox="0 0 100 50" fill="none" stroke="currentColor" stroke-width="0.8">
      <path d="M0 50 A 25 25 0 0 1 50 50 M10 50 A 15 15 0 0 1 40 50 M20 50 A 5 5 0 0 1 30 50" stroke-linecap="round"/>
      <path d="M50 50 A 25 25 0 0 1 100 50 M60 50 A 15 15 0 0 1 90 50 M70 50 A 5 5 0 0 1 80 50" stroke-linecap="round"/>
      <path d="M25 25 A 25 25 0 0 1 75 25 M35 25 A 15 15 0 0 1 65 25 M45 25 A 5 5 0 0 1 55 25" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-rf">
      
      <div class="pp-grid-12-rf__text--cols-5 rkf-partnership__main">
        <div class="pp-glass-card-roka rkf-partnership__glass-card">
          
          <header class="rkf-partnership__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Đối tác chiến lược', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-rf__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Kiến tạo không gian ẩm thực thượng hạng', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>

          <div class="rkf-partnership__body" style="font-size: 0.98rem; line-height: 1.8; color: var(--bc2);">
            <p class="rkf-partnership__lead" style="font-size: 1.15rem; color: var(--bc); font-weight: 600; margin-bottom: 1rem;">
              <?php echo esc_html__('Sự bất ngờ đầy say đắm trong từng món ăn.', 'saigonhoreca'); ?>
            </p>
            <p><?php echo esc_html__('Với phong cách Omakase, thực khách sẽ được tận hưởng cảm giác hồi hộp chờ đợi từng món ăn và thưởng thức những hương vị tinh tế, bất ngờ mà đầu bếp đã dồn hết tâm huyết vào.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Saigon Horeca vinh dự đồng hành cùng Roka Fella để thiết kế và lắp đặt hệ thống quầy Sushi hiện đại nhất, giữ trọn độ tươi ngon hoàn mỹ cho từng nguyên liệu.', 'saigonhoreca'); ?></p>
          </div>

          <div class="rkf-partnership__stats">
            <div class="rkf-partnership__stat-item">
              <span class="rkf-partnership__stat-value">100%</span>
              <span class="rkf-partnership__stat-label"><?php echo esc_html__('Nhập khẩu tươi ngon', 'saigonhoreca'); ?></span>
            </div>
            <div class="rkf-partnership__stat-item">
              <span class="rkf-partnership__stat-value">OMAKASE</span>
              <span class="rkf-partnership__stat-label"><?php echo esc_html__('Tiêu chuẩn Nhật Bản', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-rf__media--cols-7 rkf-partnership__side">
        <div class="pp-image-container-shared rkf-partnership__image-container rkf-partnership__image-container--main">
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('roka-fella/roka-fella-quay-sushi-omakase.jpg'); ?>" alt="<?php echo esc_attr__('Roka Fella Sushi Station', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Đầu bếp tỉ mỉ đặt lát cá hồi tươi lên miếng sushi nigiri trên quầy gỗ', 'saigonhoreca'); ?></div>
        </div>

        <div class="pp-image-container-shared rkf-partnership__image-container rkf-partnership__image-container--floating">
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="rkf-partnership__image-clip">
            <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-nigiri-ca-ngu.jpg'); ?>" alt="<?php echo esc_attr__('Roka Fella Omakase Dish', 'saigonhoreca'); ?>" loading="lazy">
          </div>
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khay gỗ lớn đựng Set sushi nigiri thập cẩm cao cấp bao gồm bụng cá ngừ Otoro, tôm ngọt và trứng cá hồi', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
