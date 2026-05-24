<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #6: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-skb pp-split-skb--reverse">
      <div class="pp-split-skb__body">
        <span class="pp-text-skb__divider" aria-hidden="true"></span>
        <h2 class="pp-text-skb__title"><?php echo esc_html__('Giải pháp của Saigon Horeca:', 'saigonhoreca'); ?></h2>
        <div class="pp-text-skb__body">
      <p><?php echo esc_html__('Hệ thống tủ đông và tủ mát giúp bảo quản thực phẩm luôn tươi ngon và an toàn. Lò nướng combi là sự kết hợp hoàn hảo giữa nướng và hấp, mang đến những món ăn chất lượng cao. Salamander và plancha giúp nấu nướng nhanh chóng và hiệu quả.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Lò BBQ theo phong cách Italia được Saigon Horeca sản xuất dành riêng cho nhà hàng Sol Kitchen &amp; Bar là điểm nhấn độc đáo, dễ dàng thao tác, nướng nhanh và chắc chắn. Ngoài ra còn mang tính thẩm mỹ rất lớn cho khu bếp.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Ngoài việc lắp đặt các thiết bị mới, Saigon Horeca còn tối ưu lại các thiết bị cũ. Chúng tôi kiểm tra công năng của từng thiết bị và đưa vào hệ thống mới nhằm tối ưu chi phí cho chủ đầu tư mà vẫn đảm bảo hiệu quả vận hành.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-skb__media">
        <img src="<?php echo sgh_img('2024/06/SGH-sold7-01.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
