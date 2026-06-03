<?php
/**
 * Turnkey Pricing - Area Calculation Rules
 */
$areas = [
    ['section' => 'Khối Đế Móng', 'num' => '01', 'items' => [
        ['label' => 'Móng cọc, Móng đơn', 'pct' => '30%'],
        ['label' => 'Móng băng, Móng bè', 'pct' => '50%'],
    ]],
    ['section' => 'Thân Nhà & Tầng Hầm', 'num' => '02', 'items' => [
        ['label' => 'Trệt & Các tầng (Lầu)', 'pct' => '100%', 'accent' => true],
        ['label' => 'Hầm sâu < 1.2m', 'pct' => '150%'],
        ['label' => 'Hầm sâu 1.2m - 1.7m', 'pct' => '170%'],
        ['label' => 'Hầm sâu > 1.7m', 'pct' => '200%'],
    ]],
    ['section' => 'Mái & Ngoại Vi', 'num' => '03', 'items' => [
        ['label' => 'Sân Thượng, Ban Công', 'pct' => '50%'],
        ['label' => 'Mái Tôn, Pergola', 'pct' => '30%'],
        ['label' => 'Mái Bê tông / Mái Ngói Thép', 'pct' => '50%'],
        ['label' => 'Mái BTCT Dán Ngói', 'pct' => '80%'],
    ]],
];
$extras = [
    'Ô thông tầng < 8m2 tính 100% diện tích. Ô thông tầng > 8m2 tính 50% diện tích.',
    'Sân vườn, hàng rào, cổng tính 50% diện tích (áp dụng cho sân vườn > 50m2).',
    '100% Đơn giá nền trệt (gia cố thêm sàn BTCT tính riêng 10%).',
];
?>
<section class="sh-area-rules" id="he-so-dien-tich" data-aos="fade-up">

    <div class="sh-area-rules__decor1"></div>
    <div class="sh-area-rules__decor2"></div>
    <div class="sh-area-rules__decor3"></div>

    <div class="sh-area-rules__container">
        <div class="sh-area-rules__header" data-aos="fade-up" data-aos-delay="80">
            <h2 class="sh-area-rules__title">Hệ Số Tính Diện Tích</h2>
            <p class="sh-area-rules__desc">Cách SaigonHouse tính toán diện tích tổng sàn xây dựng để đưa ra dự toán chính xác nhất cho ngôi nhà của bạn.</p>
        </div>

        <div class="sh-area-rules__grid">
            <div class="sh-area-rules__surcharge" data-aos="zoom-in" data-aos-delay="120">
                <div>
                    <h3 class="sh-area-rules__surcharge-title">
                        <?php echo sh_icon('help-circle', ''); ?>
                        Lưu ý Phụ Phí Diện Tích (Khoán Gọn)
                    </h3>
                    <p class="sh-area-rules__surcharge-desc">Đơn giá chuẩn áp dụng cho Tổng DTXD > 350m2. Các diện tích nhỏ hơn sẽ có phụ phí nhân công & vận chuyển.</p>
                </div>
                <div class="sh-area-rules__surcharge-boxes">
                    <div class="sh-area-rules__surcharge-box">
                        <div class="sh-area-rules__surcharge-label sh-area-rules__surcharge-label--yellow">250m2 - 300m2</div>
                        <div class="sh-area-rules__surcharge-value">+ 50.000<span class="sh-area-rules__surcharge-unit"> đ/m2</span></div>
                    </div>
                    <div class="sh-area-rules__surcharge-box">
                        <div class="sh-area-rules__surcharge-label sh-area-rules__surcharge-label--orange">150m2 - 250m2</div>
                        <div class="sh-area-rules__surcharge-value">+ 100.000<span class="sh-area-rules__surcharge-unit"> đ/m2</span></div>
                    </div>
                </div>
            </div>

            <?php foreach ($areas as $idx => $area): ?>
            <div class="sh-area-rules__card" data-aos="zoom-in-up" data-aos-delay="<?php echo esc_attr(min(360, 140 + ($idx * 70))); ?>">
                <h3 class="sh-area-rules__card-name">
                    <span class="sh-area-rules__card-num"><?php echo $area['num']; ?></span>
                    <?php echo $area['section']; ?>
                </h3>
                <ul class="sh-area-rules__card-list">
                    <?php foreach ($area['items'] as $item): ?>
                    <li class="sh-area-rules__card-item">
                        <span class="sh-area-rules__card-label"><?php echo $item['label']; ?></span>
                        <span class="sh-area-rules__card-pct<?php echo !empty($item['accent']) ? ' sh-area-rules__card-pct--accent' : ''; ?>"><?php echo $item['pct']; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>

            <div class="sh-area-rules__extras" data-aos="fade-up" data-aos-delay="220">
                <h3 class="sh-area-rules__extras-title">Các Hạng Mục Khác:</h3>
                <ul class="sh-area-rules__extras-list">
                    <?php foreach ($extras as $ex): ?>
                    <li class="sh-area-rules__extras-item">
                        <?php echo sh_icon('check', 'sh-area-rules__extras-icon'); ?>
                        <?php echo $ex; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
