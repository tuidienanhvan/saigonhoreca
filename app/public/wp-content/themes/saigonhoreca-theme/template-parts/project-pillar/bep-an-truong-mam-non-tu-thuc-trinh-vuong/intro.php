<?php
/**
 * Project Pillar — bep-an-truong-mam-non-tu-thuc-trinh-vuong
 * Section #2: intro (Bảo An Sức Khỏe — Friendly Split Layout · dropcap · soft-edged frame)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-intro-tvg">
  <div class="pp-intro-tvg__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-intro-tvg__grid">

      <div class="pp-intro-tvg__text">
        <div class="pp-intro-tvg__badge">
          <span class="pp-intro-tvg__badge-dot" aria-hidden="true"></span>
          <?php echo esc_html__('Bài toán đặt ra', 'saigonhoreca'); ?>
        </div>
        <h2 class="pp-intro-tvg__title">
          <?php echo esc_html__('Bếp học đường — nơi tốc độ và an toàn phải song hành', 'saigonhoreca'); ?>
        </h2>
        <span class="pp-intro-tvg__divider" aria-hidden="true"></span>

        <div class="pp-intro-tvg__body">
          <p><?php echo esc_html__('Với đặc thù bếp học đường, nơi mỗi suất ăn là một "nhiệm vụ dinh dưỡng" dành cho trẻ nhỏ, yêu cầu về tốc độ, an toàn và vệ sinh thực phẩm (VSATTP) luôn được đặt lên hàng đầu. Một sai lệch nhỏ trong quy trình nấu – vận hành đều có thể ảnh hưởng đến hàng trăm phần ăn cùng lúc.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Chính vì vậy, bếp công nghiệp cho trường học không chỉ cần thiết bị mạnh mẽ, mà quan trọng hơn là một quy hoạch khoa học – nơi từng khu vực, từng thao tác được sắp đặt để vận hành nhịp nhàng như một cỗ máy tinh chỉnh.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Ở Trường Mầm Non Tư Thục Trinh Vương, gần 500 bé từ 3 đến 7 tuổi dùng bữa trưa cùng lúc. Nghĩa là chỉ trong vài chục phút, toàn bộ quy trình từ nấu – phân khu chia – giao món phải diễn ra hoàn toàn chính xác, không chậm trễ, không sai sót.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-intro-tvg__quote">
          <svg class="pp-intro-tvg__quote-mark" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M7 7h4v4H8.5C8.5 13 9 14 11 14v3c-3 0-5-2-5-5V7Zm8 0h4v4h-2.5c0 2 .5 3 2.5 3v3c-3 0-5-2-5-5V7Z"/>
          </svg>
          <p><?php echo esc_html__('"Đúng giờ" trong môi trường học đường không chỉ là yêu cầu vận hành, mà là tiêu chuẩn chăm sóc — vì giờ ăn của trẻ gắn liền với đồng hồ sinh học và sức khỏe tiêu hóa.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-intro-tvg__media-col">
        <div class="pp-image-container-shared pp-intro-tvg__frame">
          <img src="<?php echo sgh_img('bep-an-truong-mam-non-tu-thuc-trinh-vuong/bep-an-truong-mam-non-tu-thuc-trinh-vuong-noi-nau-nghieng-inox-cong-nghiep.webp'); ?>" alt="<?php echo esc_attr__('Nồi nấu nghiêng inox công suất lớn dưới hệ chụp hút tại bếp Trường Mầm Non Trinh Vương', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Nồi nấu nghiêng inox công suất lớn — trái tim gian bếp 500 suất ăn mỗi ngày', 'saigonhoreca'); ?></div>
        </div>
        <div class="pp-image-container-shared pp-intro-tvg__frame pp-intro-tvg__frame--float">
          <img src="<?php echo sgh_img('bep-an-truong-mam-non-tu-thuc-trinh-vuong/bep-an-truong-mam-non-tu-thuc-trinh-vuong-chau-rua-doi-inox.webp'); ?>" alt="<?php echo esc_attr__('Bồn rửa đôi inox khu sơ chế với vòi cổ ngỗng tại bếp học đường Trinh Vương', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bồn rửa đôi inox khu sơ chế, vòi cổ ngỗng và kệ lưới phía dưới', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
