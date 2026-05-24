<?php
/**
 * Project Pillar — mam-mam-eatery-lounge-nang-tam-mam-com-viet
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-mml">
    <div class="pp-hero-mml__media" style="background-image:url('<?php echo sgh_img('2025/05/mam-mam-nang-tam-mam-com-viet-1.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-mml__overlay" aria-hidden="true"></div>
    <div class="pp-hero-mml__content">
        <h1 class="pp-hero-mml__title">MÂM MÂM EATERY &amp; LOUNGE</h1>
        <p class="pp-hero-mml__subhead"><?php echo esc_html__('Nâng tầm mâm cơm Việt trong không gian đậm chất quê nhà', 'saigonhoreca'); ?></p>
        <p class="pp-hero-mml__subtitle"><?php echo esc_html__('“Mâm cơm” bước ra thế giới – Khi không gian kể chuyện trở thành cầu nối văn hóa Việt', 'saigonhoreca'); ?></p>
    </div>
</section>
