<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #4: partnership — đối tác chiến lược + dashboard chỉ số + ảnh xếp lớp.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-moa pp-moa-partnership">
  <div class="pp-watermark-wheat-moa" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
      <path d="M50 95 V35" stroke-linecap="round"/>
      <path d="M50 40 C40 36 36 28 38 20 C46 22 52 28 50 40Z M50 40 C60 36 64 28 62 20 C54 22 48 28 50 40Z" stroke-linejoin="round"/>
      <path d="M50 55 C41 51 37 44 39 36 M50 55 C59 51 63 44 61 36" stroke-linecap="round"/>
    </svg>
  </div>
  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-moa">

      <div class="pp-grid-moa__text moa-partnership__main">
        <div class="moa-partnership__card">
          <header class="moa-partnership__header">
            <div class="pp-badge-moa">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Đối tác chiến lược', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-moa__title">
              <?php echo esc_html__('Hệ thống hút khói – xử lý mùi giữa phố đi bộ', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-moa__divider" aria-hidden="true"></div>
          </header>

          <div class="moa-partnership__body">
            <p class="moa-partnership__lead">
              <?php echo esc_html__('Với mô hình bếp mở và vị trí đắc địa ngay khu phố đi bộ Nguyễn Huệ, bài toán lớn nhất của Moa Moa không chỉ là nấu ngon, mà còn là kiểm soát mùi và khói.', 'saigonhoreca'); ?>
            </p>
            <p style="margin-bottom: 0;"><?php echo esc_html__('Saigon Horeca đã cung cấp và lắp đặt hệ thống chụp hút và quạt hút chuyên dụng, được tính toán kỹ lưỡng theo công suất thiết bị và đặc thù bếp Âu – bếp nướng than củi. Hệ thống này đảm bảo không gian bếp luôn thông thoáng, không ảnh hưởng đến trải nghiệm thực khách, đồng thời tuân thủ các tiêu chuẩn an toàn – vận hành trong khu vực trung tâm đông đúc.', 'saigonhoreca'); ?></p>
          </div>

          <div class="moa-partnership__stats">
            <div class="moa-partnership__stat-item">
              <span class="moa-partnership__stat-value">OPEN</span>
              <span class="moa-partnership__stat-label"><?php echo esc_html__('Bếp mở Italian kitchen', 'saigonhoreca'); ?></span>
            </div>
            <div class="moa-partnership__stat-item">
              <span class="moa-partnership__stat-value">Q.1</span>
              <span class="moa-partnership__stat-label"><?php echo esc_html__('40E Ngô Đức Kế, Bến Nghé', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="pp-grid-moa__media moa-partnership__side">
        <div class="pp-image-container-shared moa-partnership__image-container moa-partnership__image-container--main">
          <img src="<?php echo sgh_img('moa-moa/moa-moa-mi-pappardelle-nam-long-do-trung.webp'); ?>" alt="<?php echo esc_attr__('Pappardelle thủ công – đặc trưng ẩm thực Ý tại Moa Moa', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Pappardelle sợi dẹt bản to sốt bơ nâu, nấm và lòng đỏ trứng – tinh hoa pasta tươi thủ công tại Moa Moa', 'saigonhoreca'); ?></div>
        </div>

        <div class="pp-image-container-shared moa-partnership__image-container moa-partnership__image-container--floating">
          <img src="<?php echo sgh_img('moa-moa/moa-moa-cocktail-aperol-spritz-signature.webp'); ?>" alt="<?php echo esc_attr__('Đồ uống signature phục vụ tại Moa Moa Pasta Club', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Aperol Spritz cam mát lạnh bên ly nước ép điểm lá dứa – bộ đôi đồ uống signature tại Moa Moa', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
