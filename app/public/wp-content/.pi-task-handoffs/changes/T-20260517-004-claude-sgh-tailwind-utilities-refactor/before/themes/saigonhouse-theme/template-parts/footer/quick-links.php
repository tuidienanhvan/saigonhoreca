<?php
/**
 * Template Part: Footer Quick Links
 * @package SaigonHouse
 */

$footer_services = [
    ['label' => 'Đơn giá thiết kế kiến trúc', 'page' => '/bao-gia-thiet-ke-kien-truc-xay-dung-cong-trinh-1/'],
    ['label' => 'Đơn giá thiết kế nội thất',  'page' => '/bang-bao-gia-thiet-ke-xay-nha-tron-goi-gia-re-1/'],
    ['label' => 'Đơn giá phần thô',           'page' => '/bang-bao-gia-thi-cong-xay-dung-nha-phan-tho-1/'],
    ['label' => 'Đơn giá trọn gói',           'page' => '/bang-bao-gia-xay-dung-nha-tron-goi-1/'],
    ['label' => 'Báo giá theo loại nhà',      'cat' => 'bao-gia-theo-loai-nha'],
];
foreach ($footer_services as &$svc) {
    if (isset($svc['cat'])) {
        $term = get_term_by('slug', $svc['cat'], 'category');
        $svc['url'] = ($term && !is_wp_error($term)) ? get_term_link($term) : home_url('/category/' . $svc['cat'] . '/');
    } else {
        $svc['url'] = home_url($svc['page']);
    }
}
unset($svc);

$footer_support = [
    ['label' => 'Bảng giá xây dựng',   'url' => home_url('/bang-gia/')],
    ['label' => 'Dự toán online',       'url' => home_url('/du-toan/')],
    ['label' => 'Giới thiệu công ty',   'url' => home_url('/gioi-thieu/')],
    ['label' => 'Liên hệ tư vấn',      'url' => home_url('/lien-he/')],
    ['label' => 'Chính sách bảo mật',   'url' => home_url('/chinh-sach-bao-mat/')],
    ['label' => 'Điều khoản sử dụng',   'url' => home_url('/dieu-khoan-su-dung/')],
];
?>

<!-- Services -->
<div class="sh-footer__links-col" data-aos="fade-up" data-aos-delay="100">
    <h3 class="sh-footer__heading">Dịch Vụ</h3>
    <ul class="sh-footer__link-list">
        <?php foreach ($footer_services as $item): ?>
        <li><a href="<?php echo esc_url($item['url']); ?>" class="sh-footer__link"><?php echo esc_html($item['label']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Support -->
<div class="sh-footer__links-col" data-aos="fade-up" data-aos-delay="100">
    <h3 class="sh-footer__heading">Hỗ Trợ</h3>
    <ul class="sh-footer__link-list">
        <?php foreach ($footer_support as $item): ?>
        <li><a href="<?php echo esc_url($item['url']); ?>" class="sh-footer__link"><?php echo esc_html($item['label']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
