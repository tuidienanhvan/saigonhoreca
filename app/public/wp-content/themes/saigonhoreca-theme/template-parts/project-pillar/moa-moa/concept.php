<?php
/**
 * Project Pillar â€” moa-moa
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-mmo pp-section-mmo--alt scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-split-mmo">
      <div class="pp-split-mmo__body">
        <span class="pp-text-mmo__divider" aria-hidden="true"></span>
        <h2 class="pp-text-mmo__title"><?php echo esc_html__('Hệ thống bếp Âu cao cấp – Nền tảng cho chất lượng món Ý', 'saigonhoreca'); ?></h2>
        <div class="pp-text-mmo__body">
      <p><?php echo esc_html__('Trung tâm của gian bếp là bếp Âu 6 họng, cho phép xử lý đồng thời nhiều món pasta với yêu cầu nhiệt độ khác nhau – yếu tố then chốt trong ẩm thực Ý. Bên cạnh đó, n���i luộc mì chuyên dụng được bố trí hợp lý, giúp pasta luôn đạt độ al dente chuẩn mực, không bị gián đoạn trong giờ cao điểm.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Khu bàn trộn bột và chế biến pasta tươi được thiết kế như một "xưởng thủ công thu nhỏ", nơi đầu bếp có đủ không gian thao tác, kiểm soát độ ẩm, nhiệt độ và kết cấu bột – những yếu tố quyết định linh hồn của pasta tươi.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-mmo__media pp-image-container-shared">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-2.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bếp Âu 6 họng và khu chế biến pasta tươi thủ công', 'saigonhoreca'); ?></div>
      </div>
    </div>
  </div>
</section>

