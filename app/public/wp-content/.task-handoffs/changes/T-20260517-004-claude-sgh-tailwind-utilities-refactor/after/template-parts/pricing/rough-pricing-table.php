<?php
/**
 * Template Part: Báo Giá Phần Thô - Pricing Table
 * Styling: template-parts/pricing/rough-pricing-table.css
 */
$materials = [
    ["name" => "Loại nhà",  "icon" => "home",        "g1" => "Nhà trọ - Nhà cấp 4",     "g2" => "Nhà phố 1 mặt tiền",       "g3" => "Tòa Nhà Văn phòng"],
    ["name" => "Đá",        "icon" => "box",         "g1" => "Đá 1x2 Bình Điền",         "g2" => "Đá 1x2 Đồng Nai",           "g3" => "Đá 1x2 Sàng Chọn"],
    ["name" => "Cát",       "icon" => "layers",      "g1" => "Cát Xây Đồng Nai",         "g2" => "Cát Vàng Lọc Sạch",         "g3" => "Cát Vàng Siêu Sạch"],
    ["name" => "Bê tông",   "icon" => "grid",        "g1" => "Bê tông M250",             "g2" => "Bê tông M250 Thương Phẩm",  "g3" => "Bê tông M300 Thương Phẩm"],
    ["name" => "Xi măng",   "icon" => "box",         "g1" => "Xi măng Fico",             "g2" => "Xi măng Hà Tiên 1",         "g3" => "Xi măng Holcim"],
    ["name" => "Thép",      "icon" => "trending-up", "g1" => "Thép Việt Nhật",           "g2" => "Thép Việt Nhật C8300",      "g3" => "Thép Hoà Phát/Pomina"],
    ["name" => "Gạch",      "icon" => "grid",        "g1" => "Gạch Tuynel 2 lỗ",         "g2" => "Gạch Tuynel 4 lỗ",          "g3" => "Gạch Đặc Cao Cấp"],
    ["name" => "Ống Nước",  "icon" => "help-circle", "g1" => "Ống Bình Minh",            "g2" => "Ống Bình Minh (Dày)",       "g3" => "Ống PPR Nóng Lạnh"],
    ["name" => "Dây Điện",  "icon" => "zap",         "g1" => "Dây Cadivi",               "g2" => "Dây Cadivi Loại 1",         "g3" => "Dây Cadivi Đặc Biệt"],
    ["name" => "Sơn",       "icon" => "help-circle", "g1" => "Sơn Jotun",                "g2" => "Sơn Maxilite",              "g3" => "Sơn Dulux"],
    ["name" => "Mái Lợp",   "icon" => "maximize",    "g1" => "Mái Tole",                 "g2" => "Mái Ngói Thái",             "g3" => "Mái Ngói Tráng Men"],
    ["name" => "Gạch Lát",  "icon" => "grid",        "g1" => "Gạch Lát 60x60 Ceramic",  "g2" => "Gạch Lát 80x80 Bóng Kiếng","g3" => "Gạch Lát 80x80 Granite"],
];
?>

<section id="bang-gia" class="sh-rough-table">
    <div class="sh-rough-table__container">
        <div class="sh-rough-table__header">
            <div class="sh-rough-table__badge">
                <span class="sh-rough-table__badge-dot"></span>
                <span class="sh-rough-table__badge-text">Báo Giá Chi Tiết 2025</span>
            </div>
            <h2 class="sh-rough-table__title">Cấu Thành Vật Tư <span class="sh-rough-table__title-accent">3 Gói</span> Phần Thô</h2>
            <p class="sh-rough-table__desc">Bảng phân tích minh bạch từng chủng loại vật tư tương ứng với từng gói. Cam kết đúng hàng — đúng chất lượng.</p>
        </div>

        <div id="sh-pricing-table" class="sh-rough-table__wrap" data-aos="fade-up">
            <div class="sh-rough-table__scroll">
                <table class="sh-rough-table__inner">
                    <thead>
                        <tr>
                            <th id="sh-th-col1" class="sh-rough-table__th sh-rough-table__th--col1">
                                <span class="sh-rough-table__th-label">Vật Tư</span>
                            </th>
                            <th id="sh-th-col2" class="sh-rough-table__th sh-rough-table__th--col2">
                                <span class="sh-rough-table__th-tier">Gói 1 - Tiết Kiệm</span>
                                <span class="sh-rough-table__th-price">2.9 <span class="sh-rough-table__th-unit">Tr/m&sup2;</span></span>
                                <span class="sh-rough-table__th-tagline">Chi phí tối ưu</span>
                            </th>
                            <th id="sh-th-col3" class="sh-rough-table__th sh-rough-table__th--col3">
                                <span class="sh-badge-phobien sh-rough-table__th-badge sh-rough-table__th-badge--popular">Phổ Biến</span>
                                <span class="sh-rough-table__th-tier">Gói 2 - Khá</span>
                                <span class="sh-rough-table__th-price">3.1 <span class="sh-rough-table__th-unit">Tr/m&sup2;</span></span>
                            </th>
                            <th id="sh-th-col4" class="sh-rough-table__th sh-rough-table__th--col4">
                                <span class="sh-rough-table__th-badge sh-rough-table__th-badge--recommended">Khuyên Dùng</span>
                                <span class="sh-rough-table__th-tier">Gói 3 - Cao Cấp</span>
                                <span class="sh-rough-table__th-price">3.3 <span class="sh-rough-table__th-unit">Tr/m&sup2;</span></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($materials as $mat): ?>
                        <tr>
                            <td class="sh-rough-table__td sh-rough-table__td--material">
                                <div class="sh-rough-table__mat-cell">
                                    <span class="sh-rough-table__mat-icon"><?php echo sh_icon($mat['icon'], ''); ?></span>
                                    <span class="sh-rough-table__mat-name"><?php echo $mat['name']; ?></span>
                                </div>
                            </td>
                            <td class="sh-rough-table__td sh-rough-table__td--tier"><span class="sh-rough-table__val"><?php echo $mat['g1']; ?></span></td>
                            <td class="sh-rough-table__td sh-rough-table__td--tier"><span class="sh-rough-table__val"><?php echo $mat['g2']; ?></span></td>
                            <td class="sh-rough-table__td sh-rough-table__td--tier"><span class="sh-rough-table__val"><?php echo $mat['g3']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="sh-rough-table__footer">
                <div class="sh-rough-table__footer-info">
                    <div class="sh-rough-table__footer-icon"><?php echo sh_icon('help-circle', ''); ?></div>
                    <div>
                        <h4 class="sh-rough-table__footer-title">Bảng Giá Tham Khảo 2025</h4>
                        <p class="sh-rough-table__footer-desc">Đơn giá thực tế có thể thay đổi tùy thuộc vào quy mô, vị trí hẻm nhà và các điều kiện thi công cụ thể.</p>
                    </div>
                </div>
                <a href="tel:<?php echo esc_attr(saigonhouse_contact('hotline_raw')); ?>" class="sh-rough-table__footer-cta">
                    Nhận Báo Giá & Tư Vấn
                    <span class="sh-rough-table__footer-cta-icon"><?php echo sh_icon('arrow-right', ''); ?></span>
                </a>
            </div>
        </div>
    </div>
</section>
