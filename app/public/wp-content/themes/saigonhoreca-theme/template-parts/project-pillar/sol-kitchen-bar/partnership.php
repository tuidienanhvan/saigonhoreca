<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #4: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-skb pp-text-skb--center">
      <div class="pp-text-skb__body">
      <p><?php echo esc_html__('Hệ thống chụp hút khói dài 8m được thiết kế để hút khói và cấp gió tươi song song trong khu vực nấu. Hệ thống này giúp luồng khí lưu thông liên tục, tạo không gian thoải mái cho các đầu bếp làm việc và hạn chế tình trạng bí khí, đảm bảo môi trường làm việc trong lành và thoáng đãng. Trong khu vực rửa, một hệ thống chụp hút khói dài 2m cũng được lắp đặt để hút khói và cấp gió tươi, đảm bảo khu vực này luôn sạch sẽ và không bị ẩm mốc.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Chụp hút 2m:', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-skb pp-gallery-skb--cols-3" style="margin-top:2rem;">
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/chup-hut-8m-1-1.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/chup-hut-8m-2-1.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/chup-hut-2m-1.jpg'); ?>" alt="<?php echo esc_attr__('Dịch vụ sửa chữa, bảo trì hệ thống bếp công nghiệp bởi Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
