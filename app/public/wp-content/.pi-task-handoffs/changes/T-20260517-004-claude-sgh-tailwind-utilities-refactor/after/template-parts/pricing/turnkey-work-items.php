<?php
/**
 * Turnkey Pricing - Work Items (Material Standards & Exclusions)
 */
$materials = [
    ['name' => 'Đá 1x2', 'brand' => 'Đá Đồng Nai, Đá Bình Điền'],
    ['name' => 'Cát Xây Dựng', 'brand' => 'Hạt to đổ Bê Tông, Cát Mịn tô trát'],
    ['name' => 'Bê Tông', 'brand' => 'BT tươi MêKong, SMC Mac 250, M200 (theo thiết kế)'],
    ['name' => 'Xi Măng', 'brand' => 'Holcim, Hà Tiên, Sao Mai'],
    ['name' => 'Thép', 'brand' => 'Việt Nhật (Vina Kyoei), Việt Ý (Pomina)'],
    ['name' => 'Gạch Xây', 'brand' => 'Tuynel Bình Dương, Tám Quỳnh, Thành Tâm'],
    ['name' => 'Thiết Bị Điện & Mạng', 'brand' => 'Dây điện Cadivi (1.5, 2.5, 4.0, 8.0). Cáp Sino, Mpe'],
    ['name' => 'Hệ Thống Ống Nước', 'brand' => 'Bình Minh (Trục D90, Ngang D114, Cấp D27, Thoát D42)'],
];
$exclusions = [
    'Máy nước nóng trực tiếp, hệ thống năng lượng mặt trời.',
    'Vật liệu hoàn thiện các vách trang trí (ngoài sơn nước), sơn giả đá, sơn giả gỗ.',
    'Cửa cuốn, cửa kéo tự động.',
    'Đèn chùm trang trí phòng khách, đèn trụ cổng, đèn rọi tranh nghệ thuật.',
    'Hệ Tủ âm tường, tủ kệ, giường, quầy bar, thiết bị rời.',
    'Các thiết bị gia dụng (Máy lạnh, bếp gas, máy hút mùi).',
    'Sân vườn, tiểu cảnh, thác nước.',
    'Các phụ kiện WC nâng cao (Bồn tắm nằm, bồn tắm đứng kính cường lực).',
];
?>
<section class="sh-materials" data-aos="fade-up">

    <div class="sh-materials__container">
        <div class="sh-materials__grid">
            <div data-aos="fade-right" data-aos-delay="100">
                <div class="sh-materials__section-header">
                    <div class="sh-materials__section-icon sh-materials__section-icon--green"><?php echo sh_icon('home', ''); ?></div>
                    <div>
                        <h2 class="sh-materials__section-title">Vật Tư Thô Tiêu Chuẩn</h2>
                        <p class="sh-materials__section-desc">Sử dụng chung cho tất cả các gói thi công trọn gói</p>
                    </div>
                </div>
                <div class="sh-materials__items-grid">
                    <?php foreach ($materials as $index => $m): ?>
                    <div class="sh-materials__card" data-aos="zoom-in" data-aos-delay="<?php echo 100 + ($index * 50); ?>">
                        <div class="sh-materials__card-img-wrap">
                            <img src="<?php echo esc_url(sh_section_image($m['name'], '', $index)); ?>" 
                                 alt="<?php echo esc_attr($m['name']); ?>" 
                                 class="sh-materials__card-img"
                                 loading="lazy">
                            <div class="sh-materials__card-overlay"></div>
                        </div>
                        <div class="sh-materials__card-content">
                            <h3 class="sh-materials__card-name"><?php echo esc_html($m['name']); ?></h3>
                            <p class="sh-materials__card-brand"><?php echo esc_html($m['brand']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div data-aos="fade-left" data-aos-delay="180">
                <div class="sh-materials__section-header">
                    <div class="sh-materials__section-icon sh-materials__section-icon--red">
                        <?php echo sh_icon('help-circle', ''); ?>
                    </div>
                    <div>
                        <h2 class="sh-materials__section-title">Không Bao Gồm Quá Trình</h2>
                        <p class="sh-materials__section-desc">Chủ đầu tư sẽ tự trang bị hoặc SaigonHouse sẽ tính phí riêng</p>
                    </div>
                </div>
                <div class="sh-materials__excl-card">
                    <div class="sh-materials__excl-glow"></div>
                    <ul class="sh-materials__excl-list">
                        <?php foreach ($exclusions as $ex): ?>
                        <li class="sh-materials__excl-item">
                            <?php echo sh_icon('x', 'sh-materials__excl-icon'); ?>
                            <span><?php echo esc_html($ex); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
