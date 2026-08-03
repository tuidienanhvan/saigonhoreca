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
        'src' => sgh_img('saigonhoreca/SGH-partner' . $num . '.webp'),
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

        </div>
    </div>
</section>
