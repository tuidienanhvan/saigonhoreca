<?php
/**
 * Project Pillar — casa-maria
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
        <h2 class="pp-text__title"><?php echo esc_html__('Giải pháp bảo quản – Trữ đông – Chuẩn mực cho nguyên liệu Âu', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Ẩm thực Tây Ban Nha đề cao chất lượng nguyên liệu. Vì vậy, hệ thống bảo quản không chỉ là "kho lạnh", mà là một phần của chất lượng món ăn.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Trong dự án Casa Maria, Saigon Horeca triển khai:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Đây là lớp "kỹ thuật thầm lặng" nhưng quyết định trực tiếp đến độ tinh tế của từng đĩa tapas.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-4.jpg'); ?>" alt="<?php echo esc_attr__('Bếp á đôi công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
