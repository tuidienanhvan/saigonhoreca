<?php
/**
 * Project Pillar — grand-marble-thuong-hieu-banh-cao-cap-nhat-ban
 * Section #4: partnership — cinematic bg + glassmorphic philosophy card.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-gmarb scroll-reveal" style="background-image:url('<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-lo-nuong-chanmag.jpg'); ?>');">
  <div class="pp-section-bg-gmarb__overlay" aria-hidden="true"></div>
  <div class="pp-gmarb-glow pp-gmarb-glow--tr" aria-hidden="true"></div>

  <div class="pp-section-bg-gmarb__content scroll-reveal reveal-up">
    <span class="pp-text-gmarb__eyebrow"><?php echo esc_html__('Đối tác vận hành', 'saigonhoreca'); ?></span>
    <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots pp-text-gmarb__divider--center" aria-hidden="true"></div>
    <h2 class="pp-section-bg-gmarb__title"><?php echo esc_html__('Đồng Hành Cùng Phòng Làm Bánh Grand Marble', 'saigonhoreca'); ?></h2>
    <div class="pp-section-bg-gmarb__body">
      <p><?php echo esc_html__('Để tạo ra những chiếc bánh ngọt ngon lành, các nghệ nhân Grand Marble tỉ mỉ lựa chọn nguyên liệu thượng hạng, nhào nặn bột, ủ kỹ và tuân theo quy trình nghiêm ngặt – từ những bước thủ công đến sự hỗ trợ của máy móc hiện đại. Sài Gòn Horeca là đối tác tư vấn thiết kế và lắp đặt trang thiết bị toàn diện cho phòng làm bánh, từ khâu chuẩn bị nguyên liệu đến đóng gói thành phẩm.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Dựa trên bố cục dự án tổng thể kết hợp các thủ tục tiêu chuẩn cho một phòng làm bánh, chúng tôi phân chia không gian thành sáu khu vực chức năng: tiếp nhận nguyên liệu, vệ sinh, trộn và cán bột, làm bánh, lưu trữ và kiểm tra đóng gói.', 'saigonhoreca'); ?></p>
    </div>
    <div class="pp-section-bg-gmarb__zones" aria-label="<?php echo esc_attr__('Sáu khu vực chức năng của phòng làm bánh', 'saigonhoreca'); ?>">
      <span><?php echo esc_html__('Tiếp nhận nguyên liệu', 'saigonhoreca'); ?></span>
      <span><?php echo esc_html__('Vệ sinh', 'saigonhoreca'); ?></span>
      <span><?php echo esc_html__('Trộn &amp; cán bột', 'saigonhoreca'); ?></span>
      <span><?php echo esc_html__('Làm bánh', 'saigonhoreca'); ?></span>
      <span><?php echo esc_html__('Lưu trữ', 'saigonhoreca'); ?></span>
      <span><?php echo esc_html__('Kiểm tra &amp; đóng gói', 'saigonhoreca'); ?></span>
    </div>
  </div>
</section>
