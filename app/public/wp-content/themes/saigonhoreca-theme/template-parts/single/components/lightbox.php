<?php
/**
 * single.php part — lightbox xem ảnh phóng to (#sh-lightbox).
 * Chỉ render ở nhánh post; markup tĩnh, JS gắn sự kiện lúc runtime.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;
$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

$lb_labels = [
    'vi' => 'Xem ảnh phóng to',
    'en' => 'Enlarge Image',
    'ja' => '拡大画像を見る',
];

$lb_close = [
    'vi' => 'Đóng (Esc)',
    'en' => 'Close (Esc)',
    'ja' => '閉じる (Esc)',
];

$lb_hints = [
    'vi' => 'Click bất kỳ đâu hoặc nhấn <kbd>Esc</kbd> để đóng',
    'en' => 'Click anywhere or press <kbd>Esc</kbd> to close',
    'ja' => '任意の場所をクリック、または <kbd>Esc</kbd> で閉じる',
];
?>
<div id="sh-lightbox" class="sh-single__lightbox" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr($lb_labels[$lang] ?? $lb_labels['vi']); ?>" hidden>
    <div class="sh-single__lightbox-backdrop" aria-hidden="true"></div>
    <button id="sh-lightbox-close" type="button" class="sh-single__lightbox-close" aria-label="<?php echo esc_attr($lb_close[$lang] ?? $lb_close['vi']); ?>">
        <?php echo sh_icon('x', 'sh-single__lightbox-icon'); ?>
    </button>
    <figure class="sh-single__lightbox-figure">
        <div class="sh-single__lightbox-loader" aria-hidden="true"></div>
        <img id="sh-lightbox-img" src="" alt="" class="sh-single__lightbox-img">
        <figcaption id="sh-lightbox-caption" class="sh-single__lightbox-caption"></figcaption>
    </figure>
    <div class="sh-single__lightbox-hint" aria-hidden="true">
        <span><?php echo wp_kses_post($lb_hints[$lang] ?? $lb_hints['vi']); ?></span>
    </div>
</div>
