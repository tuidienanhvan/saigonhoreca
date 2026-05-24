<?php
/**
 * Archive product — pagination.
 *
 * Standard WP `paginate_links()` wrapped in the theme's `sgh-pagination`
 * shell so it picks up the shared archive pagination styles.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/* WCAG link-name — sh_icon() returns inline SVG with no text/aria-label,
   so prev/next links failed `link-name`. Wrap each icon with a visually
   hidden label so screen readers announce the link purpose. */
$prev_label = esc_html__('Trang trước', 'saigonhoreca');
$next_label = esc_html__('Trang sau', 'saigonhoreca');
$links = paginate_links([
    'mid_size'  => 2,
    'prev_text' => sh_icon('chevron-left', '') . '<span class="sr-only">' . $prev_label . '</span>',
    'next_text' => sh_icon('chevron-right', '') . '<span class="sr-only">' . $next_label . '</span>',
    'type'      => 'plain',
]);
if (!$links) return;
?>

<nav class="sgh-pagination" aria-label="<?php esc_attr_e('Trang sản phẩm', 'saigonhoreca'); ?>">
    <?php echo $links; ?>
</nav>
