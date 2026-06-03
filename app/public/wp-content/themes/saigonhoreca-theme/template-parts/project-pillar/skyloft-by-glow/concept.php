<?php
/**
 * Project Pillar — skyloft-by-glow
 * Section #3: concept — split reverse layout with image + drinks description.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-sky pp-section-sky--alt pp-concept-sky scroll-reveal">
  <div class="pp-sky-ambient-glow pp-sky-ambient-glow--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-concept-sky__layout">
      <div class="pp-concept-sky__visual scroll-reveal">
        <div class="pp-image-container-shared pp-frame-sky pp-frame-sky--cocktail">
          <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-cocktail-hoang-hon-tren-ban-kinh.webp'); ?>" alt="<?php echo esc_attr__('Cocktail signature Skyloft by Glow trên bàn kính lúc hoàng hôn', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bộ đôi cocktail thủ công đặt trên mặt bàn kính phản chiếu ánh hoàng hôn, mở đầu trải nghiệm rooftop bằng hương vị thanh lịch và giàu cảm xúc.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-concept-sky__glass-card">
        <span class="pp-concept-sky__tag"><?php echo esc_html__('Chương trình đồ uống', 'saigonhoreca'); ?></span>
        <div class="pp-sky-ornament pp-sky-ornament--right" aria-hidden="true"></div>
        <div class="pp-text-sky__divider pp-text-sky__divider--dots" aria-hidden="true"></div>
        <h2 class="pp-text-sky__title"><?php echo esc_html__('Cocktail đặc trưng (Signature Cocktails)', 'saigonhoreca'); ?></h2>
        <div class="pp-text-sky__body">
          <p><?php echo esc_html__('Phong cách đặc trưng của đồ uống tại Skyloft là sự cân bằng thủ công tinh tế, kết hợp các công thức cổ điển với hương vị độc đáo và mới lạ để tạo nên trải nghiệm vị giác khác biệt.', 'saigonhoreca'); ?></p>
        </div>
        <div class="pp-concept-sky__signals" aria-label="<?php echo esc_attr__('Đặc tính trải nghiệm cocktail', 'saigonhoreca'); ?>">
          <span><?php echo esc_html__('Hương khói tự nhiên (Aroma smoke)', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Ly hoàng hôn (Sunset glass)', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Phục vụ đêm cao điểm (Night service)', 'saigonhoreca'); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>

