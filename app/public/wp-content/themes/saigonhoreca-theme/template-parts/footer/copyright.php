<?php
$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
$company_name = $args['company_name'] ?? ($contact['company_name'] ?? get_bloginfo('name'));
?>
<div class="sh-footer__copyright">
    <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html($company_name); ?>. All rights reserved.</p>
</div>
