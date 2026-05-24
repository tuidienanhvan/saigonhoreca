<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #7: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-skb pp-text-skb--center">
      <div class="pp-text-skb__body">
      <p><?php echo esc_html__('Quy trình thi công được thực hiện một cách nghiêm ngặt và cẩn thận. Tất cả các công đoạn đều được giám sát chặt chẽ để đảm bảo an toàn lao động và hạn chế tối đa các sự cố có thể xảy ra.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca cam kết mang đến dịch vụ chất lượng cao, sự tin cậy trong kinh doanh và tính thẩm mỹ trong hệ thống bếp. Chúng tôi luôn đặt lợi ích của khách hàng lên hàng đầu và không ngừng cải tiến để đáp ứng nhu cầu ngày càng cao của thị trường.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-skb pp-gallery-skb--cols-3" style="margin-top:2rem;">
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/thi-cong-02.jpg'); ?>" alt="<?php echo esc_attr__('Hướng dẫn bảo dưỡng bàn inox công nghiệp đúng cách và hiệu quả', 'saigonhoreca'); ?>" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/thi-cong-04.jpg'); ?>" alt="thi-cong-04" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-skb__item"><img src="<?php echo sgh_img('2024/06/thi-cong-03.jpg'); ?>" alt="thi-cong-03" loading="lazy" decoding="async"></div>
    </div>
  </div>
</section>
