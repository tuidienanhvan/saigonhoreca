<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-nic">
    <div class="pp-hero-nic__media" style="background-image:url('<?php echo sgh_img('2023/11/bg-graphic-1.svg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-nic__overlay" aria-hidden="true"></div>
    <div class="pp-hero-nic__content">
        <h1 class="pp-hero-nic__title"><?php echo esc_html__('Dự án bếp ăn công nghiệp bếp căng tin công ty Nhật Nichiyo', 'saigonhoreca'); ?></h1>
    </div>
</section>
