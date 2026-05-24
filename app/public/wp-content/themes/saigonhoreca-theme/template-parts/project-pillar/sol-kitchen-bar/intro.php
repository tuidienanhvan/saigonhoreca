<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-skb pp-text-skb--center">
      <div class="pp-text-skb__body">
      <p><?php echo esc_html__('Tiếp nối sự thành công của nhà hàng Sol quận 1 khi lọt vào Michelin Guide 2023, Saigon Horeca vinh hạnh được chủ đầu tư Sol Kitchen &amp; Bar tin tưởng lựa chọn là đơn vị uy tín triển khai hệ thống bếp cao cấp cho nhà hàng tại quận 7, HCMC.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-skb pp-gallery-skb--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/Sol0D7-01.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
