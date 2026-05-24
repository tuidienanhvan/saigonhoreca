<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #5: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-yzo pp-split-yzo--reverse">
      <div class="pp-split-yzo__body">
        <div class="pp-text-yzo__body">
      <p><?php echo esc_html__('Saigon Horeca tự hào đã tạo ra những không gian bếp độc đáo cho Yuzu Omakase, thể hiện sự sáng tạo độc đáo của nhà hàng. Trang thiết bị thép không gỉ chuyên dụng được lắp đặt góp phần khẳng định trải nghiệm ẩm thực riêng biệt tại nhà hàng. Sự chú ý tỉ mỉ đến từng chi tiết và các giải pháp được tùy chỉnh của Saigon Horeca đã tạo nên một quầy bếp không chỉ đáp ứng nhu cầu vận hành mà còn là trung tâm của sự sáng tạo và xuất sắc trong nghệ thuật ẩm thực tại Yuzu Omakase.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-yzo__media">
        <img src="<?php echo sgh_img('2024/01/Yuzu-Omakase-chef.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
