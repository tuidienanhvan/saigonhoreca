<?php
/**
 * Project Pillar — du-nam-an-an
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
        <h2 class="pp-text__title"><?php echo esc_html__('Hệ thống xử lý khói & mùi – Yếu tố không thể thỏa hiệp', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Trong môi trường Nursing Home, khói và mùi không đơn thuần là vấn đề kỹ thuật, mà là yếu tố ảnh hưởng trực tiếp đến chất lượng sống của người cao tuổi.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca triển khai hệ thống xử lý khói và mùi được tính toán kỹ lưỡng theo công suất thực tế, đảm bảo không gian bếp luôn thông thoáng, hạn chế ám mùi sang khu vực sinh hoạt, đồng thời tạo môi trường làm việc dễ chịu cho nhân sự vận hành.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-4.jpg'); ?>" alt="<?php echo esc_attr__('Bếp á đôi công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
