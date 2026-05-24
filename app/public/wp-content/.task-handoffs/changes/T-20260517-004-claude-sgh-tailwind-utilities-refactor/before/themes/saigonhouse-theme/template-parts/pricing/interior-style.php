<?php $style_imgs = sh_section_images('thiet ke', 2); ?>
<section class="sh-style" data-aos="fade-up">

    <div class="sh-style__container">
        <div class="sh-style__header" data-aos="fade-up" data-aos-delay="80">
            <span class="sh-style__tag">Báo Giá Theo Phong Cách</span>
            <h2 class="sh-style__title">Chọn Gu Thẩm Mỹ Của Bạn</h2>
        </div>

        <div class="sh-style__grid">
            <div class="sh-style__card" data-aos="zoom-in-up" data-aos-delay="140">
                <div class="sh-style__img-wrap">
                    <img src="<?php echo esc_url($style_imgs[0]); ?>" class="sh-style__img" loading="lazy" decoding="async" alt="Phong cách nội thất hiện đại">
                    <div class="sh-style__img-overlay">
                        <div>
                            <span class="sh-style__img-badge">Phổ biến nhất</span>
                            <h3 class="sh-style__img-name">Hiện Đại</h3>
                            <p class="sh-style__img-desc">Tối giản, tinh tế, công năng tối ưu.</p>
                        </div>
                    </div>
                </div>
                <div class="sh-style__body">
                    <div class="sh-style__price-row">
                        <div><div class="sh-style__price-label">Đơn giá thiết kế</div><div class="sh-style__price">200k<span class="sh-style__price-unit">/m2</span></div></div>
                        <div><div class="sh-style__price-label" style="text-align:right">Thi công trọn gói</div><div class="sh-style__price-range sh-style__price-range--primary">~ 3-5 Tr/m2</div></div>
                    </div>
                    <ul class="sh-style__features">
                        <li><?php echo sh_icon('check', 'text-green-500'); ?> Phù hợp căn hộ, nhà phố diện tích nhỏ</li>
                        <li><?php echo sh_icon('check', 'text-green-500'); ?> Vật liệu: MDF An Cường, Acrylic</li>
                        <li><?php echo sh_icon('check', 'text-green-500'); ?> Thời gian thi công nhanh (15-20 ngày)</li>
                    </ul>
                    <a href="#tu-van" class="sh-style__btn sh-style__btn--default">Tư Vấn Phong Cách Này</a>
                </div>
            </div>

            <div class="sh-style__card sh-style__card--bordered" data-aos="zoom-in-up" data-aos-delay="220">
                <div class="sh-style__img-wrap">
                    <img src="<?php echo esc_url($style_imgs[1]); ?>" class="sh-style__img" loading="lazy" decoding="async" alt="Phong cách nội thất tân cổ điển">
                    <div class="sh-style__img-overlay">
                        <div>
                            <span class="sh-style__img-badge sh-style__img-badge--secondary">Cao cấp</span>
                            <h3 class="sh-style__img-name">Tân Cổ Điển</h3>
                            <p class="sh-style__img-desc">Sang trọng, đẳng cấp, chi tiết tỉ mỉ.</p>
                        </div>
                    </div>
                </div>
                <div class="sh-style__body">
                    <div class="sh-style__price-row">
                        <div><div class="sh-style__price-label">Đơn giá thiết kế</div><div class="sh-style__price">300k<span class="sh-style__price-unit">/m2</span></div></div>
                        <div><div class="sh-style__price-label" style="text-align:right">Thi công trọn gói</div><div class="sh-style__price-range sh-style__price-range--secondary">~ 6-9 Tr/m2</div></div>
                    </div>
                    <ul class="sh-style__features">
                        <li><?php echo sh_icon('check', ''); ?> Phù hợp biệt thự, căn hộ > 100m2</li>
                        <li><?php echo sh_icon('check', ''); ?> Vật liệu: Gỗ tự nhiên, PVD mạ vàng</li>
                        <li><?php echo sh_icon('check', ''); ?> Chi tiết phào chỉ, hoa văn cầu kỳ</li>
                    </ul>
                    <a href="#tu-van" class="sh-style__btn sh-style__btn--secondary">Tư Vấn Phong Cách Này</a>
                </div>
            </div>
        </div>
    </div>
</section>
