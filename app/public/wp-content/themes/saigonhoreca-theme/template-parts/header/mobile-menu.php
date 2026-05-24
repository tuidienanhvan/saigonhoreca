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
            Menu
        </span>
        <button id="mobile-menu-close" class="sh-mobile__close-btn">
            <?php echo sh_icon('chevron-right', 'sh-mobile__close-icon'); ?>
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
                <input type="search" name="s" class="sh-mobile__search-input" placeholder="<?php esc_attr_e('Tìm kiếm...', 'saigonhoreca'); ?>">
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
                    <li class="sh-mobile__menu-item <?php echo !empty($item['submenu']) ? 'sh-mobile__menu-item--has-children' : ''; ?>">
                        <a href="<?php echo esc_url($item['url'] ?? home_url('/')); ?>" class="sh-mobile__menu-link">
                            <?php echo esc_html($item['title']); ?>
                        </a>
                        <?php if (!empty($item['submenu'])) : ?>
                        <ul class="sh-mobile__submenu">
                            <?php foreach ($item['submenu'] as $sub) : ?>
                            <li class="sh-mobile__submenu-item">
                                <a href="<?php echo esc_url($sub['url'] ?? home_url('/')); ?>" class="sh-mobile__submenu-link">
                                    <?php echo esc_html($sub['title']); ?>
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
