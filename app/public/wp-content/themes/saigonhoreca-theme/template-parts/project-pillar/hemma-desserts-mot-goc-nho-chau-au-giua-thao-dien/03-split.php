<?php
/**
 * Project Pillar — hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split">
      <div class="pp-split__body">
        <span class="pp-text__divider" aria-hidden="true"></span>
        <h2 class="pp-text__title"><?php echo esc_html__('Giải pháp của Saigon Horeca', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Ngay từ khâu khảo sát, đội ngũ kỹ sư Saigon Horeca đã nhận thấy rằng: để biến không gian 11m² thành một bếp bánh kiêm bar café hiệu quả, không thể dùng thiết bị tiêu chuẩn sẵn có, mà phải custom toàn bộ thiết bị inox theo kích thước thực tế.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Từng centimet được tính toán kỹ lưỡng:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Khu rửa & lưu trữ dụng cụ: máy rửa chén và kệ inox 3 tầng giúp tối ưu không gian thẳng đứng, hạn chế bề mặt tiếp xúc – dễ vệ sinh, sạch sẽ tuyệt đối.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/11/hemma-desserts-7.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
