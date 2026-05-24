<?php $contact = saigonhouse_get_contact_info(); ?>
<section id="bang-gia-chi-tiet" class="sh-packages">

    <div class="sh-packages__container">
        <div class="sh-packages__header">
            <span class="sh-packages__tag">Lựa Chọn Gói Dịch Vụ</span>
            <h2 class="sh-packages__title">Bảng Giá Thiết Kế Kiến Trúc</h2>
            <p class="sh-packages__desc">Chúng tôi cung cấp các gói dịch vụ linh hoạt phù hợp với nhu cầu và ngân sách của từng gia chủ.</p>
        </div>

        <div class="sh-packages__grid">
            <div class="sh-packages__card sgh-card-hover" data-aos="bounce-up">
                <h3 class="sh-packages__name sh-packages__name--muted">Gói Cơ Bản</h3>
                <div class="sh-packages__sub">Xin phép xây dựng & Bố trí</div>
                <div class="sh-packages__price sh-packages__price--md">120.000<span class="sh-packages__price-unit">/m2</span></div>
                <ul class="sh-packages__list">
                    <li><?php echo sh_icon('check', ''); ?><span>Bản vẽ xin phép xây dựng</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Mặt bằng bố trí vật dụng (2D)</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Phối cảnh 3D mặt tiền</span></li>
                    <li style="color:var(--text-1-muted)"><?php echo sh_icon('x', ''); ?><span class="sh-packages__excluded">Hồ sơ kết cấu & Điện nước</span></li>
                </ul>
                <a href="#tu-van" class="sh-packages__btn sh-packages__btn--outline">Đăng Ký Tư Vấn</a>
            </div>

            <div class="sh-packages__card sh-packages__card--featured sgh-card-hover" data-aos="bounce-up" data-aos-delay="100">
                <div class="sh-packages__card-badge">KHUYÊN DÙNG</div>
                <h3 class="sh-packages__name sh-packages__name--primary">Gói Tiêu Chuẩn</h3>
                <div class="sh-packages__sub">Đầy đủ hồ sơ thi công</div>
                <div class="sh-packages__price sh-packages__price--lg">170.000<span class="sh-packages__price-unit">/m2</span></div>
                <ul class="sh-packages__list sh-packages__list--accent">
                    <li><?php echo sh_icon('check', ''); ?><span>Tất cả quyền lợi Gói Cơ Bản</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Hồ sơ thiết kế Kiến trúc chi tiết</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Hồ sơ Kết cấu & Điện nước (MEP)</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Giám sát tác giả: 03 lần</span></li>
                </ul>
                <a href="tel:<?php echo esc_attr($contact['hotline_raw']); ?>" class="sh-packages__btn sh-packages__btn--primary">Chọn Gói Này</a>
            </div>

            <div class="sh-packages__card sgh-card-hover" data-aos="bounce-up" data-aos-delay="200">
                <h3 class="sh-packages__name sh-packages__name--secondary">Gói Cao Cấp</h3>
                <div class="sh-packages__sub">Trọn gói A-Z bao gồm Nội thất</div>
                <div class="sh-packages__price sh-packages__price--md">250.000<span class="sh-packages__price-unit">/m2</span></div>
                <ul class="sh-packages__list">
                    <li><?php echo sh_icon('check', ''); ?><span>Tất cả quyền lợi Gói Tiêu Chuẩn</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Thiết kế 3D Nội thất chi tiết</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Dự toán chi phí thi công</span></li>
                    <li><?php echo sh_icon('check', ''); ?><span>Giám sát tác giả: 05 lần</span></li>
                </ul>
                <a href="#tu-van" class="sh-packages__btn sh-packages__btn--secondary">Liên Hệ Báo Giá</a>
            </div>
        </div>
    </div>
</section>
