<?php
/**
 * Project Pillar - g-cup-coffee-bistro
 * Section #6: gallery - technical editorial photo board.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-gcb pp-gallery-section-gcb pp-section-gcb--alt">
  <div class="pp-gcb-gallery-blueprint" aria-hidden="true">
    <svg viewBox="0 0 1440 860" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <pattern id="gcb-gallery-grid" width="72" height="72" patternUnits="userSpaceOnUse">
          <path d="M72 0H0V72" stroke="currentColor" stroke-width="0.7" opacity="0.12"/>
        </pattern>
        <linearGradient id="gcb-gallery-line" x1="160" y1="150" x2="1240" y2="720" gradientUnits="userSpaceOnUse">
          <stop stop-color="currentColor" stop-opacity="0"/>
          <stop offset="0.22" stop-color="currentColor" stop-opacity="0.55"/>
          <stop offset="0.78" stop-color="currentColor" stop-opacity="0.55"/>
          <stop offset="1" stop-color="currentColor" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <rect width="1440" height="860" fill="url(#gcb-gallery-grid)"/>
      <path class="pp-gcb-gallery-blueprint__route" d="M128 578C306 504 430 453 574 384C750 299 900 218 1274 236" stroke="url(#gcb-gallery-line)" stroke-width="1.2" stroke-dasharray="18 16"/>
      <path class="pp-gcb-gallery-blueprint__route pp-gcb-gallery-blueprint__route--lower" d="M228 162C420 240 545 334 690 469C838 607 1004 679 1246 648" stroke="url(#gcb-gallery-line)" stroke-width="1" stroke-dasharray="8 14"/>
      <circle cx="226" cy="162" r="36" stroke="currentColor" stroke-width="1" opacity="0.18"/>
      <circle cx="226" cy="162" r="5" fill="currentColor" opacity="0.5"/>
      <circle cx="690" cy="469" r="52" stroke="currentColor" stroke-width="1" stroke-dasharray="4 8" opacity="0.16"/>
      <circle cx="1274" cy="236" r="42" stroke="currentColor" stroke-width="1" opacity="0.16"/>
    </svg>
  </div>

  <div class="pp-container-shared">
    <div class="pp-gcb-gallery-header">
      <span class="pp-gcb-gallery-kicker"><?php echo esc_html__('SITE PHOTO BOARD / G-CUP', 'saigonhoreca'); ?></span>
      <h2 class="pp-text-gcb__title"><?php echo esc_html__('Hình ảnh thực tế dự án G-Cup Coffee & Bistro', 'saigonhoreca'); ?></h2>
    </div>

    <div class="pp-gcb-gallery-board">
      <figure class="pp-gcb-gallery-card pp-gcb-gallery-card--left">
        <div class="pp-gcb-gallery-card__media">
          <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-ban-dong-cong-nghiep-hoshizaki.webp'); ?>" alt="<?php echo esc_attr__('Bàn đông công nghiệp Hoshizaki hiển thị -16 độ', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        </div>
        <figcaption class="pp-gcb-gallery-card__caption">
          <span>01 / Hoshizaki Freezer</span>
          <?php echo esc_html__('Bàn đông công nghiệp Hoshizaki bằng inox cao cấp tích hợp màn hình led báo nhiệt độ âm chuẩn xác.', 'saigonhoreca'); ?>
        </figcaption>
      </figure>

      <figure class="pp-gcb-gallery-card pp-gcb-gallery-card--right">
        <div class="pp-gcb-gallery-card__media">
          <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-ban-mat-inox-duoi-quay-bar.webp'); ?>" alt="<?php echo esc_attr__('Bàn mát dưới quầy bar G-Cup Bistro', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        </div>
        <figcaption class="pp-gcb-gallery-card__caption">
          <span>02 / Underbar Fridge</span>
          <?php echo esc_html__('Hệ thống bàn mát dưới quầy bar (under-counter fridge) với thiết kế gọn gàng, giúp bảo quản lạnh sữa và nguyên liệu.', 'saigonhoreca'); ?>
        </figcaption>
      </figure>
    </div>
  </div>
</section>

<?php /* T-034: merged from related.php (cũ section 7) */ ?>
<section class="pp__section">
  <div class="pp-container-shared">
    <div class="pp-text-gcb pp-text-gcb--center scroll-reveal">
      <span class="pp-text-gcb__divider pp-text-gcb__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-gcb__title"><?php echo esc_html__('Kết luận', 'saigonhoreca'); ?></h2>
      <div class="pp-text-gcb__body">
        <p><?php echo esc_html__('Đây là giải pháp được lựa chọn sau khi phân tích kỹ điều kiện công trình và định hướng thiết kế không gian mở của quán đảm bảo không ảnh hưởng đến không gian chung, vừa an toàn, vừa thẩm mỹ – đúng tiêu chuẩn của các khu phức hợp cao cấp như Metropole Thủ Thiêm.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Với G-Cup, mỗi không gian đều mang theo một câu chuyện riêng. Và Saigon Horeca là người vinh dự được góp phần kể nên câu chuyện đó – bằng những giải pháp kỹ thuật tinh gọn, thông minh và linh hoạt, phù hợp với từng mét vuông mặt bằng.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
