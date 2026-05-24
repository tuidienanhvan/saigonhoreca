<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #5: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-vhp pp-text-vhp--center">
      <span class="pp-text-vhp__divider pp-text-vhp__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-vhp__title"><?php echo esc_html__('Dấu ấn Saigon Horeca – Tư duy giải pháp, không chỉ là thiết bị', 'saigonhoreca'); ?></h2>
      <div class="pp-text-vhp__body">
      <p><?php echo esc_html__('Ở dự án Vĩnh Hiệp, Saigon Horeca không tiếp cận với vai trò nhà cung cấp đơn thuần. Mỗi hạng mục đều được đặt trong một câu hỏi lớn hơn: Không gian này phục vụ ai? Vận hành như thế nào? Và góp phần gì cho hình ảnh thương hiệu?', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Từ hệ thống bếp dành cho ban lãnh đạo đến Coffee Lab trưng bày, tất cả đều được kết nối bởi một triết lý chung: bếp công nghiệp cao cấp không chỉ để nấu, mà còn để thể hiện chuẩn mực của một doanh nghiệp lớn.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Dự án Vĩnh Hiệp – Coffee Lab là minh chứng rõ nét cho cách Saigon Horeca đồng hành cùng doanh nghiệp F&B và chế biến thực phẩm ở tầm chiến lược, nơi mỗi không gian đều được thiết kế để phục vụ vận hành, con người và thương hiệu.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Không phô trương, không dư thừa, nhưng đủ tinh tế để đối tác quốc tế bước vào và hiểu ngay: đây là một nhà máy xuất khẩu cà phê hàng đầu – nghiêm túc, chuẩn mực và đầy bản sắc.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-vhp pp-gallery-vhp--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery-vhp__item"><img src="<?php echo sgh_img('2023/12/bi-quyet-tao-thuc-don-nha-hang.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
