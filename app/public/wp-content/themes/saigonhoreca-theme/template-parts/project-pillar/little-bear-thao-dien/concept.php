<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-lbt">
      <div class="pp-split-lbt__body">
        <span class="pp-text-lbt__divider" aria-hidden="true"></span>
        <h2 class="pp-text-lbt__title"><?php echo esc_html__('Thiết kế của Little Bear', 'saigonhoreca'); ?></h2>
        <div class="pp-text-lbt__body">
      <p><?php echo esc_html__('Bếp của Little Bear Thảo Điền mang phong cách kết hợp giữa sự tối giản, hiện đại và tinh tế. Saigon Horeca chủ yếu sử dụng vật liệu thép không gỉ, biến nơi đây thành một không gian làm việc chuyên nghiệp và tiện nghi cho các đầu bếp.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-lbt__media">
        <img src="<?php echo sgh_img('2024/01/Little-bear-00.jpeg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
