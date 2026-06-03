<?php
/**
 * Project Pillar — mam-mam-eatery-lounge-nang-tam-mam-com-viet
 * Section #1: hero — Vietnamese Modern Heritage. Cinematic Ken-Burns
 * hero with rice-grain gold texture, conical-hat arc ornament, mâm motif.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-mam">
    <div class="pp-hero-mam__media" style="background-image:url('<?php echo sgh_img('mam-mam-eatery-lounge-nang-tam-mam-com-viet/mam-mam-nang-tam-mam-com-viet-1.jpg'); ?>');" aria-hidden="true"></div>
    <div class="pp-hero-mam__overlay" aria-hidden="true"></div>
    <div class="pp-hero-mam__glow" aria-hidden="true"></div>
    <div class="pp-hero-mam__tray" aria-hidden="true"></div>

    <div class="pp-hero-mam__content scroll-reveal">
        <span class="pp-hero-mam__eyebrow"><?php echo esc_html__('Di sản · Nâng tầm mâm cơm Việt', 'saigonhoreca'); ?></span>
        <h1 class="pp-hero-mam__title">MÂM MÂM EATERY &amp; LOUNGE</h1>
        <p class="pp-hero-mam__subhead"><?php echo esc_html__('Nâng tầm mâm cơm Việt trong không gian đậm chất quê nhà', 'saigonhoreca'); ?></p>
        <div class="pp-hero-mam__divider" aria-hidden="true"></div>
        <p class="pp-hero-mam__subtitle"><?php echo esc_html__('“Mâm cơm” bước ra thế giới – Khi không gian kể chuyện trở thành cầu nối văn hóa Việt', 'saigonhoreca'); ?></p>
    </div>
</section>
