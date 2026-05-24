<?php
/**
 * Home — Cảm nhận từ khách hàng (match production saigonhoreca.vn).
 *
 * Layout: Title + subtitle centered, sau đó 3 testimonials xen kẽ:
 *   - Testimonial 1 (Ms Lee):     text trái + avatar card phải
 *   - Testimonial 2 (Anthony Bùi): avatar card trái + text phải
 *   - Testimonial 3 (Quốc Tín):    text trái + avatar card phải
 *
 * Mỗi testimonial: 5 sao vàng + đoạn text + card avatar riêng
 * (nền trắng, avatar circle + tên + subtitle).
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$reviews = [
    [
        'name'    => 'Ms Lee',
        'sub'     => 'SSiC Group',
        'avatar'  => '2025/04/ms-lee.jpg',
        'text'    => 'Tôi rất hài lòng với dịch vụ của Saigon Horeca. Tư vấn rõ ràng, thiết kế bếp hợp lý, thi công nhanh và đúng yêu cầu. Đội ngũ làm việc chuyên nghiệp, hỗ trợ nhiệt tình. Bếp sau thi công vận hành rất tốt. Cảm ơn Saigon Horeca!',
        'side'    => 'right', // avatar card bên phải
    ],
    [
        'name'    => 'Anthony Bùi',
        'sub'     => 'Mâm Mâm Eatery &amp; Lounge',
        'avatar'  => '2025/04/anthony-bui.jpg',
        'text'    => 'Mình rất rất ấn tượng với sự chuyên nghiệp và sáng tạo của đội ngũ thiết kế. Từng chi tiết trong không gian nhà hàng Mâm Mâm Eatery &amp; Lounge đều được chăm chút tỉ mỉ, đúng theo phong cách mong muốn. Thi công nhanh gọn, đúng tiến độ và hỗ trợ tận tình. Một trải nghiệm hợp tác tuyệt vời — xứng đáng 5 sao!',
        'side'    => 'left',
    ],
    [
        'name'    => 'Quốc Tín',
        'sub'     => '',
        'avatar'  => '2025/04/bao-quoc.png',
        'text'    => '1 Đội Ngũ Tư Vấn &amp; Thiết Kế Đẹp, Chỉnh Chu, Tiện Lợi<br>Thương hiệu làm việc Uy Tín — Chuyên Nghiệp — Chất Lượng<br>5 Sao cho Horeca Sài Gòn',
        'side'    => 'right',
    ],
];

$star = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.6 6.6L22 9.3l-5.5 5L18 22l-6-3.4L6 22l1.5-7.7L2 9.3l7.4-.7L12 2z"/></svg>';
?>
<section class="sh-testimonials" aria-label="<?php esc_attr_e('Cảm nhận khách hàng', 'saigonhoreca'); ?>">
    <div class="sh-testimonials__inner">

        <header class="sh-testimonials__header">
            <span class="sh-testimonials__divider" aria-hidden="true"></span>
            <h2 class="sh-testimonials__title"><?php esc_html_e('Cảm nhận từ khách hàng', 'saigonhoreca'); ?></h2>
            <p class="sh-testimonials__subtitle">
                <?php echo sprintf(__('Xem thêm reviews của khách hàng về <strong>SAIGON HORECA</strong> tại <a href="%s" target="_blank" rel="noopener">Facebook Reviews</a>', 'saigonhoreca'), 'https://www.facebook.com/saigonhoreca'); ?>
            </p>
        </header>

        <div class="sh-testimonials__list">
            <?php foreach ($reviews as $r) : ?>
                <article class="sh-testimonial sh-testimonial--<?php echo esc_attr($r['side']); ?>">
                    <div class="sh-testimonial__body">
                        <div class="sh-testimonial__stars" role="img" aria-label="5 sao">
                            <?php for ($i = 0; $i < 5; $i++) echo $star; ?>
                        </div>
                        <p class="sh-testimonial__text"><?php echo wp_kses_post($r['text']); ?></p>
                    </div>
                    <aside class="sh-testimonial__card">
                        <span class="sh-testimonial__avatar">
                            <img src="<?php echo esc_url(sgh_img("{$r['avatar']}")); ?>"
                                 alt="<?php echo esc_attr($r['name']); ?>"
                                 width="80" height="80"
                                 loading="lazy" decoding="async">
                        </span>
                        <h3 class="sh-testimonial__name"><?php echo esc_html($r['name']); ?></h3>
                        <?php if ($r['sub']) : ?>
                            <span class="sh-testimonial__sub"><?php echo wp_kses_post($r['sub']); ?></span>
                        <?php endif; ?>
                    </aside>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>
