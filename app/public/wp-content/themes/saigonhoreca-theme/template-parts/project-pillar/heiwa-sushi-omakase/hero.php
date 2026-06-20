<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #1: hero — full-bleed omakase space using the Parallax Clip-Path
 * technique (clip-path inset on the section confines a position:fixed bg = a
 * GPU-smooth, JS-free parallax). Bright image, NO dark overlay/panel behind the
 * title (owner feedback): only a LIGHT token gradient + legibility text-shadow.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-hwa">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed bị section clip-path giới hạn) -->
  <div class="pp-hero-hwa__bg" style="background-image:url('<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-quay-bar-bieu-dien-omakase.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-hwa__overlay" aria-hidden="true"></div>

  <div class="pp-hero-hwa__content">
    <span class="pp-hero-hwa__subhead">// <?php echo esc_html__('PROJECT PILLAR • OMAKASE EXPERTISE', 'saigonhoreca'); ?></span>
    <h1 class="pp-hero-hwa__title"><?php echo esc_html__('Heiwa Sushi Omakase', 'saigonhoreca'); ?></h1>
    <div class="pp-hero-hwa__divider" aria-hidden="true"></div>
    <p class="pp-hero-hwa__subtitle"><?php echo esc_html__('Kiến tạo không gian ẩm thực Nhật Bản đích thực, nơi tinh hoa ẩm thực truyền thống hội tụ cùng giải pháp vận hành bếp công nghiệp đỉnh cao.', 'saigonhoreca'); ?></p>
    <div class="pp-hero-hwa__meta" aria-label="<?php echo esc_attr__('Thông tin nổi bật dự án Heiwa Sushi Omakase', 'saigonhoreca'); ?>">
      <span><?php echo esc_html__('Quận 2, HCMC', 'saigonhoreca'); ?></span>
      <span><?php echo esc_html__('Bếp mở biểu diễn', 'saigonhoreca'); ?></span>
      <span><?php echo esc_html__('Wabi-Sabi', 'saigonhoreca'); ?></span>
    </div>
  </div>

  <div class="pp-hero-hwa__scroll" aria-hidden="true">
    <span class="pp-hero-hwa__scroll-line"></span>
  </div>
</section>
