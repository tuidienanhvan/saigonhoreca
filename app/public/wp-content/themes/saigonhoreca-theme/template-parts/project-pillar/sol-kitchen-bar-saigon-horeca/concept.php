<?php
/**
 * Project Pillar — sol-kitchen-bar-saigon-horeca
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-skh">
      <div class="pp-split-skh__body">
        <span class="pp-text-skh__divider" aria-hidden="true"></span>
        <h2 class="pp-text-skh__title"><?php echo esc_html__('Ý tưởng thiết kế SOL Kitchen &amp; Bar', 'saigonhoreca'); ?></h2>
        <div class="pp-text-skh__body">
      <p><?php echo esc_html__('Bước vào không gian nhà hàng với trần cao, trang trí bằng những chiếc đèn tre tạo ánh sáng ấm áp, rực rỡ, kết hợp với những mảng xanh từ những chậu cây tí hon, tạo nên một không gian hấp dẫn và thu hút về mặt thị giác. Trải nghiệm này còn được nâng cao hơn bởi đội ngũ nhân viên chu đáo, chuyên nghiệp. Hơn thế, đầu bếp-chủ nhân Adrian lại rất kinh nghiệm cùng một đội ngũ bếp đầy sáng tạo mang hương vị độc đáo.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-skh__media">
        <img src="<?php echo sgh_img('2024/01/Sol-kitchen-bar-7.jpeg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="2047" height="1365">
      </div>
    </div>
  </div>
</section>
