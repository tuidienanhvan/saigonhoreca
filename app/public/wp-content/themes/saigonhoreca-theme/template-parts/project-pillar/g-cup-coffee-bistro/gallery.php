<?php
/**
 * Project Pillar — g-cup-coffee-bistro
 * Section #6: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-gcb pp-split-gcb--reverse">
      <div class="pp-split-gcb__body">
        <div class="pp-text-gcb__body">
      <p><?php echo esc_html__('Tại G-Cup Bistro, mọi thứ đều được Saigon Horeca chọn lựa kỹ lưỡng về kích thước, công suất, và chất lượng – phù hợp với thực đơn, tần suất vận hành và gu thẩm mỹ của quán, các thiết bị được lắp đặt gồm:', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-gcb__media">
        <img src="<?php echo sgh_img('2025/05/du-an-g-cup-coffee-bistro-14.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
