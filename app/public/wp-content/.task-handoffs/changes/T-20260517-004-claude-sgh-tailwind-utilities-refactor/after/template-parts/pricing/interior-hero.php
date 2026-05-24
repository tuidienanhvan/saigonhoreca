<section class="sh-int-hero">

    <div class="sh-int-hero__bg">
        <img src="<?php echo esc_url(sh_section_image('noi that', '', 1)); ?>" alt="Thiết kế nội thất" loading="eager" fetchpriority="high">
        <div class="sh-int-hero__bg-overlay"></div>
    </div>

    <div class="sh-int-hero__container">
        <div class="sh-int-hero__label-row">
            <span class="sh-int-hero__label-line"></span>
            <span class="sh-int-hero__label">Interior Design</span>
            <span class="sh-int-hero__label-line"></span>
        </div>
        <h1 class="sh-int-hero__title" data-aos="fade-down">Không Gian Sống</h1>
        <div class="sh-int-hero__wordart">Đậm Chất Riêng</div>
        <p class="sh-int-hero__desc">Trực tiếp sản xuất tại xưởng. Tiết kiệm 30% chi phí trung gian.<br>Cam kết giống bản vẽ 99%.</p>
        <div class="sh-int-hero__actions">
            <a href="#xuong-san-xuat" class="sh-int-hero__btn-video">
                <span class="sh-int-hero__play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
                <span>Xem Video Xưởng</span>
            </a>
            <a href="#bao-gia" class="sh-int-hero__btn-cta">Nhận Báo Giá</a>
        </div>
    </div>
</section>

<!-- FACTORY HIGHLIGHT -->
<section id="xuong-san-xuat" class="sh-factory">

    <div class="sh-factory__glow1"></div>
    <div class="sh-factory__glow2"></div>

    <div class="sh-factory__container">
        <div class="sh-factory__grid">
            <div>
                <div class="sh-factory__bento">
                    <div class="sh-factory__bento-main">
                        <img src="<?php echo esc_url(sh_section_image('noi that', '', 2)); ?>" alt="Máy móc CNC SaigonHouse" loading="lazy" decoding="async">
                    </div>
                    <div class="sh-factory__bento-right">
                        <div class="sh-factory__bento-exp">
                            <?php $interior_grid = sh_section_images('noi that', 4); ?>
                            <div class="sh-factory__bento-exp-grid">
                                <img src="<?php echo esc_url($interior_grid[0]); ?>" alt="Thiết kế 1" loading="lazy" decoding="async">
                                <img src="<?php echo esc_url($interior_grid[1]); ?>" alt="Thiết kế 2" loading="lazy" decoding="async">
                                <img src="<?php echo esc_url($interior_grid[2]); ?>" alt="Thiết kế 3" loading="lazy" decoding="async">
                                <img src="<?php echo esc_url($interior_grid[3]); ?>" alt="Thiết kế 4" loading="lazy" decoding="async">
                            </div>
                            <div class="sh-factory__exp-card">
                                <div class="sh-factory__exp-icon"><?php echo sh_icon('check-circle', ''); ?></div>
                                <h3 class="sh-factory__exp-num">15+</h3>
                                <p class="sh-factory__exp-label">Năm<br>Kinh Nghiệm</p>
                            </div>
                        </div>
                        <div class="sh-factory__bento-bottom">
                            <img src="<?php echo esc_url(sh_section_image('noi that', '', 5)); ?>" alt="Nội thất cao cấp" loading="lazy" decoding="async">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="sh-factory__badge"><span class="sh-factory__badge-dot"></span> Tại sao chọn chúng tôi?</div>
                <h2 class="sh-factory__title">Lợi Thế Cạnh Tranh Của <span class="sh-factory__title-accent">SaigonHouse</span></h2>

                <div class="sh-factory__features">
                    <div class="sh-factory__feature">
                        <div class="sh-factory__feature-num">01</div>
                        <div class="sh-factory__feature-content">
                            <h3 class="sh-factory__feature-title">Xưởng Sản Xuất Trực Tiếp</h3>
                            <p class="sh-factory__feature-desc">Không qua trung gian. Trang bị máy móc CNC hiện đại nhập khẩu Châu Âu, đảm bảo độ chuẩn xác 100% theo bản vẽ thiết kế 3D.</p>
                        </div>
                    </div>
                    <div class="sh-factory__divider"></div>
                    <div class="sh-factory__feature">
                        <div class="sh-factory__feature-num">02</div>
                        <div class="sh-factory__feature-content">
                            <h3 class="sh-factory__feature-title">Vật Liệu Chuẩn Chính Hãng</h3>
                            <p class="sh-factory__feature-desc">Đối tác chiến lược toàn diện của An Cường, Hafele, Blum. Cam kết đền bù gấp 10 lần nếu phát hiện sử dụng vật liệu tráo đổi sai cam kết.</p>
                        </div>
                    </div>
                    <div class="sh-factory__divider"></div>
                    <div class="sh-factory__feature">
                        <div class="sh-factory__feature-num">03</div>
                        <div class="sh-factory__feature-content">
                            <h3 class="sh-factory__feature-title">Miễn Phí Thiết Kế 100%</h3>
                            <p class="sh-factory__feature-desc">Hoàn phí thiết kế 100% ngay khi khách hàng chốt phương án và tiến hành ký hợp đồng thi công nội thất trọn gói. Giá trị thật đến tay khách hàng thật.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
