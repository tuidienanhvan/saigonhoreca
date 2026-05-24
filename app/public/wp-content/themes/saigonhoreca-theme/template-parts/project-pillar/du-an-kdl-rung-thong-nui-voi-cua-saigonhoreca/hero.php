<?php
/**
 * Project Pillar — du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-rtn">
    <div class="pp-hero-rtn__media" style="background-image:url('<?php echo sgh_img('2024/08/lienkhuong.png'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-rtn__overlay" aria-hidden="true"></div>
    <div class="pp-hero-rtn__content">
        <h1 class="pp-hero-rtn__title"><?php echo esc_html__('KDL RỪNG THÔNG NÚI VOI', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-rtn__subhead"><?php echo esc_html__('Thiên Nhiên Hùng Vĩ - Trải Nghiệm Tuyệt Vời', 'saigonhoreca'); ?></p>
        <p class="pp-hero-rtn__subtitle"><?php echo esc_html__('Saigon Horeca và hành trình kiến tạo ẩm thực Tây Nguyên tinh tế tại', 'saigonhoreca'); ?></p>
    </div>
</section>
