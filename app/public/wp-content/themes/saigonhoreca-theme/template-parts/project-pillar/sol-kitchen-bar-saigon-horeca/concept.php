<?php
/**
 * Project Pillar — sol-kitchen-bar-saigon-horeca
 * Section #3: concept — editorial magazine layout, glass card overlapping a
 * tall framed image, numbered watermark + ambient glow.
 * Copy: high-ceilinged hacienda dining room (wicker lamps + succulents + Adrian).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-solshr pp-section-solshr--alt pp-concept-solshr scroll-reveal">
  <div class="pp-solshr-ambient pp-solshr-ambient--bl" aria-hidden="true"></div>
  <div class="pp-concept-solshr__watermark" aria-hidden="true">02</div>

  <div class="pp-container-shared">
    <div class="pp-concept-solshr__layout">
      <div class="pp-concept-solshr__visual scroll-reveal">
        <div class="pp-image-container-shared pp-frame-solshr pp-frame-solshr--tall">
          <img src="<?php echo esc_url(sgh_img('sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-phong-an-tuong-ruou-backlit-neon.webp')); ?>" alt="<?php echo esc_attr__('Không gian phòng ăn đông khách Sol Kitchen & Bar với tường trưng bày rượu phát sáng và bảng hiệu đèn neon chữ Sol', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Phòng ăn rộn ràng của SOL Kitchen & Bar — bức tường rượu vang chiếu sáng hậu và bảng neon chữ \'Sol\' tạo điểm nhấn thị giác ấm áp, sang trọng.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-concept-solshr__card">
        <span class="pp-concept-solshr__tag"><?php echo esc_html__('Ý tưởng thiết kế', 'saigonhoreca'); ?></span>
        <div class="pp-solshr-ornament pp-solshr-ornament--right" aria-hidden="true"></div>
        <span class="pp-text-solshr__divider" aria-hidden="true"></span>
        <h2 class="pp-text-solshr__title"><?php echo esc_html__('Ý tưởng thiết kế SOL Kitchen & Bar', 'saigonhoreca'); ?></h2>
        <div class="pp-text-solshr__body">
          <p><?php echo esc_html__('Bước vào không gian nhà hàng với trần cao, trang trí bằng những chiếc đèn tre tạo ánh sáng ấm áp, rực rỡ, kết hợp với những mảng xanh từ những chậu cây tí hon, tạo nên một không gian hấp dẫn và thu hút về mặt thị giác. Trải nghiệm này còn được nâng cao hơn bởi đội ngũ nhân viên chu đáo, chuyên nghiệp. Hơn thế, đầu bếp-chủ nhân Adrian lại rất kinh nghiệm cùng một đội ngũ bếp đầy sáng tạo mang hương vị độc đáo.', 'saigonhoreca'); ?></p>
        </div>
        <div class="pp-concept-solshr__signals" aria-label="<?php echo esc_attr__('Đặc trưng không gian', 'saigonhoreca'); ?>">
          <span><?php echo esc_html__('Đèn tre', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Mảng xanh', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Trần cao', 'saigonhoreca'); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>
