<?php
/**
 * Before/After Image Comparison Slider (CSS-only)
 *
 * Styling: template-parts/components/before-after.css (bundled into dist/theme.css)
 *
 * Usage: get_template_part('template-parts/components/before-after', null, ['before' => $url1, 'after' => $url2]);
 *
 * @package SaigonHouse
 */

$before = $args['before'] ?? '';
$after  = $args['after'] ?? '';
if (!$before || !$after) return;

$uid = 'sh-ba-' . wp_unique_id();
?>
<div class="sh-before-after" data-aos="zoom-in">
    <img src="<?php echo esc_url($after); ?>" alt="After" class="sh-before-after__after" loading="lazy">

    <div id="<?php echo $uid; ?>-clip" class="sh-before-after__before-clip">
        <img src="<?php echo esc_url($before); ?>" alt="Before" class="sh-before-after__before" loading="lazy">
    </div>

    <div id="<?php echo $uid; ?>-line" class="sh-before-after__line">
        <div class="sh-before-after__handle">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M6 10L2 10M2 10L4.5 7.5M2 10L4.5 12.5M14 10L18 10M18 10L15.5 7.5M18 10L15.5 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>

    <input type="range" min="0" max="100" value="50" class="sh-before-after__range"
        oninput="(function(el){var v=el.value+'%';el.parentElement.querySelector('#<?php echo $uid; ?>-clip').style.width=v;el.parentElement.querySelector('#<?php echo $uid; ?>-line').style.left=v;})(this)"
        aria-label="Kéo để so sánh trước và sau">

    <span class="sh-before-after__label sh-before-after__label--before">Trước</span>
    <span class="sh-before-after__label sh-before-after__label--after">Sau</span>
</div>
