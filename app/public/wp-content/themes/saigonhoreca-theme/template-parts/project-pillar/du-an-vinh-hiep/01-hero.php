<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero">
    <div class="pp-hero__media" style="background-image:url('<?php echo sgh_img('2025/01/sheh-fung-5.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero__overlay" aria-hidden="true"></div>
    <div class="pp-hero__content">
        <h1 class="pp-hero__title"><?php echo esc_html__('Dự án Vĩnh Hiệp – Coffee Lab', 'saigonhoreca'); ?></h1>
        <p class="pp-hero__subhead"><?php echo esc_html__('Khi không gian bếp và cà phê cùng kể một câu chuyện về chuẩn mực xuất khẩu', 'saigonhoreca'); ?></p>
    </div>
</section>
