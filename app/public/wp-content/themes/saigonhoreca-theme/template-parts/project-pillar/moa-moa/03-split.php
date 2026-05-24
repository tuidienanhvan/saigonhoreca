<?php
/**
 * Project Pillar — moa-moa
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
        <h2 class="pp-text__title"><?php echo esc_html__('Hệ thống bếp Âu cao cấp – Nền tảng cho chất lượng món Ý', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Trung tâm của gian bếp là bếp Âu 6 họng, cho phép xử lý đồng thời nhiều món pasta với yêu cầu nhiệt độ khác nhau – yếu tố then chốt trong ẩm thực Ý. Bên cạnh đó, nồi trụng mì chuyên dụng được bố trí hợp lý, giúp pasta luôn đạt độ al dente chuẩn mực, không bị gián đoạn trong giờ cao điểm.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Khu bàn trộn bột và chế biến pasta tươi được thiết kế như một "xưởng thủ công thu nhỏ", nơi đầu bếp có đủ không gian thao tác, kiểm soát độ ẩm, nhiệt độ và kết cấu bột – những yếu tố quyết định linh hồn của pasta tươi.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-2.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
