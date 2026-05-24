<?php
/**
 * Project Pillar — casa-maria
 * Section #5: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-cma pp-text-cma--center">
      <span class="pp-text-cma__divider pp-text-cma__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-cma__title"><?php echo esc_html__('Hệ thống hút mùi – Giữ trọn không gian thưởng thức', 'saigonhoreca'); ?></h2>
      <div class="pp-text-cma__body">
      <p><?php echo esc_html__('Một nhà hàng tapas – wine không cho phép mùi bếp lấn át mùi rượu và không khí trò chuyện. Hệ thống hút mùi do Saigon Horeca triển khai được tính toán để:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Giải pháp này giúp Casa Maria giữ được sự cân bằng giữa bếp hoạt động và không gian thưởng thức.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Casa Maria là ví dụ điển hình cho cách Saigon Horeca tiếp cận dự án F&B cao cấp:', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Từ bếp tapas, quầy bar wine – cafe đến hệ thống bảo quản và hút mùi, mỗi giải pháp đều được thiết kế để phục vụ đúng tinh thần Tây Ban Nha hiện đại, đồng thời đáp ứng tiêu chuẩn vận hành chuyên nghiệp cho một nhà hàng quốc tế tại Thảo Điền.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Một căn bếp tốt không chỉ giúp nấu ăn ngon hơn – mà còn giúp concept nhà hàng được kể trọn vẹn, bền vững và có chiều sâu.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-cma pp-gallery-cma--cols-1" style="margin-top:2rem;">
      <div class="pp-gallery-cma__item"><img src="<?php echo sgh_img('2023/12/bi-quyet-tao-thuc-don-nha-hang.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
