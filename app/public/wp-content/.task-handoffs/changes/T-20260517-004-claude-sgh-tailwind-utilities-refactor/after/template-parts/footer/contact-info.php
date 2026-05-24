<?php
$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
?>
<div class="sh-footer__contact" data-aos="fade-up" data-aos-delay="200">
    <h3 class="sh-footer__heading sh-footer__heading--accent">LIÊN HỆ</h3>
    <ul class="sh-footer__contact-list">
        <?php if (!empty($contact['addresses']) && is_array($contact['addresses'])) : foreach ($contact['addresses'] as $branch) : ?>
        <li class="sh-footer__contact-item">
            <div class="sh-footer__contact-icon"><?php echo sh_icon('map-pin', 'sh-footer__icon-svg'); ?></div>
            <div>
                <strong class="sh-footer__contact-name"><?php echo esc_html($branch['name']); ?></strong>
                <span class="sh-footer__contact-detail"><?php echo esc_html($branch['address']); ?></span>
            </div>
        </li>
        <?php endforeach; endif; ?>

        <?php if (!empty($contact['hotline'])) : ?>
        <li class="sh-footer__contact-item">
            <div class="sh-footer__contact-icon"><?php echo sh_icon('phone', 'sh-footer__icon-svg'); ?></div>
            <div>
                <span class="sh-footer__contact-detail">Hotline: <strong class="sh-footer__contact-highlight"><?php echo esc_html($contact['hotline']); ?></strong></span>
                <?php if (!empty($contact['tel'])) : ?>
                <span class="sh-footer__contact-sub">Tel: <?php echo esc_html($contact['tel']); ?></span>
                <?php endif; ?>
            </div>
        </li>
        <?php endif; ?>

        <?php if (!empty($contact['email_primary'])) : ?>
        <li class="sh-footer__contact-item">
            <div class="sh-footer__contact-icon"><?php echo sh_icon('mail', 'sh-footer__icon-svg'); ?></div>
            <div>
                <a href="mailto:<?php echo esc_attr($contact['email_primary']); ?>" class="sh-footer__contact-link"><?php echo esc_html($contact['email_primary']); ?></a>
                <?php if (!empty($contact['email_secondary'])) : ?>
                <a href="mailto:<?php echo esc_attr($contact['email_secondary']); ?>" class="sh-footer__contact-sub-link"><?php echo esc_html($contact['email_secondary']); ?></a>
                <?php endif; ?>
            </div>
        </li>
        <?php endif; ?>

        <?php if (!empty($contact['website'])) : ?>
        <li class="sh-footer__contact-item">
            <div class="sh-footer__contact-icon"><?php echo sh_icon('maximize', 'sh-footer__icon-svg'); ?></div>
            <div>
                <a href="<?php echo esc_url($contact['website']); ?>" class="sh-footer__contact-link"><?php echo esc_html($contact['website']); ?></a>
                <?php if (!empty($contact['mst'])) : ?>
                <span class="sh-footer__contact-mst">MST: <?php echo esc_html($contact['mst']); ?></span>
                <?php endif; ?>
            </div>
        </li>
        <?php endif; ?>
    </ul>
</div>
