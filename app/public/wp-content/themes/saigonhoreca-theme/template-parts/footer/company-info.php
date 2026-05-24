<?php
$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
$logo_url = $args['logo_url'] ?? get_template_directory_uri() . '/assets/images/apple-touch-icon.png';
?>
<div class="sh-footer__company">
    <div class="sh-footer__logo-wrap">
        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($contact['company_name'] ?? ''); ?>" width="48" height="48" class="sh-footer__logo-img" loading="lazy">
        <span class="sh-footer__company-name"><?php echo esc_html($contact['company_full'] ?? ''); ?></span>
    </div>
    <?php if (!empty($contact['description'])) : ?>
    <p class="sh-footer__company-desc"><?php echo esc_html($contact['description']); ?></p>
    <?php endif; ?>
    <div class="sh-footer__socials">
        <?php if($fb = get_theme_mod('saigonhouse_facebook', $contact['facebook'] ?? '')): ?>
        <a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener noreferrer" class="sh-footer__social-btn" aria-label="Facebook">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/facebook-icon.webp" alt="Social" class="sh-footer__social-img" loading="lazy">
        </a>
        <?php endif; ?>
        <?php if(!empty($contact['zalo'])): ?>
        <a href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" class="sh-footer__social-btn" aria-label="Zalo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/zalo-icon.webp" alt="Social" class="sh-footer__social-img" loading="lazy">
        </a>
        <?php endif; ?>
        <?php if($yt = get_theme_mod('saigonhouse_youtube', $contact['youtube'] ?? '')): ?>
        <a href="<?php echo esc_url($yt); ?>" target="_blank" rel="noopener noreferrer" class="sh-footer__social-btn" aria-label="YouTube">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/youtube-icon.webp" alt="Social" class="sh-footer__social-img" loading="lazy">
        </a>
        <?php endif; ?>
    </div>
</div>
