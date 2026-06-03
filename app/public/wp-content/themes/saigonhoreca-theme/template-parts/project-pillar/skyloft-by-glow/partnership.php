<?php
/**
 * Project Pillar — skyloft-by-glow
 * Section #5: partnership — cinematic bg + glassmorphic content card with CSS Parallax.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-sky">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="pp-section-bg-sky__bg" style="background-image:url('<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-dem-tiec-rooftop-anh-den-san-khau.webp'); ?>');"></div>
  <div class="pp-section-bg-sky__overlay" aria-hidden="true"></div>
  <div class="pp-section-bg-sky__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <!-- Di chuyển scroll-reveal vào khối nội dung để tránh xung đột transform của fixed background parallax -->
    <div class="pp-section-bg-sky__content scroll-reveal">
      <div class="pp-text-sky__divider pp-text-sky__divider--dots" aria-hidden="true"></div>
      <h2 class="pp-section-bg-sky__title"><?php echo esc_html__('Những món Cocktail nhất định phải thử', 'saigonhoreca'); ?></h2>
      <div class="pp-section-bg-sky__body">
        <p><?php echo esc_html__('Khi đến với Skyloft by Glow, có ba món cocktail độc đáo mà bạn không nên bỏ lỡ. Đầu tiên là \'The Bird is Word\', mang lại cảm giác nhẹ nhàng, bay bổng như cánh chim giữa bầu trời kết hợp cùng hương khói quả mọng rừng tự nhiên. Tiếp theo là \'Dawn of the Walking\', một bản biến tấu đầy thú vị từ ly cocktail TiKi huyền thoại – Zombie. Cuối cùng là \'Fish out of Water\', thức uống lấy cảm hứng từ hương vị Mexico truyền thống được lên men từ dứa chín.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
