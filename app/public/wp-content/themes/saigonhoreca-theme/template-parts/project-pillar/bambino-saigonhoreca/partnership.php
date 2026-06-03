<?php
/**
 * Project Pillar — bambino-saigonhoreca
 * Section #4: bg_section (Bespoke Glassmorphism Editorial Layout)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="bb-partnership-section">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="bb-partnership-section__bg" style="background-image:url('<?php echo sgh_img('bambino/bambino-thiet-bi-bep-nau-nhap-khau.jpg'); ?>');"></div>
  <div class="bb-partnership-section__overlay" aria-hidden="true"></div>
  
  <div class="bb-partnership-section__content">
    <div class="bb-partnership-card">
      
      <!-- Tiêu đề và divider phong cách tạp chí -->
      <div class="bb-partnership-card__header">
        <span class="pp-text-section__divider pp-text-section__divider--center" aria-hidden="true"></span>
        <h2 class="pp-text-section__title">
          <?php echo esc_html__('Sự Hợp Tác Giữa Bambino Superclub và Saigon Horeca', 'saigonhoreca'); ?>
        </h2>
      </div>
      
      <!-- PHẦN 1: Tầm Nhìn & Ý Tưởng Khởi Đầu (2 Cột thoáng đãng) -->
      <div class="bb-partnership-row-top">
        <div class="bb-partnership-col-left">
          <div class="bb-partnership-quote">
            <span class="bb-quote-icon" aria-hidden="true">“</span>
            <p class="bb-quote-text">
              <?php echo esc_html__('Sự hợp tác chặt chẽ để kiến tạo một không gian bếp tùy chỉnh, tối ưu hiệu năng và đạt hiệu quả vận hành vượt trội.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
        <div class="bb-partnership-col-right">
          <p class="bb-partnership-lead">
            <?php echo esc_html__('Trong giai đoạn lên ý tưởng khởi đầu, Saigon Horeca đã tham gia vào các buổi thảo luận và phân tích sâu rộng để có được một sự hiểu biết sâu sắc những yêu cầu độc đáo và cơ bản về hoạt động của Bambino.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
      <!-- PHẦN 2: Hiện Thực Hóa Chuyên Môn (Dạng cột catalog tạp chí kiến trúc) -->
      <div class="bb-partnership-catalog">
        <div class="bb-catalog-item">
          <div class="bb-catalog-meta">
            <span class="bb-catalog-num">01</span>
            <span class="bb-catalog-tag">Tương Tác & Thấu Hiểu</span>
          </div>
          <p class="bb-catalog-text">
            <?php echo esc_html__('Qua sự hợp tác chặt chẽ, đội ngũ của Saigon Horeca tương tác mật thiết với các bên liên quan của Bambino, khám phá những chi tiết của sở thích ẩm thực, động lực công việc, và nhu cầu không gian của nhà hàng. Phương pháp hợp tác này tập trung vào việc thúc đẩy sự nắm bắt toàn diện về tầm nhìn của đối tác.', 'saigonhoreca'); ?>
          </p>
        </div>
        
        <div class="bb-catalog-item">
          <div class="bb-catalog-meta">
            <span class="bb-catalog-num">02</span>
            <span class="bb-catalog-tag">Tiêu Chuẩn & Kỹ Thuật</span>
          </div>
          <p class="bb-catalog-text">
            <?php echo esc_html__('Chuyên môn của Saigon Horeca đảm bảo rằng thiết kế nhà bếp tuân theo các tiêu chuẩn và quy định của bếp Âu trong ngành thiết bị bếp công nghiệp, đồng thời tạo ra một môi trường khuyến khích sự đổi mới trong ẩm thực. Sự chú ý đến chi tiết bao gồm các hệ thống thông gió, giải pháp lưu trữ, tối ưu hóa quy trình công việc.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
      <!-- PHẦN 3: Đỉnh Cao Sự Hợp Tác (Full-width Callout kết luận) -->
      <div class="bb-partnership-footer-callout">
        <div class="bb-callout-inner">
          <svg class="bb-callout-sparkle" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L15 9L22 12L15 15L12 22L9 15L2 12L9 9L12 2Z" fill="var(--gold)" />
          </svg>
          <p class="bb-callout-text">
            <?php echo esc_html__('Cuối cùng, dịch vụ tư vấn và thiết kế của Saigon Horeca trở thành một phần không thể thiếu của bản sắc Bambino – một tổ chức nuôi dưỡng cho sự xuất sắc trong nghệ thuật ẩm thực.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
    </div>
  </div>
</section>
