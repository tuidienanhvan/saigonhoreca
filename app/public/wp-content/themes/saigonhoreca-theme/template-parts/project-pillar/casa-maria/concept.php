<?php
/**
 * Project Pillar — casa-maria
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-cma">
      <div class="pp-split-cma__body">
        <span class="pp-text-cma__divider" aria-hidden="true"></span>
        <h2 class="pp-text-cma__title"><?php echo esc_html__('Quầy bar Wine & Cafe – Trái tim cảm xúc của Casa Maria', 'saigonhoreca'); ?></h2>
        <div class="pp-text-cma__body">
      <p><?php echo esc_html__('Với Casa Maria, quầy bar không chỉ là nơi pha chế, mà là điểm chạm trải nghiệm: wine tasting, cafe, trò chuyện và tương tác.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca triển khai quầy bar với tư duy mở, nơi bartender không chỉ pha chế mà còn giao tiếp, kể chuyện và dẫn dắt trải nghiệm. Hệ thống thiết bị bar, khu bảo quản rượu vang và không gian pha chế cafe được thiết kế để đảm bảo độ ổn định về chất lượng, đồng thời giữ được sự mềm mại trong vận hành. Ly vang luôn đúng nhiệt độ, cafe luôn nhất quán, nhưng không làm mất đi sự thư thả vốn có của không gian tapas bar.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca triển khai hệ thống quầy bar với tư duy rất rõ ràng:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Mọi chi tiết kỹ thuật đều phục vụ cho một mục tiêu: để rượu, cafe và con người kết nối với nhau một cách tự nhiên.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-cma__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-2.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
