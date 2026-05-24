<?php
function saigonhouse_customize_colors($wp_customize) {
    // =========================================================================
    // 1. Colors
    // =========================================================================
    $wp_customize->add_section('saigonhouse_colors', array(
        'title'    => __('Màu sắc chủ đạo', 'saigonhoreca'),
        'priority' => 20,
    ));

    // Primary Color (Gold)
    $wp_customize->add_setting('saigonhouse_color_primary', array(
        'default'           => '#F9C349',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'saigonhouse_color_primary', array(
        'label'   => __('Màu chính (Primary)', 'saigonhoreca'),
        'section' => 'saigonhouse_colors',
    )));

    // Secondary Color (Gold Dark)
    $wp_customize->add_setting('saigonhouse_color_secondary', array(
        'default'           => '#ffb100',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'saigonhouse_color_secondary', array(
        'label'   => __('Màu phụ (Secondary)', 'saigonhoreca'),
        'section' => 'saigonhouse_colors',
    )));
}
