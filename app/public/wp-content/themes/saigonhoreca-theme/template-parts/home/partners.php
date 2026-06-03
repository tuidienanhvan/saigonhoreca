<?php
/**
 * Home — Đối tác của Saigon Horeca.
 * Grid of 12 partner logos.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
$partners = [];
for ($i = 1; $i <= 12; $i++) {
    $partners[] = sprintf('saigonhoreca/SGH-partner%05d.jpg', $i);
}
?>
<section  class="sh-partners">
    <div class="sh-partners__inner">
        <header class="sh-partners__header scroll-reveal reveal-letter-wide duration-2000">
            <span class="sh-partners__accent" aria-hidden="true"></span>
            <h2 class="sh-partners__title"><?php esc_html_e('Đối tác của Saigon Horeca', 'saigonhoreca'); ?></h2>
        </header>
        <div class="sh-partners__grid scroll-reveal reveal-spring-up duration-1800">
            <?php foreach ($partners as $src) : ?>
                <div class="sh-partners__cell">
                    <img src="<?php echo esc_url(sgh_img("$src")); ?>"
                         alt="Đối tác Saigon Horeca"
                         width="200" height="200"
                         loading="lazy" decoding="async">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
