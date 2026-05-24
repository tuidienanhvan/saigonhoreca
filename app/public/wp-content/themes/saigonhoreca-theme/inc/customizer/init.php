<?php
/**
 * SaigonHoreca Theme Customizer
 *
 * @package SaigonHoreca
 */

// Load separate customizer files
require get_template_directory() . '/inc/customizer/colors.php';
require get_template_directory() . '/inc/customizer/footer.php';
require get_template_directory() . '/inc/customizer/front-page.php';
require get_template_directory() . '/inc/customizer/top-bar.php';

function saigonhouse_customize_register($wp_customize) {
    saigonhouse_customize_colors($wp_customize);
    saigonhouse_customize_footer($wp_customize);
    saigonhouse_customize_top_bar($wp_customize);
}
add_action('customize_register', 'saigonhouse_customize_register');

// NOTE: CSS variables không còn được inject từ PHP.
// Toàn bộ design tokens sống trong style.css (:root block).
// Customizer chỉ dùng để hiển thị color pickers trong WP Admin.
