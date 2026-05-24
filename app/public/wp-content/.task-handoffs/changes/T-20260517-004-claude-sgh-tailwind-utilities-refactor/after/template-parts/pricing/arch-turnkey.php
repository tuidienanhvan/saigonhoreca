<?php
$contact = saigonhouse_get_contact_info();
?>
<section class="sh-offers" data-aos="fade-up">

    <div class="sh-offers__glow sh-offers__glow--right"></div>
    <div class="sh-offers__glow sh-offers__glow--left"></div>

    <div class="sh-offers__container">
        <h2 class="sh-offers__title" data-aos="fade-up" data-aos-delay="80">Ưu Đãi Đặc Biệt <span class="sh-offers__title-accent">2025</span></h2>

        <div class="sh-offers__grid">
            <div class="sh-offers__card" data-aos="zoom-in-up" data-aos-delay="140">
                <div class="sh-offers__pct sh-offers__pct--secondary">50%</div>
                <h3 class="sh-offers__card-title">Miễn Phí Thiết Kế</h3>
                <p class="sh-offers__card-desc">Khi ký hợp đồng thi công <strong>Phần Thô</strong></p>
                <div class="sh-offers__tag">Điều kiện áp dụng</div>
            </div>

            <div class="sh-offers__card sh-offers__card--highlight" data-aos="zoom-in-up" data-aos-delay="220">
                <div class="sh-offers__pct sh-offers__pct--green">100%</div>
                <h3 class="sh-offers__card-title">Miễn Phí Thiết Kế</h3>
                <p class="sh-offers__card-desc">Khi ký hợp đồng thi công <strong>Trọn Gói</strong></p>
                <a href="#tu-van" class="sh-offers__cta-btn">Đăng Ký Ngay</a>
            </div>
        </div>

        <p class="sh-offers__note">
            * Chi phí thiết kế sẽ được khấu trừ trực tiếp vào hợp đồng thi công. Chương trình áp dụng cho các công trình ký hợp đồng trong tháng này. Liên hệ Hotline <strong><?php echo esc_html($contact['hotline']); ?></strong> để biết thêm chi tiết.
        </p>
    </div>
</section>
