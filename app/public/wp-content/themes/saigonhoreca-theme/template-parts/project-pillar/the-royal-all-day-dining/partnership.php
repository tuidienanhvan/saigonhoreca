<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #4: bg_section
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-trd" style="background-image:url('<?php echo sgh_img('2025/05/the-royal-sgh-6.jpg'); ?>');">
  <div class="pp-section-bg-trd__overlay" aria-hidden="true"></div>
  <div class="pp-section-bg-trd__content">
    <span class="pp-text-trd__divider pp-text-trd__divider--center" aria-hidden="true"></span>
    <h2 class="pp-text-trd__title"><?php echo esc_html__('The Royal – Dự án bếp công nghiệp cao cấp do Saigon Horeca thực hiện', 'saigonhoreca'); ?></h2>
    <div class="pp-text-trd__body">
      <p><?php echo wp_kses_post(sprintf(esc_html__('Ngay từ cái nhìn đầu tiên, không gian bếp tại The Royal gây ấn tượng mạnh với sự %1$s – từ khu sơ chế, chế biến, nấu nướng đến khu ra món. Tất cả các thiết bị đều được Saigon Horeca sử dụng chất liệu inox cao cấp, mang lại sự %2$s và đảm bảo vệ sinh an toàn thực phẩm tuyệt đối.', 'saigonhoreca'), '<strong>' . esc_html__('bố trí khoa học, tối ưu hóa quy trình vận hành một chiều', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('bền bỉ, dễ dàng vệ sinh', 'saigonhoreca') . '</strong>')); ?></p>
    </div>
  </div>
</section>
