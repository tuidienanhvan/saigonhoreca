<?php
/**
 * Template Part: About Hero
 */
$page_id = get_the_ID();
$hero_img_id = get_post_meta($page_id, '_sgh_about_hero_image', true);
$hero_img_url = $hero_img_id ? wp_get_attachment_image_url($hero_img_id, 'full') : sh_section_image('kien truc', '', 1);
?>
<div class="sh-about-hero">
    <div class="sh-about-hero__image" style="background-image: url('<?php echo esc_url($hero_img_url); ?>');"></div>
    <div class="sh-about-hero__overlay"></div>

    <div class="sh-about-hero__container">
        <span class="sh-about-hero__badge">Từ Năm 2014</span>
        <h1 class="sh-about-hero__title" data-aos="fade-down">Về Chúng Tôi</h1>
        <p class="sh-about-hero__subtitle" data-aos="fade-up" data-aos-delay="200">"Mang An Tâm Đến Mọi Nhà"</p>
    </div>
</div>
