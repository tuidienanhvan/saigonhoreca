<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #5: split
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
        <h2 class="pp-text__title"><?php echo esc_html__('Ý tưởng thiết kế bếp công nghiệp', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Khu vực rửa chén được thiết kế với 2 bồn rửa kép bằng thép không gỉ, một bàn rửa, kệ gắn tường và tủ lớn bằng thép không gỉ để lưu trữ chén đĩa. Dựa trên đặc điểm hoạt động của nhà hàng, phục vụ khách hàng từ 10 giờ sáng đến 11 giờ tối, khu vực này được sử dụng để rửa chén vào cuối ngày và chuẩn bị nguyên liệu sơ chế vào sáng hôm sau, đảm bảo vệ sinh và an toàn thực phẩm bằng cách kiểm soát ô nhiễm với nguyên liệu sạch.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Khu vực này được trang bị đầy đủ 100% bởi Saigon Horeca với thiết kế và sản xuất riêng của mình. Ngoài ra, một khu vực chuẩn bị dự phòng cũng được sắp xếp gần khu vực rửa chén, phục vụ như một sự dự phòng cho các hoạt động chuẩn bị và rửa chén đồng thời.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2024/02/swh-ban-ve-tong-the-khu-bep.png'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1541" height="788">
      </div>
    </div>
  </div>
</section>
