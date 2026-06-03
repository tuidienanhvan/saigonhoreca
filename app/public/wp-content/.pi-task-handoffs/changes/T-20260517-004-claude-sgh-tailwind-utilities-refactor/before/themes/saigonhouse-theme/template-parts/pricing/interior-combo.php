<?php $combo_imgs = sh_section_images('noi that', 3); ?>
<section class="sh-combo" data-aos="fade-up">

    <div class="sh-combo__container">
        <div class="sh-combo__header" data-aos="fade-up" data-aos-delay="80">
            <span class="sh-combo__tag">Combo Tiết Kiệm</span>
            <h2 class="sh-combo__title">Gói Nội Thất Theo Phòng</h2>
            <p class="sh-combo__desc">Giải pháp hoàn hảo cho căn hộ chung cư. Mua theo gói - Tiết kiệm hơn.</p>
        </div>

        <div class="sh-combo__grid">
            <div class="sh-combo__card" data-aos="zoom-in-up" data-aos-delay="120">
                <div class="sh-combo__img-wrap">
                    <img src="<?php echo esc_url($combo_imgs[0]); ?>" class="sh-combo__img" loading="lazy" decoding="async" alt="Phòng khách">
                    <div class="sh-combo__img-badge">Phòng Khách</div>
                </div>
                <div class="sh-combo__body">
                    <div class="sh-combo__price-row">
                        <span class="sh-combo__price-old">35.000.000đ</span>
                        <span class="sh-combo__price">25.000.000đ</span>
                    </div>
                    <ul class="sh-combo__list"><li>• Sofa nỉ cao cấp (2m2)</li><li>• Bàn trà mặt đá</li><li>• Kệ Tivi treo tường (2m)</li><li>• Vách ốp sau Sofa</li></ul>
                    <a href="<?php echo home_url('/lien-he'); ?>" class="sh-combo__btn sh-combo__btn--default">Xem Chi Tiết</a>
                </div>
            </div>

            <div class="sh-combo__card sh-combo__card--featured" data-aos="zoom-in-up" data-aos-delay="200">
                <div class="sh-combo__img-wrap">
                    <img src="<?php echo esc_url($combo_imgs[1]); ?>" class="sh-combo__img" loading="lazy" decoding="async" alt="Phòng bếp">
                    <div class="sh-combo__img-badge sh-combo__img-badge--accent">Phòng Bếp</div>
                </div>
                <div class="sh-combo__body sh-combo__body--tinted">
                    <div class="sh-combo__price-row">
                        <span class="sh-combo__price-old">45.000.000đ</span>
                        <span class="sh-combo__price sh-combo__price--accent">35.000.000đ</span>
                    </div>
                    <ul class="sh-combo__list"><li>• Tủ bếp trên + dưới (3md)</li><li>• Đá bếp Kim Sa</li><li>• Kính ốp bếp cường lực</li><li>• Bộ bàn ăn 4 ghế</li></ul>
                    <a href="<?php echo home_url('/lien-he'); ?>" class="sh-combo__btn sh-combo__btn--primary">Đặt Gói Này</a>
                </div>
            </div>

            <div class="sh-combo__card" data-aos="zoom-in-up" data-aos-delay="280">
                <div class="sh-combo__img-wrap">
                    <img src="<?php echo esc_url($combo_imgs[2]); ?>" class="sh-combo__img" loading="lazy" decoding="async" alt="Phòng ngủ">
                    <div class="sh-combo__img-badge">Phòng Ngủ</div>
                </div>
                <div class="sh-combo__body">
                    <div class="sh-combo__price-row">
                        <span class="sh-combo__price-old">30.000.000đ</span>
                        <span class="sh-combo__price">22.000.000đ</span>
                    </div>
                    <ul class="sh-combo__list"><li>• Giường ngủ bọc nệm (1m6)</li><li>• Tủ áo kịch trần (1m8)</li><li>• Bàn trang điểm</li><li>• Tab đầu giường</li></ul>
                    <a href="<?php echo home_url('/lien-he'); ?>" class="sh-combo__btn sh-combo__btn--default">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
    </div>
</section>
