<?php
/**
 * Project Pillar — mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam
 * Section #8: image-gallery + text
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-gallery-mcs pp-gallery-mcs--cols-4" style="margin-bottom:2rem;">
      <div class="pp-gallery-mcs__item"><img src="<?php echo sgh_img('2023/12/Mua-kitchen-04.jpg'); ?>" alt="Mua-kitchen-04.jpg" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mcs__item"><img src="<?php echo sgh_img('2023/12/Mua-kitchen-03.jpg'); ?>" alt="Mua-kitchen-03.jpg" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mcs__item"><img src="<?php echo sgh_img('2023/12/Mua-kitchen-02.jpg'); ?>" alt="Mua-kitchen-02.jpg" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mcs__item"><img src="<?php echo sgh_img('2023/12/Mua-kitchen-01.jpg'); ?>" alt="Mua-kitchen-01.jpg" loading="lazy" decoding="async"></div>
    </div>
    <div class="pp-text-mcs pp-text-mcs--center">
      <div class="pp-text-mcs__body">
        <p><?php echo esc_html__('Nằm tại số 07 Lê Ngô Cát, Quận 3, TP.HCM, Mùa Sake không chỉ là một điểm đến izakaya hấp dẫn mà còn là minh chứng cho tài nghệ tinh xảo và trang thiết bị tiên tiến của Saigon Horeca.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
