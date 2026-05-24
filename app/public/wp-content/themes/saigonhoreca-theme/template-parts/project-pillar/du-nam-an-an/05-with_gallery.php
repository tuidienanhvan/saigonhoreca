<?php
/**
 * Project Pillar — du-nam-an-an
 * Section #5: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text pp-text--center">
      <span class="pp-text__divider pp-text__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text__title"><?php echo esc_html__('Nam An & Saigon Horeca – Sự gặp nhau của hai chuẩn mực', 'saigonhoreca'); ?></h2>
      <div class="pp-text__body">
      <p><?php echo esc_html__('Dự án Nam An là minh chứng rõ nét cho cách Saigon Horeca đồng hành cùng chủ đầu tư từ triết lý đến giải pháp. Khi Nam An đặt ra yêu cầu cao về chất lượng bữa ăn và sự an toàn cho người cao tuổi, Saigon Horeca đáp lại bằng một hệ thống bếp được thiết kế chỉn chu, vận hành bền bỉ và hướng đến giá trị lâu dài.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Nam An – Nursing Home Kitchen không chỉ là một dự án bếp công nghiệp, mà là nơi kỹ thuật, vận hành và sự thấu cảm cùng gặp nhau – đúng với tinh thần mà Saigon Horeca luôn theo đuổi trong mỗi công trình.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery pp-gallery--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2023/12/bi-quyet-tao-thuc-don-nha-hang.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
