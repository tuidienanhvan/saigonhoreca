<?php
/**
 * Template Part: Footer Quick Links
 * @package SaigonHoreca
 */

// Slugs match static-mirror/saigonhoreca.vn/danh-muc-san-pham/* (production canonical).
$footer_services = [
    ['label' => __('Thiết bị bếp công nghiệp', 'saigonhoreca'), 'slug' => 'thiet-bi-bep-cong-nghiep'],
    ['label' => __('Thiết bị quầy bar cafe', 'saigonhoreca'),   'slug' => 'thiet-bi-quay-bar-cafe'],
    ['label' => __('Thiết bị lạnh công nghiệp', 'saigonhoreca'), 'slug' => 'thiet-bi-lanh-cong-nghiep'],
    ['label' => __('Hệ thống hút khói', 'saigonhoreca'),        'slug' => 'he-thong-hut-khoi'],
    ['label' => __('Máy rửa ly chén', 'saigonhoreca'),          'slug' => 'may-rua-ly-chen'],
];
foreach ($footer_services as &$svc) {
    $term = get_term_by('slug', $svc['slug'], 'product_category');
    if (!$term || is_wp_error($term)) {
        $term = get_term_by('slug', $svc['slug'] . '-sgh', 'product_category');
    }
    if ($term && !is_wp_error($term)) {
        $svc['url'] = get_term_link($term);
    } else {
        $svc['url'] = function_exists('sgh_url') ? sgh_url('products_index') : home_url('/san-pham/');
    }
}
unset($svc);

// Chỉ giữ link trỏ tới trang đã có (production scrape verified).
$footer_support = [
    ['label' => __('Về Saigon Horeca', 'saigonhoreca'), 'url' => sgh_url('about')],
    ['label' => __('Dự án tiêu biểu', 'saigonhoreca'),   'url' => sgh_url('projects_index')],
    ['label' => __('Sản phẩm', 'saigonhoreca'),          'url' => sgh_url('products_index')],
    ['label' => __('Tin tức & sự kiện', 'saigonhoreca'), 'url' => sgh_url('news')],
    ['label' => __('Liên hệ tư vấn', 'saigonhoreca'),   'url' => sgh_url('contact')],
];
?>

<!-- Services -->
<div class="sh-footer__links-col">
    <h3 class="sh-footer__heading"><?php esc_html_e('Dịch Vụ', 'saigonhoreca'); ?></h3>
    <ul class="sh-footer__link-list">
        <?php foreach ($footer_services as $item): ?>
        <li><a href="<?php echo esc_url($item['url']); ?>" class="sh-footer__link"><?php echo esc_html($item['label']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Support -->
<div class="sh-footer__links-col">
    <h3 class="sh-footer__heading"><?php esc_html_e('Hỗ Trợ', 'saigonhoreca'); ?></h3>
    <ul class="sh-footer__link-list">
        <?php foreach ($footer_support as $item): ?>
        <li><a href="<?php echo esc_url($item['url']); ?>" class="sh-footer__link"><?php echo esc_html($item['label']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
