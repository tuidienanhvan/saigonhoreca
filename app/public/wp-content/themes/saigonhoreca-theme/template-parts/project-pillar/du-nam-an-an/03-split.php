<?php
/**
 * Project Pillar — du-nam-an-an
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
        <h2 class="pp-text__title"><?php echo esc_html__('Thiết bị bếp – Lựa chọn vì sự ổn định lâu dài', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Thay vì chạy theo số lượng, Saigon Horeca tập trung vào những thiết bị cốt lõi, vận hành ổn định và phù hợp với đặc thù Nursing Home Kitchen.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Hệ bếp Âu 6 họng điện được lựa chọn nhằm đảm bảo khả năng kiểm soát nhiệt chính xác, giảm thiểu rủi ro cháy nổ so với bếp gas, đồng thời phù hợp với môi trường vận hành liên tục.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Hệ 4 tủ lạnh đứng 4 cánh đóng vai trò trung tâm trong việc bảo quản và lưu trữ thực phẩm, nguyên liệu theo từng nhóm riêng biệt, giúp kiểm soát chất lượng và hạn sử dụng chặt chẽ hơn.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Máy rửa chén dạng âm quầy được tích hợp tinh gọn trong layout, giúp tối ưu diện tích nhưng vẫn đảm bảo tiêu chuẩn làm sạch, tiết kiệm thời gian cho đội ngũ nhân viên chăm sóc.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-2.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
