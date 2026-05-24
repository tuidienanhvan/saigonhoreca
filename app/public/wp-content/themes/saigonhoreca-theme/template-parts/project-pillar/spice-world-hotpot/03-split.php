<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split">
      <div class="pp-split__body">
        <span class="pp-text__divider" aria-hidden="true"></span>
        <h2 class="pp-text__title"><?php echo esc_html__('Thiết kế của nhà hàng Spice World HotPot', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Theo truyền thống, lẩu Sichuan thường được ưa chuộng vào những tháng mùa đông tại Trung Quốc để làm ấm cơ thể. Tuy nhiên, tại Việt Nam, sự phổ biến của lẩu đã vượt qua giới hạn của thời tiết, khi người Việt có thể thưởng thức lẩu bất cứ lúc nào họ mong muốn.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Nhà hàng có hai tầng với 180 chỗ ngồi và ba phòng riêng, mỗi phòng phục vụ 10 khách. Màu chủ đạo là màu đỏ kết hợp với màu đen và trắng, tạo nên một sự kết hợp giữa thế giới cũ và hiện đại. Một nửa không gian đại diện cho thế giới hiện đại với trần nhà bằng thép màu đen, tường kính trong suốt và thiết kế nội thất tối giản trong màu đen và trắng. Nửa còn lại thể hiện thế giới cũ với tường được trang trí bằng các bức tranh của những nhân vật lịch sử, chủ yếu là màu đỏ, tạo nên một không khí hấp dẫn và bí ẩn.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2024/02/Spice-World-Hot-Pot-03.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
