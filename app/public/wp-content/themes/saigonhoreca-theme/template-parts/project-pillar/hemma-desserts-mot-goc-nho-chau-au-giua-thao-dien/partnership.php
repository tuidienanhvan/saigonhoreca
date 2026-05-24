<?php
/**
 * Project Pillar — hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien
 * Section #4: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-hmd pp-split-hmd--reverse">
      <div class="pp-split-hmd__body">
        <span class="pp-text-hmd__divider" aria-hidden="true"></span>
        <h2 class="pp-text-hmd__title"><?php echo esc_html__('Điểm nhấn mạnh mẽ trong thiết kế của Saigon Horeca', 'saigonhoreca'); ?></h2>
        <div class="pp-text-hmd__body">
      <p><?php echo esc_html__('Đặc biệt, Saigon Horeca đã ứng dụng hệ thống tủ ray trượt thông minh thay cho tủ mở truyền thống. Giải pháp này giúp:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Nhờ sự sắp đặt logic này, Hemma Desserts có thể vận hành hai quy trình độc lập (bánh & cà phê) trong cùng một không gian – vừa hiệu quả, vừa an toàn, vừa thẩm mỹ.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-hmd__media">
        <img src="<?php echo sgh_img('2025/11/hemma-desserts-10.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
