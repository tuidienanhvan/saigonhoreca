<?php
/**
 * Top Bar Customizer Settings
 *
 * @package SaigonHoreca
 */

function saigonhouse_customize_top_bar($wp_customize) {
    // Section: Top Bar
    $wp_customize->add_section('saigonhouse_top_bar_section', [
        'title' => 'Top Bar Settings',
        'priority' => 30, // Below Title & Tagline
        'description' => 'Tùy chỉnh nội dung chữ chạy trên thanh Top Bar.',
    ]);

    // Setting: Show Top Bar
    $wp_customize->add_setting('saigonhouse_top_bar_show', [
        'default' => true,
        'sanitize_callback' => 'saigonhouse_sanitize_checkbox',
    ]);
    $wp_customize->add_control('saigonhouse_top_bar_show', [
        'label' => 'Hiển thị Top Bar',
        'section' => 'saigonhouse_top_bar_section',
        'type' => 'checkbox',
    ]);

    // Marquee Lines (1-4)
    $defaults = [
        'Chào mừng kỷ niệm 15 năm thành lập Saigon Horeca!',
        'Giảm ngay 50% phí thiết kế khi thi công trọn gói.',
        'Top 10 Thương Hiệu Uy Tín 2024',
        ''
    ];

    for ($i = 1; $i <= 4; $i++) {
        $setting_id = 'saigonhouse_top_bar_line_' . $i;
        $default_val = isset($defaults[$i-1]) ? $defaults[$i-1] : '';

        $wp_customize->add_setting($setting_id, [
            'default' => $default_val,
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        
        $wp_customize->add_control($setting_id, [
            'label' => 'Dòng thông báo ' . $i,
            'section' => 'saigonhouse_top_bar_section',
            'type' => 'text',
        ]);
    }
}

// Checkbox sanitization helper
if (!function_exists('saigonhouse_sanitize_checkbox')) {
    function saigonhouse_sanitize_checkbox($checked) {
        return ((isset($checked) && true == $checked) ? true : false);
    }
}
