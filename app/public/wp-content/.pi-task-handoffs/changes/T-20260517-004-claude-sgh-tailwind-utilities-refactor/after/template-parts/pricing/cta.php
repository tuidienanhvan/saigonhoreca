<?php
/**
 * Pricing CTA
 * @package SaigonHouse
 */
?>
<section id="contact" class="sh-price-cta">
    <div class="sh-price-cta__bg" style="background-image: url('<?php echo esc_url(sh_section_image('xay dung', '', 4)); ?>');"></div>
    <div class="sh-price-cta__fade"></div>

    <div class="sh-price-cta__container" data-aos="fade-up">
        <h2 class="sh-price-cta__title">Bạn Cần Báo Giá Chi Tiết Hơn?</h2>
        <p class="sh-price-cta__desc">Mỗi ngôi nhà là một tác phẩm riêng biệt. Hãy dùng công cụ dự toán online của chúng tôi hoặc liên hệ trực tiếp để được tư vấn miễn phí.</p>

        <div class="sh-price-cta__buttons">
            <a href="<?php echo home_url('/du-toan/'); ?>" class="sh-price-cta__btn sh-price-cta__btn--primary sgh-glow-btn">
                <?php echo sh_icon('calculator', 'sh-price-cta__btn-icon'); ?>
                Tính Dự Toán Online
            </a>

            <?php
            $hotline_action = get_theme_mod('saigonhouse_hotline_action', saigonhouse_contact('hotline_raw'));
            $hotline = get_theme_mod('saigonhouse_hotline', saigonhouse_contact('hotline'));
            ?>
            <a href="tel:<?php echo esc_attr($hotline_action); ?>" class="sh-price-cta__btn sh-price-cta__btn--outline">
                <?php echo sh_icon('phone', 'sh-price-cta__btn-icon'); ?>
                Gọi Ngay: <?php echo esc_html($hotline); ?>
            </a>
        </div>
    </div>
</section>
