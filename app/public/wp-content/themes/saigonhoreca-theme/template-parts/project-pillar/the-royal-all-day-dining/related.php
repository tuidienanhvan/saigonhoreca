<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #7: closing block — intro paragraphs + image + bullet list + closing h2 + outro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-trd pp-text-trd--narrow">
      <span class="pp-text-trd__divider" aria-hidden="true"></span>
      <div class="pp-text-trd__body">
        <p><?php echo esc_html__('Điểm đặc biệt ở dự án này là Saigon Horeca không chỉ lắp đặt bếp theo yêu cầu, mà còn đồng hành cùng chủ đầu tư từ giai đoạn lên ý tưởng vận hành.', 'saigonhoreca'); ?></p>
        <p><?php echo wp_kses_post(sprintf(esc_html__('Từng chi tiết nhỏ, từ %1$s, %2$s, đến %3$s… đều được Saigon Horeca tính toán dựa trên %4$s của đội ngũ đầu bếp.', 'saigonhoreca'), '<strong>' . esc_html__('chiều cao quầy sơ chế', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('vị trí máy rửa', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('khoảng cách lý tưởng giữa các bếp nấu', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('thói quen vận hành thực tế', 'saigonhoreca') . '</strong>')); ?></p>
      </div>
    </div>
    <figure class="pp-figure-trd">
      <img src="<?php echo sgh_img('2025/05/the-royal-sgh-8.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
    </figure>
    <div class="pp-text-trd pp-text-trd--narrow">
      <div class="pp-text-trd__body">
        <p><?php echo wp_kses_post(sprintf(esc_html__('Điều đó cho thấy, %1$s, mà là sự kết tinh giữa:', 'saigonhoreca'), '<strong>' . esc_html__('bếp công nghiệp chuyên nghiệp không đơn thuần là tập hợp thiết bị đắt tiền', 'saigonhoreca') . '</strong>')); ?></p>
        <ul class="pp-list-trd">
          <li><?php echo esc_html__('Tư duy vận hành', 'saigonhoreca'); ?></li>
          <li><?php echo esc_html__('Hiểu biết về thói quen lao động chuyên nghiệp', 'saigonhoreca'); ?></li>
          <li><?php echo esc_html__('Tuân thủ nghiêm ngặt các tiêu chuẩn vệ sinh an toàn thực phẩm', 'saigonhoreca'); ?></li>
          <li><?php echo esc_html__('Sự thấu cảm với nhịp sống ẩm thực đẳng cấp.', 'saigonhoreca'); ?></li>
        </ul>
        <p><?php echo wp_kses_post(sprintf(esc_html__('The Royal không chỉ sở hữu một hệ thống bếp hiện đại, mà còn có trong mình một %1$s, sẵn sàng đưa những trải nghiệm ẩm thực tại nhà hàng lên một tầm cao mới.', 'saigonhoreca'), '<strong>' . esc_html__('linh hồn vận hành chuẩn chỉnh', 'saigonhoreca') . '</strong>')); ?></p>
      </div>
    </div>
    <div class="pp-text-trd pp-text-trd--center pp-text-trd--narrow" style="margin-top:3rem;">
      <h2 class="pp-text-trd__title"><?php echo esc_html__('Saigon Horeca – Đối tác chiến lược trong hành trình nâng tầm bếp Việt', 'saigonhoreca'); ?></h2>
      <span class="pp-text-trd__divider pp-text-trd__divider--center" aria-hidden="true"></span>
      <div class="pp-text-trd__body">
        <p><?php echo wp_kses_post(sprintf(esc_html__('Với dự án The Royal, Saigon Horeca một lần nữa khẳng định vị thế là %1$s trong lĩnh vực thiết kế – thi công bếp công nghiệp cao cấp tại Việt Nam.', 'saigonhoreca'), '<strong>' . esc_html__('đối tác chiến lược tin cậy', 'saigonhoreca') . '</strong>')); ?></p>
        <p><?php echo wp_kses_post(sprintf(esc_html__('Tận tâm, am hiểu và sáng tạo, Saigon Horeca đã, đang và sẽ tiếp tục đồng hành cùng nhiều nhà hàng, khách sạn, resort… trên hành trình %1$s.', 'saigonhoreca'), '<strong>' . esc_html__('kiến tạo những không gian bếp không chỉ đẹp, mà còn thực sự cao cấp và bền vững', 'saigonhoreca') . '</strong>')); ?></p>
      </div>
    </div>
  </div>
</section>
