<?php
/**
 * About Page Section — Values & Approach
 *
 * Dark luxe: 2-col split.
 * Left  = dark bg + 3 value pills + gold CTA.
 * Right = Tall Architectural SVG Photo Frame (Asymmetric Chamfer) chứa ảnh phủ 100% + Glassmorphic Card.
 * BEM: sh-about-values
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri    = get_template_directory_uri();
$bg_img = sgh_img('bambino/bambino-khong-gian-am-thuc-y-sang-trong.webp');

// 3 values với SVG icons riêng mỗi cái
$values = [
    [
        'icon' => '
        <svg class="sh-about-values__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle class="svg-creativity-orbit" cx="24" cy="24" r="20" stroke="currentColor" stroke-width="1.2" stroke-dasharray="4 3" opacity="0.3"/>
            <path d="M24 2v4M24 42v4M2 24h4M42 24h4" stroke="currentColor" stroke-width="1.2" opacity="0.4"/>
            <path class="svg-creativity-bulb" d="M17 19c0-3.866 3.134-7 7-7s7 3.134 7 7c0 2.378-1.185 4.48-2.998 5.746-.777.544-1.252 1.433-1.252 2.394v.86c0 .552-.448 1-1 1h-3.5c-.552 0-1-.448-1-1v-.86c0-.961-.475-1.85-1.252-2.394C18.185 23.48 17 21.378 17 19z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M20.25 32.5h7.5M21.5 35.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle class="svg-creativity-particle" cx="12" cy="12" r="2" fill="currentColor"/>
            <circle class="svg-creativity-particle" cx="36" cy="36" r="2" fill="currentColor"/>
            <path class="svg-creativity-star" d="M24 16.5l1.2 2.3 2.5.4-1.8 1.8.4 2.5-2.3-1.2-2.3 1.2.4-2.5-1.8-1.8 2.5-.4z" fill="currentColor"/>
        </svg>',
        'label' => __('Sáng tạo', 'saigonhoreca'),
        'desc'  => 'Mỗi giải pháp là một tác phẩm được đầu tư tâm huyết, không theo khuôn mẫu.',
    ],
    [
        'icon' => '
        <svg class="sh-about-values__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path class="svg-artistry-grid" d="M6 6h36v36H6zM6 24h36M24 6v36" stroke="currentColor" stroke-width="0.8" opacity="0.15" stroke-dasharray="2 2"/>
            <path d="M6 6l36 36M42 6L6 42" stroke="currentColor" stroke-width="0.8" opacity="0.1" stroke-dasharray="3 3"/>
            <path class="svg-artistry-spiral" d="M24 24a1 1 0 0 1 1 1c0 .9-.7 1.5-1.5 1.5a2.5 2.5 0 0 1-2.5-2.5c0-2.2 1.8-4 4-4a6 6 0 0 1 6 6c0 5.5-4.5 10-10 10s-15-6.7-15-15a21 21 0 0 1 21-21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path class="svg-artistry-compass" d="M24 8l6 24M24 8l-6 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="24" cy="8" r="2.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
        </svg>',
        'label' => __('Nghệ thuật', 'saigonhoreca'),
        'desc'  => 'Thẩm mỹ và công năng song hành — không gian đẹp phải vận hành hiệu quả.',
    ],
    [
        'icon' => '
        <svg class="sh-about-values__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle class="svg-refinement-target" cx="24" cy="24" r="21" stroke="currentColor" stroke-width="1" stroke-dasharray="3 4" opacity="0.25"/>
            <circle cx="24" cy="24" r="16" stroke="currentColor" stroke-width="0.8" opacity="0.15"/>
            <path d="M24 2v5M24 41v5M2 24h5M41 24h5" stroke="currentColor" stroke-width="1.2" opacity="0.4"/>
            <path class="svg-refinement-diamond" d="M24 9l11 8v14L24 39 13 31V17z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path class="svg-refinement-facets" d="M24 9v30M13 17h22M13 31h22M24 17l11 14M24 17L13 31" stroke="currentColor" stroke-width="1" opacity="0.7"/>
            <path class="svg-refinement-beam" d="M24 17l-9-9M24 17l9-9M24 31l-9 9" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" opacity="0.4" stroke-dasharray="2 3"/>
        </svg>',
        'label' => __('Tinh tế', 'saigonhoreca'),
        'desc'  => 'Từng chi tiết nhỏ đều có ý nghĩa — sự tinh tế tạo nên đẳng cấp khác biệt.',
    ],
];
?>
<section class="sh-about-values" aria-label="<?php esc_attr_e('Giá trị cốt lõi Saigon Horeca', 'saigonhoreca'); ?>">
    <div class="sh-about-values__inner">

        <!-- Left: Values Pills & Eyebrow -->
        <div class="sh-about-values__left">
            <div class="sh-about-values__left-content">

                <span class="sh-about-values__eyebrow"><?php esc_html_e('Giải pháp của chúng tôi', 'saigonhoreca'); ?></span>

                <h2 class="sh-about-values__title">
                    <span class="sh-wordart-1">Sáng tạo</span><span class="sh-about-values__title-sep"> · </span><span class="sh-wordart-2">Nghệ thuật</span><span class="sh-about-values__title-sep"> · </span><span class="sh-wordart-3">Tinh tế</span>
                </h2>

                <!-- 3 value cards -->
                <div class="sh-about-values__cards">
                    <?php foreach ($values as $v): ?>
                        <div class="sh-about-values__card">
                            <span class="sh-about-values__card-icon" aria-hidden="true">
                                <?php echo $v['icon']; ?>
                            </span>
                            <div class="sh-about-values__card-body">
                                <strong class="sh-about-values__card-label"><?php echo esc_html($v['label']); ?></strong>
                                <p class="sh-about-values__card-desc"><?php echo esc_html($v['desc']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <a class="sh-about-values__cta" href="tel:+84901304365">
                    <svg class="sh-about-values__cta-phone-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="16" height="16">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.06 6.06l.94-.93a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <?php esc_html_e('Liên hệ ngay', 'saigonhoreca'); ?>
                </a>
            </div>
        </div>

        <!-- Right: Standalone Architectural SVG Photo Frame Canvas -->
        <div class="sh-about-values__right">
            
            <!-- STANDALONE ARCHITECTURAL SVG PHOTO FRAME CONTAINER -->
            <div class="sh-about-values__photo-frame">
                
                <span class="sh-about-values__cad-label">+ ARCHITECTURAL SPEC // VALUES</span>

                <div class="sh-about-values__photo-wrap">
                    <!-- Ảnh được phóng to lấp đầy 100% bằng object-fit: cover -->
                    <img src="<?php echo esc_url($bg_img); ?>" alt="Không gian ẩm thực cao cấp Saigon Horeca" class="sh-about-values__photo-img" width="800" height="1066" loading="lazy" />
                    <!-- Lớp phủ Gradient Tối màu bên trong Khung Ảnh Dọc -->
                    <div class="sh-about-values__photo-overlay" aria-hidden="true"></div>
                </div>

                <!-- Non-scaling Gold Vector Frame Outline — Vát chéo đối xứng 2 góc (trên-phải & dưới-trái)
                     + điểm nhấn CAD ở 2 góc vuông còn lại (trên-trái & dưới-phải) -->
                <svg class="sh-about-values__photo-svg-outline" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <!-- Viền chính: hexagon vát 2 góc chéo -->
                    <path d="M 0 0 L 85 0 L 100 12 L 100 100 L 15 100 L 0 88 Z" fill="none" stroke="var(--p)" stroke-width="2.5" stroke-opacity="0.85" vector-effect="non-scaling-stroke"/>
                    <!-- Thanh nhấn vàng đậm trên 2 cạnh vát -->
                    <line x1="85" y1="0" x2="100" y2="12" stroke="var(--p)" stroke-width="4" vector-effect="non-scaling-stroke"/>
                    <line x1="15" y1="100" x2="0" y2="88" stroke="var(--p)" stroke-width="4" vector-effect="non-scaling-stroke"/>
                    <!-- Chấm kim cương nhỏ tại 2 đỉnh vát, tạo điểm nhấn trang sức -->
                    <path d="M 92.5 6 l 2.2 2.2 l -2.2 2.2 l -2.2 -2.2 Z" fill="var(--p)" opacity="0.95"/>
                    <path d="M 7.5 94 l 2.2 2.2 l -2.2 2.2 l -2.2 -2.2 Z" fill="var(--p)" opacity="0.95"/>
                    <!-- Ngoặc góc CAD (corner bracket) tại 2 góc vuông còn lại: trên-trái & dưới-phải -->
                    <path class="sh-about-values__corner-mark" d="M 0 9 L 0 0 L 9 0" fill="none" stroke="var(--p)" stroke-width="1.5" stroke-opacity="0.7" vector-effect="non-scaling-stroke"/>
                    <path class="sh-about-values__corner-mark" d="M 91 100 L 100 100 L 100 91" fill="none" stroke="var(--p)" stroke-width="1.5" stroke-opacity="0.7" vector-effect="non-scaling-stroke"/>
                    <circle cx="3" cy="3" r="1.1" fill="var(--p)" opacity="0.8"/>
                    <circle cx="97" cy="97" r="1.1" fill="var(--p)" opacity="0.8"/>
                </svg>

                <!-- GLASSCARD LỒNG Ở CHÂN KHUNG ÁNH DỌC (ĐÁY PHẲNG ÔM TRỌN 100%) -->
                <div class="sh-about-values__glass-card">
                    <div class="sh-about-values__glass-card-border" aria-hidden="true"></div>
                    
                    <div class="sh-about-values__right-content">
                        <span class="sh-about-values__right-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14" aria-hidden="true">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                            </svg>
                            Premium F&amp;B Solutions
                        </span>

                        <h3 class="sh-about-values__passion-title">
                            <?php esc_html_e('Đam mê sáng tạo trong hành trình khám phá', 'saigonhoreca'); ?>
                        </h3>
                        
                        <div class="sh-about-values__rule"></div>
                        
                        <p class="sh-about-values__passion-body">
                            Sài Gòn Horeca luôn thể hiện niềm đam mê và khát vọng khám phá trong quá trình thiết kế sản phẩm và tư vấn giải pháp. Sự kết hợp giữa đam mê và tinh thần khám phá chính là chìa khóa tạo ra những sản phẩm mang tính đột phá.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
