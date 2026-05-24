<?php
/**
 * Project Pillar — tales-by-chapter
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
        <h2 class="pp-text__title"><?php echo esc_html__('Tầng 3 – nơi những chương mới được viết', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Saigon Horeca đã thiết kế không gian này như một phòng nghiên cứu thực sự, nơi mọi thứ đều được sắp xếp linh hoạt để đội ngũ Tales thoải mái thử nghiệm những công thức mới.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Từ hệ thống quầy kệ, máy rửa ly đến thùng đá âm bàn đều được tính toán kỹ, giúp mọi thao tác diễn ra trơn tru mà không bị vướng víu.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Tại đây, không có áp lực phải phục vụ khách ngay lập tức, mà chỉ có sự tập trung để tìm tòi và sáng tạo ra những món ăn, thức uống độc đáo cho tương lai.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-4.jpg'); ?>" alt="<?php echo esc_attr__('Bếp á đôi công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
