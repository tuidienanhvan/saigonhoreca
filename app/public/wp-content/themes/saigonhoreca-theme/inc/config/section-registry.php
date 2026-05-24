<?php
/**
 * Page Section Registry — Defines editable sections for each page template.
 *
 * Provides the section map to Pi Dashboard so it can render a section editor
 * when editing pages. Template parts read post_meta with hardcoded fallbacks.
 *
 * Field types: text, textarea, image, link, number, select
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

add_filter('pi_page_sections', 'sgh_register_page_sections');

function sgh_register_page_sections(array $sections): array {

    // ─── Giới Thiệu ───
    $sections['page-templates/page-gioi-thieu.php'] = [
        'label' => 'Giới Thiệu',
        'sections' => [
            'about_hero' => [
                'label' => 'Hero Banner',
                'icon'  => 'image',
                'fields' => [
                    ['key' => '_sgh_about_hero_image', 'type' => 'image', 'label' => 'Ảnh nền Hero'],
                ],
            ],
            'about_story' => [
                'label' => 'Câu Chuyện Thương Hiệu',
                'icon'  => 'book-open',
                'fields' => [
                    ['key' => '_sgh_story_tag',      'type' => 'text',     'label' => 'Tag nhỏ',    'default' => 'Tầm Nhìn & Sứ Mệnh'],
                    ['key' => '_sgh_story_title',    'type' => 'text',     'label' => 'Tiêu đề',    'default' => 'KIẾN TẠO'],
                    ['key' => '_sgh_story_accent',   'type' => 'text',     'label' => 'Chữ gradient','default' => 'Di Sản'],
                    ['key' => '_sgh_story_subtitle', 'type' => 'text',     'label' => 'Phụ đề',     'default' => 'Cho Thế Hệ Tương Lai'],
                    ['key' => '_sgh_story_quote',    'type' => 'textarea', 'label' => 'Câu quote',  'default' => 'Tại Saigon Horeca, chúng tôi không chỉ xây dựng những ngôi nhà vô tri, mà chúng tôi kiến tạo những Tổ Ấm.'],
                    ['key' => '_sgh_story_body',     'type' => 'textarea', 'label' => 'Nội dung',   'default' => 'Hơn 10 năm hành trình, sứ mệnh của chúng tôi là hiện thực hóa giấc mơ về một không gian sống đẳng cấp, bền vững.'],
                    ['key' => '_sgh_story_badge',    'type' => 'text',     'label' => 'Badge text',  'default' => 'Cam Kết Chất Lượng'],
                    ['key' => '_sgh_story_badge_sub','type' => 'text',     'label' => 'Badge sub',   'default' => 'Bảo hành kết cấu 10 năm'],
                ],
            ],
            'about_values' => [
                'label' => 'Giá Trị Cốt Lõi (3 Cards)',
                'icon'  => 'heart',
                'fields' => [
                    ['key' => '_sgh_values_heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Lý Do Chọn Saigon Horeca'],
                    ['key' => '_sgh_val1_title', 'type' => 'text', 'label' => 'Card 1 — Tiêu đề', 'default' => 'Tận Tâm'],
                    ['key' => '_sgh_val1_desc',  'type' => 'textarea', 'label' => 'Card 1 — Mô tả', 'default' => 'Chúng tôi coi ngôi nhà của bạn như chính ngôi nhà của mình.'],
                    ['key' => '_sgh_val2_title', 'type' => 'text', 'label' => 'Card 2 — Tiêu đề', 'default' => 'Chất Lượng'],
                    ['key' => '_sgh_val2_desc',  'type' => 'textarea', 'label' => 'Card 2 — Mô tả', 'default' => 'Cam kết không dùng vật tư giả. Quy trình ISO giám sát chặt chẽ.'],
                    ['key' => '_sgh_val3_title', 'type' => 'text', 'label' => 'Card 3 — Tiêu đề', 'default' => 'Đúng Tiến Độ'],
                    ['key' => '_sgh_val3_desc',  'type' => 'textarea', 'label' => 'Card 3 — Mô tả', 'default' => 'Cam kết chuẩn tiến độ 100%. Phạt hợp đồng nếu chậm trễ.'],
                ],
            ],
            'about_intro' => [
                'label' => 'Giới Thiệu Công Ty',
                'icon'  => 'info',
                'fields' => [
                    ['key' => '_sgh_intro_tag',   'type' => 'text',     'label' => 'Tag',      'default' => 'Về Chúng Tôi'],
                    ['key' => '_sgh_intro_title', 'type' => 'text',     'label' => 'Tiêu đề',  'default' => 'Kiến Tạo Không Gian sống Đẳng Cấp & Bền Vững'],
                    ['key' => '_sgh_intro_lead',  'type' => 'textarea', 'label' => 'Lead text', 'default' => 'Saigon Horeca tự hào là đơn vị hàng đầu trong lĩnh vực thiết kế và thi công nội thất, kiến trúc.'],
                    ['key' => '_sgh_intro_stat1', 'type' => 'text', 'label' => 'Stat 1', 'default' => '500+'],
                    ['key' => '_sgh_intro_stat1_label', 'type' => 'text', 'label' => 'Stat 1 label', 'default' => 'Dự Án Hoàn Thành'],
                    ['key' => '_sgh_intro_stat2', 'type' => 'text', 'label' => 'Stat 2', 'default' => '98%'],
                    ['key' => '_sgh_intro_stat2_label', 'type' => 'text', 'label' => 'Stat 2 label', 'default' => 'Khách Hàng Hài Lòng'],
                    ['key' => '_sgh_intro_stat3', 'type' => 'text', 'label' => 'Stat 3', 'default' => '50+'],
                    ['key' => '_sgh_intro_stat3_label', 'type' => 'text', 'label' => 'Stat 3 label', 'default' => 'Kiến Trúc Sư'],
                ],
            ],
            'about_cta' => [
                'label' => 'Call To Action',
                'icon'  => 'phone',
                'fields' => [
                    ['key' => '_sgh_cta_title', 'type' => 'text', 'label' => 'Tiêu đề', 'default' => 'Bạn Đã Sẵn Sàng Xây Dựng Tổ Ấm?'],
                    ['key' => '_sgh_cta_desc',  'type' => 'textarea', 'label' => 'Mô tả', 'default' => 'Hãy để Saigon Horeca đồng hành cùng bạn hiện thực hóa ngôi nhà mơ ước.'],
                ],
            ],
        ],
    ];

    // ─── Bảng Giá ───
    $sections['page-templates/page-bang-gia.php'] = [
        'label' => 'Bảng Giá',
        'sections' => [
            'pricing_hero' => [
                'label' => 'Hero Banner',
                'icon'  => 'dollar-sign',
                'fields' => [
                    ['key' => '_sgh_price_badge',    'type' => 'text', 'label' => 'Badge',    'default' => 'Estimates 2026'],
                    ['key' => '_sgh_price_title',    'type' => 'text', 'label' => 'Tiêu đề',  'default' => 'BẢNG GIÁ'],
                    ['key' => '_sgh_price_subtitle', 'type' => 'text', 'label' => 'Phụ đề',   'default' => 'Xây Dựng & Thiết Kế'],
                    ['key' => '_sgh_price_desc',     'type' => 'textarea', 'label' => 'Mô tả', 'default' => 'Minh bạch từng hạng mục. Chi tiết từng vật tư.'],
                    ['key' => '_sgh_price_link1',    'type' => 'link', 'label' => 'Nút 1 — URL', 'default' => '/bao-gia-thiet-ke-kien-truc-xay-dung-cong-trinh-1/'],
                    ['key' => '_sgh_price_link1_text','type' => 'text', 'label' => 'Nút 1 — Text', 'default' => 'Đơn Giá Thiết Kế'],
                    ['key' => '_sgh_price_link2',    'type' => 'link', 'label' => 'Nút 2 — URL', 'default' => '/bang-bao-gia-thi-cong-xay-dung-nha-phan-tho-1/'],
                    ['key' => '_sgh_price_link2_text','type' => 'text', 'label' => 'Nút 2 — Text', 'default' => 'Thi Công Phần Thô'],
                    ['key' => '_sgh_price_link3',    'type' => 'link', 'label' => 'Nút 3 — URL', 'default' => '/bang-bao-gia-xay-dung-nha-tron-goi-1/'],
                    ['key' => '_sgh_price_link3_text','type' => 'text', 'label' => 'Nút 3 — Text', 'default' => 'Xây Nhà Trọn Gói'],
                ],
            ],
        ],
    ];

    // ─── Liên Hệ ───
    $sections['page-templates/page-lien-he.php'] = [
        'label' => 'Liên Hệ',
        'sections' => [
            'contact_hero' => [
                'label' => 'Hero Banner',
                'icon'  => 'mail',
                'fields' => [
                    ['key' => '_sgh_contact_hero_image', 'type' => 'image', 'label' => 'Ảnh nền Hero'],
                ],
            ],
            'contact_form' => [
                'label' => 'Form Liên Hệ',
                'icon'  => 'edit-3',
                'fields' => [
                    ['key' => '_sgh_contact_title',   'type' => 'text', 'label' => 'Tiêu đề trái',  'default' => 'Liên Hệ Tư Vấn'],
                    ['key' => '_sgh_contact_subtitle','type' => 'text', 'label' => 'Phụ đề',         'default' => 'Đội ngũ KTS giàu kinh nghiệm sẵn sàng lắng nghe.'],
                    ['key' => '_sgh_contact_form_title','type' => 'text', 'label' => 'Tiêu đề form', 'default' => 'Gửi yêu cầu của bạn'],
                ],
            ],
            'contact_map' => [
                'label' => 'Bản Đồ',
                'icon'  => 'map-pin',
                'fields' => [
                    ['key' => '_sgh_map_hours', 'type' => 'text', 'label' => 'Giờ làm việc', 'default' => 'Thứ 2 - Thứ 7: 8:00 - 17:30'],
                ],
            ],
        ],
    ];

    // ─── Trang Chủ (Front Page) ───
    $sections['front-page.php'] = [
        'label' => 'Trang Chủ',
        'sections' => [

            // Hero Carousel
            'home_hero' => [
                'label' => 'Hero Carousel',
                'icon'  => 'image',
                'fields' => [
                    ['key' => '_sgh_hero_badge',    'type' => 'text', 'label' => 'Badge text',   'default' => 'Kiến Trúc & Xây Dựng Saigon Horeca'],
                    ['key' => '_sgh_hero_cta2',     'type' => 'text', 'label' => 'Nút CTA phụ',  'default' => 'Dự Toán Online'],
                    ['key' => '_sgh_hero_cta2_url',  'type' => 'link', 'label' => 'URL nút CTA phụ', 'default' => '/du-toan/'],
                    ['key' => '_pi_hero_1_bg',      'type' => 'image', 'label' => 'Slide 1 — Ảnh nền'],
                    ['key' => '_pi_hero_1_title',   'type' => 'text',  'label' => 'Slide 1 — Tiêu đề'],
                    ['key' => '_pi_hero_1_subtitle', 'type' => 'textarea', 'label' => 'Slide 1 — Mô tả'],
                    ['key' => '_pi_hero_1_btn_text', 'type' => 'text',  'label' => 'Slide 1 — Nút text', 'default' => 'Xem Chi Tiết'],
                    ['key' => '_pi_hero_1_btn_link', 'type' => 'link',  'label' => 'Slide 1 — Nút URL'],
                    ['key' => '_pi_hero_2_bg',      'type' => 'image', 'label' => 'Slide 2 — Ảnh nền'],
                    ['key' => '_pi_hero_2_title',   'type' => 'text',  'label' => 'Slide 2 — Tiêu đề'],
                    ['key' => '_pi_hero_2_subtitle', 'type' => 'textarea', 'label' => 'Slide 2 — Mô tả'],
                    ['key' => '_pi_hero_2_btn_text', 'type' => 'text',  'label' => 'Slide 2 — Nút text', 'default' => 'Xem Chi Tiết'],
                    ['key' => '_pi_hero_2_btn_link', 'type' => 'link',  'label' => 'Slide 2 — Nút URL'],
                    ['key' => '_pi_hero_3_bg',      'type' => 'image', 'label' => 'Slide 3 — Ảnh nền'],
                    ['key' => '_pi_hero_3_title',   'type' => 'text',  'label' => 'Slide 3 — Tiêu đề'],
                    ['key' => '_pi_hero_3_subtitle', 'type' => 'textarea', 'label' => 'Slide 3 — Mô tả'],
                    ['key' => '_pi_hero_3_btn_text', 'type' => 'text',  'label' => 'Slide 3 — Nút text', 'default' => 'Xem Chi Tiết'],
                    ['key' => '_pi_hero_3_btn_link', 'type' => 'link',  'label' => 'Slide 3 — Nút URL'],
                ],
            ],

            // Service Links (3 cards dưới hero)
            'home_services' => [
                'label' => 'Service Links (3 Cards)',
                'icon'  => 'grid',
                'fields' => [
                    ['key' => '_sgh_svc1_title',    'type' => 'text', 'label' => 'Card 1 — Tiêu đề', 'default' => 'Thiết Kế Kiến Trúc'],
                    ['key' => '_sgh_svc1_subtitle', 'type' => 'text', 'label' => 'Card 1 — Phụ đề',  'default' => 'Sáng tạo không gian sống đẳng cấp'],
                    ['key' => '_sgh_svc1_url',      'type' => 'link', 'label' => 'Card 1 — URL',     'default' => '/bao-gia-thiet-ke-kien-truc-xay-dung-cong-trinh-1/'],
                    ['key' => '_sgh_svc2_title',    'type' => 'text', 'label' => 'Card 2 — Tiêu đề', 'default' => 'Xây Dựng Phần Thô'],
                    ['key' => '_sgh_svc2_subtitle', 'type' => 'text', 'label' => 'Card 2 — Phụ đề',  'default' => 'Vững chắc nền móng tương lai'],
                    ['key' => '_sgh_svc2_url',      'type' => 'link', 'label' => 'Card 2 — URL',     'default' => '/bang-bao-gia-thi-cong-xay-dung-nha-phan-tho-1/'],
                    ['key' => '_sgh_svc3_title',    'type' => 'text', 'label' => 'Card 3 — Tiêu đề', 'default' => 'Xây Nhà Trọn Gói'],
                    ['key' => '_sgh_svc3_subtitle', 'type' => 'text', 'label' => 'Card 3 — Phụ đề',  'default' => 'Chìa khóa trao tay'],
                    ['key' => '_sgh_svc3_url',      'type' => 'link', 'label' => 'Card 3 — URL',     'default' => '/bang-bao-gia-xay-dung-nha-tron-goi-1/'],
                ],
            ],

            // Villa Designs
            'home_villa' => [
                'label' => 'Villa Designs (Biệt Thự)',
                'icon'  => 'home',
                'fields' => [
                    ['key' => '_sgh_villa_tag',    'type' => 'text',     'label' => 'Tag',       'default' => 'Kiến Trúc'],
                    ['key' => '_sgh_villa_title',  'type' => 'text',     'label' => 'Tiêu đề',   'default' => 'DI SẢN'],
                    ['key' => '_sgh_villa_accent', 'type' => 'text',     'label' => 'Chữ accent', 'default' => 'XANH'],
                    ['key' => '_sgh_villa_desc',   'type' => 'textarea', 'label' => 'Mô tả',     'default' => 'Nơi thiên nhiên giao hòa trong từng hơi thở, kiến tạo chuẩn mực sống thượng lưu giữa lòng phố thị.'],
                    ['key' => '_sgh_villa_spec1_label', 'type' => 'text', 'label' => 'Spec 1 label', 'default' => 'Diện Tích'],
                    ['key' => '_sgh_villa_spec1_value', 'type' => 'text', 'label' => 'Spec 1 value', 'default' => '300m²'],
                    ['key' => '_sgh_villa_spec2_label', 'type' => 'text', 'label' => 'Spec 2 label', 'default' => 'Phong Cách'],
                    ['key' => '_sgh_villa_spec2_value', 'type' => 'text', 'label' => 'Spec 2 value', 'default' => 'Modern'],
                    ['key' => '_sgh_villa_cta',    'type' => 'text',     'label' => 'Nút CTA mobile', 'default' => 'Xem Tất Cả Biệt Thự'],
                    ['key' => '_sgh_villa_cta_url', 'type' => 'link',    'label' => 'URL CTA',   'default' => '/category/thiet-ke-biet-thu/'],
                ],
            ],

            // Townhouse Designs
            'home_townhouse' => [
                'label' => 'Townhouse Designs (Nhà Phố)',
                'icon'  => 'layout',
                'fields' => [
                    ['key' => '_sgh_town_label',  'type' => 'text',     'label' => 'Label',     'default' => 'Bộ Sưu Tập 2026'],
                    ['key' => '_sgh_town_title',  'type' => 'text',     'label' => 'Tiêu đề',   'default' => 'Kiến Trúc'],
                    ['key' => '_sgh_town_accent', 'type' => 'text',     'label' => 'Chữ accent', 'default' => 'Nhà Phố'],
                    ['key' => '_sgh_town_desc',   'type' => 'textarea', 'label' => 'Mô tả',     'default' => 'Tuyển tập những mẫu thiết kế nhà phố tối ưu công năng, chuẩn phong thủy và mang đậm dấu ấn cá nhân.'],
                    ['key' => '_sgh_town_cta',    'type' => 'text',     'label' => 'Nút CTA mobile', 'default' => 'Xem Tất Cả Nhà Phố'],
                    ['key' => '_sgh_town_cta_url', 'type' => 'link',    'label' => 'URL CTA',   'default' => '/thiet-ke-nha-pho'],
                ],
            ],

            // Work Process (5 Steps)
            'home_process' => [
                'label' => 'Quy Trình Làm Việc (5 Bước)',
                'icon'  => 'list',
                'fields' => [
                    ['key' => '_sgh_proc_badge',   'type' => 'text',     'label' => 'Badge',     'default' => 'QUY TRÌNH CHUYÊN NGHIỆP'],
                    ['key' => '_sgh_proc_title',   'type' => 'text',     'label' => 'Tiêu đề',   'default' => 'QUY TRÌNH LÀM VIỆC'],
                    ['key' => '_sgh_proc_accent',  'type' => 'text',     'label' => 'Chữ accent', 'default' => '5 BƯỚC CƠ BẢN'],
                    ['key' => '_sgh_proc_desc',    'type' => 'textarea', 'label' => 'Mô tả',     'default' => 'Chúng tôi cam kết quy trình làm việc minh bạch, chuyên nghiệp và hiệu quả từ giai đoạn ý tưởng đến khi bàn giao chìa khóa.'],
                    ['key' => '_sgh_proc_s1_title', 'type' => 'text', 'label' => 'Bước 1 — Tiêu đề', 'default' => 'TƯ VẤN & KHẢO SÁT'],
                    ['key' => '_sgh_proc_s1_desc',  'type' => 'text', 'label' => 'Bước 1 — Mô tả',   'default' => 'Tư vấn chuyên sâu, khảo sát hiện trạng miễn phí.'],
                    ['key' => '_sgh_proc_s2_title', 'type' => 'text', 'label' => 'Bước 2 — Tiêu đề', 'default' => 'THIẾT KẾ PHƯƠNG ÁN'],
                    ['key' => '_sgh_proc_s2_desc',  'type' => 'text', 'label' => 'Bước 2 — Mô tả',   'default' => 'Lên ý tưởng 2D/3D, tối ưu công năng sử dụng.'],
                    ['key' => '_sgh_proc_s3_title', 'type' => 'text', 'label' => 'Bước 3 — Tiêu đề', 'default' => 'KÝ KẾT HỢP ĐỒNG'],
                    ['key' => '_sgh_proc_s3_desc',  'type' => 'text', 'label' => 'Bước 3 — Mô tả',   'default' => 'Minh bạch pháp lý, cam kết không phát sinh.'],
                    ['key' => '_sgh_proc_s4_title', 'type' => 'text', 'label' => 'Bước 4 — Tiêu đề', 'default' => 'THI CÔNG & GIÁM SÁT'],
                    ['key' => '_sgh_proc_s4_desc',  'type' => 'text', 'label' => 'Bước 4 — Mô tả',   'default' => 'Đội ngũ lành nghề, kỹ sư giám sát 24/7.'],
                    ['key' => '_sgh_proc_s5_title', 'type' => 'text', 'label' => 'Bước 5 — Tiêu đề', 'default' => 'BÀN GIAO & BẢO HÀNH'],
                    ['key' => '_sgh_proc_s5_desc',  'type' => 'text', 'label' => 'Bước 5 — Mô tả',   'default' => 'Nghiệm thu chi tiết, bảo hành kết cấu 10 năm.'],
                ],
            ],

            // Testimonials / Construction Diary
            'home_diary' => [
                'label' => 'Nhật Ký Thi Công (Video)',
                'icon'  => 'video',
                'fields' => [
                    ['key' => '_sgh_diary_eyebrow', 'type' => 'text',     'label' => 'Eyebrow',    'default' => 'Thước Phim Kiến Tạo'],
                    ['key' => '_sgh_diary_title',   'type' => 'text',     'label' => 'Tiêu đề',    'default' => 'Nhật Ký Thi Công'],
                    ['key' => '_sgh_diary_subtitle', 'type' => 'text',    'label' => 'Phụ đề',     'default' => 'Từ Bản Vẽ Đến Hiện Thực'],
                    ['key' => '_sgh_diary_lead',    'type' => 'textarea', 'label' => 'Lead text',   'default' => 'Đồng hành cùng chúng tôi gọt giũa từng khối bê tông trần. Ghi lại chân thực mọi công đoạn phác họa nên không gian sống của bạn rực rỡ qua mỗi ngày.'],
                    ['key' => '_sgh_diary_cta',     'type' => 'text',     'label' => 'CTA text',    'default' => 'Xem Toàn Bộ Nhật Ký'],
                    ['key' => '_sgh_diary_cta_url',  'type' => 'link',    'label' => 'CTA URL',     'default' => '/category/thiet-ke/'],
                    ['key' => '_sgh_diary_yt_handle', 'type' => 'text',   'label' => 'YouTube handle', 'default' => '@saigonhouse2550'],
                ],
            ],

            // Latest News
            'home_news' => [
                'label' => 'Tin Tức & Sự Kiện',
                'icon'  => 'file-text',
                'fields' => [
                    ['key' => '_sgh_news_label',  'type' => 'text', 'label' => 'Label',    'default' => 'Cẩm Nang Xây Dựng'],
                    ['key' => '_sgh_news_title',  'type' => 'text', 'label' => 'Tiêu đề',  'default' => 'Tin Tức &'],
                    ['key' => '_sgh_news_accent', 'type' => 'text', 'label' => 'Chữ accent', 'default' => 'Sự Kiện'],
                ],
            ],

            // Featured Projects
            'home_projects' => [
                'label' => 'Dự Án Tiêu Biểu',
                'icon'  => 'award',
                'fields' => [
                    ['key' => '_sgh_proj_badge',    'type' => 'text',     'label' => 'Badge',    'default' => 'Năng Lực Thi Công'],
                    ['key' => '_sgh_proj_title',    'type' => 'text',     'label' => 'Tiêu đề',  'default' => 'Dự Án'],
                    ['key' => '_sgh_proj_accent',   'type' => 'text',     'label' => 'Chữ accent', 'default' => 'Tiêu Biểu'],
                    ['key' => '_sgh_proj_subtitle', 'type' => 'textarea', 'label' => 'Phụ đề',   'default' => 'Tinh hoa kiến trúc & dấu ấn cá nhân trong từng công trình'],
                ],
            ],

            // ─── Header Globals (gắn vào trang chủ) ───
            'global_topbar' => [
                'label' => '🌐 Header — Top Bar Marquee',
                'icon'  => 'tv',
                'fields' => [
                    ['key' => '_sgh_topbar_line1', 'type' => 'text', 'label' => 'Dòng chạy 1', 'default' => 'Chào mừng kỷ niệm 15 năm thành lập Saigon Horeca!'],
                    ['key' => '_sgh_topbar_line2', 'type' => 'text', 'label' => 'Dòng chạy 2', 'default' => 'Giảm ngay 50% phí thiết kế khi thi công trọn gói.'],
                    ['key' => '_sgh_topbar_line3', 'type' => 'text', 'label' => 'Dòng chạy 3', 'default' => 'Top 10 Thương Hiệu Uy Tín 2026'],
                    ['key' => '_sgh_topbar_cta',   'type' => 'text', 'label' => 'CTA button text', 'default' => 'LIÊN HỆ NGAY'],
                ],
            ],

            // Footer / Cookie
            'global_footer' => [
                'label' => '🌐 Footer & Cookie Consent',
                'icon'  => 'shield',
                'fields' => [
                    ['key' => '_sgh_cookie_msg',  'type' => 'textarea', 'label' => 'Cookie message', 'default' => 'Chúng tôi sử dụng cookie để cải thiện trải nghiệm của bạn trên website. Bằng cách tiếp tục sử dụng trang web, bạn đồng ý với chính sách cookie của chúng tôi.'],
                    ['key' => '_sgh_footer_copyright', 'type' => 'text', 'label' => 'Copyright text', 'default' => 'All rights reserved.'],
                ],
            ],
        ],
    ];

    return $sections;
}
