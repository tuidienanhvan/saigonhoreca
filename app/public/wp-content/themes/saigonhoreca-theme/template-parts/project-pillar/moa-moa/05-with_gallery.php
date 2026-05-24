<?php
/**
 * Project Pillar — moa-moa
 * Section #5: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text pp-text--center">
      <span class="pp-text__divider pp-text__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text__title"><?php echo esc_html__('Thiết bị lạnh & lưu trữ: Giữ trọn độ tươi cho từng nguyên liệu', 'saigonhoreca'); ?></h2>
      <div class="pp-text__body">
      <p><?php echo esc_html__('Ẩm thực Ý đề cao nguyên liệu tươi và xử lý nhanh. Vì vậy, Moa Moa sử dụng tủ đông – tủ mát dạng ngăn kéo, cho phép đầu bếp lấy nguyên liệu ngay tại vị trí thao tác mà không phá vỡ nhịp làm việc. Đây là chi tiết nhỏ nhưng thể hiện rõ tư duy thiết kế bếp theo hướng lấy con người và quy trình làm trung tâm.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Dự án Moa Moa không đơn thuần là việc cung cấp thiết bị. Đó là quá trình tư vấn – thiết kế – lắp đặt dựa trên sự thấu hiểu mô hình pasta restaurant, văn hoá ẩm thực Ý và áp lực vận hành thực tế tại trung tâm TP.HCM.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Với Moa Moa, Saigon Horeca tiếp tục khẳng định vai trò của mình: không chỉ xây bếp, mà kiến tạo nền tảng để ẩm thực được kể đúng câu chuyện của nó.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery pp-gallery--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2023/12/bi-quyet-tao-thuc-don-nha-hang.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
