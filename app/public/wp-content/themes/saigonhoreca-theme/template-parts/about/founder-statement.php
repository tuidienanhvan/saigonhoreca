<?php
/**
 * About Page Section — Founder Statement & Impact Stats
 *
 * Architecture: Luxury Architectural Horeca Masterpiece
 * Uses SVG ClipPath Shape (objectBoundingBox) & Strict Theme Design Tokens (--p, --b1, --b2, --bc, --bd).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$main_img = sgh_img('the-brix/the-brix-he-thong-bep-nau-chinh-inox.webp');
$sub_img  = sgh_img('saigonhoreca/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp');

$stats = [
    [
        'number' => '10+',
        'label'  => __('Năm kinh nghiệm', 'saigonhoreca'),
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
    ],
    [
        'number' => '500+',
        'label'  => __('Dự án hoàn thành', 'saigonhoreca'),
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    ],
    [
        'number' => '12',
        'label'  => __('Đối tác quốc tế', 'saigonhoreca'),
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10z"/></svg>',
    ],
];
?>

<section class="sh-about-founder" aria-label="<?php esc_attr_e('Thông điệp nhà sáng lập Saigon Horeca', 'saigonhoreca'); ?>">
    
    <!-- SVG ClipPath Definitions for Unique Architectural Frame (objectBoundingBox) -->
    <svg width="0" height="0" style="position:absolute; width:0; height:0; overflow:hidden; pointer-events:none;" aria-hidden="true">
        <defs>
            <!-- Architectural Chamfer ClipPath (Responsive 100% Object Bounding Box) -->
            <clipPath id="sgh-founder-clip" clipPathUnits="objectBoundingBox">
                <path d="M 0.03,0 L 0.85,0 L 1,0.12 L 1,0.97 C 1,0.99 0.99,1 0.97,1 L 0.15,1 L 0,0.88 L 0,0.03 C 0,0.01 0.01,0 0.03,0 Z" />
            </clipPath>
        </defs>
    </svg>

    <!-- Architectural Blueprint Pattern Background (SVG) -->
    <svg class="sh-about-founder__blueprint-bg" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <pattern id="sgh-blueprint-grid" width="60" height="60" patternUnits="userSpaceOnUse">
                <path d="M 60 0 L 0 0 0 60" fill="none" stroke="var(--p)" stroke-opacity="0.1" stroke-width="0.8"/>
                <circle cx="0" cy="0" r="1.5" fill="var(--p)" fill-opacity="0.25"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#sgh-blueprint-grid)" />
    </svg>

    <div class="sh-about-founder__inner">
        <div class="sh-about-founder__grid">
            
            <!-- CỘT TRÁI: ARCHITECTURAL SVG CREATIVE CANVAS -->
            <div class="sh-about-founder__collage">
                
                <span class="sh-about-founder__drafting-mark sh-about-founder__drafting-mark--tl">+ CAD SPEC // SGH-FOUNDER</span>
                <span class="sh-about-founder__drafting-mark sh-about-founder__drafting-mark--tr">LAT. 10.7769° N</span>

                <!-- Masked Main Frame Container -->
                <div class="sh-about-founder__main-frame">
                    <div class="sh-about-founder__main-img-wrap">
                        <img class="sh-about-founder__main-img" src="<?php echo esc_url($main_img); ?>" alt="Dự án bếp công nghiệp Saigon Horeca thi công" width="800" height="600" loading="lazy" />
                    </div>

                    <!-- Non-scaling Gold Vector Frame Outline (Fits 100% pixel-perfect) -->
                    <svg class="sh-about-founder__svg-frame-art" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M 3 0 L 85 0 L 100 12 L 100 97 C 100 99 99 100 97 100 L 15 100 L 0 88 L 0 3 C 0 1 1 0 3 0 Z" fill="none" stroke="var(--p)" stroke-width="2" stroke-opacity="0.85" vector-effect="non-scaling-stroke"/>
                        <!-- Gold Notch Corner Accent Lines -->
                        <line x1="85" y1="0" x2="100" y2="12" stroke="var(--p)" stroke-width="3.5" vector-effect="non-scaling-stroke"/>
                        <line x1="0" y1="88" x2="15" y2="100" stroke="var(--p)" stroke-width="3.5" vector-effect="non-scaling-stroke"/>
                    </svg>
                </div>

                <!-- Overlapping Sub 3D Glass Badge -->
                <div class="sh-about-founder__sub-card">
                    <span class="sh-about-founder__sub-badge">
                        <span class="sh-about-founder__sub-badge-dot"></span>
                        THIẾT BỊ BẾP INOX HORECA
                    </span>
                    <div class="sh-about-founder__sub-img-wrap">
                        <img class="sh-about-founder__sub-img" src="<?php echo esc_url($sub_img); ?>" alt="Hệ thống thiết bị bếp inox Saigon Horeca" width="400" height="250" loading="lazy" />
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: LUXURY FOUNDER QUOTE & TYPOGRAPHY -->
            <div class="sh-about-founder__right-col">
                
                <!-- Quote Header -->
                <div class="sh-about-founder__quote-header">
                    <svg class="sh-about-founder__quote-svg-icon" viewBox="0 0 48 38" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M0 21.6C0 9.6 7.2 2.4 21.6 0L24 4.8C16.8 6.4 12 11.2 12 16.8H21.6V38H0V21.6ZM24 21.6C24 9.6 31.2 2.4 45.6 0L48 4.8C40.8 6.4 36 11.2 36 16.8H45.6V38H24V21.6Z" />
                    </svg>
                    <span class="sh-about-founder__quote-tag"><?php esc_html_e('Tuyên ngôn giá trị', 'saigonhoreca'); ?></span>
                </div>

                <!-- Quote Statement Text -->
                <blockquote class="sh-about-founder__statement">
                    <span class="sh-about-founder__brand-highlight">Saigon Horeca</span> không chỉ là nơi bạn tìm thấy những sản phẩm chất lượng, mà còn là <span class="sh-about-founder__text-gold">đối tác đáng tin cậy</span>, luôn đồng hành cùng bạn trên con đường phát triển kinh doanh bền vững.
                </blockquote>

                <!-- Author Block & Executive Signature Calligraphy -->
                <div class="sh-about-founder__author-block">
                    <div class="sh-about-founder__author-divider" aria-hidden="true"></div>
                    <div class="sh-about-founder__author-details">
                        <h3 class="sh-about-founder__author-name"><?php esc_html_e('Dương Văn Giáp', 'saigonhoreca'); ?></h3>
                        <p class="sh-about-founder__author-title"><?php esc_html_e('SÁNG LẬP — SAIGON HORECA', 'saigonhoreca'); ?></p>
                    </div>

                    <!-- LUXURY CALLIGRAPHY EXECUTIVE SIGNATURE SVG (ViewBox 320x80 Vươn Rộng Dài) -->
                    <div class="sh-about-founder__signature-wrap" title="Chữ ký Dương Văn Giáp">
                        <svg class="sh-about-founder__signature-svg" viewBox="0 0 320 80" xmlns="http://www.w3.org/2000/svg" aria-label="Chữ ký Dương Văn Giáp">
                            <!-- Nét chữ D lớn uốn lượn nghệ thuật -->
                            <path class="sig-path" d="M 22 58 C 15 35, 12 15, 28 10 C 44 5, 62 18, 52 42 C 44 58, 20 52, 38 68 C 52 78, 88 58, 122 48" stroke="var(--p)" stroke-width="2.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <!-- Nét móc đỉnh chữ D -->
                            <path class="sig-path" d="M 25 12 C 38 8, 55 10, 48 24 C 42 38, 28 34, 45 22" stroke="var(--p)" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                            <!-- Nét ký uốn lượn vươn dài "Văn Giáp" trải dài bên phải -->
                            <path class="sig-path" d="M 112 46 C 120 30, 132 28, 138 42 C 146 58, 160 32, 172 28 C 182 26, 192 48, 202 32 C 212 22, 228 38, 240 24 C 252 16, 268 34, 288 18" stroke="var(--p)" stroke-width="2.6" fill="none" stroke-linecap="round"/>
                            <!-- Nét gạch chân bay bổng nét thanh nét đậm trải dài lấp đầy 100% khoảng trống -->
                            <path class="sig-path" d="M 45 66 C 95 78, 185 74, 285 46 C 298 42, 308 34, 298 38" stroke="var(--p)" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                            <!-- Ngôi sao nhỏ nghệ thuật ở đuôi nét chữ ký -->
                            <polygon points="292,16 294,22 300,23 295,27 297,33 292,29 287,33 289,27 284,23 290,22" fill="var(--p)"/>
                        </svg>
                    </div>
                </div>

                <!-- 3D Gold Impact Stats Cards -->
                <div class="sh-about-founder__stats-grid">
                    <?php foreach ($stats as $stat): ?>
                        <div class="sh-about-founder__stat-card">
                            <span class="sh-about-founder__stat-icon" aria-hidden="true"><?php echo $stat['icon']; ?></span>
                            <span class="sh-about-founder__stat-number"><?php echo esc_html($stat['number']); ?></span>
                            <span class="sh-about-founder__stat-label"><?php echo esc_html($stat['label']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>

        </div>
    </div>
</section>
