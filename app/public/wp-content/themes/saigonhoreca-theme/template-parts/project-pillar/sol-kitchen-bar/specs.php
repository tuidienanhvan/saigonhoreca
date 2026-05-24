<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #5: bg_section
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-skb" style="background-image:url('<?php echo sgh_img('2024/06/SGH-sold7-01.jpg'); ?>');">
  <div class="pp-section-bg-skb__overlay" aria-hidden="true"></div>
  <div class="pp-section-bg-skb__content">
    <span class="pp-text-skb__divider pp-text-skb__divider--center" aria-hidden="true"></span>
    <h2 class="pp-text-skb__title"><?php echo esc_html__('Sự Tinh Tế của Saigon Horeca Trong Thiết Kế', 'saigonhoreca'); ?></h2>
    <div class="pp-text-skb__body">
      <p><?php echo esc_html__('Sol Kitchen &amp; Bar quận 7 đã được trang bị một khu bếp rộng hơn 70m2, đảm bảo sự tiện nghi và chuyên nghiệp trong quá trình vận hành. Khu bếp này được trang bị các thiết bị bếp cao cấp như hệ thống tủ đông, tủ mát, lò nướng combi, salamander, plancha, và đặc biệt là lò BBQ do Saigon Horeca sản xuất riêng cho Sol quận 7.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
