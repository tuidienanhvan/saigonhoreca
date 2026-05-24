<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #8: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-skb pp-text-skb--center">
      <div class="pp-text-skb__body">
      <p><?php echo esc_html__('Với kinh nghiệm và uy tín đã được khẳng định 10 năm nay, Saigon Horeca tự hào là đối tác đáng tin cậy của nhiều nhà hàng và quán Bar nổi tiếng như Bambino, Loco Complex, Yuzu Omakase, Unic Bar, …', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Hãy để lại thông tin liên hệ nếu quý khách có nhu cầu về tư vấn, thiết kế hệ thống bếp cao cấp cho nhà hàng, quán bar của mình, Saigon Horeca sẽ chủ động trao đổi với quý khách, khảo sát trực tiếp tại Site để lắng nghe toàn bộ yêu cầu nhằm đưa ra giải pháp tối ưu nhất, vừa tiết kiệm chi phí, vừa đảm bảo hệ thống chất lượng trong suốt thời gian vận hành thực sự ổn định.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca | Kitchen Equipment and Bar Solutions.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-skb pp-gallery-skb--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/sol-sgh.jpg'); ?>" alt="<?php echo esc_attr__('Tầm quan trọng của hệ thống hút khói và cấp khí tươi cho hệ thống bếp công nghiệp cao cấp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
