<?php
$_pid = get_queried_object_id();
$_p_badge    = get_post_meta($_pid, '_sgh_price_badge', true) ?: 'Báo Giá 2026';
$_p_title    = get_post_meta($_pid, '_sgh_price_title', true) ?: 'BẢNG GIÁ';
$_p_subtitle = get_post_meta($_pid, '_sgh_price_subtitle', true) ?: 'Xây Dựng &amp; Thiết Kế';
$_p_desc     = get_post_meta($_pid, '_sgh_price_desc', true) ?: 'Minh bạch từng hạng mục. Chi tiết từng vật tư. Cam kết <strong>không phát sinh</strong> chi phí sau khi ký kết hợp đồng.';
$_links = [
    ['url' => get_post_meta($_pid, '_sgh_price_link1', true) ?: '/bao-gia-thiet-ke-kien-truc-xay-dung-cong-trinh-1/', 'text' => get_post_meta($_pid, '_sgh_price_link1_text', true) ?: 'Đơn Giá Thiết Kế', 'icon' => 'layout', 'mod' => ''],
    ['url' => get_post_meta($_pid, '_sgh_price_link2', true) ?: '/bang-bao-gia-thi-cong-xay-dung-nha-phan-tho-1/', 'text' => get_post_meta($_pid, '_sgh_price_link2_text', true) ?: 'Thi Công Phần Thô', 'icon' => 'hammer', 'mod' => ''],
    ['url' => get_post_meta($_pid, '_sgh_price_link3', true) ?: '/bang-bao-gia-xay-dung-nha-tron-goi-1/', 'text' => get_post_meta($_pid, '_sgh_price_link3_text', true) ?: 'Xây Nhà Trọn Gói', 'icon' => 'home', 'mod' => ' sh-price-hero__anchor--primary'],
];
?>
<section class="sh-price-hero">
    <div class="sh-price-hero__grid-bg"></div>
    <div class="sh-price-hero__fade"></div>

    <div class="sh-price-hero__container">
        <div class="sh-price-hero__badge">
            <span class="sh-price-hero__badge-dot"></span>
            <span class="sh-price-hero__badge-text"><?php echo esc_html($_p_badge); ?></span>
        </div>

        <h1 class="sh-price-hero__title" data-aos="fade-down">
            <span><?php echo esc_html($_p_title); ?></span>
            <span class="sh-price-hero__title-accent"><?php echo wp_kses_post($_p_subtitle); ?></span>
        </h1>

        <p class="sh-price-hero__desc" data-aos="fade-up" data-aos-delay="200">
            <?php echo wp_kses_post($_p_desc); ?>
        </p>

        <div class="sh-price-hero__anchors">
            <?php foreach ($_links as $link): ?>
            <a href="<?php echo esc_url(home_url($link['url'])); ?>" class="sh-price-hero__anchor<?php echo $link['mod']; ?>">
                <?php echo sh_icon($link['icon'], 'sh-price-hero__anchor-icon'); ?>
                <?php echo esc_html($link['text']); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
