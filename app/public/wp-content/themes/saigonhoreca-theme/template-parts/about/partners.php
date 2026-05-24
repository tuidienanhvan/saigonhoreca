<?php
/**
 * About Page Section — Partners & Quote
 *
 * Redesigned into a Luxury Global Brand Hub.
 * Reflective Tech Matrix Grid with blueprint corner marks.
 * 3D Overlapping Image Collage & Glassmorphic Stats.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

// Partner logo paths từ static-mirror
$partners = [];
for ($i = 1; $i <= 12; $i++) {
    $num = str_pad($i, 5, '0', STR_PAD_LEFT);
    $partners[] = [
        'src' => sgh_img('2025/04/SGH-partner') . $num . '.webp',
        'alt' => 'Đối tác Saigon Horeca ' . $i,
    ];
}
?>
<section class="sh-about-partners" aria-label="<?php esc_attr_e('Đối tác và dự án', 'saigonhoreca'); ?>">
    <div class="sh-about-partners__inner">

        <!-- Heading -->
        <div class="sh-about-partners__heading-wrap">
            <span class="sh-about-partners__eyebrow"><?php esc_html_e('Được tin dùng bởi', 'saigonhoreca'); ?></span>
            <h2 class="sh-about-partners__heading"><?php esc_html_e('Đối tác tin cậy của chúng tôi', 'saigonhoreca'); ?></h2>
            <div class="sh-about-partners__heading-rule">
                <span class="sh-about-partners__heading-rule-line"></span>
                <span class="sh-about-partners__heading-rule-dot"></span>
                <span class="sh-about-partners__heading-rule-line"></span>
            </div>
        </div>

        <!-- Logo grid - Reflective Tech Matrix -->
        <div class="sh-about-partners__grid" role="list">
            <?php foreach ($partners as $p): ?>
                <div class="sh-about-partners__logo-cell" role="listitem">
                    <!-- Corner technical markers -->
                    <span class="sh-about-partners__cell-marker sh-about-partners__cell-marker--tl">+</span>
                    <span class="sh-about-partners__cell-marker sh-about-partners__cell-marker--tr">+</span>
                    <span class="sh-about-partners__cell-marker sh-about-partners__cell-marker--bl">+</span>
                    <span class="sh-about-partners__cell-marker sh-about-partners__cell-marker--br">+</span>
                    
                    <!-- Laser Gold Hairline Borders -->
                    <span class="sh-about-partners__laser-border sh-about-partners__laser-border--t"></span>
                    <span class="sh-about-partners__laser-border sh-about-partners__laser-border--r"></span>
                    <span class="sh-about-partners__laser-border sh-about-partners__laser-border--b"></span>
                    <span class="sh-about-partners__laser-border sh-about-partners__laser-border--l"></span>
                    
                    <img
                        src="<?php echo esc_url($p['src']); ?>"
                        alt="<?php echo esc_attr($p['alt']); ?>"
                        loading="lazy"
                        decoding="async"
                        width="180"
                        height="80"
                    />
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Quote panel — 3D Overlapping Collage & Glassmorphic Stats -->
        <div class="sh-about-partners__quote-panel">

            <!-- Cột trái: Collage Ảnh 3D Chồng Lớp -->
            <div class="sh-about-partners__images-collage">
                <!-- Decorative drafting background elements -->
                <div class="sh-about-partners__collage-grid-line sh-about-partners__collage-grid-line--h" aria-hidden="true"></div>
                <div class="sh-about-partners__collage-grid-line sh-about-partners__collage-grid-line--v" aria-hidden="true"></div>
                
                <!-- Blueprint coordinates / drafting axis marks (Decorative only, no English labels) -->
                <div class="sh-about-partners__collage-axis sh-about-partners__collage-axis--x" aria-hidden="true">
                    <span></span>
                </div>
                <div class="sh-about-partners__collage-axis sh-about-partners__collage-axis--y" aria-hidden="true">
                    <span></span>
                </div>

                <!-- Main finished project image -->
                <div class="sh-about-partners__img-main-frame">
                    <img
                        src="<?php echo esc_url(sgh_img('2024/02/The-Brix-05-1-600x337.webp')); ?>"
                        alt="Du an nha hang cao cap The Brix — Saigon Horeca thi cong"
                        loading="lazy"
                        decoding="async"
                        width="600"
                        height="337"
                    />
                    <!-- Subtle technical focus overlay -->
                    <div class="sh-about-partners__tech-cross" aria-hidden="true"></div>
                    <!-- Dimension label overlay -->
                    
                </div>

                <!-- Secondary accent image (3D Overlap) -->
                <div class="sh-about-partners__img-accent-frame">
                    <img
                        src="<?php echo esc_url(sgh_img('2024/02/SGH-Bambino-1-600x337.webp')); ?>"
                        alt="He thong bep cong nghiep inox cao cap"
                        loading="lazy"
                        decoding="async"
                        width="600"
                        height="337"
                    />
                    <!-- Dimension label overlay -->
                    
                </div>
            </div>

            <!-- Cột phải: Quote & Stats -->
            <div class="sh-about-partners__quote-body">

                <!-- Large decorative SVG quote mark -->
                <svg class="sh-about-partners__quote-svg" viewBox="0 0 80 60" fill="none" aria-hidden="true">
                    <path d="M0 60V36C0 16.118 11.176 4.706 33.529 1L36 7.059C25.647 9.176 20 15 20 24H36V60H0ZM44 60V36C44 16.118 55.176 4.706 77.529 1L80 7.059C69.647 9.176 64 15 64 24H80V60H44Z" fill="currentColor"/>
                </svg>

                <blockquote class="sh-about-partners__blockquote">
                    <span class="text-gradient-gold">Saigon Horeca</span> không chỉ là nơi bạn tìm thấy những sản phẩm chất lượng, mà còn là đối tác đáng tin cậy, luôn đồng hành cùng bạn trên con đường phát triển kinh doanh bền vững.
                </blockquote>

                <div class="sh-about-partners__quote-rule"></div>

                <div class="sh-about-partners__quote-meta">
                    <span class="sh-about-partners__quote-name"><?php esc_html_e('Dương Văn Giáp', 'saigonhoreca'); ?></span>
                    <span class="sh-about-partners__quote-role"><?php esc_html_e('Sáng lập — Saigon Horeca', 'saigonhoreca'); ?></span>
                </div>

                <!-- Glassmorphic Stats Row -->
                <div class="sh-about-partners__stats">
                    <div class="sh-about-partners__stat-card">
                        <span class="sh-about-partners__stat-num"><?php esc_html_e('10+', 'saigonhoreca'); ?></span>
                        <span class="sh-about-partners__stat-label"><?php esc_html_e('Năm kinh nghiệm', 'saigonhoreca'); ?></span>
                    </div>
                    <div class="sh-about-partners__stat-card">
                        <span class="sh-about-partners__stat-num"><?php esc_html_e('500+', 'saigonhoreca'); ?></span>
                        <span class="sh-about-partners__stat-label"><?php esc_html_e('Dự án hoàn thành', 'saigonhoreca'); ?></span>
                    </div>
                    <div class="sh-about-partners__stat-card">
                        <span class="sh-about-partners__stat-num">12</span>
                        <span class="sh-about-partners__stat-label"><?php esc_html_e('Đối tác quốc tế', 'saigonhoreca'); ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
