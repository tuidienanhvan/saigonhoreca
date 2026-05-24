<?php
/**
 * Template Part: Desktop Navigation (Saigon Horeca)
 *
 * Tries the WP nav menu first (lets admin curate items via Appearance →
 * Menus). When none assigned, falls back to a hard-coded list mirroring
 * the live saigonhoreca.vn header so the site has a working nav out of
 * the box. The walker (`SaigonHoreca_Desktop_Walker`) renders dropdowns
 * via `.sh-dropdown` + `.sh-nav-item--has-children`.
 *
 * Nav items (fallback, matches live site):
 *   - Về Saigon Horeca (dropdown: 4 sub-items)
 *   - Dự Án
 *   - Sản Phẩm
 *   - Tin Tức
 *   - Liên hệ
 *
 * @package SaigonHoreca
 */

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
?>
<nav class="sh-nav" aria-label="<?php esc_attr_e('Điều hướng chính', 'saigonhoreca'); ?>">
<?php if ($has_valid_menu && class_exists('SaigonHoreca_Desktop_Walker')) : ?>
    <?php
    wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'sh-nav__list',
        'item_spacing'   => 'discard',
        'walker'         => new SaigonHoreca_Desktop_Walker(),
    ]);
    ?>
<?php else :
    // Fallback nav mirrors saigonhoreca.vn. Admin can override via
    // Appearance → Menus → assign menu to "Primary".
    $fallback_items = function_exists('sgh_get_primary_navigation_definition')
        ? sgh_get_primary_navigation_definition()
        : [
            ['title' => __('Dự Án', 'saigonhoreca'), 'url' => sgh_url('projects_index')],
            ['title' => __('Sản Phẩm', 'saigonhoreca'), 'url' => sgh_url('products_index')],
            ['title' => __('Tin Tức', 'saigonhoreca'), 'url' => sgh_url('news')],
            ['title' => __('Liên hệ', 'saigonhoreca'), 'url' => sgh_url('contact')],
        ];
    ?>
    <ul class="sh-nav__list">
        <?php foreach ($fallback_items as $item) : ?>
        <li class="sh-nav-item <?php echo !empty($item['submenu']) ? 'sh-nav-item--has-children' : ''; ?>">
            <a href="<?php echo esc_url($item['url'] ?? home_url('/')); ?>" class="sh-nav-item__link">
                <span><?php echo esc_html($item['title']); ?></span>
                <?php if (!empty($item['submenu'])) : ?>
                <span class="sh-nav-item__chevron" aria-hidden="true">▾</span>
                <?php endif; ?>
            </a>
            <?php if (!empty($item['submenu'])) : ?>
            <div class="sh-dropdown sh-dropdown--standard sh-dropdown--left">
                <ul class="sh-dropdown__list sh-dropdown__list--stack">
                    <?php foreach ($item['submenu'] as $sub) : ?>
                    <li class="sh-dropdown__item">
                        <a href="<?php echo esc_url($sub['url'] ?? home_url('/')); ?>" class="sh-dropdown__link">
                            <?php echo esc_html($sub['title']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</nav>
