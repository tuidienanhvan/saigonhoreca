<?php
/**
 * Template Part: Báo Giá Phần Thô - Hạng Mục Công Việc
 */
?>
<section class="sh-scope" data-aos="fade-up">

    <div class="sh-scope__container">
        <div class="sh-scope__header" data-aos="fade-up" data-aos-delay="80">
            <span class="sh-scope__badge">Scope of Work</span>
            <h2 class="sh-scope__title">Quy Mô Công Việc Cơ Bản</h2>
            <p class="sh-scope__desc">Hạng mục công việc bao gồm thi công phần thô theo tiêu chuẩn kỹ thuật số 1 và nhân công hoàn thiện (vật tư chủ nhà cung cấp).</p>
        </div>

        <div class="sh-scope__grid">
            <!-- CARD 1: Phần Thô -->
            <div class="sh-scope__card" data-aos="zoom-in-up" data-aos-delay="140">
                <div class="sh-scope__card-watermark">01</div>
                <div class="sh-scope__card-inner">
                    <div class="sh-scope__card-header">
                        <div class="sh-scope__card-icon sh-scope__card-icon--orange"><?php echo sh_icon('hammer', ''); ?></div>
                        <div>
                            <h3 class="sh-scope__card-name">Vật Tư & Nhân Công Thô</h3>
                            <div class="sh-scope__card-meta">
                                <span class="sh-scope__card-meta-muted">14 Hạng Mục</span>
                                <span class="sh-scope__card-meta-dot"></span>
                                <span class="sh-scope__card-meta-accent">Cơ bản & Cốt lõi</span>
                            </div>
                        </div>
                    </div>
                    <ul class="sh-scope__list sh-scope__list--orange">
                        <?php
                        $tho_items = ["Tổ chức công trường, lán trại cho công nhân, kho bãi","Đào đất, lấp đất móng, hầm phốt, hố ga","Đổ đất dư, đổ xà bần trong quá trình xây nhà","Thi công móng (từ đầu cọc ép/khoan nhồi trở lên)","Thi công BTCT dầm, sàn, vách tầng hầm (nếu có)","Thi công BTCT móng, cổ cột, đà kiềng","Thi công hầm phốt, hố ga, bể đựng bồn nước ngầm","Thi công BTCT cột, dầm sàn, đà lanh tô, mái","Đổ BTCT cầu thang, xây bậc bằng gạch","Xây tô tường bao, tường ngăn, vách thiết kế","Thi công hệ xà gồ thép để lợp mái (nếu có)","Đường dây điện, cáp mạng âm tường cơ bản","Lắp đặt ống cấp & thoát nước lạnh uPVC","Đào đất & Lắp đặt hệ thống thoát nước thải"];
                        foreach($tho_items as $item):
                        ?>
                        <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- CARD 2: Nhân Công Hoàn Thiện -->
            <div class="sh-scope__card sh-scope__card--green" data-aos="zoom-in-up" data-aos-delay="220">
                <div class="sh-scope__card-watermark">02</div>
                <div class="sh-scope__card-inner">
                    <div class="sh-scope__card-header">
                        <div class="sh-scope__card-icon sh-scope__card-icon--green"><?php echo sh_icon('home', ''); ?></div>
                        <div>
                            <h3 class="sh-scope__card-name">Nhân Công Hoàn Thiện</h3>
                            <div class="sh-scope__card-meta">
                                <span class="sh-scope__card-meta-muted">07 Hạng Mục</span>
                                <span class="sh-scope__card-meta-dot"></span>
                                <span class="sh-scope__card-meta-accent sh-scope__card-meta-accent--green">Gia chủ mua vật tư</span>
                            </div>
                        </div>
                    </div>
                    <ul class="sh-scope__list sh-scope__list--green" style="margin-bottom:2rem">
                        <?php
                        $ht_items = ["Nhân công ốp lát gạch nền, tường, lợp ngói","Nhân công ốp lát khu vực nhà bếp, WC, mặt tiền","Nhân công sơn nước toàn bộ nhà (trong & ngoài)","Nhân công chống thấm WC, ban công, sân thượng","Dọn dẹp vệ sinh cơ bản trước khi bàn giao nhà","Nhân công lắp đặt thiết bị vệ sinh (Lavabo, bồn...)","Nhân công lắp đặt thiết bị chiếu sáng, công tắc, CB"];
                        foreach($ht_items as $item):
                        ?>
                        <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="sh-scope__note">
                        <div class="sh-scope__note-decor"></div>
                        <div class="sh-scope__note-line"></div>
                        <div class="sh-scope__note-inner">
                            <div class="sh-scope__note-icon-wrap">
                                <div class="sh-scope__note-icon-glow"></div>
                                <div class="sh-scope__note-icon"><?php echo sh_icon('help-circle', ''); ?></div>
                            </div>
                            <div>
                                <div class="sh-scope__note-title">
                                    CĐT Cần Lưu Ý:
                                    <span class="sh-scope__note-ping"></span>
                                </div>
                                <p class="sh-scope__note-text">
                                    Phần vật tư hoàn thiện (sơn nước, gạch ốp, thiết bị...) <span class="sh-scope__note-highlight">gia chủ tự mua</span>. Nhà thầu chỉ đảm nhiệm <span class="sh-scope__note-tag">nhân công thi công/lắp đặt</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
