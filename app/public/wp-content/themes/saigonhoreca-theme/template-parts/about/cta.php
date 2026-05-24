<?php
/**
 * About Page Section — CTA Final + Extra Testimonials
 *
 * Dark luxe: 3-col testimonial grid + full-width CTA banner with bg image.
 * BEM: sh-about-cta
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri    = get_template_directory_uri();
$bg_url = sgh_img('2023/12/Saigon-Horeca-thiet-bi-bep-cong-nghiep.jpg');

$extra_testimonials = [
    [
        'quote'   => 'Tôi đã sử dụng <strong>thiết bị bếp inox</strong> từ Saigon Horeca trong thời gian dài và rất ấn tượng với độ bền và chất lượng. Đây là lựa chọn hoàn hảo cho các nhà hàng và cơ sở thực phẩm.',
        'author'  => 'Mr. Quan',
        'company' => 'Bliss Hotel',
    ],
    [
        'quote'   => 'Cảm ơn Saigon Horeca đã giúp chúng tôi thiết kế và lắp đặt bếp nhà hàng. Dịch vụ tư vấn và thi công rất chuyên nghiệp, giúp tiết kiệm thời gian và chi phí mà vẫn đảm bảo chất lượng.',
        'author'  => 'Mr. Vu',
        'company' => 'Unic Restaurant & Lounge',
    ],
    [
        'quote'   => 'Chất lượng <strong>bếp công nghiệp</strong> từ Saigon Horeca không chỉ đáng tin cậy mà còn đáp ứng được mọi yêu cầu kỹ thuật. Tôi rất hài lòng và sẽ tiếp tục hợp tác.',
        'author'  => 'Mr. Vinh Le',
        'company' => 'ZumWhere Modern Izakaya Saigon',
    ],
];
?>
<section class="sh-about-cta" aria-label="<?php esc_attr_e('CTA và đánh giá bổ sung', 'saigonhoreca'); ?>">
    <div class="sh-about-cta__inner">

        <!-- Extra testimonials -->
        <div class="sh-about-cta__reviews">
            <?php foreach ($extra_testimonials as $t): ?>
                <div class="sh-about-cta__review-card">
                    <div class="sh-about-cta__stars" role="img" aria-label="5 sao">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="sh-about-cta__review-quote">
                        <?php echo $t['quote']; ?>
                    </blockquote>
                    <footer class="sh-about-cta__review-footer">
                        <cite class="sh-about-cta__review-author"><?php echo esc_html($t['author']); ?></cite>
                        <span class="sh-about-cta__review-company"><?php echo esc_html($t['company']); ?></span>
                    </footer>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA Banner -->
        <div class="sh-about-cta__banner" style="background-image:url('<?php echo esc_url($bg_url); ?>')">
            <div class="sh-about-cta__banner-overlay"></div>
            <div class="sh-about-cta__banner-content">
                <h2 class="sh-about-cta__banner-title"><?php esc_html_e('Đồng hành cùng Saigon Horeca', 'saigonhoreca'); ?></h2>
                <p class="sh-about-cta__banner-sub">
                    Kiến tạo không gian ẩm thực, bếp ăn công nghiệp và quầy bar chuyên nghiệp — tối ưu công năng hàng đầu.
                </p>
                <div class="sh-about-cta__banner-actions">
                    <a class="sh-about-cta__btn sh-about-cta__btn--primary"
                       href="<?php echo esc_url(sgh_url('contact')); ?>">
                        <?php esc_html_e('Liên hệ tư vấn', 'saigonhoreca'); ?>
                    </a>
                    <a class="sh-about-cta__btn sh-about-cta__btn--outline"
                       href="tel:+84901304365">
                        Hotline: 0901.304.365
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>
