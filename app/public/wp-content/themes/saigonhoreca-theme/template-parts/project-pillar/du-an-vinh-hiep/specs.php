<?php
/**
 * Project Pillar â€” du-an-vinh-hiep
 * Section #5: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-vhp pp-section-vhp--alt scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-text-vhp pp-text-vhp--center">
      <span class="pp-text-vhp__divider pp-text-vhp__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-vhp__title"><?php echo esc_html__('Vĩnh Hiệp & Saigon Horeca – Khi bếp trở thành một phần của câu chuyện thương hiệu', 'saigonhoreca'); ?></h2>
      <div class="pp-text-vhp__body">
      <p><?php echo esc_html__('Dự án Vĩnh Hiệp không chỉ dừng lại ở việc cung cấp thiết bị bếp. Đó là quá trình Saigon Horeca cùng chủ đầu tư xây dựng một hệ thống bếp – showroom phản ánh đúng triết lý thương hiệu: chỉn chu, chuẩn mực và hướng đến giá trị lâu dài.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Từ Coffee Lab tiếp đón đối tác quốc tế, khu bếp lãnh đạo tinh gọn, đến khu bếp nhân viên công suất lớn – mỗi không gian đều được thiết kế với tư duy riêng, nhưng cùng phục vụ một mục tiêu chung: nâng tầm trải nghiệm vận hành tại nhà máy chế biến cà phê xuất khẩu hàng đầu Việt Nam.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Với Vĩnh Hiệp, Saigon Horeca tiếp tục khẳng định vai trò của mình: không chỉ lắp bếp, mà kiến tạo hệ thống vận hành xứng tầm thương hiệu.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-vhp pp-gallery-vhp--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery-vhp__item pp-image-container-shared"><img src="<?php echo sgh_img('2023/12/bi-quyet-tao-thuc-don-nha-hang.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"><div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống bếp – showroom xứng tầm thương hiệu xuất khẩu', 'saigonhoreca'); ?></div></div>
    </div>
  </div>
</section>

