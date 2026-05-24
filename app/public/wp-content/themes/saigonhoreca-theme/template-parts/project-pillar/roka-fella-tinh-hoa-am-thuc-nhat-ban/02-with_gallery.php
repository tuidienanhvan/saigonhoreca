<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text pp-text--center">
      <span class="pp-text__divider pp-text__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text__title"><?php echo esc_html__('Roka Fella: Tinh hoa ẩm thực Nhật Bản', 'saigonhoreca'); ?></h2>
      <div class="pp-text__body">
      <p><?php echo esc_html__('Nhắc đến xứ sở Mặt Trời Mọc, ai cũng biết đến sự đa dạng của văn hóa Nhật Bản, chẳng hạn như nghi lễ trà đạo, văn hóa xin lỗi, văn hóa ẩm thực và nhiều điều khác. Nếu nói về văn hóa ẩm thực Nhật Bản, Sushi chính là món ăn nổi tiếng nhất, được chế biến từ hải sản tươi ngon như tôm, cá, bào ngư, cùng cơm nắm được ướp giấm và muối.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Tọa lạc tại trung tâm Quận 1, Roka Fella được xem là điểm đến lý tưởng để thực khách trải nghiệm những món ăn nổi tiếng của xứ sở Mặt Trời Mọc.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery pp-gallery--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2020/12/roka.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async"></div>
    </div>
  </div>
</section>
