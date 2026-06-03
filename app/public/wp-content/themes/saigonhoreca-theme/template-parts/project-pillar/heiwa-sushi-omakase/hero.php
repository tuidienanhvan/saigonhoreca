<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #1: hero — Ken-Burns omakase space with zen glass content card.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-hwa">
  <div class="pp-hero-hwa__media" style="background-image:url('<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-quay-bar-bieu-dien-omakase.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-hwa__overlay" aria-hidden="true"></div>
  <div class="pp-hero-hwa__grid" aria-hidden="true"></div>

  <div class="pp-hero-hwa__glow pp-hero-hwa__glow--lt" aria-hidden="true"></div>
  <div class="pp-hero-hwa__glow pp-hero-hwa__glow--rb" aria-hidden="true"></div>
  <div class="pp-hero-hwa__rail pp-hero-hwa__rail--left" aria-hidden="true">OMAKASE / DISTRICT 2</div>
  <div class="pp-hero-hwa__rail pp-hero-hwa__rail--right" aria-hidden="true">SUSHI BAR SYSTEM / SGH</div>

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
