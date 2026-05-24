<?php
/**
 * Template Part: Contact Hero
 * @package SaigonHouse
 */
$contact_hero_img_url = sh_section_image('thi cong', '', 5);
?>
<section class="sh-contact-hero">
    <div class="sh-contact-hero__image" style="background-image: url('<?php echo esc_url($contact_hero_img_url); ?>');"></div>
    <div class="sh-contact-hero__fade"></div>

    <div class="sh-contact-hero__container">
        <span class="sh-contact-hero__label">Liên hệ với chúng tôi</span>
        <h1 class="sh-contact-hero__title" data-aos="bounce-up">
            Kết nối với <span class="sh-contact-hero__title-accent">Saigon House</span>
        </h1>
        <p class="sh-contact-hero__desc">Chúng tôi luôn sẵn sàng lắng nghe và biến ý tưởng về ngôi nhà mơ ước của bạn thành hiện thực. Hãy liên hệ ngay hôm nay!</p>
    </div>
</section>
