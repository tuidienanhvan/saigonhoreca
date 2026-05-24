<?php
/**
 * Template Part: About Core Values
 */
$_pid = get_queried_object_id();
$_v_heading = get_post_meta($_pid, '_sgh_values_heading', true) ?: 'Lý Do Chọn Saigon House';
$_v = [];
for ($i = 1; $i <= 3; $i++) {
    $_v[$i] = [
        'title' => get_post_meta($_pid, "_sgh_val{$i}_title", true),
        'desc'  => get_post_meta($_pid, "_sgh_val{$i}_desc", true),
    ];
}
$_v_defaults = [
    1 => ['title' => 'Tận Tâm', 'desc' => '"Chúng tôi coi ngôi nhà của bạn như chính ngôi nhà của mình. Tỉ mỉ trong từng chi tiết."'],
    2 => ['title' => 'Chất Lượng', 'desc' => '"Cam kết không dùng vật tư giả. Quy trình ISO giám sát chặt chẽ bởi kỹ sư 10 năm kinh nghiệm."'],
    3 => ['title' => 'Đúng Tiến Độ', 'desc' => '"Cam kết chuẩn tiến độ 100%. Phạt hợp đồng nếu chậm trễ. Minh bạch trong từng giai đoạn."'],
];
for ($i = 1; $i <= 3; $i++) {
    $_v[$i]['title'] = $_v[$i]['title'] ?: $_v_defaults[$i]['title'];
    $_v[$i]['desc']  = $_v[$i]['desc'] ?: $_v_defaults[$i]['desc'];
}
?>
<section class="sh-values">

    <div class="sh-values__grid-bg"></div>
    <div class="sh-values__grid-bg sh-values__grid-bg--large"></div>

    <div class="sh-values__container">
        <div class="sh-values__header">
            <h2 class="sh-values__heading">
                <?php echo esc_html($_v_heading); ?>
            </h2>
            <div class="sh-values__divider"></div>
        </div>

        <div class="sh-values__cards">
            <div class="sh-values__card sgh-card-hover" data-aos="elastic-zoom" style="--value-accent: var(--brand, #007d3d);">
                <div class="sh-values__corner sh-values__corner--tl"></div>
                <div class="sh-values__corner sh-values__corner--br"></div>
                <div class="sh-values__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                </div>
                <h3 class="sh-values__title"><?php echo esc_html($_v[1]['title']); ?></h3>
                <p class="sh-values__desc"><?php echo esc_html($_v[1]['desc']); ?></p>
            </div>

            <div class="sh-values__card sgh-card-hover" data-aos="elastic-zoom" data-aos-delay="100" style="--value-accent: var(--secondary, #ffb703);">
                <div class="sh-values__corner sh-values__corner--tl"></div>
                <div class="sh-values__corner sh-values__corner--br"></div>
                <div class="sh-values__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 3h12l4 6-10 13L2 9Z"/><path d="M11 3 8 9l4 13 4-13-3-6"/><path d="M2 9h20"/></svg>
                </div>
                <h3 class="sh-values__title"><?php echo esc_html($_v[2]['title']); ?></h3>
                <p class="sh-values__desc"><?php echo esc_html($_v[2]['desc']); ?></p>
            </div>

            <div class="sh-values__card sgh-card-hover" data-aos="zoom-in" data-aos-delay="200" style="--value-accent: var(--info, #2563eb);">
                <div class="sh-values__corner sh-values__corner--tl"></div>
                <div class="sh-values__corner sh-values__corner--br"></div>
                <div class="sh-values__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="sh-values__title"><?php echo esc_html($_v[3]['title']); ?></h3>
                <p class="sh-values__desc"><?php echo esc_html($_v[3]['desc']); ?></p>
            </div>
        </div>
    </div>
</section>
