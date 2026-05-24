<?php
/**
 * Front Page Customizer (Kirki Integration)
 * 
 * Defines sections for Hero, Pricing, Testimonials, etc.
 *
 * @package SaigonHoreca
 */

function saigonhouse_customize_front_page($wp_customize) {
    if (!class_exists('Kirki')) {
        return;
    }

    Kirki::add_config('saigonhouse_theme_config', [
        'capability'    => 'edit_theme_options',
        'option_type'   => 'theme_mod',
    ]);

    // PANEL: HOME PAGE
    Kirki::add_panel('saigonhouse_home_panel', [
        'priority'    => 31,
        'title'       => esc_html__('Trang Chủ (Home)', 'saigonhoreca'),
        'description' => esc_html__('Tùy chỉnh các thành phần trang chủ.', 'saigonhoreca'),
    ]);

    // ==========================================
    // SECTION: HERO SLIDER (Info Only)
    // Hero slides are managed via Front Page Meta Box (Page Editor).
    // ==========================================
    Kirki::add_section('saigonhouse_home_hero', [
        'title'          => esc_html__('Hero Slider', 'saigonhoreca'),
        'panel'          => 'saigonhouse_home_panel',
        'priority'       => 10,
        'description'    => esc_html__('Hero Slides được quản lý trong Page Editor của Trang Chủ (Front Page Meta Box). Vào Pages → Trang Chủ → Hero / Slider Section Settings để chỉnh sửa.', 'saigonhoreca'),
    ]);

    Kirki::add_field('saigonhouse_theme_config', [
        'type'        => 'custom',
        'section'     => 'saigonhouse_home_hero',
        'settings'    => 'saigonhouse_hero_info_notice',
        'default'     => '<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;font-size:13px;line-height:1.6;color:#166534;">'
            . '<strong>Hero Slides</strong> được quản lý tại:<br>'
            . '<code>Pages → Trang Chủ → Hero / Slider Section Settings</code><br><br>'
            . 'Nội dung slide (title, subtitle) lấy tự động từ bài viết mới nhất. Meta Box chỉ override ảnh nền.'
            . '</div>',
    ]);

    // ==========================================
    // SECTION: PRICING
    // ==========================================
    Kirki::add_section('saigonhouse_home_pricing', [
        'title'          => esc_html__('Bảng Giá (Pricing)', 'saigonhoreca'),
        'panel'          => 'saigonhouse_home_panel',
        'priority'       => 20,
    ]);

    Kirki::add_field('saigonhouse_theme_config', [
        'type'        => 'repeater',
        'label'       => esc_html__('Danh sách gói giá', 'saigonhoreca'),
        'section'     => 'saigonhouse_home_pricing',
        'settings'    => 'saigonhouse_pricing_cards',
        'row_label'   => ['type' => 'field', 'value' => 'title', 'field' => 'title'],
        'default'     => [
            [
                'title' => 'BÁO GIÁ THIẾT KẾ KIẾN TRÚC',
                'subtitle' => 'Miễn phí khi thi công',
                'link' => '/thiet-ke',
                'color' => 'primary'
            ],
            [
                'title' => 'BÁO GIÁ XÂY DỰNG PHẦN THÔ',
                'subtitle' => 'Miễn phí GPXD - Thiết kế kiến trúc',
                'link' => '/xay-nha-phan-tho',
                'color' => 'primary'
            ],
            [
                'title' => 'BÁO GIÁ XÂY NHÀ TRỌN GÓI',
                'subtitle' => 'Miễn phí GPXD - Thiết kế kiến trúc',
                'link' => '/xay-nha-tron-goi',
                'color' => 'secondary' // Allows highlighting
            ],
        ],
        'fields' => [
            'title' => [ 'type' => 'text', 'label' => 'Tên gói (dòng 1)' ],
            'subtitle' => [ 'type' => 'text', 'label' => 'Mô tả phụ (dòng 2)' ],
            'link' => [ 'type' => 'text', 'label' => 'Link liên kết' ],
            'color' => [ 
                'type' => 'select', 
                'label' => 'Màu sắc',
                'choices' => [
                    'primary' => 'Xanh (Primary)',
                    'secondary' => 'Cam (Secondary)',
                ]
            ],
        ]
    ]);

    // ==========================================
    // SECTION: PROJECTS
    // ==========================================
    Kirki::add_section('saigonhouse_home_projects', [
        'title'          => esc_html__('Dự Án (Featured)', 'saigonhoreca'),
        'panel'          => 'saigonhouse_home_panel',
        'priority'       => 30,
    ]);
    
    Kirki::add_field('saigonhouse_theme_config', [
        'type'     => 'text',
        'settings' => 'saigonhouse_project_title',
        'label'    => 'Tiêu đề mục dự án',
        'section'  => 'saigonhouse_home_projects',
        'default'  => 'Dự Án Tiêu Biểu',
    ]);
    
    Kirki::add_field('saigonhouse_theme_config', [
        'type'     => 'number',
        'settings' => 'saigonhouse_project_limit',
        'label'    => 'Số lượng dự án (nếu dùng bài viết tự động)',
        'section'  => 'saigonhouse_home_projects',
        'default'  => 4,
    ]);

    Kirki::add_field('saigonhouse_theme_config', [
        'type'        => 'repeater',
        'label'       => esc_html__('Danh sách Dự Án Thủ Công', 'saigonhoreca'),
        'section'     => 'saigonhouse_home_projects',
        'settings'    => 'saigonhouse_featured_projects',
        'row_label'   => ['type' => 'field', 'value' => 'title', 'field' => 'title'],
        'default'     => [
            [
                'title' => 'NHÀ PHỐ 5 TẦNG - Q.10',
                'subtitle' => 'Trọn gói - 2023',
                'image' => '',
                'link' => '#'
            ],
            [
                'title' => 'BIỆT THỰ GÒ VẤP',
                'subtitle' => 'Thiết kế - 2023',
                'image' => '',
                'link' => '#'
            ]
        ],
        'fields' => [
            'title' => [ 'type' => 'text', 'label' => 'Tên Dự Án' ],
            'subtitle' => [ 'type' => 'text', 'label' => 'Mô tả ngắn (Năm/Loại)' ],
            'image' => [ 'type' => 'image', 'label' => 'Hình ảnh' ],
            'link' => [ 'type' => 'text', 'label' => 'Link chi tiết' ],
        ]
    ]);

    // ==========================================
    // SECTION: TESTIMONIALS
    // ==========================================
    Kirki::add_section('saigonhouse_home_testimonials', [
        'title'          => esc_html__('Video Cảm Nhận (Reviews)', 'saigonhoreca'),
        'panel'          => 'saigonhouse_home_panel',
        'priority'       => 40,
    ]);

    Kirki::add_field('saigonhouse_theme_config', [
        'type'        => 'repeater',
        'label'       => esc_html__('Danh sách Video', 'saigonhoreca'),
        'section'     => 'saigonhouse_home_testimonials',
        'settings'    => 'saigonhouse_video_reviews',
        'row_label'   => ['type' => 'field', 'value' => 'title', 'field' => 'title'],
        'default'     => [
            [
                'title' => 'Nhật Ký Thi Công - Dự Án 1',
                'video_url' => 'https://www.youtube.com/embed/fKJ7I4I7bT0',
                'image' => '',
            ],
            [
                'title' => 'Nhật Ký Thi Công - Dự Án 2',
                'video_url' => 'https://www.youtube.com/embed/Avwi_LHOp1E',
                'image' => '',
            ],
            [
                'title' => 'Nhật Ký Thi Công - Dự Án 3',
                'video_url' => 'https://www.youtube.com/embed/5qJWr8NrhkQ',
                'image' => '',
            ],
            [
                'title' => 'Nhật Ký Thi Công - Dự Án 4',
                'video_url' => 'https://www.youtube.com/embed/2-9bIAG-th4',
                'image' => '',
            ],
            [
                'title' => 'Nhật Ký Thi Công - Dự Án 5',
                'video_url' => 'https://www.youtube.com/embed/AjpktY4MiW4',
                'image' => '',
            ]
        ],
        'fields' => [
            'title' => [ 'type' => 'text', 'label' => 'Tiêu đề Video' ],
            'video_url' => [ 'type' => 'text', 'label' => 'Link Embed Youtube', 'description' => 'Dạng https://www.youtube.com/embed/ID' ],
            'image' => [ 'type' => 'image', 'label' => 'Ảnh bìa (Thumbnail)' ],
        ]
    ]);
}
add_action('customize_register', 'saigonhouse_customize_front_page');
