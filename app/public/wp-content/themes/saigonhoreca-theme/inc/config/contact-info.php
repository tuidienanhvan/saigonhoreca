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
    $lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : (defined('PI_CURRENT_LANGUAGE') ? PI_CURRENT_LANGUAGE : 'vi');

    $slogans = [
        'vi' => 'Thiết bị bếp công nghiệp & giải pháp horeca chuyên nghiệp',
        'en' => 'Commercial Kitchen Equipment & Professional Horeca Solutions',
        'ja' => '業務用厨房機器 ＆ プロフェッショナルHorecaソリューション',
    ];

    $descriptions = [
        'vi' => 'Saigon Horeca cung cấp đa dạng các sản phẩm thiết bị bếp công nghiệp và thiết bị quầy bar phục vụ cho khách hàng trong lĩnh vực F&B, bao gồm tư vấn thiết kế, thi công lắp đặt, cung cấp thiết bị, và tư vấn vận hành kinh doanh.',
        'en' => 'Saigon Horeca provides a comprehensive range of commercial kitchen and bar equipment for the F&B industry, offering full-cycle solutions from design consultancy and fabrication to installation, equipment supply, and operational guidance.',
        'ja' => 'Saigon Horeca（サイゴンホレカ）は、F&B業界の多様なニーズに応える業務用厨房設備およびバー設備を提供しています。設計コンサルティングから施工、機器導入、店舗運営サポートまで一貫したワンストップソリューションを展開しています。',
    ];

    $hq_names = [
        'vi' => 'Trụ sở TP. Hồ Chí Minh',
        'en' => 'Ho Chi Minh City Headquarters',
        'ja' => 'ホーチミン本社',
    ];

    $addresses = [
        'vi' => 'Số 40 Đường Số 6, Khu Dân Cư Melosa Khang Điền, Phường Long Trường, TP. Hồ Chí Minh',
        'en' => 'No. 40, Street 6, Melosa Khang Dien Residence, Long Truong Ward, Ho Chi Minh City, Vietnam',
        'ja' => 'ベトナム ホーチミン市 ロントゥオン街区 メロサ・カンディエン 6番街 40号',
    ];

    $addresses_short = [
        'vi' => 'Số 40, Đường Số 6, KDC Melosa Khang Điền, Long Trường, TP.HCM',
        'en' => 'No. 40, St. 6, Melosa Khang Dien, Long Truong, HCMC',
        'ja' => 'ホーチミン市 ロントゥオン街区 メロサ・カンディエン 6番街 40号',
    ];

    $cities = [
        'vi' => 'Hồ Chí Minh',
        'en' => 'Ho Chi Minh City',
        'ja' => 'ホーチミン市',
    ];

    return [
        // Company info
        'company_name'    => 'SAIGON HORECA',
        'company_full'    => ($lang === 'ja' ? 'サイゴンホレカ株式会社' : ($lang === 'en' ? 'SAIGON HORECA CO., LTD' : 'CÔNG TY TNHH SÀI GÒN HORECA')),
        'slogan'          => $slogans[$lang] ?? $slogans['vi'],
        'description'     => $descriptions[$lang] ?? $descriptions['vi'],
        'mst'             => '',

        // Primary contact
        'hotline'         => '0901 304 365',
        'hotline_raw'     => '0901304365',
        'tel'             => '0901 304 365',
        'hotline_alt'     => '0909 040 920',
        'hotline_alt_raw' => '0909040920',

        // Emails
        'email_primary'   => 'contact@saigonhoreca.com',
        'email_secondary' => 'gin@saigonhoreca.com',

        // Website
        'website'         => 'https://saigonhoreca.vn',

        // Social
        'zalo'            => 'https://zalo.me/0901304365',
        'facebook'        => 'https://www.facebook.com/saigonhoreca',
        'youtube'         => 'https://www.youtube.com/@saigonhoreca',
        'instagram'       => 'https://www.instagram.com/saigonhoreca/',

        // Addresses
        'address'         => $addresses[$lang] ?? $addresses['vi'],
        'city'            => $cities[$lang] ?? $cities['vi'],
        'country_code'    => 'VN',
        'address_short'   => $addresses_short[$lang] ?? $addresses_short['vi'],
        'addresses'       => [
            'hcm' => [
                'name'    => $hq_names[$lang] ?? $hq_names['vi'],
                'address' => $addresses[$lang] ?? $addresses['vi'],
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
