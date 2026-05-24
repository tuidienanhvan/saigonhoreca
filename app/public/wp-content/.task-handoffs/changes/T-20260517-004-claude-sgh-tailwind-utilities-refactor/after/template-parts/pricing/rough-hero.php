<?php
/**
 * Template Part: Báo Giá Phần Thô - Hero Section
 */
?>
<section class="sh-rough-hero" data-aos="fade-in">

    <div class="sh-rough-hero__decor1"></div>
    <div class="sh-rough-hero__decor2"></div>
    <div class="sh-rough-hero__decor3"></div>

    <div class="sh-rough-hero__container">
        <div class="sh-rough-hero__grid">
            <div class="sh-rough-hero__content" data-aos="fade-right" data-aos-delay="100">
                <span class="sh-rough-hero__badge">
                    <span class="sh-rough-hero__badge-dot"></span> Báo Giá 2025
                </span>

                <h1 class="sh-rough-hero__title">
                    Thi Công Xây Dựng<br>
                    <span class="sh-rough-hero__title-accent">
                        Nhà Phần Thô
                        <span class="sh-rough-hero__title-underline"></span>
                    </span>
                </h1>

                <p class="sh-rough-hero__desc">
                    Giữa biến động vật giá, Saigon House cam kết giữ nguyên bản báo giá minh bạch, bám sát thị trường. Đơn giá đã bao gồm các hạng mục kết cấu cốt lõi và nhân công hoàn thiện trọn vẹn.
                </p>

                <div class="sh-rough-hero__actions">
                    <a href="#bang-gia" class="sh-rough-hero__btn sh-rough-hero__btn--primary">
                        Xem Bảng Giá
                        <?php echo sh_icon('arrow-right', 'sh-rough-hero__btn-icon'); ?>
                    </a>
                    <a href="tel:<?php echo esc_attr(saigonhouse_contact('hotline_raw')); ?>" class="sh-rough-hero__btn sh-rough-hero__btn--secondary">
                        <span class="sh-rough-hero__phone-icon"><?php echo sh_icon('phone', ''); ?></span>
                        Tư Vấn Ngay
                    </a>
                </div>
            </div>

            <div class="sh-rough-hero__image-side" data-aos="zoom-in" data-aos-delay="180">
                <div class="sh-rough-hero__img-wrap">
                    <div class="sh-rough-hero__img-overlay"></div>
                    <img src="<?php echo esc_url(sh_section_image('thi cong', '', 2)); ?>" alt="Thi công phần thô" class="sh-rough-hero__img" loading="eager" fetchpriority="high" decoding="async">
                </div>

                <div class="sh-rough-hero__float">
                    <div class="sh-rough-hero__float-icon"><?php echo sh_icon('gift', ''); ?></div>
                    <div class="sh-rough-hero__float-tag">Đặc quyền 2025</div>
                    <h4 class="sh-rough-hero__float-title">Miễn Phí<br>Hồ Sơ Thiết Kế</h4>
                    <p class="sh-rough-hero__float-desc">Áp dụng cho HĐ > 350m&sup2;</p>
                </div>
            </div>
        </div>
    </div>
</section>
