<?php
/**
 * Project Pillar — grand-marble-thuong-hieu-banh-cao-cap-nhat-ban
 * Section #1: hero — Ken-Burns pastry banner with warm caramel ambient glow.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-gmarb">
    <div class="pp-hero-gmarb__media" style="background-image:url('<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-hero.jpg'); ?>');" aria-hidden="true"></div>
    <div class="pp-hero-gmarb__overlay" aria-hidden="true"></div>
    <div class="pp-hero-gmarb__grid" aria-hidden="true"></div>

    <div class="pp-hero-gmarb__glow pp-hero-gmarb__glow--lt" aria-hidden="true"></div>
    <div class="pp-hero-gmarb__glow pp-hero-gmarb__glow--rb" aria-hidden="true"></div>
    <div class="pp-hero-gmarb__rail pp-hero-gmarb__rail--left" aria-hidden="true">KYOTO &middot; SINCE 1996</div>
    <div class="pp-hero-gmarb__rail pp-hero-gmarb__rail--right" aria-hidden="true">MARBLE DANISH / SGH</div>

    <div class="pp-hero-gmarb__content scroll-reveal reveal-up">
        <div class="pp-hero-gmarb__subhead"><?php echo esc_html__('Thương hiệu bánh cao cấp Nhật Bản', 'saigonhoreca'); ?></div>
        <h1 class="pp-hero-gmarb__title"><?php echo esc_html__('Grand Marble', 'saigonhoreca'); ?></h1>
        <div class="pp-hero-gmarb__divider" aria-hidden="true"></div>
        <p class="pp-hero-gmarb__subtitle"><?php echo esc_html__('Kiệt tác bánh Danish Marble từ cố đô Kyoto, nơi hương vị truyền thống Nhật Bản hòa quyện cùng bí quyết làm bánh phương Tây.', 'saigonhoreca'); ?></p>
        <div class="pp-hero-gmarb__meta" aria-label="<?php echo esc_attr__('Thông tin nổi bật dự án Grand Marble', 'saigonhoreca'); ?>">
            <span><?php echo esc_html__('Kyoto · 1996', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Danish Marble', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Chocolate Marou', 'saigonhoreca'); ?></span>
        </div>
    </div>
</section>
