<?php
/**
 * Bottom Mobile Navigation Bar
 * Fixed at bottom on mobile devices (< 768px), hidden on desktop.
 * 5 tabs: Home, Thiết Kế, Dự Toán, Tin Tức, Liên Hệ
 *
 * @package SaigonHouse
 */

$current_url = $_SERVER['REQUEST_URI'] ?? '';
$thiet_ke_term = get_term_by('slug', 'thiet-ke', 'category');
$thiet_ke_url = ($thiet_ke_term && !is_wp_error($thiet_ke_term)) ? get_term_link($thiet_ke_term) : home_url('/category/thiet-ke/');

$items = [
    ['label' => 'Trang chủ', 'url' => home_url('/'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1', 'match' => '/^\/$|^\/trang-chu/'],
    ['label' => 'Thiết Kế',  'url' => $thiet_ke_url, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5', 'match' => '/thiet-ke|category/'],
    ['label' => 'Bảng Giá',  'url' => home_url('/bang-gia/'), 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'match' => '/bang-gia/'],
    ['label' => 'Dự Toán',  'url' => home_url('/du-toan/'), 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'match' => '/du-toan/'],
    ['label' => 'Liên Hệ',  'url' => home_url('/lien-he/'), 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'match' => '/lien-he/'],
];
?>
<!-- Bottom Mobile Nav (visible < 768px only) -->
<nav id="sh-mobile-nav" class="md:hidden" aria-label="Mobile navigation">
    <div class="sh-mnav">
        <?php foreach ($items as $item):
            $active = preg_match($item['match'], $current_url);
        ?>
        <a href="<?php echo esc_url($item['url']); ?>" class="sh-mnav-item <?php echo $active ? 'sh-mnav-active' : ''; ?>" <?php echo $active ? 'aria-current="page"' : ''; ?>>
            <svg class="sh-mnav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="<?php echo $item['icon']; ?>"/>
            </svg>
            <span class="sh-mnav-label"><?php echo esc_html($item['label']); ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</nav>
