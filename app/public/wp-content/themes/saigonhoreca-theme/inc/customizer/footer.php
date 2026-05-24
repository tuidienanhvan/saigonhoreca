<?php
function saigonhouse_customize_footer($wp_customize) {
    // =========================================================================
    // 2. Contact Information
    // =========================================================================
    $wp_customize->add_section('saigonhouse_contact', array(
        'title'    => __('Thông tin liên hệ', 'saigonhoreca'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('saigonhouse_hotline', array(
        'default'           => saigonhouse_contact('hotline'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saigonhouse_hotline', array(
        'label'   => __('Hotline hiển thị', 'saigonhoreca'),
        'section' => 'saigonhouse_contact',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('saigonhouse_hotline_action', array(
        'default'           => saigonhouse_contact('hotline_raw'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saigonhouse_hotline_action', array(
        'label'       => __('Số điện thoại gọi (tel:)', 'saigonhoreca'),
        'description' => 'Nhập số liền mạch không dấu cách',
        'section'     => 'saigonhouse_contact',
        'type'        => 'text',
    ));

    // Email
    $wp_customize->add_setting('saigonhouse_email', array(
        'default'           => saigonhouse_contact('email_primary'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('saigonhouse_email', array(
        'label'   => __('Email', 'saigonhoreca'),
        'section' => 'saigonhouse_contact',
        'type'    => 'email',
    ));

    // Address
    $wp_customize->add_setting('saigonhouse_address', array(
        'default'           => saigonhouse_contact('address'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('saigonhouse_address', array(
        'label'   => __('Địa chỉ', 'saigonhoreca'),
        'section' => 'saigonhouse_contact',
        'type'    => 'textarea',
    ));

    // =========================================================================
    // 3. Social Media
    // =========================================================================
    $wp_customize->add_section('saigonhouse_social', array(
        'title'    => __('Mạng xã hội', 'saigonhoreca'),
        'priority' => 31,
    ));

    $socials = [
        'facebook' => 'Facebook URL',
        'zalo'     => 'Zalo URL',
        'youtube'  => 'YouTube URL',
        'tiktok'   => 'TikTok URL'
    ];

    foreach ($socials as $key => $label) {
        $setting_id = "saigonhouse_{$key}";
        $wp_customize->add_setting($setting_id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'   => $label,
            'section' => 'saigonhouse_social',
            'type'    => 'url',
        ));
    }
}
