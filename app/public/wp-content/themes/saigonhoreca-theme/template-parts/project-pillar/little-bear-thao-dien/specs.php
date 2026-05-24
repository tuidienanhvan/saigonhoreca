<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #5: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-lbt pp-split-lbt--reverse">
      <div class="pp-split-lbt__body">
        <span class="pp-text-lbt__divider" aria-hidden="true"></span>
        <h2 class="pp-text-lbt__title"><?php echo esc_html__('Giải pháp của Saigon Horeca:', 'saigonhoreca'); ?></h2>
        <div class="pp-text-lbt__body">
      <p><?php echo esc_html__('Khu vực lưu trữ và chế biến trung tâm:Hai bàn ngăn kéo tủ mát lưng tựa lưng để tối đa hóa việc sử dụng không gian và tối ưu hóa việc lưu trữ thực phẩm tươi sống.Tủ, bồn rửa và kệ để các thiết bị sơ chế.Khu vực nấu nướng:Nằm ở bên trái của khu vực làm việc, tượng trưng cho sự ấm áp và đam mê của bếp.Được trang bị các thiết bị nấu nướng chất lượng cao để tạo ra những món ăn ngon miệng.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-lbt__media">
        <img src="<?php echo sgh_img('2024/01/Little-bear-01.jpeg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
      </div>
    </div>
  </div>
</section>
