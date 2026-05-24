<?php
/**
 * Home — Hero Carousel (Saigon Horeca)
 *
 * Cloned from saigonhoreca-theme/template-parts/home/hero-carousel.php so
 * motion logic stays identical (Ken Burns zoom 6s + cross-fade 1.2s +
 * `is-hero-ready` class trigger from `assets/js/hero-carousel.js`).
 *
 * Differences from saigonhouse hero:
 *   - Caption text (TƯ VẤN / BESPOKE / etc.) is BAKED into the JPGs, so
 *     no HTML overlay needed. Just bg image + light overlay.
 *   - 2 production banners (SGH-banner.jpg, SGH-banner-02.jpg).
 *
 * IMPORTANT — DOM structure that the JS expects:
 *   <section class="sh-hero">
 *     <div id="sh-hero-slider" class="sh-hero__slider">   ← id HERE, not on section
 *       <div class="sh-hero__slides">
 *         <div class="carousel-slide active" style="display:block;opacity:1;z-index:20;">
 *           <img class="slide-bg sh-hero__bg-img" …>     ← BOTH classes for CSS+JS compat
 *           <div class="sh-hero__overlay"></div>
 *         </div>
 *       </div>
 *       <button id="hero-prev">…</button>
 *       <button id="hero-next">…</button>
 *       <div class="sh-hero__dots">
 *         <button class="carousel-dot sh-hero__dot active" data-index="0"></button>
 *       </div>
 *     </div>
 *   </section>
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$slides = [
    [
        'img' => '2025/05/SGH-banner.jpg',
        'alt' => 'Bespoke Kitchen & Bar — Saigon Horeca',
    ],
    [
        'img' => '2025/04/SGH-banner-02.jpg',
        'alt' => 'Tư vấn thiết kế thi công bếp công nghiệp — Saigon Horeca',
    ],
];
?>
<section class="sh-hero" aria-label="<?php esc_attr_e('Slider giới thiệu Saigon Horeca', 'saigonhoreca'); ?>">
    <div id="sh-hero-slider" class="sh-hero__slider">
        <div class="sh-hero__slides">
            <?php foreach ($slides as $index => $s) :
                $is_active     = ($index === 0);
                $active_class  = $is_active ? 'active' : '';
                $initial_style = $is_active ? 'display:block;opacity:1;z-index:20;' : 'display:none;opacity:0;z-index:0;';
                $loading_attr  = $is_active ? 'eager' : 'lazy';
                $priority_attr = $is_active ? ' fetchpriority="high"' : '';
                $decoding_attr = $is_active ? 'sync' : 'async';
            ?>
                <div class="carousel-slide <?php echo $active_class; ?>"
                     style="<?php echo esc_attr($initial_style); ?>"
                     data-index="<?php echo (int) $index; ?>">
                    <?php
                    $img_ext = pathinfo($s['img'], PATHINFO_EXTENSION);
                    $img_base = substr($s['img'], 0, -(strlen($img_ext) + 1));
                    $img_mobile = "{$img_base}-mobile.{$img_ext}";
                    ?>
                    <img
                        src="<?php echo esc_url(sgh_img("{$s['img']}")); ?>"
                        srcset="<?php echo esc_url(sgh_img($img_mobile)); ?> 600w, <?php echo esc_url(sgh_img("{$s['img']}")); ?> 1200w"
                        sizes="(max-width: 576px) 600px, 100vw"
                        alt="<?php echo esc_attr($s['alt']); ?>"
                        width="1200" height="675"
                        loading="<?php echo esc_attr($loading_attr); ?>"
                        decoding="<?php echo esc_attr($decoding_attr); ?>"<?php echo $priority_attr; ?>
                        class="slide-bg sh-hero__bg-img"
                    />
                    <div class="sh-hero__overlay"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="hero-prev" class="sh-hero__nav sh-hero__nav--prev" aria-label="<?php esc_attr_e('Slide trước', 'saigonhoreca'); ?>" title="<?php esc_attr_e('Slide trước', 'saigonhoreca'); ?>">
            <svg class="sh-hero__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <button id="hero-next" class="sh-hero__nav sh-hero__nav--next" aria-label="<?php esc_attr_e('Slide tiếp theo', 'saigonhoreca'); ?>" title="<?php esc_attr_e('Slide tiếp theo', 'saigonhoreca'); ?>">
            <svg class="sh-hero__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>

        <div class="sh-hero__dots">
            <?php foreach ($slides as $index => $s) : ?>
                <button class="carousel-dot sh-hero__dot <?php echo $index === 0 ? 'active' : ''; ?>"
                        data-index="<?php echo (int) $index; ?>"
                        aria-label="<?php echo esc_attr(sprintf(__('Đến slide %d', 'saigonhoreca'), $index + 1)); ?>"
                        title="<?php echo esc_attr(sprintf(__('Slide %d', 'saigonhoreca'), $index + 1)); ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
