<?php
/**
 * Project Pillar — tales-by-chapter
 * Section #2: intro — opening chapter (narrative + gallery + stat chips).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tbc pp-section-tbc--alt pp-intro-tbc">
  <div class="pp-chapter-mark-tbc" aria-hidden="true">01</div>
  <div class="pp-ambient-glow-tbc pp-ambient-glow-tbc--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <header class="pp-intro-tbc__header">
      <div class="pp-badge-tbc">
        <span class="pp-badge-tbc__num">Chương 01</span>
        <?php echo esc_html__('Lời mở đầu', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-text-tbc__title pp-intro-tbc__title">
        <?php echo esc_html__('Một con đường rất khác giữa lòng Sài Gòn', 'saigonhoreca'); ?>
      </h2>
      <span class="pp-text-tbc__divider" aria-hidden="true"></span>
    </header>

    <div class="pp-intro-tbc__body">
      <div class="pp-intro-tbc__lead">
        <p><?php echo esc_html__('Giữa trung tâm Sài Gòn nhộn nhịp, Tales by Chapter chọn cho mình một con đường rất khác.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Tọa lạc tại số 10 Nguyễn Thành Ý, Đa Kao, Quận 1, nhà hàng không đi theo những lối mòn cũ mà chọn một hướng đi riêng: tử tế với môi trường qua mô hình zero waste và nâng tầm các nguyên liệu thuần thực vật. Với TBC, mỗi nguyên liệu đều quý giá và xứng đáng được thưởng thức trọn vẹn nhất. Với mô hình The first zero waste plant-based dining in Vietnam, Tales lựa chọn con đường khó hơn: không thịt cá, không dư thừa, không lãng phí – nhưng vẫn phải đủ chiều sâu, kỹ thuật và cảm xúc để chạm đến thực khách.', 'saigonhoreca'); ?></p>
      </div>
      <div class="pp-intro-tbc__story">
        <p><?php echo esc_html__('Để triết lý đó có thể vận hành trọn vẹn trong thực tế, Tales by Chapter cần nhiều hơn một gian bếp đẹp. Họ cần một hệ thống được tư duy bài bản, nơi thiết bị không áp đặt cách nấu, mà âm thầm hỗ trợ cho sự sáng tạo. Saigon Horeca tham gia dự án với vai trò tư vấn lựa chọn, cung cấp thiết bị và thiết kế layout cho toàn bộ quầy bar tầng 1, khu bếp tầng 2 và lab bar tầng 3 – ba không gian với ba chức năng hoàn toàn khác nhau, nhưng cùng phục vụ một tinh thần chung.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Tầng 1 của Tales by Chapter là không gian thưởng thức. Không phô trương, không cầu kỳ. Chỉ là một quầy bar vừa đủ, để thực khách chậm lại, quan sát, và bắt đầu cảm nhận tinh thần của nhà hàng. Saigon Horeca tiếp cận không gian này bằng một tư duy rất rõ ràng: quầy bar không cần nhiều thiết bị để gây ấn tượng, mà cần đủ công năng để phục vụ trọn vẹn trải nghiệm. Tất cả đều lùi lại một bước, để nhường chỗ cho câu chuyện và cảm xúc của thực khách.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-intro-tbc__stats">
      <div class="pp-intro-tbc__stat">
        <strong>03</strong>
        <span><?php echo esc_html__('Tầng – bar, bếp & lab', 'saigonhoreca'); ?></span>
      </div>
      <div class="pp-intro-tbc__stat">
        <strong>10–30</strong>
        <span><?php echo esc_html__('Khách / quy mô quầy bar', 'saigonhoreca'); ?></span>
      </div>
      <div class="pp-intro-tbc__stat">
        <strong><?php echo esc_html__('Zero waste', 'saigonhoreca'); ?></strong>
        <span><?php echo esc_html__('Plant-based dining đầu tiên tại VN', 'saigonhoreca'); ?></span>
      </div>
    </div>

    <div class="pp-intro-tbc__gallery">
      <figure class="pp-intro-tbc__gallery-item pp-intro-tbc__gallery-item--large pp-image-container-shared">
        <img src="<?php echo sgh_img('tales-by-chapter/tales-by-chapter-mat-tien-bien-hieu-buoi-toi.jpg'); ?>" alt="<?php echo esc_attr__('Mặt tiền Tales by Chapter buổi tối', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Mặt tiền Tales by Chapter tại 10 Nguyễn Thành Ý, Đa Kao', 'saigonhoreca'); ?></figcaption>
      </figure>
      <figure class="pp-intro-tbc__gallery-item pp-intro-tbc__gallery-item--wide pp-image-container-shared">
        <img src="<?php echo sgh_img('tales-by-chapter/tales-by-chapter-mon-plant-based-trinh-bay-nghe-thuat.jpg'); ?>" alt="<?php echo esc_attr__('Ẩm thực plant-based tại Tales by Chapter', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Ẩm thực plant-based trình bày như một tác phẩm', 'saigonhoreca'); ?></figcaption>
      </figure>
      <figure class="pp-intro-tbc__gallery-item pp-image-container-shared">
        <img src="<?php echo sgh_img('tales-by-chapter/tales-by-chapter-mon-khai-vi-nuoc-dung-rau-cu.jpg'); ?>" alt="<?php echo esc_attr__('Món khai vị nước dùng rau củ thuần chay', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Món thuần chay tinh tế từ triết lý zero waste', 'saigonhoreca'); ?></figcaption>
      </figure>
    </div>
  </div>
</section>
