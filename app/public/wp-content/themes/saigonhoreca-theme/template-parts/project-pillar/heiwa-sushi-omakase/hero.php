<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-hwa">
  <div class="pp-hero-hwa__media" style="background-image:url('<?php echo sgh_img('2024/01/SGH-Heiwasushi-1.webp'); ?>');"></div>
  <div class="pp-hero-hwa__overlay" aria-hidden="true"></div>
  <div class="pp-hero-hwa__content">
    <span class="pp-hero-hwa__subhead">// <?php echo esc_html__('PROJECT PILLAR • OMAKASE EXPERTISE', 'saigonhoreca'); ?></span>
    <h1 class="pp-hero-hwa__title"><?php echo esc_html__('HEIWA SUSHI OMAKASE', 'saigonhoreca'); ?></h1>
    <p class="pp-hero-hwa__subtitle"><?php echo esc_html__('Kiến tạo không gian ẩm thực Nhật Bản đích thực, nơi tinh hoa ẩm thực truyền thống hội tụ cùng giải pháp vận hành bếp công nghiệp đỉnh cao.', 'saigonhoreca'); ?></p>
  </div>
  
  <!-- Scroll indicator thanh mảnh -->
  <div class="pp-hero-hwa__scroll" aria-hidden="true">
    <span class="pp-hero-hwa__scroll-line"></span>
  </div>
</section>

