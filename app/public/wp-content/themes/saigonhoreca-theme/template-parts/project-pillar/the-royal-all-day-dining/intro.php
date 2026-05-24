<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #2: address + intro text + image with caption + secondary heading/body
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-trd pp-text-trd--center pp-text-trd--narrow">
      <span class="pp-text-trd__divider pp-text-trd__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-trd__title">The Royal</h2>
      <div class="pp-text-trd__body">
        <p><strong><?php echo esc_html__('Địa chỉ:', 'saigonhoreca'); ?></strong> <?php echo esc_html__('41-47 Dong Du, Ben Nghe Ward, District 1, Ho Chi Minh City.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Từ 11h00 AM – 11h00 AM (Thứ hai – Thứ bảy)', 'saigonhoreca'); ?></p>
        <p><em><?php echo esc_html__('Không gian nơi đây được trau chuốt kỹ lưỡng đến từng chi tiết, phản ánh gu thẩm mỹ thời thượng và sự chỉn chu tuyệt đối trong thiết kế. The Royal không chạy theo xu hướng ngắn hạn, mà kiến tạo một phong cách kiến trúc riêng – hiện đại, thanh lịch và mang đậm dấu ấn của sự tinh mỹ vượt thời gian.', 'saigonhoreca'); ?></em></p>
      </div>
    </div>
    <figure class="pp-figure-trd">
      <img src="<?php echo sgh_img('2025/11/the-royal-sgh-1.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      <figcaption class="pp-figure-trd__caption">credit: @_theroyal.alldaydining</figcaption>
    </figure>
    <div class="pp-text-trd pp-text-trd--narrow">
      <h2 class="pp-text-trd__title"><?php echo esc_html__('The Royal – Khi vẻ đẹp chỉn chu tạo nên sự khác biệt', 'saigonhoreca'); ?></h2>
      <div class="pp-text-trd__body">
        <p><em><?php echo esc_html__('Nằm ngay trung tâm thành phố, THE ROYAL ALL DAY DINING là điểm hẹn lý tưởng dành cho những ai yêu thích sự tinh tế trong ẩm thực. Tại đây, mỗi món ăn là một sự kết hợp khéo léo giữa kỹ thuật ẩm thực hiện đại phương Tây và những giá trị truyền thống sâu sắc của ẩm thực Việt Nam. Mặt tiền được thiết kế với tông màu gỗ sáng, khung cửa kính tối màu, và những chi tiết vuông vức dứt khoát – tất cả gợi lên một sự gần gũi tự nhiên, như lời mời gọi bước vào một không gian không chỉ để ăn uống, mà còn để tạm dừng, tận hưởng, và kết nối.', 'saigonhoreca'); ?></em></p>
      </div>
    </div>
  </div>
</section>
