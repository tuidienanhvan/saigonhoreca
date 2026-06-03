<?php
/**
 * Home — Quy Trình Làm Việc (match production saigonhoreca.vn).
 *
 * Layout 2-col:
 *   - Trái: 1 ảnh portrait (the-royal-sgh-10.jpg)
 *   - Phải: H3 title + intro paragraph + 3 items (Tư Vấn / Thiết Kế / Thi Công)
 *           mỗi item có icon SVG vàng + heading bold + text.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$steps = [
    [
        'title' => __('Tư Vấn', 'saigonhoreca'),
        'desc'  => 'Đội ngũ chuyên gia của chúng tôi trong quá trình vận hành <strong>nhà bếp công nghiệp và quầy bar</strong> sẽ luôn lắng nghe và trao đổi ý tưởng một cách cẩn thận với khách hàng để tư vấn giải pháp tốt nhất và tối ưu nhất cho doanh nghiệp của bạn.',
        'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
    ],
    [
        'title' => __('Thiết Kế', 'saigonhoreca'),
        'desc'  => 'Những ý tưởng thiết kế mang đến cho khách hàng khu vực <strong>nhà bếp/bar đa dụng</strong> và thoải mái cho nhân viên nhà bếp/bar, từ đó tạo ra không gian gọn gàng và hiệu quả.',
        'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21l3-1 11-11-2-2L4 18l-1 3z"/><path d="M14.5 5.5l4 4"/><path d="M21 21H10"/></svg>',
    ],
    [
        'title' => __('Thi Công', 'saigonhoreca'),
        'desc'  => 'Dịch vụ thi công lắp đặt thiết bị của chúng tôi là sự kết hợp giữa việc <strong>hiểu rõ về tất cả sản phẩm</strong> và triển khai theo thiết kế chi tiết và rõ ràng trước đó.',
        'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    ],
];
?>
<section  class="sh-work-process" aria-label="<?php esc_attr_e('Quy trình làm việc Saigon Horeca', 'saigonhoreca'); ?>">
    <div class="sh-work-process__inner">
        <div class="sh-work-process__media scroll-reveal reveal-skew-x duration-2000 delay-100">
            <img src="<?php echo esc_url(sgh_img('saigonhoreca/the-royal-sgh-10.webp')); ?>"
                 alt="Quy trình làm việc Saigon Horeca"
                 width="542" height="722"
                 loading="lazy" decoding="async">
        </div>

        <div class="sh-work-process__copy scroll-reveal reveal-spring-right duration-1800 delay-300">
            <h3 class="sh-work-process__title"><?php esc_html_e('Quy Trình Làm Việc', 'saigonhoreca'); ?></h3>
            <p class="sh-work-process__intro">
                <strong>Saigon Horeca</strong> tự tin cung cấp những dịch vụ chất lượng và cung cấp thiết bị <strong>bếp công nghiệp &amp; quầy bar</strong> tốt nhất cho khách hàng.
            </p>

            <ul class="sh-work-process__steps">
                <?php foreach ($steps as $s) : ?>
                    <li class="sh-work-process__step">
                        <span class="sh-work-process__icon"><?php echo $s['svg']; ?></span>
                        <div class="sh-work-process__step-body">
                            <h4 class="sh-work-process__step-title"><?php echo esc_html($s['title']); ?></h4>
                            <p class="sh-work-process__step-desc"><?php echo wp_kses_post($s['desc']); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
