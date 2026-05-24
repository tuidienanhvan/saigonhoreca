<?php
/**
 * Footer Widget Row — 4 columns.
 *
 * Rendered only when at least one of `saigonhouse-footer-1` … `-4` has a widget.
 * When all 4 are empty, footer.php falls back to the legacy hardcoded grid
 * (company-info / quick-links / contact-info).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;
if (!function_exists('sh_footer_has_widgets') || !sh_footer_has_widgets()) return;
?>

<div class="sh-footer__widgets">
    <div class="sh-footer__widgets-grid">
        <?php for ($i = 1; $i <= 4; $i++) :
            if (!is_active_sidebar('saigonhouse-footer-' . $i)) continue; ?>
            <div class="sh-footer__widgets-col sh-footer__widgets-col--<?php echo $i; ?>">
                <?php dynamic_sidebar('saigonhouse-footer-' . $i); ?>
            </div>
        <?php endfor; ?>
    </div>
</div>
