<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #4: bg_section
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-hwa">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="pp-section-bg-hwa__bg" style="background-image:url('<?php echo sgh_img('2024/02/Heiwa-Sushi-00002.jpg'); ?>');"></div>
  <div class="pp-section-bg-hwa__overlay" aria-hidden="true"></div>
  <div class="pp-section-bg-hwa__content">
    <span class="pp-text-hwa__divider pp-text-hwa__divider--center" aria-hidden="true"></span>
    <h2 class="pp-text-hwa__title"><?php echo esc_html__('Trải Nghiệm Ẩm Thực Tương Tác', 'saigonhoreca'); ?></h2>
    <div class="pp-text-hwa__body">
      <p><?php echo esc_html__('Tại Heiwa Sushi, sự kết hợp giữa quầy bếp và quầy bar tạo nên một trải nghiệm ẩm thực hài hòa, nơi hương vị và nghệ thuật thẩm mỹ hòa quyện vào nhau.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Trái tim của Heiwa Sushi, chính là căn bếp hiện đại và tiện nghi được Saigon Horeca chăm chút tỉ mỉ. Những thiết bị cao cấp như tủ đông Hoshizaki, quầy lạnh bảo quản thực phẩm, máy làm đá, hệ thống giá treo tường và các dụng cụ bằng thép không gỉ đóng vai trò quan trọng trong thành công của nhà hàng. Bên cạnh đó, Heiwa Sushi còn chú trọng đến việc duy trì nhiệt độ và độ ẩm thích hợp trong bếp, nhằm đảm bảo nguyên liệu luôn trong trạng thái tốt nhất.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Sự đầu tư bài bản vào hệ thống bếp không chỉ góp phần vào chất lượng món ăn mà còn tạo nên môi trường làm việc an toàn, thoải mái cho các đầu bếp. Điều này gián tiếp mang đến trải nghiệm ẩm thực tuyệt vời cho thực khách.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
