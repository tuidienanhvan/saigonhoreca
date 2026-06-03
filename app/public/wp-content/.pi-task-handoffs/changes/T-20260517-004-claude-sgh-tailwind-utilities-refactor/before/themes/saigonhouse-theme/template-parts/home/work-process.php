<?php
/**
 * Template Part: Work Process — 5-step timeline
 * @package SaigonHouse
 */

$fp_id = (int) get_option('page_on_front');

$steps = [
    ['number' => '01', 'title' => get_post_meta($fp_id, '_sgh_proc_s1_title', true) ?: 'TƯ VẤN & KHẢO SÁT', 'desc' => get_post_meta($fp_id, '_sgh_proc_s1_desc', true) ?: 'Tư vấn chuyên sâu, khảo sát hiện trạng miễn phí.', 'icon' => 'file-text'],
    ['number' => '02', 'title' => get_post_meta($fp_id, '_sgh_proc_s2_title', true) ?: 'THIẾT KẾ PHƯƠNG ÁN', 'desc' => get_post_meta($fp_id, '_sgh_proc_s2_desc', true) ?: 'Lên ý tưởng 2D/3D, tối ưu công năng sử dụng.', 'icon' => 'crosshair'],
    ['number' => '03', 'title' => get_post_meta($fp_id, '_sgh_proc_s3_title', true) ?: 'KÝ KẾT HỢP ĐỒNG', 'desc' => get_post_meta($fp_id, '_sgh_proc_s3_desc', true) ?: 'Minh bạch pháp lý, cam kết không phát sinh.', 'icon' => 'edit'],
    ['number' => '04', 'title' => get_post_meta($fp_id, '_sgh_proc_s4_title', true) ?: 'THI CÔNG & GIÁM SÁT', 'desc' => get_post_meta($fp_id, '_sgh_proc_s4_desc', true) ?: 'Đội ngũ lành nghề, kỹ sư giám sát 24/7.', 'icon' => 'hammer'],
    ['number' => '05', 'title' => get_post_meta($fp_id, '_sgh_proc_s5_title', true) ?: 'BÀN GIAO & BẢO HÀNH', 'desc' => get_post_meta($fp_id, '_sgh_proc_s5_desc', true) ?: 'Nghiệm thu chi tiết, bảo hành kết cấu 10 năm.', 'icon' => 'home'],
];
?>
<section class="sh-process sgh-cv-auto">
    <div class="sh-process__bg-grid"></div>
    <div class="sh-process__bg-deco">
        <span class="sh-process__deco sh-process__deco--tl">+</span>
        <span class="sh-process__deco sh-process__deco--br">+</span>
        <span class="sh-process__deco-circle"></span>
        <span class="sh-process__deco-line"></span>
    </div>

    <div class="sh-process__container">
        <!-- Header -->
        <div class="sh-process__header" data-aos="rotate-in">
            <span class="sh-process__badge"><?php echo esc_html(get_post_meta($fp_id, '_sgh_proc_badge', true) ?: 'QUY TRÌNH CHUYÊN NGHIỆP'); ?></span>
            <h2 class="sh-process__title">
                <?php echo esc_html(get_post_meta($fp_id, '_sgh_proc_title', true) ?: 'QUY TRÌNH LÀM VIỆC'); ?> <br/>
                <span class="sh-process__title-accent"><?php echo esc_html(get_post_meta($fp_id, '_sgh_proc_accent', true) ?: '5 BƯỚC CƠ BẢN'); ?></span>
            </h2>
            <div class="sh-process__title-bar"></div>
            <p class="sh-process__desc">
                <?php echo wp_kses_post(get_post_meta($fp_id, '_sgh_proc_desc', true) ?: 'Chúng tôi cam kết quy trình làm việc <strong>minh bạch</strong>, <strong>chuyên nghiệp</strong> và <strong>hiệu quả</strong> từ giai đoạn ý tưởng đến khi bàn giao chìa khóa.'); ?>
            </p>
        </div>

        <!-- Steps -->
        <div class="sh-process__steps">
            <?php foreach ($steps as $index => $step) : ?>
            <div class="sh-process__step" data-aos="swing-in" data-aos-delay="<?php echo $index * 150; ?>">
                <?php if ($index < count($steps) - 1): ?>
                    <div class="sh-process__mobile-line"></div>
                <?php endif; ?>

                <div class="sh-process__icon-circle">
                    <div class="sh-process__number"><?php echo $step['number']; ?></div>
                    <div class="sh-process__icon"><?php echo sh_icon($step['icon'], 'sh-process__icon-svg'); ?></div>
                    <div class="sh-process__connector-line"></div>
                    <div class="sh-process__connector-dot"></div>
                </div>

                <div class="sh-process__card sh-process__card--corners">
                    <h3 class="sh-process__card-title"><?php echo $step['title']; ?></h3>
                    <p class="sh-process__card-desc"><?php echo $step['desc']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
