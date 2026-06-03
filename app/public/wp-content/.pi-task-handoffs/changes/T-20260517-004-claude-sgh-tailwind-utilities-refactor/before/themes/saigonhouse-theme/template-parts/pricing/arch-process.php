<?php
/**
 * Template Part: Architecture / Construction Process Timeline
 * Redesigned: Animated vertical alternating timeline, dark mode, 6 steps.
 *
 * @package SaigonHouse
 */
$contact = saigonhouse_get_contact_info();

$steps = [
    [
        'num'   => '01',
        'title' => 'Tiếp Nhận & Tư Vấn',
        'desc'  => 'Gặp gỡ, lắng nghe yêu cầu, khảo sát hiện trạng đất và tư vấn phong thủy. Hoàn toàn miễn phí.',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
        'tag'   => 'Miễn phí',
    ],
    [
        'num'   => '02',
        'title' => 'Ký Hợp Đồng & Bản Vẽ',
        'desc'  => 'Chốt phương án mặt bằng, phối cảnh 3D ngoại thất. Ký hợp đồng minh bạch, rõ điều khoản bảo hành.',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>',
        'tag'   => 'Hợp đồng',
    ],
    [
        'num'   => '03',
        'title' => 'Phê Duyệt & Hồ Sơ Kỹ Thuật',
        'desc'  => 'Hoàn thiện bản vẽ Kiến trúc, Kết cấu, Điện nước (MEP). Bàn giao 02 bộ hồ sơ in & hỗ trợ xin phép xây dựng.',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
        'tag'   => 'Thiết kế',
    ],
    [
        'num'   => '04',
        'title' => 'Thi Công Xây Dựng',
        'desc'  => 'Đội ngũ thi công chuyên nghiệp, giám sát kỹ lưỡng từng hạng mục: móng, dầm sàn, hoàn thiện. Báo cáo tiến độ hàng tuần.',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>',
        'tag'   => 'Thi công',
    ],
    [
        'num'   => '05',
        'title' => 'Nghiệm Thu & Bàn Giao',
        'desc'  => 'Kiểm tra toàn bộ hạng mục, hoàn thiện nội ngoại thất. Bàn giao chìa khóa và hồ sơ công trình.',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'tag'   => 'Bàn giao',
    ],
    [
        'num'   => '06',
        'title' => 'Bảo Hành & Hậu Mãi',
        'desc'  => 'Bảo hành kết cấu 5 năm, thấm dột 2 năm. Hỗ trợ bảo trì trọn đời — chúng tôi đồng hành cùng ngôi nhà của bạn.',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        'tag'   => 'Bảo hành',
    ],
];
?>
<section class="sh-process-section" id="quy-trinh">
    <div class="sh-process-container">

        <!-- Header -->
        <div class="sh-process-header">
            <span class="sh-process-eyebrow">Quy Trình Chuẩn 6 Bước</span>
            <h2 class="sh-process-title">Từ Bản Vẽ <span>Đến Hiện Thực</span></h2>
            <p class="sh-process-lead">Minh bạch hóa từng bước thiết kế & thi công — để bạn luôn nắm rõ tiến độ và yên tâm về chất lượng ngôi nhà tương lai.</p>
        </div>

        <!-- Timeline -->
        <div class="sh-process-timeline" id="sh-process-timeline">
            <!-- Animated progress line -->
            <div class="sh-process-line" aria-hidden="true">
                <div class="sh-process-line-fill" id="sh-process-line-fill"></div>
            </div>

            <?php foreach ($steps as $i => $step): ?>
            <div class="sh-process-item <?php echo ($i % 2 === 0) ? 'sh-process-item--left' : 'sh-process-item--right'; ?>"
                 data-step="<?php echo $i; ?>" data-aos="swing-in" data-aos-delay="<?php echo $i * 100; ?>">

                <!-- Dot -->
                <div class="sh-process-dot" aria-hidden="true">
                    <span class="sh-process-dot-num"><?php echo esc_html($step['num']); ?></span>
                </div>

                <!-- Card -->
                <div class="sh-process-card">
                    <div class="sh-process-card-icon" aria-hidden="true">
                        <?php echo $step['icon']; ?>
                    </div>
                    <div class="sh-process-card-body">
                        <span class="sh-process-tag"><?php echo esc_html($step['tag']); ?></span>
                        <h3 class="sh-process-card-title"><?php echo esc_html($step['title']); ?></h3>
                        <p class="sh-process-card-desc"><?php echo esc_html($step['desc']); ?></p>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA -->
        <div class="sh-process-cta">
            <p class="sh-process-cta-text">Sẵn sàng xây dựng ngôi nhà mơ ước?</p>
            <a href="tel:<?php echo esc_attr($contact['hotline_raw']); ?>" class="sh-process-cta-btn">
                <svg style="width:1.25rem;height:1.25rem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.11 9.5 19.79 19.79 0 0 1 1 .88a2 2 0 0 1 2-2.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Tư Vấn Miễn Phí: <?php echo esc_html($contact['hotline']); ?>
            </a>
        </div>
    </div>

    <script>
    (function() {
        // Scroll-triggered step reveal
        if (!('IntersectionObserver' in window)) return;
        var items = document.querySelectorAll('.sh-process-item');
        var lineFill = document.getElementById('sh-process-line-fill');
        var timeline = document.getElementById('sh-process-timeline');
        var total = items.length;
        var revealed = 0;

        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealed++;
                    // Animate the line fill
                    if (lineFill && total > 0) {
                        lineFill.style.height = Math.min((revealed / total) * 100, 100) + '%';
                    }
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.25 });

        items.forEach(function(el) { obs.observe(el); });
    })();
    </script>
</section>
