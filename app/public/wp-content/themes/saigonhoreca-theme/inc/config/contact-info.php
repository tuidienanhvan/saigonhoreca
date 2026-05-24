<?php
/**
 * Centralized Contact Information — saigonhoreca
 *
 * All contact data in one place. Values taken from the production
 * saigonhoreca.vn /lien-he/ scrape (Phase 2 data migration).
 *
 * Usage: $contact = saigonhouse_get_contact_info();
 * (Function name kept as `saigonhouse_*` for backward compatibility with
 * the parent theme; do not rename without sweeping all callers in
 * template-parts/header, template-parts/footer, template-parts/contact.)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get all contact information.
 */
function saigonhouse_get_contact_info() {
    return [
        // Company info
        'company_name'    => 'SAIGON HORECA',
        'company_full'    => 'CÔNG TY TNHH SÀI GÒN HORECA',
        'slogan'          => 'Thiết bị bếp công nghiệp & giải pháp horeca chuyên nghiệp',
        'description'     => 'Saigon Horeca cung cấp đa dạng các sản phẩm thiết bị bếp công nghiệp và thiết bị quầy bar phục vụ cho khách hàng trong lĩnh vực F&B, bao gồm tư vấn thiết kế, thi công lắp đặt, cung cấp thiết bị, và tư vấn vận hành kinh doanh.',
        'mst'             => '',

        // Primary contact
        'hotline'         => '0901 304 365',
        'hotline_raw'     => '0901304365',
        'tel'             => '0901 304 365',
        'hotline_alt'     => '0909 040 920',
        'hotline_alt_raw' => '0909040920',

        // Emails
        'email_primary'   => 'contact@saigonhoreca.com',
        'email_secondary' => 'admin@saigonhoreca.com',

        // Website
        'website'         => 'https://saigonhoreca.vn',

        // Social
        'zalo'            => 'https://zalo.me/0901304365',
        'facebook'        => 'https://www.facebook.com/saigonhoreca',
        'youtube'         => 'https://www.youtube.com/@saigonhoreca',

        // Addresses
        'address'         => 'Số 40 Đường Số 6, Khu Dân Cư Melosa Khang Điền, Phường Long Trường, TP. Hồ Chí Minh',
        'address_short'   => 'Số 40, Đường Số 6, KDC Melosa Khang Điền, Long Trường, TP.HCM',
        'addresses'       => [
            'hcm' => [
                'name'    => 'Trụ sở TP. Hồ Chí Minh',
                'address' => 'Số 40 Đường Số 6, Khu Dân Cư Melosa Khang Điền, Phường Long Trường, TP. Hồ Chí Minh',
                'is_hq'   => true,
            ],
        ],
    ];
}

/**
 * Helper: get single contact field.
 */
function saigonhouse_contact($key, $default = '') {
    $info = saigonhouse_get_contact_info();
    return isset($info[$key]) ? $info[$key] : $default;
}

/**
 * Helper: get formatted phone link.
 */
function saigonhouse_phone_link() {
    return 'tel:' . saigonhouse_contact('hotline_raw');
}

/**
 * Helper: get primary email link.
 */
function saigonhouse_email_link() {
    return 'mailto:' . saigonhouse_contact('email_primary');
}
