<?php
/**
 * Template Part: Mobile Menu (Sidebar)
 *
 * T-016: Wrapped in <template> so the ~180 DOM nodes inside don't count
 * toward Lighthouse `dom-size` on desktop visits. JS clones the template
 * into the empty shell on first hamburger click — see `main-modern.js`
 * Mobile Sidebar block.
 *
 * @package SaigonHoreca
 */

$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
$phone_link = $args['phone_link'] ?? (function_exists('saigonhouse_phone_link') ? saigonhouse_phone_link() : 'tel:' . str_replace([' ', '.', ','], '', $contact['hotline'] ?? ''));
$email_link = $args['email_link'] ?? (function_exists('saigonhouse_email_link') ? saigonhouse_email_link() : 'mailto:' . ($contact['email_primary'] ?? ''));
$theme_uri  = get_template_directory_uri();
?>

<!-- Mobile Sidebar Overlay (always present so JS can toggle it) -->
<div id="mobile-sidebar-overlay" class="sh-mobile-overlay"></div>

<!-- Mobile Sidebar mount point (empty until JS hydrates the template) -->
<div id="mobile-sidebar" class="sh-mobile" data-sgh-mobile-menu="lazy"></div>

<!-- Mobile menu markup stored in <template> — NOT part of the live DOM
     until JS clones it on first toggle. Saves ~180 DOM nodes for the
     90 %+ of homepage visits that never open the mobile menu. -->
<template id="sgh-mobile-menu-tpl">
    <!-- Header -->
    <div class="sh-mobile__header">
        <span class="sh-mobile__header-title">
            <?php echo sh_icon('list', 'sh-mobile__header-icon'); ?>
            <?php esc_html_e('Menu', 'saigonhoreca'); ?>
        </span>
        <?php
        $current_lang = defined('PI_CURRENT_LANGUAGE') ? PI_CURRENT_LANGUAGE : 'vi';
        $vi_url = class_exists('Pi_Multilang_Frontend') ? (Pi_Multilang_Frontend::url_in_language('vi') ?: home_url('/')) : home_url('/');
        $en_url = class_exists('Pi_Multilang_Frontend') ? (Pi_Multilang_Frontend::url_in_language('en') ?: home_url('/en/')) : home_url('/en/');
        $ja_url = class_exists('Pi_Multilang_Frontend') ? (Pi_Multilang_Frontend::url_in_language('ja') ?: home_url('/ja/')) : home_url('/ja/');
        ?>
        <div class="sh-header__lang-group sh-mobile__lang-group" style="margin-left:auto; margin-right:12px;">
            <a href="<?php echo esc_url($vi_url); ?>" class="sh-header__lang <?php echo $current_lang === 'vi' ? 'sh-header__lang--active active is-active' : ''; ?>" aria-label="Tiếng Việt" title="Tiếng Việt" <?php echo $current_lang === 'vi' ? 'aria-current="page"' : ''; ?>>
                <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="22" height="22">
                    <defs>
                        <clipPath id="sh-vn-clip-mob">
                            <circle cx="16" cy="16" r="16"/>
                        </clipPath>
                        <radialGradient id="sh-vn-grad-mob" cx="38%" cy="28%" r="72%">
                            <stop offset="0%" stop-color="#EA2820"/>
                            <stop offset="100%" stop-color="#A80D08"/>
                        </radialGradient>
                        <linearGradient id="sh-vn-star-mob" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#FFF275"/>
                            <stop offset="100%" stop-color="#FFB800"/>
                        </linearGradient>
                    </defs>
                    <g clip-path="url(#sh-vn-clip-mob)">
                        <circle cx="16" cy="16" r="16" fill="url(#sh-vn-grad-mob)"/>
                        <polygon points="16,8.2 17.76,13.62 23.46,13.62 18.85,16.97 20.61,22.39 16,19.04 11.39,22.39 13.15,16.97 8.54,13.62 14.24,13.62" fill="url(#sh-vn-star-mob)"/>
                        <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1"/>
                    </g>
                </svg>
            </a>
            <a href="<?php echo esc_url($en_url); ?>" class="sh-header__lang <?php echo $current_lang === 'en' ? 'sh-header__lang--active active is-active' : ''; ?>" aria-label="English" title="English" <?php echo $current_lang === 'en' ? 'aria-current="page"' : ''; ?>>
                <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="22" height="22">
                    <defs>
                        <clipPath id="sh-uk-clip-mob">
                            <circle cx="16" cy="16" r="16"/>
                        </clipPath>
                    </defs>
                    <g clip-path="url(#sh-uk-clip-mob)">
                        <circle cx="16" cy="16" r="16" fill="#012169"/>
                        <path d="M-4,-4 L36,36 M36,-4 L-4,36" stroke="#FFFFFF" stroke-width="5.5" stroke-linecap="square"/>
                        <path d="M-4,-4 L16,16 M36,-4 L16,16 M36,36 L16,16 M-4,36 L16,16" stroke="#C8102E" stroke-width="2"/>
                        <path d="M16,-4 V36 M-4,16 H36" stroke="#FFFFFF" stroke-width="9"/>
                        <path d="M16,-4 V36 M-4,16 H36" stroke="#C8102E" stroke-width="5.5"/>
                        <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1"/>
                    </g>
                </svg>
            </a>
            <a href="<?php echo esc_url($ja_url); ?>" class="sh-header__lang <?php echo $current_lang === 'ja' ? 'sh-header__lang--active active is-active' : ''; ?>" aria-label="日本語" title="日本語" <?php echo $current_lang === 'ja' ? 'aria-current="page"' : ''; ?>>
                <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="22" height="22">
                    <defs>
                        <clipPath id="sh-ja-clip-mob">
                            <circle cx="16" cy="16" r="16"/>
                        </clipPath>
                        <radialGradient id="sh-ja-sun-mob" cx="42%" cy="36%" r="65%">
                            <stop offset="0%" stop-color="#DC143C"/>
                            <stop offset="100%" stop-color="#9E001F"/>
                        </radialGradient>
                    </defs>
                    <g clip-path="url(#sh-ja-clip-mob)">
                        <circle cx="16" cy="16" r="16" fill="#F8F9FA"/>
                        <circle cx="16" cy="16" r="9.6" fill="url(#sh-ja-sun-mob)"/>
                        <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(0,0,0,0.08)" stroke-width="1"/>
                        <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="0.75"/>
                    </g>
                </svg>
            </a>
        </div>
        <button id="mobile-menu-close" class="sh-mobile__close-btn" aria-label="<?php esc_attr_e('Đóng menu', 'saigonhoreca'); ?>">
            <?php echo sh_icon('x', 'sh-mobile__close-icon'); ?>
        </button>
    </div>

    <!-- Content -->
    <div class="sh-mobile__body">
        <!-- Search -->
        <div class="sh-mobile__search-wrap">
            <form role="search" method="get" class="sh-mobile__search" action="<?php echo esc_url(home_url('/')); ?>">
                <span class="sh-mobile__search-icon-wrap">
                    <?php echo sh_icon('search', 'sh-mobile__search-svg'); ?>
                </span>
                <input type="search" name="s" class="sh-mobile__search-input" aria-label="<?php esc_attr_e('Tìm kiếm', 'saigonhoreca'); ?>" placeholder="<?php esc_attr_e('Tìm kiếm...', 'saigonhoreca'); ?>">
            </form>
        </div>

        <!-- Nav Menu -->
        <nav class="sh-mobile__nav">
            <?php
            $has_menu = has_nav_menu('primary');
            $menu_items = [];
            if ($has_menu) {
                $locations = get_nav_menu_locations();
                $menu_id = $locations['primary'] ?? 0;
                if ($menu_id) {
                    $menu_items = wp_get_nav_menu_items($menu_id);
                }
            }
            $has_valid_menu = !empty($menu_items) && !is_wp_error($menu_items) && count($menu_items) > 0;

            if (isset($args['menu_html'])) {
                echo $args['menu_html'];
            } elseif ($has_valid_menu) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'sh-mobile__menu-list',
                    'walker'         => class_exists('SaigonHoreca_Mobile_Walker') ? new SaigonHoreca_Mobile_Walker() : '',
                ));
            } else {
                $fallback_items = function_exists('sgh_get_primary_navigation_definition')
                    ? sgh_get_primary_navigation_definition()
                    : [
                        ['title' => __('Dự Án', 'saigonhoreca'), 'url' => sgh_url('projects_index')],
                        ['title' => __('Sản Phẩm', 'saigonhoreca'), 'url' => sgh_url('products_index')],
                        ['title' => __('Tin Tức', 'saigonhoreca'), 'url' => sgh_url('news')],
                        ['title' => __('Liên hệ', 'saigonhoreca'), 'url' => sgh_url('contact')],
                    ];
                ?>
                <ul class="sh-mobile__menu-list">
                    <?php foreach ($fallback_items as $item) : ?>
                    <li class="sh-mob-item <?php echo !empty($item['submenu']) ? 'sh-mob-item--has-children' : ''; ?>">
                        <div class="sh-mob-item__row">
                            <a href="<?php echo esc_url($item['url'] ?? home_url('/')); ?>" class="sh-mob-item__link">
                                <?php echo esc_html($item['title']); ?>
                            </a>
                            <?php if (!empty($item['submenu'])) : ?>
                            <button class="mobile-submenu-toggle sh-mob-item__toggle">
                                <?php echo sh_icon('chevron-down', 'sh-mob-item__toggle-icon'); ?>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($item['submenu'])) : ?>
                        <ul class="sub-menu sh-mob-submenu">
                            <?php foreach ($item['submenu'] as $sub) : ?>
                            <li class="sh-mob-sub-item">
                                <a href="<?php echo esc_url($sub['url'] ?? home_url('/')); ?>" class="sh-mob-sub-item__link">
                                    <span class="sh-mob-sub-item__dot"></span>
                                    <span class="sh-mob-sub-item__text"><?php echo esc_html($sub['title']); ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php
            }
            ?>
        </nav>

        <!-- Footer -->
        <div class="sh-mobile__footer">
            <h4 class="sh-mobile__section-title">
                <span class="sh-mobile__accent-bar"></span> <?php esc_html_e('Thông tin liên hệ', 'saigonhoreca'); ?>
            </h4>
            <div class="sh-mobile__contact-list">
                <a href="<?php echo esc_url($phone_link); ?>" class="sh-mobile__contact-card sh-mobile__contact-card--phone">
                    <div class="sh-mobile__contact-icon-wrap">
                        <?php echo sh_icon('phone', 'sh-mobile__contact-svg'); ?>
                    </div>
                    <div>
                        <span class="sh-mobile__contact-label"><?php esc_html_e('Hotline', 'saigonhoreca'); ?></span>
                        <span class="sh-mobile__contact-value"><?php echo esc_html($contact['hotline'] ?? ''); ?></span>
                    </div>
                </a>
                <a href="<?php echo esc_url($email_link); ?>" class="sh-mobile__contact-card">
                    <div class="sh-mobile__contact-icon-wrap">
                        <?php echo sh_icon('mail', 'sh-mobile__contact-svg'); ?>
                    </div>
                    <span class="sh-mobile__contact-email"><?php echo esc_html($contact['email_primary'] ?? ''); ?></span>
                </a>
            </div>

            <!-- Social -->
            <div class="sh-mobile__social-section">
                <h4 class="sh-mobile__section-title">
                    <span class="sh-mobile__accent-bar"></span> <?php esc_html_e('Kết nối', 'saigonhoreca'); ?>
                </h4>
                <div class="sh-mobile__social-list">
                    <?php if (!empty($contact['facebook'])) : ?>
                    <a href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="sh-mobile__social-btn" aria-label="<?php esc_attr_e('Facebook', 'saigonhoreca'); ?>">
                        <img src="<?php echo $theme_uri; ?>/assets/images/facebook-icon.webp" alt="Facebook" class="sh-mobile__social-img" loading="lazy">
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['youtube'])) : ?>
                    <a href="<?php echo esc_url($contact['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="sh-mobile__social-btn" aria-label="<?php esc_attr_e('YouTube', 'saigonhoreca'); ?>">
                        <img src="<?php echo $theme_uri; ?>/assets/images/youtube-icon.webp" alt="YouTube" class="sh-mobile__social-img" loading="lazy">
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['zalo'])) : ?>
                    <a href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" class="sh-mobile__social-btn" aria-label="<?php esc_attr_e('Zalo', 'saigonhoreca'); ?>">
                        <img src="<?php echo $theme_uri; ?>/assets/images/zalo-icon.webp" alt="Zalo" class="sh-mobile__social-img" loading="lazy">
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sh-mobile__copyright">
                &copy; <?php echo date('Y'); ?> <?php esc_html_e('SAIGON HORECA.', 'saigonhoreca'); ?>
            </div>
        </div>
    </div>
</template>
