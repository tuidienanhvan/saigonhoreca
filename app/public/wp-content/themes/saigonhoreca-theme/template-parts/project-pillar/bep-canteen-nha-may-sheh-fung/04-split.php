<?php
/**
 * Project Pillar — bep-canteen-nha-may-sheh-fung
 * Section #4: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split pp-split--reverse">
      <div class="pp-split__body">
        <span class="pp-text__divider" aria-hidden="true"></span>
        <h2 class="pp-text__title"><?php echo esc_html__('Điểm nhấn mạnh mẽ trong thiết kế của Saigon Horeca', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Saigon Horeca hoàn thiện việc lắp đặt hệ thống ống gió, chụp hút khói, và cấp khí tươi cho canteen nhà máy Sheh Fung với thiết kế và triển khai theo tiêu chuẩn cao nhất, đảm bảo hai yếu tố quan trọng:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Hệ thống được thiết kế thông minh và triển khai bài bản, đảm bảo toàn bộ lượng khói sinh ra trong quá trình nấu ăn được thoát ra ngoài nhanh chóng, giữ cho không gian bếp luôn sạch sẽ và không bị ám mùi. Đồng thời, hệ thống cấp khí tươi song song cung cấp luồng không khí trong lành từ bên ngoài, giúp khu vực bếp duy trì sự thông thoáng và thoải mái tối đa, tạo điều kiện làm việc lý tưởng cho đội ngũ nhân viên.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-4.jpg'); ?>" alt="<?php echo esc_attr__('Bếp á đôi công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
