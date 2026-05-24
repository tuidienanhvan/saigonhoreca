<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #3: Thiết kế (heading + body interleaved with images + 2-col carousel)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-trd pp-text-trd--center pp-text-trd--narrow">
      <h2 class="pp-text-trd__title"><?php echo esc_html__('Thiết kế của The Royal All Day Dining', 'saigonhoreca'); ?></h2>
      <span class="pp-text-trd__divider pp-text-trd__divider--center" aria-hidden="true"></span>
      <div class="pp-text-trd__body">
        <p><?php echo esc_html__('Tọa lạc tại 41–47 Đông Du, Bến Nghé, Quận 1, TP. Hồ Chí Minh, The Royal All Day Dining mang dáng dấp của một nhà hàng đẳng cấp – nơi mỗi trải nghiệm ẩm thực đều được chăm chút tỉ mỉ. Không phô trương hình ảnh, The Royal lựa chọn cách chinh phục khách hàng bằng chất lượng, sự chỉn chu và phong cách vận hành chuyên nghiệp.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Không gian và thiết kế không cần lời quảng bá rầm rộ – chính cảm nhận của thực khách sẽ là minh chứng rõ ràng nhất cho sự khác biệt mang tên The Royal.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <figure class="pp-figure-trd">
      <img src="<?php echo sgh_img('2025/05/the-royal-sgh-1.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      <figcaption class="pp-figure-trd__caption"><?php echo esc_html__('Không gian bên trong The Royal', 'saigonhoreca'); ?></figcaption>
    </figure>
    <div class="pp-text-trd pp-text-trd--narrow">
      <div class="pp-text-trd__body">
        <p><?php echo esc_html__('Bên trong, không gian mang hơi hướng một phòng trà châu Âu thế kỷ trước, với ghế bọc da nâu cổ điển, tường ốp gỗ tự nhiên và hệ thống đèn chùm ánh vàng dịu.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <figure class="pp-figure-trd">
      <img src="<?php echo sgh_img('2025/05/the-royal-sgh-2.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
    </figure>
    <div class="pp-text-trd pp-text-trd--narrow">
      <div class="pp-text-trd__body">
        <p><?php echo esc_html__('Cách bố trí ghế sofa cong mềm mại, đan cài cùng những vách ngăn thấp bằng gỗ và kính, tạo nên từng "khoảng trời riêng" cho thực khách mà không làm mất đi sự liên kết tổng thể. Một chi tiết nhỏ nhưng tinh tế chính là sự lặp lại nhẹ nhàng của họa tiết đường cong — từ cửa vòm, đến lưng ghế, đến khung kính tường — như những giai điệu cổ xưa vang lên trong không gian hiện đại.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-trd pp-gallery-trd--cols-2" style="margin: 2.5rem 0;">
      <div class="pp-gallery-trd__item"><img src="<?php echo sgh_img('2025/05/the-royal-sgh-3.jpg'); ?>" alt="<?php echo esc_attr__('the-royal-sgh (3)', 'saigonhoreca'); ?>" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-trd__item"><img src="<?php echo sgh_img('2025/05/the-royal-sgh-4.jpg'); ?>" alt="<?php echo esc_attr__('the-royal-sgh (4)', 'saigonhoreca'); ?>" loading="lazy" decoding="async"></div>
    </div>
    <div class="pp-text-trd pp-text-trd--narrow">
      <div class="pp-text-trd__body">
        <p><?php echo esc_html__('Ánh sáng ở The Royal được tiết chế kỹ lưỡng. Không gian ấm cúng và sang trọng, vừa đủ để mỗi bữa ăn trở thành một khoảnh khắc tận hưởng trọn vẹn. Đèn chùm kiểu dáng tối giản, sử dụng lớp thủy tinh mờ để ánh sáng không chói lóa, lại làm nổi bật được độ óng ả của gỗ và da trong phòng.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('The Royal không cố gắng làm mình khác biệt một cách ồn ào, mà chọn cách lặng lẽ chinh phục lòng người — giống như một bản nhạc trữ tình ngân vang giữa những bộn bề náo động. Đó chính là "chất" Royal: tinh tế, trầm lắng và bền bỉ theo thời gian.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
