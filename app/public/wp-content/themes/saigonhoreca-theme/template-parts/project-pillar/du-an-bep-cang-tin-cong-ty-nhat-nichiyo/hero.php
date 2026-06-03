<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #1: hero — Japanese industrial canteen, Ken-Burns kitchen + hinomaru disc.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-nichiyo">
    <div class="pp-hero-nichiyo__media" style="background-image:url('<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-du-an-1.jpg'); ?>');" aria-hidden="true"></div>
    <div class="pp-hero-nichiyo__overlay" aria-hidden="true"></div>
    <div class="pp-hero-nichiyo__grid" aria-hidden="true"></div>
    <div class="pp-hero-nichiyo__disc" aria-hidden="true"></div>
    <div class="pp-hero-nichiyo__rail pp-hero-nichiyo__rail--left" aria-hidden="true">工 · KOJO · MONOZUKURI</div>

    <div class="pp-hero-nichiyo__content">
        <div class="pp-hero-nichiyo__eyebrow"><?php echo esc_html__('Bếp căng tin công nghiệp · Nichiyo', 'saigonhoreca'); ?></div>
        <h1 class="pp-hero-nichiyo__title"><?php echo esc_html__('Dự án bếp ăn công nghiệp bếp căng tin công ty Nhật Nichiyo', 'saigonhoreca'); ?></h1>
        <div class="pp-hero-nichiyo__divider" aria-hidden="true"></div>
        <div class="pp-hero-nichiyo__meta" aria-label="<?php echo esc_attr__('Thông tin nổi bật dự án Nichiyo', 'saigonhoreca'); ?>">
            <span><?php echo esc_html__('Warabeya Nichiyo Group', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Monozukuri precision', 'saigonhoreca'); ?></span>
            <span><?php echo esc_html__('Inox tiêu chuẩn Nhật', 'saigonhoreca'); ?></span>
        </div>
    </div>
</section>
