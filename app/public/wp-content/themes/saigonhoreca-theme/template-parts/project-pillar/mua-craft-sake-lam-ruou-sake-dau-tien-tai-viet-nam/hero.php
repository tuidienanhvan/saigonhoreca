<?php
/**
 * Project Pillar — mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam
 * Section #1: hero — cinematic brewery banner (bright image, text-on-image).
 *
 * Owner feedback applied: KHÔNG lớp nền đen, KHÔNG khung chữ phía sau title.
 * Title + subtitle nằm trực tiếp trên ảnh sáng, chỉ dùng text-shadow để
 * đảm bảo độ đọc.
 *
 * Kỹ thuật "Parallax Clip-Path" (chuyển từ ken-burns sang): section dùng
 * clip-path: inset(0) để giam lớp nền position:fixed bên trong khung hero =
 * parallax mượt bằng GPU, KHÔNG JavaScript. Lớp __bg là ảnh hero thật ở chế
 * độ fixed; iframe video phủ lên cũng fixed; lớp __overlay gradient token nhẹ
 * giữ ảnh tươi sáng. Mobile: __bg/__video về absolute cho ổn định.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-hero-mua">
    <!-- Lớp nền Parallax Clip-Path: ảnh hero thật, position:fixed (giam bởi clip-path của section) -->
    <div class="pp-hero-mua__bg" aria-hidden="true" style="background-image:url('<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-hero.jpg'); ?>');"></div>
    <!-- Video nền phủ lên ảnh, cũng fixed để cùng nhịp parallax với clip-path -->
    <iframe class="pp-hero-mua__video" src="https://www.youtube.com/embed/38Dr2BO7xzE?autoplay=1&mute=1&loop=1&playlist=38Dr2BO7xzE&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="<?php echo esc_attr__('Video nền Mùa Craft Sake', 'saigonhoreca'); ?>" tabindex="-1" loading="lazy" aria-hidden="true"></iframe>
    <!-- Overlay gradient token NHẸ — giữ ảnh sáng, KHÔNG phải lớp đen -->
    <div class="pp-hero-mua__overlay" aria-hidden="true"></div>
    <div class="pp-hero-mua__kanji" aria-hidden="true">酒</div>
    <div class="pp-hero-mua__content">
        <p class="pp-hero-mua__subhead scroll-reveal reveal-up-short duration-800"><?php echo esc_html__('Nhà máy rượu sake thủ công đầu tiên tại Việt Nam', 'saigonhoreca'); ?></p>
        <h1 class="pp-hero-mua__title scroll-reveal reveal-up duration-1000 delay-100"><?php echo esc_html__('Mùa Craft Sake', 'saigonhoreca'); ?></h1>
        <span class="pp-hero-mua__divider scroll-reveal reveal-fade duration-1000 delay-300" aria-hidden="true"></span>
        <p class="pp-hero-mua__subtitle scroll-reveal reveal-up-short duration-1000 delay-400"><?php echo esc_html__('Brewing Fresh Sake for the people', 'saigonhoreca'); ?></p>
    </div>
</section>
