<?php
/**
 * Home — Services (gộp 4 sections thành 1 template part).
 *
 * Match production saigonhoreca.vn:
 *   1. SAIGON HORECA — Thiết bị bếp công nghiệp cao cấp (intro, có phone CTA)
 *   2. Cải tạo bếp nhà hàng hiện đại hiệu quả
 *   3. Giải pháp hệ thống hút khói & cấp khí tươi cao cấp
 *   4. Tư vấn thiết kế quầy bar
 *
 * Layout 2-col image-right / image-left alternating, nền đen liền mạch.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$blocks = [
    // 1 — Intro company section (image right)
    [
        'eyebrow' => __('Saigon Horeca', 'saigonhoreca'),
        'title'   => __('THIẾT BỊ BẾP CÔNG NGHIỆP CAO CẤP', 'saigonhoreca'),
        'paras'   => [
            'Saigon Horeca là đơn vị uy tín trong ngành <strong>bếp công nghiệp cao cấp</strong> cho nhà hàng, quán bar và bếp ăn công nghiệp, cung cấp dịch vụ tư vấn thiết kế, thi công và lắp đặt trọn gói hệ thống bếp nhà hàng.',
            'Saigon Horeca cung cấp giải pháp thiết bị bếp công nghiệp toàn diện cho các nhà hàng, nhà máy canteen. Từ khảo sát, tư vấn, thiết kế đến lắp đặt và vận hành, chúng tôi đảm bảo toàn bộ khu vực nhà bếp được tối ưu hoá, mang lại hiệu quả cao và sự an tâm tuyệt đối cho khách hàng.',
            'Với kinh nghiệm làm việc cùng các nhà hàng đạt Michelin Guide 2023 như Sol Kitchen &amp; Bar, Saigon Horeca sẽ tư vấn giải pháp giúp bếp nhà hàng và bếp công nghiệp của bạn trở nên tối ưu và đẳng cấp.',
        ],
        'cta'     => ['label' => __('Liên Hệ Ngay Hôm Nay', 'saigonhoreca'), 'sub' => '0901 304 365 | 0909 040 920', 'href' => 'tel:0901304365', 'is_phone' => true],
        'img'     => '2025/05/SGH-banner.jpg',
        'alt'     => 'Bếp công nghiệp cao cấp Saigon Horeca',
        'reverse' => false,
    ],
    // 2 — Cải tạo bếp (image left)
    [
        'eyebrow' => __('Dịch vụ chính', 'saigonhoreca'),
        'title'   => __('CẢI TẠO BẾP NHÀ HÀNG HIỆN ĐẠI HIỆU QUẢ', 'saigonhoreca'),
        'paras'   => [
            'Saigon Horeca là đơn vị uy tín trong ngành <strong>bếp công nghiệp cao cấp</strong> cho nhà hàng, quán bar và bếp ăn công nghiệp, cung cấp dịch vụ tư vấn thiết kế, thi công và lắp đặt trọn gói hệ thống bếp nhà hàng.',
            'Với kinh nghiệm hơn 10 năm và đội ngũ chuyên gia tận tâm, chúng tôi tự tin mang đến dịch vụ <strong>cải tạo bếp nhà hàng chuyên nghiệp</strong>, giúp nâng cao trải nghiệm ẩm thực cho khách hàng và tăng cường hiệu suất làm việc của đội ngũ đầu bếp.',
            'Saigon Horeca cam kết bảo trì định kỳ, bảo hành lâu dài để đảm bảo thiết bị luôn hoạt động ổn định. Chúng tôi luôn đồng hành cùng đối tác, sẵn sàng hỗ trợ mọi lúc để mang lại sự an tâm và hiệu quả tối ưu cho doanh nghiệp của bạn.',
        ],
        'cta'     => null,
        'img'     => '2025/05/SGH-banner-1.jpg',
        'alt'     => 'Cải tạo bếp nhà hàng Saigon Horeca',
        'reverse' => true,
    ],
    // 3 — Hút khói & cấp khí (image right)
    [
        'eyebrow' => __('HVAC chuyên dụng', 'saigonhoreca'),
        'title'   => __('GIẢI PHÁP HỆ THỐNG HÚT KHÓI &amp; CẤP KHÍ TƯƠI CAO CẤP', 'saigonhoreca'),
        'paras'   => [
            'Saigon Horeca chuyên cung cấp dịch vụ tư vấn thiết kế, thi công và lắp đặt trọn gói <strong>hệ thống hút khói, cấp khí tươi cao cấp</strong> cho nhà hàng, quán bar và bếp ăn công nghiệp. Với nhiều năm kinh nghiệm triển khai hệ thống cho những nhà hàng nổi tiếng đạt sao Michelin tại TP.HCM như Sol Kitchen &amp; Bar, Yuzu Omakase, Bambino, Little Bear…',
            'Đội ngũ chuyên gia của Saigon Horeca sẽ tư vấn và cung cấp giải pháp đảm bảo các tiêu chuẩn thiết kế hệ thống hút khói và cấp khí tươi bếp nhà hàng để giúp tối ưu, thẩm mỹ, đảm bảo hiệu năng cho bếp công nghiệp của khách hàng.',
        ],
        'cta'     => null,
        'img'     => '2024/09/sgh-hethonghutkhoi.jpg',
        'alt'     => 'Hệ thống hút khói cấp khí tươi Saigon Horeca',
        'reverse' => false,
    ],
    // 4 — Quầy bar (image left)
    [
        'eyebrow' => __('Bespoke Bar', 'saigonhoreca'),
        'title'   => __('TƯ VẤN THIẾT KẾ QUẦY BAR', 'saigonhoreca'),
        'paras'   => [
            'Saigon Horeca cũng cung cấp dịch vụ tư vấn <strong>thiết kế quầy bar trọn gói</strong>, từ ý tưởng đến hoàn thiện, giúp bạn tạo ra không gian quầy bar hiện đại cao cấp, thu hút khách hàng ngay từ cái nhìn đầu tiên.',
            'Saigon Horeca cung cấp giải pháp thiết kế quầy bar cafe, quầy bar nhà hàng và quầy bar trong bếp với thiết kế riêng tối ưu cho từng không gian. Dù là quầy bar cafe ấm cúng, quầy bar nhà hàng sang trọng hay quầy bar trong bếp gọn gàng, chúng tôi đảm bảo mọi thiết kế đều phát huy tối đa công năng và thẩm mỹ, phù hợp với phong cách của bạn.',
        ],
        'cta'     => null,
        'img'     => '2025/05/SGH-bespoke-bar.jpg',
        'alt'     => 'Thiết kế quầy bar bespoke Saigon Horeca',
        'reverse' => true,
    ],
];
?>
<section class="sh-services" aria-label="<?php esc_attr_e('Dịch vụ Saigon Horeca', 'saigonhoreca'); ?>">
    <?php foreach ($blocks as $i => $b) : ?>
        <div class="sh-services__block <?php echo $b['reverse'] ? 'sh-services__block--reverse' : ''; ?>">
            <div class="sh-services__inner">
                <div class="sh-services__copy">
                    <?php if (!empty($b['eyebrow'])) : ?>
                        <span class="sh-services__eyebrow"><?php echo esc_html($b['eyebrow']); ?></span>
                    <?php endif; ?>
                    <h2 class="sh-services__title"><?php echo wp_kses_post($b['title']); ?></h2>
                    <?php foreach ($b['paras'] as $p) : ?>
                        <p class="sh-services__desc"><?php echo wp_kses_post($p); ?></p>
                    <?php endforeach; ?>
                    <?php if (!empty($b['cta'])) : ?>
                        <a href="<?php echo esc_url($b['cta']['href']); ?>" class="sh-services__cta">
                            <?php if (!empty($b['cta']['is_phone'])) : ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 2.08 4.18 2 2 0 0 1 4.07 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            <?php endif; ?>
                            <span class="sh-services__cta-text">
                                <strong><?php echo esc_html($b['cta']['label']); ?></strong>
                                <?php if (!empty($b['cta']['sub'])) : ?>
                                    <em><?php echo esc_html($b['cta']['sub']); ?></em>
                                <?php endif; ?>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="sh-services__media">
                    <?php
                    $img_path = $b['img'];
                    $img_ext = pathinfo($img_path, PATHINFO_EXTENSION);
                    $img_base = substr($img_path, 0, -(strlen($img_ext) + 1));
                    $img_mobile = "{$img_base}-mobile.{$img_ext}";
                    
                    $uploads_dir = function_exists('sgh_uploads_base_dir')
                        ? sgh_uploads_base_dir()
                        : wp_normalize_path(wp_get_upload_dir()['basedir']);
                    $local_mobile_exists = file_exists($uploads_dir . '/' . $img_mobile);
                    
                    $srcset_attr = '';
                    if ($local_mobile_exists) {
                        $srcset_attr = ' srcset="' . esc_url(sgh_img($img_mobile)) . ' 380w, ' . esc_url(sgh_img($img_path)) . ' 600w" sizes="(max-width: 576px) 380px, 600px"';
                    }
                    ?>
                    <img src="<?php echo esc_url(sgh_img("{$b['img']}")); ?>"
                         <?php echo $srcset_attr; ?>
                         alt="<?php echo esc_attr($b['alt']); ?>"
                         width="600" height="450"
                         loading="lazy" decoding="async">
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>
