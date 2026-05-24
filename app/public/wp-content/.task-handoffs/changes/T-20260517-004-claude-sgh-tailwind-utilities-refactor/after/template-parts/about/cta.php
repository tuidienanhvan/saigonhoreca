<?php
/**
 * Template Part: About CTA
 */
$_pid = get_queried_object_id();
$_cta_title = get_post_meta($_pid, '_sgh_cta_title', true) ?: 'Bạn Đã Sẵn Sàng Xây Dựng Tổ Ấm?';
$_cta_desc  = get_post_meta($_pid, '_sgh_cta_desc', true) ?: 'Hãy để Saigon House đồng hành cùng bạn hiện thực hóa ngôi nhà mơ ước. Liên hệ ngay hôm nay để nhận tư vấn miễn phí!';
?>
<section class="about-cta">

    <div class="about-cta__pattern"></div>
    <div class="about-cta__container" data-aos="fade-up">
        <h2 class="about-cta__heading"><?php echo esc_html($_cta_title); ?></h2>
        <p class="about-cta__copy">
            <?php echo esc_html($_cta_desc); ?>
        </p>
        <div class="about-cta__actions" data-aos="zoom-in" data-aos-delay="200">
             <a href="tel:<?php echo esc_attr(saigonhouse_contact('hotline_raw')); ?>" class="about-cta__btn about-cta__btn--call sgh-glow-btn">
                 <?php echo sh_icon('phone', 'about-cta__btn-icon'); ?>
                 <?php echo esc_html(saigonhouse_contact('hotline')); ?>
             </a>
             <a href="<?php echo home_url('/lien-he'); ?>" class="about-cta__btn about-cta__btn--mail sgh-glow-btn">
                 <?php echo sh_icon('mail', 'about-cta__btn-icon'); ?>
                 Gửi Yêu Cầu
             </a>
        </div>
    </div>
</section>
