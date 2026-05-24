<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #5: bg_section
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-specs-section">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="sgh-specs-section__bg" style="background-image:url('<?php echo sgh_img('2024/06/SGH-sold7-01.jpg'); ?>');"></div>
  <div class="sgh-specs-overlay" aria-hidden="true"></div>
  <div class="sgh-specs-container">
    <div class="sgh-specs-card">
      <div class="sgh-specs-badge">
        <span class="sgh-specs-badge__accent">//</span> <?php echo esc_html__('TƯ DUY KỸ THUẬT', 'saigonhoreca'); ?>
      </div>
      <h2 class="sgh-specs-title"><?php echo esc_html__('Sự Tinh Tế Trong Thiết Kế & Quy Hoạch Bếp', 'saigonhoreca'); ?></h2>
      <div class="sgh-specs-line" aria-hidden="true"></div>
      <p class="sgh-specs-paragraph">
        <?php echo esc_html__('Sol Kitchen & Bar quận 7 đã được trang bị một khu bếp rộng hơn 70m2, đảm bảo sự tiện nghi và chuyên nghiệp tối đa trong quá trình vận hành. Toàn bộ khu vực được lắp đặt các thiết bị bếp inox cao cấp như hệ thống tủ đông, tủ mát, lò nướng combi, salamander, plancha, và đặc biệt là hệ thống lò BBQ độc quyền do chính Saigon Horeca thiết kế, đo đạc và sản xuất riêng biệt để đáp ứng hoàn hảo thực đơn Mỹ Latinh đặc trưng của Sol quận 7.', 'saigonhoreca'); ?>
      </p>
    </div>
  </div>
</section>

