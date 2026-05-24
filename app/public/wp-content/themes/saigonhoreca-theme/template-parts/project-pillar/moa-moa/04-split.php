<?php
/**
 * Project Pillar — moa-moa
 * Section #4: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split pp-split--reverse">
      <div class="pp-split__body">
        <span class="pp-text__divider" aria-hidden="true"></span>
        <h2 class="pp-text__title"><?php echo esc_html__('Hệ thống hút khói – xử lý mùi: Giữ trọn trải nghiệm giữa phố đi bộ', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Với mô hình bếp mở và vị trí đắc địa ngay khu phố đi bộ Nguyễn Huệ, bài toán lớn nhất của Moa Moa không chỉ là nấu ngon, mà còn là kiểm soát mùi và khói.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca đã cung cấp và lắp đặt hệ thống chụp hút và quạt hút chuyên dụng, được tính toán kỹ lưỡng theo công suất thiết bị và đặc thù bếp Âu – bếp nướng than củi. Hệ thống này đảm bảo không gian bếp luôn thông thoáng, không ảnh hưởng đến trải nghiệm thực khách, đồng thời tuân thủ các tiêu chuẩn an toàn – vận hành trong khu vực trung tâm đông đúc.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-4.jpg'); ?>" alt="<?php echo esc_attr__('Bếp á đôi công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
