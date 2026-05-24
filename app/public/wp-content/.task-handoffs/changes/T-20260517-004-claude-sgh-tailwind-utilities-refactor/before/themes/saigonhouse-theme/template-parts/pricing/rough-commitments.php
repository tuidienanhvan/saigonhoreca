<?php
/**
 * Template Part: Báo Giá Phần Thô - 17 Cam Kết Vàng
 */
?>
<section class="sh-commitments" data-aos="fade-up">

    <div class="sh-commitments__glow-line"></div>
    <div class="sh-commitments__glow sh-commitments__glow--r"></div>
    <div class="sh-commitments__glow sh-commitments__glow--l"></div>

    <div class="sh-commitments__container">
        <div class="sh-commitments__header" data-aos="fade-up" data-aos-delay="80">
            <div class="sh-commitments__tag-row">
                <span class="sh-commitments__tag-line"></span>
                <span class="sh-commitments__tag">Uy Tín &amp; Trách Nhiệm</span>
                <span class="sh-commitments__tag-line"></span>
            </div>
            <h2 class="sh-commitments__title">17 Cam Kết Vàng</h2>
            <p class="sh-commitments__desc">Bảo chứng niềm tin trong suốt quá trình đồng hành cùng tổ ấm người Việt. Sẵn sàng chịu phạt hợp đồng nếu vi phạm tiêu chuẩn.</p>
        </div>

        <div class="sh-commitments__scroll sh-hscroll">
            <?php
            $promises = [
                "Vật tư chính hãng đúng hợp đồng. Phạt hợp đồng lập tức nếu phát hiện dùng hàng giả, kém chất lượng.",
                "Chia đường dây điện thông minh có tự ngắt, thi công chuẩn nhà cao tầng, thử áp lực ống nước cẩn thận trước khi che đậy.",
                "Sử dụng Coffa sắt, thép tiền chế, dàn giáo kiên cố tuyệt đối đảm bảo không võng thủng sàn bê tông.",
                "Trang bị máy Laser, máy Toàn đạc công nghệ cao cho độ chính xác tuyệt đối.",
                "Bảo dưỡng bề mặt bê tông, gạch tường bằng nước sạch liên tục trong những ngày đầu.",
                "Chống thấm vệ sinh, ban công bằng Kova-CT11A 3 lớp, ngầm nước thử nghiệm suốt 24h. Bảo hành thấm dột lên đến 5 năm.",
                "Đóng lưới mắc cáo chống nứt mép cửa, nách dầm 100%. Vữa trộn bằng máy tời chuyên dụng.",
                "Tuyệt đối đúng tiến độ như cam kết, có điều khoản thưởng phạt phân minh rõ ràng.",
                "Bảo vệ công trình, chống lãng phí vật tư của nhà thầu và gia chủ tối đa.",
                "Bao che lưới an toàn toàn bộ mặt ngoài giữ an toàn 100% cho thợ và hàng xóm.",
                "Có Kỹ Sư chuyên môn trực tiếp điều hành xử lý bản vẽ, rắc rối với thanh tra xây dựng. Pháp lý 100% chuẩn.",
                "Tổ đội chuyên môn thi công trực tiếp của công ty, cam kết không bán thầu phụ, qua trung gian.",
                "Miễn phí hoàn toàn các thay đổi nhỏ (như tường ngăn, dời ổ cắm), cam kết KHÔNG PHÁT SINH vô lý.",
                "Chịu hoàn toàn trách nhiệm quản lý mặt bằng, vệ sinh môi trường, hàng xóm. Lý lịch nhân công thi công rõ ràng.",
                "Giữ nguyên giá ký kết ban đầu cho đến khi hoàn thành, DÙ VẬT TƯ THỊ TRƯỜNG CÓ TĂNG GIÁ.",
                "Cam kết bảo hành kết cấu 5 năm, bao gồm lún cục bộ nứt thân tường. Tổng thể 12 tháng khắc phục sớm nhất.",
                "Tiêu chuẩn xanh: Dọn dẹp sạch sẽ gọn gàng bãi chiến trường hàng ngày vào cuối mỗi ca làm việc."
            ];
            foreach($promises as $i => $text):
                $num = sprintf('%02d', $i + 1);
            ?>
            <div class="sh-commitments__card" data-aos="zoom-in-up" data-aos-delay="<?php echo esc_attr(min(500, 120 + ($i * 40))); ?>">
                <div class="sh-commitments__card-glow"></div>
                <div class="sh-commitments__card-inner">
                    <div class="sh-commitments__num"><?php echo $num; ?>.</div>
                    <p class="sh-commitments__text"><?php echo $text; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
