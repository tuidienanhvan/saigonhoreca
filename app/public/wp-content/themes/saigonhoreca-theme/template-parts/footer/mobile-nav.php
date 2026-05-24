<?php
/**
 * Bottom Mobile Navigation Bar.
 *
 * Mirrors SaigonHoreca's canonical public routes, not SaigonHouse's
 * architecture/pricing routes.
 *
 * @package SaigonHoreca
 */

$current_url = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
$items = [
    [
        'label' => __('Trang chủ', 'saigonhoreca'),
        'url'   => home_url('/'),
        'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1',
        'match' => '#^/$#',
    ],
    [
        'label' => __('Dự Án', 'saigonhoreca'),
        'url'   => sgh_url('projects_index'),
        'icon'  => 'M4 7a2 2 0 012-2h3l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V7zm4 6h8m-8 3h5',
        'match' => '#^/du-an(?:/|$)#',
    ],
    [
        'label' => __('Sản Phẩm', 'saigonhoreca'),
        'url'   => sgh_url('products_index'),
        'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'match' => '#^/(?:san-pham|danh-muc-san-pham|thuong-hieu)(?:/|$)#',
    ],
    [
        'label' => __('Tin Tức', 'saigonhoreca'),
        'url'   => sgh_url('news'),
        'icon'  => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
        'match' => '#^/tin-tuc(?:/|$)#',
    ],
    [
        'label' => __('Liên hệ', 'saigonhoreca'),
        'url'   => sgh_url('contact'),
        'icon'  => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'match' => '#^/lien-he(?:/|$)#',
    ],
];
?>
<!-- Bottom Mobile Nav (visible < 768px only) -->
<nav id="sh-mobile-nav" class="md:hidden" aria-label="Mobile navigation">
    <div class="sh-mnav">
        <?php foreach ($items as $item) :
            $active = preg_match($item['match'], $current_url);
        ?>
        <a href="<?php echo esc_url($item['url']); ?>" class="sh-mnav-item <?php echo $active ? 'sh-mnav-active' : ''; ?>" <?php echo $active ? 'aria-current="page"' : ''; ?>>
            <svg class="sh-mnav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="<?php echo esc_attr($item['icon']); ?>"/>
            </svg>
            <span class="sh-mnav-label"><?php echo esc_html($item['label']); ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</nav>
