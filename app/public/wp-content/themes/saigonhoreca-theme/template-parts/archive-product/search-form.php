<?php
/**
 * Archive product — search form.
 *
 * Rendered at the top of the main product grid section.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$search_val = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
$search_action = home_url($lang === 'vi' ? '/san-pham/' : "/{$lang}/products/");
?>

<div class="sh-archive__search-wrapper">
    <form role="search" method="get" action="<?php echo esc_url($search_action); ?>" class="sh-archive__search sh-archive__search--main">
        <label for="sh-archive-search" class="sh-archive__search-label sr-only">
            <?php echo esc_html(sgh_t('search_label')); ?>
        </label>
        <span class="sh-archive__search-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>
            </svg>
        </span>
        <input type="search"
               id="sh-archive-search"
               name="s"
               value="<?php echo esc_attr($search_val); ?>"
               placeholder="<?php echo esc_attr(sgh_t('search_placeholder')); ?>"
               class="sh-archive__search-input"
               autocomplete="off">
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="sh-archive__search-btn">
            <?php echo esc_html(sgh_t('search_btn')); ?>
        </button>
    </form>
</div>
