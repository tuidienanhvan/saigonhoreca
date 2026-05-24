<?php
/**
 * Project Pillar — ganh-hao-noi-hon-bien-trong-tung-net-kien-truc
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-gha">
    <div class="pp-hero-gha__media" style="background-image:url('<?php echo sgh_img('2025/05/ganh-hao-sgh-1.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-gha__overlay" aria-hidden="true"></div>
    <div class="pp-hero-gha__content">
        <h1 class="pp-hero-gha__title"><?php echo esc_html__('Gành Hào', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-gha__subtitle"><?php echo esc_html__('– nơi hồn biển hội tụ trong từng nét kiến trúc', 'saigonhoreca'); ?></p>
    </div>
</section>
