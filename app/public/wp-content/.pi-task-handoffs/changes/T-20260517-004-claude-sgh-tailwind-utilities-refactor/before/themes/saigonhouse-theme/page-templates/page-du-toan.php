<?php
/* Template Name: Du Toan Chi Phi (Brand-Aligned) */
get_header(); ?>

<section class="sh-calc" data-aos="fade-up">
    <div class="sh-calc__container">

        <!-- Header -->
        <div class="sh-calc__header" data-aos="fade-up" data-aos-delay="80">
            <h1 class="sh-calc__title">
                <span class="sh-calc__title-accent">Dự Toán</span> <span class="sh-calc__title-muted">Chi Phí Tự Động</span>
            </h1>
            <p class="sh-calc__subtitle">Hệ thống tính toán diện tích & chi phí xây dựng chính xác.</p>
        </div>

        <div class="sh-calc__layout">

            <!-- LEFT: INPUT FORM -->
            <div class="sh-calc__form" data-aos="fade-right" data-aos-delay="120">

                <!-- 1. General Info -->
                <div class="sh-calc__group">
                    <label class="sh-calc__group-label"><span class="sh-calc__dot"></span> 01. Thông tin cơ bản</label>
                    <div class="sh-calc__row sh-calc__row--2col">
                        <div>
                            <span class="sh-calc__field-label">Loại Công Trình</span>
                            <select id="calc-loainha" class="sh-calc__select">
                                <option value="nhapho">Nhà Phố</option>
                                <option value="bietthu">Biệt Thự</option>
                            </select>
                        </div>
                        <div>
                            <span class="sh-calc__field-label">Lộ giới / Hẻm (m)</span>
                            <select id="calc-logioi" class="sh-calc__select">
                                <option value="3">Hẻm &lt; 3m</option>
                                <option value="5">Hẻm 3m - 5m</option>
                                <option value="6">Hẻm 5m - 7m</option>
                                <option value="10">Đường 7m - 12m (Có ban công 0.9m)</option>
                                <option value="15" selected>Đường 12m - 20m (Có ban công 1.2m)</option>
                                <option value="25">Đường &gt; 20m (Có ban công 1.4m)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 2. Dimensions -->
                <div class="sh-calc__group">
                    <label class="sh-calc__group-label"><span class="sh-calc__dot"></span> 02. Kích Thước (m)</label>
                    <div class="sh-calc__row sh-calc__row--2col">
                        <div class="sh-calc__input-box">
                            <span class="sh-calc__field-label">Chiều Rộng</span>
                            <input type="number" id="calc-rongtret" value="5" min="3" max="50" step="0.1" class="sh-calc__number-input">
                        </div>
                        <div class="sh-calc__input-box">
                            <span class="sh-calc__field-label">Chiều Dài</span>
                            <input type="number" id="calc-daitret" value="20" min="5" max="100" step="0.1" class="sh-calc__number-input">
                        </div>
                    </div>
                </div>

                <!-- 3. Structure -->
                <div class="sh-calc__group">
                    <label class="sh-calc__group-label"><span class="sh-calc__dot"></span> 03. Quy Mô Xây Dựng</label>
                    <div class="sh-calc__row sh-calc__row--3col">
                        <div>
                            <span class="sh-calc__field-label">Số Lầu</span>
                            <select id="calc-solau" class="sh-calc__select">
                                <option value="0">Chỉ có Trệt (Cấp 4)</option>
                                <option value="1" selected>1 Trệt, 1 Lầu</option>
                                <option value="2">1 Trệt, 2 Lầu</option>
                                <option value="3">1 Trệt, 3 Lầu</option>
                                <option value="4">1 Trệt, 4 Lầu</option>
                                <option value="5">1 Trệt, 5 Lầu</option>
                            </select>
                        </div>
                        <div>
                            <span class="sh-calc__field-label">Loại Móng</span>
                            <select id="calc-loaimong" class="sh-calc__select">
                                <option value="don">Móng Đơn (30%)</option>
                                <option value="coc" selected>Móng Cọc (50%)</option>
                                <option value="bang">Móng Băng (50%)</option>
                            </select>
                        </div>
                        <div>
                            <span class="sh-calc__field-label">Loại Mái</span>
                            <select id="calc-loaimai" class="sh-calc__select">
                                <option value="tole">Mái Tole (30%)</option>
                                <option value="btct" selected>Mái BTCT (50%)</option>
                                <option value="ngoi">Mái Ngói kèo sắt (70%)</option>
                                <option value="ngoibt">Mái Ngói BTCT (100%)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 4. Package -->
                <div class="sh-calc__group">
                    <label class="sh-calc__group-label"><span class="sh-calc__dot"></span> 04. Gói Báo Giá</label>
                    <div class="sh-calc__row sh-calc__row--2col">
                        <label class="sh-calc__radio-card">
                            <input type="radio" name="calc-goi" value="tho" class="sh-calc__radio-input" checked>
                            <div class="sh-calc__radio-body">
                                <div class="sh-calc__radio-title">Phần Thô</div>
                                <div class="sh-calc__radio-desc">Bao gồm nhân công hoàn thiện.</div>
                            </div>
                            <div class="sh-calc__radio-check"><?php echo sh_icon('check-circle', 'sh-calc__radio-check-icon'); ?></div>
                        </label>
                        <label class="sh-calc__radio-card">
                            <input type="radio" name="calc-goi" value="trongoi" class="sh-calc__radio-input">
                            <div class="sh-calc__radio-body">
                                <div class="sh-calc__radio-title">Trọn Gói</div>
                                <div class="sh-calc__radio-desc">Chìa khóa trao tay.</div>
                            </div>
                            <div class="sh-calc__radio-check"><?php echo sh_icon('check-circle', 'sh-calc__radio-check-icon'); ?></div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- RIGHT: RECEIPT -->
            <div class="sh-calc__result-col" data-aos="fade-left" data-aos-delay="180">
                <div class="sh-calc__result-sticky">
                    <div class="sh-calc__receipt">
                        <div class="sh-calc__receipt-header">
                            <h3 class="sh-calc__receipt-title">Bảng Dự Toán</h3>
                            <span class="sh-calc__receipt-brand">SAIGON HOUSE</span>
                        </div>

                        <!-- Area -->
                        <div class="sh-calc__receipt-section">
                            <h4 class="sh-calc__receipt-label">Chi Tiết Diện Tích</h4>
                            <div class="sh-calc__receipt-rows" id="calc-breakdown"></div>
                            <div class="sh-calc__receipt-total-row">
                                <span class="sh-calc__receipt-total-label">Tổng Diện Tích Xây Dựng</span>
                                <span class="sh-calc__receipt-total-value"><span id="res-total-area">0</span> m²</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="sh-calc__receipt-price-box">
                            <div class="sh-calc__receipt-price-row">
                                <span class="sh-calc__receipt-price-label">Đơn giá / m²</span>
                                <span class="sh-calc__receipt-price-value" id="res-unit-price">0</span>
                            </div>
                            <div class="sh-calc__receipt-fee-row" id="res-hem-fee-row" style="display:none;">
                                <span>Phụ phí hẻm nhỏ</span>
                                <span id="res-hem-fee">+0</span>
                            </div>
                            <div class="sh-calc__receipt-grand">
                                <span class="sh-calc__receipt-grand-label">Khái Toán Tổng Chi Phí (VNĐ)</span>
                                <div class="sh-calc__receipt-grand-value" id="res-total-cost">0</div>
                            </div>
                        </div>

                        <a href="<?php echo home_url('/lien-he'); ?>" class="sh-calc__receipt-cta">Liên Hệ Nhận Báo Giá Chính Xác</a>
                        <p class="sh-calc__receipt-note">* Ước tính sơ bộ. Giá chưa bao gồm VAT.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
