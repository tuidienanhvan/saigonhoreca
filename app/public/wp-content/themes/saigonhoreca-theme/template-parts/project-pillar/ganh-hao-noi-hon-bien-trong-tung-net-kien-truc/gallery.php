<?php
/**
 * Project Pillar — ganh-hao-noi-hon-bien-trong-tung-net-kien-truc
 * Section #6: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-gha pp-text-gha--center">
      <div class="pp-text-gha__body">
      <p><?php echo esc_html__('Ở một nơi mà gió biển thổi ngày đêm, độ ẩm và hơi muối luôn là thách thức lớn cho mọi thiết bị kim loại – Saigon Horeca đã nghiên cứu và đưa vào các giải pháp chống gỉ, tối ưu thoát nhiệt và giảm tiếng ồn, đảm bảo gian bếp không chỉ hoạt động hiệu quả, mà còn thân thiện với nhân viên vận hành – những người đứng sau ánh đèn sân khấu ẩm thực.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Với dự án này, Saigon Horeca không chỉ bàn giao một hệ thống bếp công nghiệp, mà còn góp phần duy trì và nâng tầm trải nghiệm ẩm thực tại một địa điểm đặc biệt của Vũng Tàu. Chúng tôi tự hào khi được đồng hành cùng Gành Hào – nơi không chỉ giữ hồn biển qua từng món ăn, mà còn thể hiện sự chỉn chu từ khâu chuẩn bị phía sau.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-gha pp-gallery-gha--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery-gha__item"><img src="<?php echo sgh_img('2025/05/ganh-hao-sgh-8.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
