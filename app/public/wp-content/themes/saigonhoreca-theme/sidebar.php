<?php
/**
 * Sidebar — single-post sidebar.
 *
 * Content is driven entirely by the admin:
 *   1. CTA list       → Appearance → Menus → Sidebar — Tiện Ích Nổi Bật
 *   2. Widget slot    → Appearance → Widgets → Sidebar — Widget Area
 *   3. Hotline number → Customizer → saigonhouse_hotline theme mod
 *   4. Contact URL    → filterable via `sh_sidebar_contact_url`
 *
 * @package SaigonHoreca
 */

$hotline       = get_theme_mod('saigonhouse_hotline', '0961 868 968');
$hotline_clean = preg_replace('/[^0-9+]/', '', (string) $hotline);
$contact_url   = apply_filters('sh_sidebar_contact_url', home_url('/lien-he'));
$cta_title     = apply_filters('sh_sidebar_cta_title', __('Tiện Ích Nổi Bật', 'saigonhoreca'));
$has_menu      = has_nav_menu('sidebar_menu');
$has_widgets   = is_active_sidebar('saigonhouse-sidebar');
?>

<aside class="sh-sidebar">

    <?php if ($has_menu) : ?>
    <!-- Widget: CTA Menu (Appearance → Menus → Sidebar — Tiện Ích Nổi Bật) -->
    <div class="sh-sidebar__widget">
        <h3 class="sh-sidebar__widget-title">
            <span class="sh-sidebar__title-bar" aria-hidden="true"></span>
            <?php echo esc_html($cta_title); ?>
        </h3>

        <?php
        wp_nav_menu([
            'theme_location' => 'sidebar_menu',
            'container'      => false,
            'menu_class'     => 'sh-sidebar__cta-list',
            'items_wrap'     => '<nav class="%2$s" aria-label="' . esc_attr($cta_title) . '">%3$s</nav>',
            'walker'         => new SaigonHoreca_Sidebar_Walker(),
            'fallback_cb'    => false,
            'depth'          => 1,
        ]);
        ?>
    </div>
    <?php endif; ?>

    <?php if ($has_widgets) : ?>
    <!-- Widget Area: dynamic_sidebar — kéo thả widget từ Appearance → Widgets -->
    <div class="sh-sidebar__widgets-area">
        <?php dynamic_sidebar('saigonhouse-sidebar'); ?>
    </div>
    <?php else : ?>
    <!-- Default fallback: Dịch vụ nổi bật.
         Shown when admin hasn't added any widgets yet. To customise,
         go to Appearance → Widgets → Sidebar — Widget Area and drag in
         a Text / Custom HTML / Nav Menu widget — this block will disappear. -->
    <div class="sh-sidebar__widget sh-sidebar__widget--default">
        <h3 class="sh-sidebar__widget-title">
            <span class="sh-sidebar__title-bar" aria-hidden="true"></span>
            <?php esc_html_e('Bảng Báo Giá', 'saigonhoreca'); ?>
        </h3>
        <ul class="sh-sidebar__service-list">
            <li><a href="<?php echo esc_url(home_url('/don-gia-thiet-ke-kien-truc')); ?>" class="sh-sidebar__service-link">
                <?php echo sh_icon('chevron-right', 'sh-sidebar__service-icon'); ?>
                <span><?php esc_html_e('Đơn giá thiết kế kiến trúc', 'saigonhoreca'); ?></span>
            </a></li>
            <li><a href="<?php echo esc_url(home_url('/don-gia-thiet-ke-noi-that')); ?>" class="sh-sidebar__service-link">
                <?php echo sh_icon('chevron-right', 'sh-sidebar__service-icon'); ?>
                <span><?php esc_html_e('Đơn giá thiết kế nội thất', 'saigonhoreca'); ?></span>
            </a></li>
            <li><a href="<?php echo esc_url(home_url('/don-gia-phan-tho')); ?>" class="sh-sidebar__service-link">
                <?php echo sh_icon('chevron-right', 'sh-sidebar__service-icon'); ?>
                <span><?php esc_html_e('Đơn giá phần thô', 'saigonhoreca'); ?></span>
            </a></li>
            <li><a href="<?php echo esc_url(home_url('/don-gia-tron-goi')); ?>" class="sh-sidebar__service-link">
                <?php echo sh_icon('chevron-right', 'sh-sidebar__service-icon'); ?>
                <span><?php esc_html_e('Đơn giá trọn gói', 'saigonhoreca'); ?></span>
            </a></li>
            <li><a href="<?php echo esc_url(home_url('/bao-gia-theo-loai-nha')); ?>" class="sh-sidebar__service-link">
                <?php echo sh_icon('chevron-right', 'sh-sidebar__service-icon'); ?>
                <span><?php esc_html_e('Báo giá theo loại nhà', 'saigonhoreca'); ?></span>
            </a></li>
            <li><a href="<?php echo esc_url(home_url('/bang-gia')); ?>" class="sh-sidebar__service-link sh-sidebar__service-link--primary">
                <?php echo sh_icon('calculator', 'sh-sidebar__service-icon'); ?>
                <span><?php esc_html_e('Bảng Báo Giá Chi Tiết', 'saigonhoreca'); ?></span>
            </a></li>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Sticky Contact Banner -->
    <div class="sh-sidebar__sticky">
        <div class="sh-sidebar__contact">
            <div class="sh-sidebar__contact-deco" aria-hidden="true"></div>
            <div class="sh-sidebar__contact-glow" aria-hidden="true"></div>

            <div class="sh-sidebar__contact-icon-wrap">
                <?php echo sh_icon('phone-call', 'sh-sidebar__contact-icon'); ?>
            </div>

            <h3 class="sh-sidebar__contact-title"><?php esc_html_e('Cần Hỗ Trợ?', 'saigonhoreca'); ?></h3>
            <p class="sh-sidebar__contact-desc">
                <?php esc_html_e('Liên hệ với SaigonHoreca để nhận tư vấn và báo giá hoàn toàn miễn phí.', 'saigonhoreca'); ?>
            </p>

            <?php if (!empty($hotline_clean)) : ?>
            <a href="tel:<?php echo esc_attr($hotline_clean); ?>"
               class="sh-sidebar__contact-phone"
               aria-label="<?php echo esc_attr(sprintf(__('Gọi hotline %s', 'saigonhoreca'), $hotline)); ?>">
                <?php echo sh_icon('phone', 'sh-sidebar__contact-phone-icon'); ?>
                <span class="sh-sidebar__contact-phone-number"><?php echo esc_html($hotline); ?></span>
            </a>
            <?php endif; ?>

            <a href="<?php echo esc_url($contact_url); ?>" class="sh-sidebar__contact-btn">
                <span><?php esc_html_e('Gửi Yêu Cầu Tư Vấn', 'saigonhoreca'); ?></span>
                <?php echo sh_icon('arrow-right', 'sh-sidebar__contact-btn-icon'); ?>
            </a>
        </div>
    </div>

</aside>
