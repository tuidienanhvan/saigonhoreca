<?php
/**
 * Turnkey Pricing - Pricing Table
 */
$packages = [
    ['name' => 'Tiết Kiệm', 'price' => '4.350.000'],
    ['name' => 'Cơ Bản',    'price' => '5.050.000'],
    ['name' => 'Khá',       'price' => '5.350.000'],
    ['name' => 'Tốt',       'price' => '5.850.000'],
    ['name' => 'Cao Cấp',   'price' => '6.850.000'],
];
$materials = [
    ['group' => 'Sơn Nước - Sơn Dầu', 'rows' => [
        ['Sơn ngoại thất (1 lót 2 phủ)', 'Maxilite Kinh tế', 'Maxilite ICI A919', 'Dulux Inspire', 'Dulux Weathershield', 'Dulux Weathershield'],
        ['Sơn nội thất (2 lớp phủ)', 'Maxilite', 'Maxilite', 'Dulux Inspire', 'Dulux 5 in 1', 'Dulux 5 in 1'],
        ['Bột trét Matit', 'Việt Mỹ', 'Joton', 'Joton', 'Joton', 'Jotun / Dulux'],
    ]],
    ['group' => 'Gạch Ốp Lát', 'rows' => [
        ['Gạch nền nhà (vnđ/m2)', '150.000', '180.000', '220.000', '250.000', '330.000'],
        ['Gạch nền sân, ban công', '85.000', '135.000', '165.000', '195.000', '250.000'],
        ['Gạch nền & ốp WC (Cao tối đa 2m4)', '95.000', '135.000', '160.000', '185.000', '250.000'],
    ]],
    ['group' => 'Cầu Thang & Lan Can', 'rows' => [
        ['Đá bậc thang (Tối đa 7m)', 'Gạch', 'Đá Trắng Suối Lâu', 'Đá Đen Campuchia', 'Đá Kim Sa Trung', 'Đá Xà Cừ Xanh'],
        ['Lan can, tay vịn', 'Sắt', 'Sắt', 'Kính Cường Lực 10ly', 'Kính Cường Lực 10ly', 'Kính Cường Lực 10ly'],
    ]],
    ['group' => 'Cửa Đi & Cửa Sổ', 'rows' => [
        ['Cửa chính tầng trệt (4 cánh)', 'Sắt mạ kẽm (3x3)', 'Sắt mạ kẽm (4x8)', 'Nhựa lõi thép, Kính 8ly', 'Nhựa lõi thép, Kính 8ly', 'Nhôm Xingfa'],
        ['Cửa phòng ngủ (Mỗi phòng 1 bộ)', 'Nhựa giả Gỗ Đài Loan', 'Nhôm Tungshin', 'Nhựa lõi thép', 'Cửa gỗ HDF', 'Gỗ Căm Xe'],
    ]],
    ['group' => 'Thiết Bị Vệ Sinh', 'rows' => [
        ['Bàn cầu (vnđ/bộ)', '1.250.000', '1.950.000', '3.150.000', '4.750.000', '7.750.000'],
        ['Lavabo & phụ kiện (vnđ/bộ)', '650.000', '1.050.000', '1.950.000', '2.250.000', '3.150.000'],
    ]],
];
?>
<section class="sh-tk-table">

    <div class="sh-tk-table__container">
        <!-- Notice -->
        <div class="sh-tk-table__notice">
            <h3 class="sh-tk-table__notice-title">
                <span class="sh-tk-table__notice-icon"><?php echo sh_icon('help-circle', ''); ?></span>
                Lưu ý quan trọng:
            </h3>
            <ul class="sh-tk-table__notice-list">
                <li class="sh-tk-table__notice-item"><?php echo sh_icon('check', 'sh-tk-table__notice-check'); ?> Bảng giá áp dụng cho <strong>Quý II-2021</strong> đến khi có cập nhật mới.</li>
                <li class="sh-tk-table__notice-item"><?php echo sh_icon('check', 'sh-tk-table__notice-check'); ?> Đơn giá thi công thô (Tiêu Chuẩn): <strong>3.500.000 VNĐ/m2</strong> (Nhà phố 1 mặt tiền).</li>
                <li class="sh-tk-table__notice-item"><?php echo sh_icon('check', 'sh-tk-table__notice-check'); ?> Áp dụng cho công trình có tổng diện tích sàn từ <strong>350m2 trở lên</strong>.</li>
                <li class="sh-tk-table__notice-item"><?php echo sh_icon('check', 'sh-tk-table__notice-check'); ?> Hình thức hợp đồng là khoán gọn công trình. Giá chưa bao gồm 10% VAT.</li>
            </ul>
        </div>

        <!-- Header -->
        <div class="sh-tk-table__header">
            <span class="sh-tk-table__tag">Bảng Giá Thi Công</span>
            <h2 class="sh-tk-table__title">Các Gói Vật Tư Hoàn Thiện</h2>
            <p class="sh-tk-table__desc">Lựa chọn 1 trong 5 gói hoàn thiện "Chìa khóa trao tay" phù hợp với ngân sách và nhu cầu thẩm mỹ của gia đình bạn.</p>
        </div>

        <!-- Package Cards -->
        <div class="sh-tk-table__packages">
            <?php foreach ($packages as $i => $pkg):
                $is_popular = ($pkg['name'] === 'Tốt');
            ?>
            <div class="sh-tk-table__pkg<?php echo $is_popular ? ' sh-tk-table__pkg--popular' : ''; ?> sgh-card-hover" data-aos="fade-up" data-aos-delay="<?php echo $i * 100; ?>">
                <?php if ($is_popular): ?><div class="sh-tk-table__pkg-badge">Phổ Biến Nhất</div><?php endif; ?>
                <div class="sh-tk-table__pkg-label">Gói <?php echo $i + 1; ?></div>
                <div class="sh-tk-table__pkg-name"><?php echo $pkg['name']; ?></div>
                <div class="sh-tk-table__pkg-price-wrap">
                    <span class="sh-tk-table__pkg-price"><?php echo $pkg['price']; ?></span>
                    <span class="sh-tk-table__pkg-unit">VNĐ / m2</span>
                </div>
                <a href="#table-materials" class="sh-tk-table__pkg-btn <?php echo $is_popular ? 'sh-tk-table__pkg-btn--primary' : 'sh-tk-table__pkg-btn--default'; ?>">Xem Chi Tiết</a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Comparison Table -->
        <div id="table-materials" class="sh-tk-table__comparison" style="scroll-margin-top:8rem">
            <div class="sh-tk-table__comparison-header">
                <div class="sh-tk-table__comparison-line"></div>
                <h2 class="sh-tk-table__comparison-title">Tra Cứu Vật Tư Tương Ứng</h2>
                <div class="sh-tk-table__comparison-line"></div>
            </div>

            <div class="sh-tk-table__table-wrap">
                <div class="sh-tk-table__scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Phân Loại<br>Hạng Mục</th>
                                <?php foreach ($packages as $pkg): ?>
                                <th><div><?php echo $pkg['name']; ?></div></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $group): ?>
                            <tr class="sh-tk-table__group">
                                <td colspan="6">
                                    <div class="sh-tk-table__group-inner">
                                        <?php echo sh_icon('grid', 'sh-tk-table__group-icon'); ?>
                                        <?php echo $group['group']; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php foreach ($group['rows'] as $row): ?>
                            <tr>
                                <td><?php echo $row[0]; ?></td>
                                <?php for ($c = 1; $c <= 5; $c++):
                                    $is_highlight = ($c >= 4);
                                ?>
                                <td<?php echo $is_highlight ? ' class="sh-tk-table__highlight"' : ''; ?>><?php echo $row[$c]; ?></td>
                                <?php endfor; ?>
                            </tr>
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="sh-tk-table__footer">
                    <p class="sh-tk-table__footer-text">
                        <?php echo sh_icon('help-circle', ''); ?>
                        (Bảng trên trích xuất một số hạng mục chính để tham khảo. Bộ hồ sơ báo giá dự toán sẽ liệt kê đầy đủ 100% các hạng mục vật tư)
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
