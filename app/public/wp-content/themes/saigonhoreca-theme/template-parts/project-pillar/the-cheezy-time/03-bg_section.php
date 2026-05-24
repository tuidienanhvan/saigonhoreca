<?php
/**
 * Project Pillar — the-cheezy-time
 * Section #3: bg_section
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg" style="background-image:url('<?php echo sgh_img('2026/03/du-an-thecheezytime.jpg'); ?>');">
  <div class="pp-section-bg__overlay" aria-hidden="true"></div>
  <div class="pp-section-bg__content">
    <span class="pp-text__divider pp-text__divider--center" aria-hidden="true"></span>
    <h2 class="pp-text__title"><?php echo esc_html__('Khi concept có chiều sâu, căn bếp không thể là phần phụ', 'saigonhoreca'); ?></h2>
    <div class="pp-text__body">
      <p><?php echo esc_html__('Xuất phát từ tinh thần "dòng chảy" xuyên suốt concept The Cheezy Time, Saigon Horeca tiếp cận dự án này không theo cách thông thường của một đơn vị cung cấp thiết bị bếp công nghiệp. Thay vào đó, toàn bộ hạng mục được triển khai như một quá trình xây dựng giải pháp bếp đồng hành cùng câu chuyện của nhà hàng, ngay từ giai đoạn lên ý tưởng ban đầu.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
