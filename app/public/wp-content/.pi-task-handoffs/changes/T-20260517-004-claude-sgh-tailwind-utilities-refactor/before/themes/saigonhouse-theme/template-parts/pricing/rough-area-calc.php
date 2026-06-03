<?php
/**
 * Template Part: Báo Giá Phần Thô - Cách Tính Diện Tích
 */
?>
<section class="sh-area-grid" data-aos="fade-up">

    <div class="sh-area-grid__decor1"></div>
    <div class="sh-area-grid__decor2"></div>

    <div class="sh-area-grid__container">
        <div class="sh-area-grid__layout">
            <div class="sh-area-grid__sidebar" data-aos="fade-right" data-aos-delay="80">
                <div class="sh-area-grid__sidebar-header">
                    <span class="sh-area-grid__badge">Phương pháp đo bóc</span>
                    <h2 class="sh-area-grid__title">Hệ Số<br>Tính Diện Tích</h2>
                    <p class="sh-area-grid__lead">Hệ số diện tích được áp dụng minh bạch cho từng phần của công trình, giúp dự toán chính xác nhất chi phí đầu tư.</p>
                </div>

                <div class="sh-area-grid__bill">
                    <div class="sh-area-grid__bill-line"></div>
                    <div class="sh-area-grid__bill-header">
                        <div class="sh-area-grid__bill-icon"><?php echo sh_icon('file-text', ''); ?></div>
                        <h4 class="sh-area-grid__bill-title">Quy Định Phụ Phí</h4>
                    </div>
                    <ul class="sh-area-grid__bill-list">
                        <li class="sh-area-grid__bill-item"><span class="sh-area-grid__bill-label">Tổng DT 250 - 350m&sup2;</span><strong class="sh-area-grid__bill-value">+50k/m&sup2;</strong></li>
                        <li class="sh-area-grid__bill-item"><span class="sh-area-grid__bill-label">Tổng DT 150 - 250m&sup2;</span><strong class="sh-area-grid__bill-value">+100k/m&sup2;</strong></li>
                        <li class="sh-area-grid__bill-sep"></li>
                        <li class="sh-area-grid__bill-item"><span class="sh-area-grid__bill-label">Tổng DT &lt; 150m&sup2;</span><strong class="sh-area-grid__bill-multiplier">x 1.2</strong></li>
                        <li class="sh-area-grid__bill-item"><span class="sh-area-grid__bill-label">Kiến trúc Cổ Điển</span><strong class="sh-area-grid__bill-multiplier">x 1.2</strong></li>
                        <li class="sh-area-grid__bill-item"><span class="sh-area-grid__bill-label">Nhà hẻm nhỏ / 2 MT</span><strong class="sh-area-grid__bill-multiplier">x 1.2</strong></li>
                    </ul>
                </div>
            </div>

            <div class="sh-area-grid__masonry">
                <div class="sh-area-grid__col">
                    <!-- Phần Thân -->
                    <div class="sh-area-grid__card sh-area-grid__card--dark" data-aos="zoom-in-up" data-aos-delay="120">
                        <div class="sh-area-grid__card-header">
                            <div class="sh-area-grid__card-icon sh-area-grid__card-icon--white"><?php echo sh_icon('layers', ''); ?></div>
                            <h3 class="sh-area-grid__card-name">Phần Thân</h3>
                        </div>
                        <ul class="sh-area-grid__card-list">
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--white"></span><span>Trệt, lầu (Sàn trong nhà): <strong>100%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--white"></span><span>Sân thượng có mái che: <strong>100%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--white"></span><span>Sân thượng không mái che: <strong>50%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--white"></span><span>Gia cố nền trệt sàn BTCT: <strong>10-20%</strong></span></li>
                        </ul>
                    </div>
                    <!-- Tầng Hầm -->
                    <div class="sh-area-grid__card sh-area-grid__card--white" data-aos="zoom-in-up" data-aos-delay="180">
                        <div class="sh-area-grid__card-header">
                            <div class="sh-area-grid__card-icon sh-area-grid__card-icon--muted"><?php echo sh_icon('box', ''); ?></div>
                            <h3 class="sh-area-grid__card-name">Tầng Hầm</h3>
                        </div>
                        <ul class="sh-area-grid__card-list">
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Sâu &lt; 1.2m (so với vỉa hè): <strong>150%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Sâu &lt; 1.8m: <strong>170%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Sâu &gt; 2.0m: <strong>200%</strong></span></li>
                        </ul>
                    </div>
                    <!-- Phần Khác -->
                    <div class="sh-area-grid__card sh-area-grid__card--white" data-aos="zoom-in-up" data-aos-delay="240">
                        <div class="sh-area-grid__card-header">
                            <div class="sh-area-grid__card-icon sh-area-grid__card-icon--muted"><?php echo sh_icon('layout', ''); ?></div>
                            <h3 class="sh-area-grid__card-name">Phần Khác</h3>
                        </div>
                        <ul class="sh-area-grid__card-list">
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Sàn giả: <strong>50%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Ô trống &lt; 8m&sup2;: <strong>100%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Ô trống &gt; 8m&sup2;: <strong>50%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Sân vườn, rào (&lt; 40m&sup2;): <strong>70%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Sân vườn, rào (&gt; 40m&sup2;): <strong>50%</strong></span></li>
                        </ul>
                    </div>
                </div>

                <div class="sh-area-grid__col">
                    <!-- Phần Mái -->
                    <div class="sh-area-grid__card sh-area-grid__card--accent" data-aos="zoom-in-up" data-aos-delay="160">
                        <div class="sh-area-grid__card-header" style="position:relative;z-index:10">
                            <div class="sh-area-grid__card-icon sh-area-grid__card-icon--accent"><?php echo sh_icon('home', ''); ?></div>
                            <h3 class="sh-area-grid__card-name">Phần Mái</h3>
                        </div>
                        <ul class="sh-area-grid__card-list" style="position:relative;z-index:10">
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Mái bằng Tôn: <strong>30%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Dàn bông tựa mái/Pergola: <strong>30%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Mái BTCT, mái tum hở: <strong>50%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Mái ngói xà gồ thép: <strong>70%</strong></span></li>
                            <li class="sh-area-grid__card-item"><span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span><span>Mái BTCT dán ngói: <strong>100%</strong></span></li>
                        </ul>
                    </div>
                    <!-- Phần Móng -->
                    <div class="sh-area-grid__card sh-area-grid__card--white" data-aos="zoom-in-up" data-aos-delay="220">
                        <div class="sh-area-grid__card-header">
                            <div class="sh-area-grid__card-icon sh-area-grid__card-icon--muted"><?php echo sh_icon('layers', ''); ?></div>
                            <h3 class="sh-area-grid__card-name">Phần Móng</h3>
                        </div>
                        <ul class="sh-area-grid__card-list">
                            <li class="sh-area-grid__card-item">
                                <span class="sh-area-grid__card-dot sh-area-grid__card-dot--muted"></span>
                                <div><span style="display:block;margin-bottom:0.5rem">Móng đơn, đài cọc, băng, bè:</span><strong style="display:inline-block;padding:0.375rem 0.75rem;background:var(--bg-alt, #f1f5f9);border-radius:0.5rem;font-size:0.875rem">20% - 50% thô</strong></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
