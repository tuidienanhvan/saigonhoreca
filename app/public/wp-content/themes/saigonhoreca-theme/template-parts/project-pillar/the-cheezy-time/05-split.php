<?php
/**
 * Project Pillar — the-cheezy-time
 * Section #5: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split pp-split--reverse">
      <div class="pp-split__body">
        <div class="pp-text__body">
      <p><?php echo esc_html__('Trong cấu trúc bếp, khu bếp Âu và khu bếp pizza được tổ chức để vừa tách biệt về nhiệt và công năng, vừa kết nối mạch lạc trong luồng vận hành chung. Cấu hình thiết bị được lựa chọn nhằm đảm bảo sự ổn định và tính linh hoạt, bao gồm:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Lò pizza lò gạch không chỉ là một thiết bị nấu. Đây là lựa chọn mang tính định hướng trải nghiệm, giúp những chiếc pizza và món mỳ đút lò tại The Cheezy Time có được hương khói tự nhiên, lớp vỏ đặc trưng và tinh thần ẩm thực Ý nguyên bản.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Trong tổng thể concept, lò gạch đóng vai trò như "linh hồn" của khu bếp, nơi hương vị và cảm xúc giao thoa rõ rệt nhất.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2026/03/du-an-thecheezytime-7.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
      </div>
    </div>
  </div>
</section>
