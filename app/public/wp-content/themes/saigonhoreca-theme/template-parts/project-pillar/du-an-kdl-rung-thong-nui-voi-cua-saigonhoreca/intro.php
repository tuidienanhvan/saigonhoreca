<?php
/**
 * Project Pillar — du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-rtn pp-text-rtn--center">
      <span class="pp-text-rtn__divider pp-text-rtn__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-rtn__title"><?php echo esc_html__('KDL Rừng Thông Núi Voi', 'saigonhoreca'); ?></h2>
      <div class="pp-text-rtn__body">
      <p><?php echo esc_html__('Thiên nhiên hùng vĩ – trải nghiệm tuyệt vời.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Trốn phố về rừng, đắm chìm trong thiên nhiên hùng vĩ tại KDL Rừng Thông Núi Voi', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('KDL Rừng Thông Núi Voi là điểm đến lý tưởng để du khách thưởng thức văn hóa ẩm thực Tây Nguyên độc đáo. Với 6 nhà hàng riêng biệt giữa khu vực rừng thông nguyên bản, mỗi nơi đều có phong cách và thực đơn phong phú. Các món ăn truyền thống và hiện đại đều được chế biến từ nguyên liệu tươi ngon, đảm bảo an toàn vệ sinh thực phẩm, đảm bảo quý thực khách sẽ có những trải nghiệm ẩm thực tuyệt vời trong không gian sang trọng, từ cơm lam, món nướng đậm đà đến rượu cần nồng nàn, tất cả đều được chế biến tinh tế và đầy nghệ thuật.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Với yêu cầu khắt khe về chất lượng thiết bị bếp và vị trí nằm trong rừng nguyên sinh, Saigon Horeca đã thiết kế và thi công hệ thống bếp cao cấp đáp ứng mọi yêu cầu từ chủ đầu tư. Layout bếp được thiết kế tối ưu, quy trình làm việc rõ ràng, đảm bảo môi trường làm việc sạch sẽ và an toàn. Hệ thống bếp của Saigon Horeca triển khai luôn vận hành một cách trơn tru, duy trì quy trình vệ sinh an toàn thực phẩm, giúp nhà hàng phục vụ thực khách một cách hoàn hảo nhất, đáp ứng mọi phong cách ẩm thực của các nhà hàng trong KDL.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-rtn pp-gallery-rtn--cols-4" style="margin-top:2rem;">
      <div class="pp-gallery-rtn__item"><img src="<?php echo sgh_img('2024/08/2-1.png'); ?>" alt="2" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-rtn__item"><img src="<?php echo sgh_img('2024/08/1-1.png'); ?>" alt="1" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-rtn__item"><img src="<?php echo sgh_img('2024/08/3-1.png'); ?>" alt="3" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-rtn__item"><img src="<?php echo sgh_img('2024/08/4-1.png'); ?>" alt="4" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-rtn__item"><img src="<?php echo sgh_img('2024/08/nha-hang-voi-vang-2.png'); ?>" alt="<?php echo esc_attr__('Bếp nhà hàng KDL Rừng Thông Núi Voi', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
