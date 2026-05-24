<?php
/**
 * Project Pillar — mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam
 * Section #7: gallery + text
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section">
  <div class="pp__container">
    <div class="pp-gallery-mcs pp-gallery-mcs--cols-2" style="margin-bottom:2rem;">
      <div class="pp-gallery-mcs__item"><img src="<?php echo sgh_img('2023/12/mua-sake-equipment-2.jpg'); ?>" alt="mua-sake-equipment-2.jpg" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mcs__item"><img src="<?php echo sgh_img('2023/12/mua-sake-equipment-1.jpg'); ?>" alt="mua-sake-equipment-1.jpg" loading="lazy" decoding="async"></div>
    </div>
    <div class="pp-text-mcs pp-text-mcs--center">
      <div class="pp-text-mcs__body">
        <p><?php echo esc_html__('Từ nguyên liệu tươi mát ngon lành, bàn bếp inox sáng bóng cho đến các thiết bị nấu nướng và pha chế hiện đại, mọi thứ ở Mùa Sake đều thể hiện Saigon Horeca luôn tâm huyết và tận tâm trong việc tạo nên không gian bếp và quầy bar hoàn hảo.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Với sự chuyên môn của Saigon Horeca trong xây dựng nhà bếp và quầy bar, Mùa Sake không chỉ xuất sắc trong việc cung cấp ẩm thực mà còn thiết lập một tiêu chuẩn mới về tính chức năng và hiệu quả. Sự hòa quyện giữa tầm nhìn của Mùa Sake và khả năng kỹ thuật của Saigon Horeca đã tạo ra một thiên đường ẩm thực kết hợp sự chân thực của văn hóa izakaya với những tiến bộ mới nhất trong thiết bị.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
