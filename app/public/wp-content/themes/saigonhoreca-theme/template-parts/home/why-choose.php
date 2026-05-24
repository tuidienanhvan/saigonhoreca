<?php
/**
 * Home — Tại sao chọn Saigon Horeca (match production saigonhoreca.vn).
 *
 * Layout 3-col:
 *   - Trái: H2 big title vàng (có badge SVG ngôi sao) + 1 USP (Kinh nghiệm)
 *   - Giữa: 1 ảnh vuông (SGH-mammam.jpg)
 *   - Phải: 2 USPs (Dịch vụ toàn diện / Thiết bị uy tín)
 * Mỗi USP có icon SVG vàng decorative trong pill nhỏ phía trên heading.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

// SVG icons cho từng USP — vàng decorative
$icon_award = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.5 13.5L17 22l-5-3-5 3 1.5-8.5"/></svg>';
$icon_handshake = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 17l-5-5 5-5"/><path d="M13 7l5 5-5 5"/><path d="M6 12h12"/></svg>';
$icon_shield = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg>';
$icon_star  = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.6 6.6L22 9.3l-5.5 5L18 22l-6-3.4L6 22l1.5-7.7L2 9.3l7.4-.7L12 2z"/></svg>';
?>
<section class="sh-why-choose" aria-label="<?php esc_attr_e('Tại sao chọn Saigon Horeca', 'saigonhoreca'); ?>">
    <div class="sh-why-choose__inner">

        <!-- Col 1: Title + USP 1 -->
        <div class="sh-why-choose__col sh-why-choose__col--left">
            <div class="sh-why-choose__head">
                <span class="sh-why-choose__star"><?php echo $icon_star; ?></span>
                <span class="sh-why-choose__eyebrow"><?php esc_html_e('Why Saigon Horeca', 'saigonhoreca'); ?></span>
            </div>
            <h2 class="sh-why-choose__title">
                <?php esc_html_e('Tại sao nên chọn thiết bị bếp Saigon Horeca?', 'saigonhoreca'); ?>
            </h2>
            <span class="sh-why-choose__divider" aria-hidden="true"></span>

            <div class="sh-why-choose__usp">
                <span class="sh-why-choose__usp-icon"><?php echo $icon_award; ?></span>
                <h3 class="sh-why-choose__usp-title"><?php esc_html_e('Kinh nghiệm tư vấn thiết kế bếp nhà hàng cho đối tác lớn', 'saigonhoreca'); ?></h3>
                <p class="sh-why-choose__usp-desc">
                    Sài Gòn Horeca là đơn vị đã tư vấn, thiết kế và thi công bếp nhà hàng cho rất nhiều khách hàng lớn như <strong>Hoàng An Nhiên, ZumWhere, Circle K, Bambino</strong>…
                </p>
            </div>
        </div>

        <!-- Col 2: Image -->
        <div class="sh-why-choose__col sh-why-choose__col--media">
            <img src="<?php echo esc_url(sgh_img('2025/05/SGH-mammam.jpg')); ?>"
                 alt="Mâm Mâm — Saigon Horeca"
                 width="412" height="412"
                 loading="lazy" decoding="async">
        </div>

        <!-- Col 3: 2 USPs -->
        <div class="sh-why-choose__col sh-why-choose__col--right">
            <div class="sh-why-choose__usp">
                <span class="sh-why-choose__usp-icon"><?php echo $icon_handshake; ?></span>
                <h3 class="sh-why-choose__usp-title"><?php esc_html_e('Dịch vụ toàn diện và chuyên nghiệp', 'saigonhoreca'); ?></h3>
                <p class="sh-why-choose__usp-desc">
                    Saigon Horeca cung cấp dịch vụ toàn diện từ <strong>tư vấn thiết kế, lắp đặt đến hỗ trợ vận hành kinh doanh</strong>. Tư duy thiết kế hiện đại phù hợp với từng dự án.
                </p>
            </div>
            <div class="sh-why-choose__usp">
                <span class="sh-why-choose__usp-icon"><?php echo $icon_shield; ?></span>
                <h3 class="sh-why-choose__usp-title"><?php esc_html_e('Thiết bị bếp uy tín, chất lượng cao', 'saigonhoreca'); ?></h3>
                <p class="sh-why-choose__usp-desc">
                    Saigon Horeca cam kết chỉ cung cấp những <strong>thiết bị bếp chất lượng cao</strong>, được sản xuất từ những thương hiệu uy tín trên thị trường quốc tế.
                </p>
            </div>
        </div>

    </div>
</section>
