<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt pp-concept-yzo">
  <div class="pp__container">
    <div class="pp-split-yzo pp-split-yzo--concept">
      <div class="pp-split-yzo__body">
        <span class="pp-concept-yzo__kicker">Design concept</span>
        <span class="pp-text-yzo__divider" aria-hidden="true"></span>
        <h2 class="pp-text-yzo__title"><?php echo esc_html__('Ý tưởng thiết kế của Yuzu Omakase', 'saigonhoreca'); ?></h2>
        <div class="pp-text-yzo__body">
          <p><?php echo esc_html__('Dựa vào kinh nghiệm của mình, Saigon Horeca đã trực quan một thiết kế bếp công nghiệp nhằm tối ưu hóa tính chức năng, hiệu suất và thẩm mỹ. Điểm quan trọng đặt ở việc đáp ứng các nhu cầu thực tế và biến không gian bếp thành một nơi mà sáng tạo là yếu tố hàng đầu.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Thiết kế tích hợp trang thiết bị bếp từ thép không gỉ tiên tiến được tùy chỉnh phù hợp với phương pháp nấu ẩm thực đặc biệt và menu của Yuzu Omakase. Mọi khía cạnh của bố trí bếp, từ sắp xếp các vị trí làm việc đến việc lắp đặt các thiết bị chuyên biệt, đều được lên kế hoạch tỉ mỉ để tăng cường quy trình làm việc và tối đa hóa năng suất.', 'saigonhoreca'); ?></p>
        </div>
        <div class="pp-concept-yzo__notes" aria-label="<?php echo esc_attr__('Điểm nhấn thiết kế', 'saigonhoreca'); ?>">
          <span><?php echo esc_html__('Tối ưu line bếp', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Thiết bị inox tùy chỉnh', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Phục vụ omakase chuẩn nhịp', 'saigonhoreca'); ?></span>
        </div>
      </div>
      <div class="pp-split-yzo__media">
        <div class="pp-concept-yzo__mask">
          <img src="<?php echo sgh_img('2024/01/SGH-Yuzu-Omakase.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-concept-yzo__frame"></div>
        </div>
        <div class="pp-concept-yzo__caption">
          <span>Yuzu Omakase</span>
          <strong><?php echo esc_html__('Không gian bếp mở', 'saigonhoreca'); ?></strong>
        </div>
      </div>
    </div>
  </div>
</section>
