<?php
/**
 * Project Pillar — g-cup-coffee-bistro
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-gcb">
    <div class="pp-hero-gcb__media" style="background-image:url('<?php echo sgh_img('2025/05/du-an-g-cup-coffee-bistro.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-gcb__overlay" aria-hidden="true"></div>
    <div class="pp-hero-gcb__content">
        <h1 class="pp-hero-gcb__title"><?php echo esc_html__('G.CUP COFFEE & BISTRO', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-gcb__subhead"><?php echo esc_html__('Bếp vận hành đồng điệu với không gian được trau chuốt đến từng chi tiết', 'saigonhoreca'); ?></p>
        <p class="pp-hero-gcb__subtitle"><?php echo esc_html__('Giữa lòng Metropole Thủ Thiêm – khu đô thị hiện đại và sôi động bậc nhất TP.HCM – G.Cup Coffee & Bistro chọn cho mình một hướng đi riêng: tinh tế, trau chuốt và đặt trọn tâm huyết vào từng trải nghiệm của khách hàng.', 'saigonhoreca'); ?></p>
    </div>
</section>
