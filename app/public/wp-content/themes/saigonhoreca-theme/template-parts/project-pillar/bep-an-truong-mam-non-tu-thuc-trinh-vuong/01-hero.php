<?php
/**
 * Project Pillar — bep-an-truong-mam-non-tu-thuc-trinh-vuong
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero">
    <div class="pp-hero__media" style="background-image:url('<?php echo sgh_img('2025/08/goi-y-thiet-ke-bep-truong-hoc.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero__overlay" aria-hidden="true"></div>
    <div class="pp-hero__content">
        <h1 class="pp-hero__title"><?php echo esc_html__('TRƯỜNG MẦM NON TRINH VƯƠNG', 'saigonhoreca'); ?></h1>
        <p class="pp-hero__subhead"><?php echo esc_html__('"Một bếp – 500 suất ăn – 0 phút trễ"', 'saigonhoreca'); ?></p>
        <p class="pp-hero__subtitle"><?php echo esc_html__('500 suất ăn, 0 phút trễ — đó không chỉ là con số thể hiện hiệu suất vận hành của bếp ăn Trường Mầm Non Tư Thục Trinh Vương, mà còn là minh chứng cho sự tính toán tỉ mỉ trong từng chi tiết thiết kế mà Saigon Horeca (SGH) đã thực hiện.', 'saigonhoreca'); ?></p>
    </div>
</section>
