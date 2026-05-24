<?php
/**
 * Project Pillar — hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-hmd">
    <div class="pp-hero-hmd__media" style="background-image:url('<?php echo sgh_img('2025/11/hemma-desserts-3.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-hmd__overlay" aria-hidden="true"></div>
    <div class="pp-hero-hmd__content">
        <h1 class="pp-hero-hmd__title"><?php echo esc_html__('HEMMA DESSERTS', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-hmd__subhead"><?php echo esc_html__('Một góc nhỏ châu Âu giữa Thảo Điền', 'saigonhoreca'); ?></p>
        <p class="pp-hero-hmd__subtitle"><?php echo esc_html__('Ẩn mình ở cuối con đường Nguyễn Bá Huân, Hemma Desserts là một tiệm tráng miệng kiểu Âu nhỏ xinh được vận hành bởi hai founder trẻ người nước ngoài, cũng chính là đầu bếp đứng bếp bánh mỗi ngày.', 'saigonhoreca'); ?></p>
    </div>
</section>
