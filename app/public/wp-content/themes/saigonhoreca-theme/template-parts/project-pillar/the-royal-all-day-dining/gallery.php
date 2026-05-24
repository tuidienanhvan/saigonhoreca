<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #6: DNA split (text LEFT bullet list, image RIGHT)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-trd">
      <div class="pp-split-trd__body">
        <h2 class="pp-text-trd__title"><?php echo esc_html__('DNA của Saigon Horeca: Sự đồng bộ của hệ thống thiết bị', 'saigonhoreca'); ?></h2>
        <span class="pp-text-trd__divider" aria-hidden="true"></span>
        <div class="pp-text-trd__body">
          <ul class="pp-list-trd">
            <li><strong><?php echo esc_html__('Hệ thống lò hấp – nướng – giữ nhiệt hiện đại', 'saigonhoreca'); ?></strong></li>
            <li><strong><?php echo esc_html__('Tủ mát, tủ đông inox chuyên dụng', 'saigonhoreca'); ?></strong> <?php echo esc_html__('cho việc lưu trữ nguyên liệu tươi sống, được phối hợp hài hòa, không chỉ giúp đầu bếp đạt hiệu suất tối đa, mà còn nâng cao độ bền, an toàn và tiết kiệm năng lượng trong vận hành lâu dài.', 'saigonhoreca'); ?></li>
            <li><strong><?php echo esc_html__('Bếp chiên, nướng, bếp gas công nghiệp', 'saigonhoreca'); ?></strong> <?php echo esc_html__('với công suất lớn từ thương hiệu Fujimak', 'saigonhoreca'); ?></li>
          </ul>
          <p><?php echo wp_kses_post(sprintf(esc_html__('Ngoài ra, bề mặt inox mượt mà, gạch ốp tường xanh rêu cao cấp, hệ thống ánh sáng vàng ấm… tất cả tạo nên một bầu không khí bếp vừa %1$s, vừa %2$s.', 'saigonhoreca'), '<strong>' . esc_html__('sạch sẽ chuẩn mực', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('ấm cúng gợi cảm hứng sáng tạo', 'saigonhoreca') . '</strong>')); ?></p>
        </div>
      </div>
      <div class="pp-split-trd__media">
        <img src="<?php echo sgh_img('2025/05/the-royal-sgh-10.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
      </div>
    </div>
  </div>
</section>
