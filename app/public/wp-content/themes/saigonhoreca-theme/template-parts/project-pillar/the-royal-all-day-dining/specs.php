<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #5: Giải pháp split (image LEFT stacked, text RIGHT)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-trd">
      <div class="pp-split-trd__media pp-split-trd__media--stack">
        <img src="<?php echo sgh_img('2025/05/the-royal-sgh-5.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <img src="<?php echo sgh_img('2025/05/the-royal-sgh-6.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
      <div class="pp-split-trd__body">
        <h2 class="pp-text-trd__title"><?php echo esc_html__('Giải pháp của Saigon Horeca:', 'saigonhoreca'); ?></h2>
        <span class="pp-text-trd__divider" aria-hidden="true"></span>
        <div class="pp-text-trd__body">
          <p><?php echo wp_kses_post(sprintf(esc_html__('%1$s ra đời như một tuyên ngôn về sự hoàn mỹ trong từng chi tiết. Và Saigon Horeca, với vai trò thiết kế và thi công hệ thống bếp công nghiệp, đã thể hiện xuất sắc sứ mệnh đó: %2$s', 'saigonhoreca'), '<strong>The Royal</strong>', '<strong>' . esc_html__('biến mỗi mét vuông nhà bếp thành một nền tảng cho sự bứt phá trong nghệ thuật ẩm thực.', 'saigonhoreca') . '</strong>')); ?></p>
          <p><?php echo wp_kses_post(sprintf(esc_html__('Bếp công nghiệp The Royal được xây dựng theo triết lý %1$s', 'saigonhoreca'), '<strong>' . esc_html__('"Bếp là trái tim của nhà hàng – nơi khởi nguồn cho mọi cảm xúc thực"', 'saigonhoreca') . '</strong>')); ?></p>
          <p><?php echo wp_kses_post(sprintf(esc_html__('Mọi khu vực trong bếp – từ sơ chế, chế biến, nấu nướng đến hoàn thiện món – đều được %1$s, tối ưu theo dòng chảy một chiều, giúp tiết kiệm thời gian, giảm thiểu sai sót và tạo nên nhịp làm việc nhịp nhàng, chính xác đến từng thao tác.', 'saigonhoreca'), '<strong>' . esc_html__('vận hành cực kỳ khoa học', 'saigonhoreca') . '</strong>')); ?></p>
          <p><?php echo esc_html__('Tại The Royal, bếp không chỉ là nơi tạo ra món ăn, mà là "ngôi nhà" của các chef thỏa sức sáng tạo và thổi hồn vào từng món ăn – khởi nguồn cho trải nghiệm đẳng cấp đến với thực khách.', 'saigonhoreca'); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
