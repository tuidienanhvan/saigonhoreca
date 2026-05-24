<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #12: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text pp-text--center">
      <span class="pp-text__divider pp-text__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text__title"><?php echo esc_html__('Phòng Bếp Nấu Ăn và Quầy Sushi Của Roka Fella', 'saigonhoreca'); ?></h2>
      <div class="pp-text__body">
      <p><?php echo esc_html__('Mặc dù tương đối nhỏ với diện tích khoảng 4 mét vuông, khu vực bếp tại Roka Fella được Saigon Horeca bố trí tỉ mỉ để đáp ứng mọi yêu cầu hoạt động. Từng thiết bị được lựa chọn kỹ lưỡng và sắp xếp khoa học, đảm bảo luồng khí lưu thông, cung cấp thêm thông gió, mang đến môi trường làm việc thoải mái, mát mẻ cho các đầu bếp trong quá trình chế biến.Trái với không gian nấu nướng trong bếp, quầy Sushi tại Roka Fella lại tập trung vào việc giữ trọn độ tươi ngon của cá, tôm. Chính vì thế, khu vực này được trang bị rất nhiều tủ lạnh và tủ đông hiện đại, đảm bảo hương vị của món ăn, sẵn sàng cho màn trình diễn nghệ thuật Sushi của các đầu bếp tài ba.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Trải nghiệm Omakase tại Roka Fella không chỉ xoay quanh tài nghệ của đầu bếp, mà còn phụ thuộc vào chất lượng nguyên liệu tươi ngon thượng hạng. Chính vì vậy, khu vực bếp được Saigon Horeca trang bị hệ thống tủ lạnh hiện đại, đáp ứng nhu cầu bảo quản đa dạng cho các loại như cá hồi, cá ngừ đại dương, tôm và vô số hải sản khác. Thậm chí, một máy làm đá chuyên dụng cũng được đưa vào sử dụng, đảm bảo cung cấp những viên đá tinh khiết, sẵn sàng phục vụ nghệ thuật trình bày và giữ trọn hương vị tươi ngon của món ăn.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery pp-gallery--cols-2" style="margin-top:2rem;">
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2020/12/roka-bv-bo-tri-thiet-bi.png'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async"></div>
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2024/03/Roka-fella-bep-nau.jpg'); ?>" alt="<?php echo esc_attr__('Tầm quan trọng của bảo trì định kỳ thiết bị nhà bếp căn tin', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
