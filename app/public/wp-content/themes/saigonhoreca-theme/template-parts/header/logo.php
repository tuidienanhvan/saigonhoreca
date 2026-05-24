<?php
/**
 * Template Part: Logo (Saigon Horeca)
 *
 * Replaces the Saigon Horeca construction-themed logo (50×50 with green
 * gradient + skyline accent) with the Horeca brand mark (125×58 wordmark
 * on dark background). Matches the live saigonhoreca.vn header.
 *
 * @package SaigonHoreca
 */

$company_name = $args['company_name'] ?? get_bloginfo('name', 'display');
if (empty($company_name) || $company_name === 'saigonhoreca') {
    $company_name = 'Saigon Horeca';
}
$home_url = $args['home_url'] ?? home_url('/');

// Logo asset: defaults to bundled wordmark; overridable via theme mod.
$logo_url = $args['logo_url'] ?? '';
if (empty($logo_url)) {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $img = wp_get_attachment_image_src($custom_logo_id, 'full');
        if (is_array($img) && !empty($img[0])) {
            $logo_url = $img[0];
        }
    }
}
if (empty($logo_url)) {
    $logo_file = '/assets/images/logo.webp';
    $logo_path = get_template_directory() . $logo_file;
    $logo_url  = get_template_directory_uri() . $logo_file;
    if (file_exists($logo_path)) {
        $logo_url .= '?v=' . filemtime($logo_path);
    }
}
?>
<div class="sh-logo">
    <a href="<?php echo esc_url($home_url); ?>" class="sh-logo__link" aria-label="<?php echo esc_attr($company_name); ?> — <?php esc_attr_e('Trang chủ', 'saigonhoreca'); ?>">
        <img src="<?php echo esc_url($logo_url); ?>"
             alt="<?php echo esc_attr($company_name); ?>"
             width="125" height="58"
             fetchpriority="high"
             decoding="sync"
             class="sh-logo__image">
    </a>
</div>
