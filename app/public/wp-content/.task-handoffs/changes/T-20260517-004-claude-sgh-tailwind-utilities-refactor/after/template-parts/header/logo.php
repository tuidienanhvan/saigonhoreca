<?php
/**
 * Template Part: Logo
 *
 * @package SaigonHouse
 */

$contact = function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : [];

$company_name = $args['company_name'] ?? ($contact['company_name'] ?? get_bloginfo('name'));
$slogan       = $args['slogan'] ?? ($contact['slogan'] ?? get_bloginfo('description'));
$home_url     = $args['home_url'] ?? home_url();

if (isset($args['logo_url'])) {
    $logo_url = $args['logo_url'];
} else {
    $logo_file = '/assets/images/logo.webp';
    $logo_path = get_template_directory() . $logo_file;
    $logo_url  = get_template_directory_uri() . $logo_file;
    if (file_exists($logo_path)) {
        $logo_url .= '?v=' . filemtime($logo_path);
    }
}
?>
<div class="sh-logo">
    <a href="<?php echo esc_url($home_url); ?>" class="sh-logo__link">
        <img src="<?php echo esc_url($logo_url); ?>"
             alt="<?php echo esc_attr($company_name); ?>"
             width="50" height="50"
             class="sh-logo__image">
        <div class="sh-logo__text">
            <span class="sh-logo__name"><?php echo esc_html($company_name); ?></span>
            <span class="sh-logo__slogan"><?php echo esc_html($slogan); ?></span>
        </div>
    </a>
</div>
