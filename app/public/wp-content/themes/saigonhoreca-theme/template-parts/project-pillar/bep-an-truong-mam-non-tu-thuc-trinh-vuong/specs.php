<?php
/**
 * Project Pillar — bep-an-truong-mam-non-tu-thuc-trinh-vuong
 * Section #5: specs (Thông Số An Toàn Thực Phẩm — Clean Dashboard Spec + sáng-bóng parallax)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-specs-tvg">
  <div class="pp-container-shared">
    <header class="pp-specs-tvg__head">
      <div class="pp-specs-tvg__badge">
        <span class="pp-specs-tvg__badge-dot" aria-hidden="true"></span>
        <?php echo esc_html__('Thiết bị chủ lực', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-specs-tvg__title"><?php echo esc_html__('Thông số thiết bị inox 304 — công suất lớn, dễ vận hành', 'saigonhoreca'); ?></h2>
      <p class="pp-specs-tvg__lead"><?php echo esc_html__('Để đáp ứng nhu cầu phục vụ 500 suất ăn cùng lúc, SGH lựa chọn và lắp đặt hệ thống thiết bị inox phù hợp cả về dung tích và hiệu suất — đạt chuẩn an toàn điện – gas và dễ dàng bảo trì sau bàn giao.', 'saigonhoreca'); ?></p>
    </header>

    <div class="pp-specs-tvg__dashboard">
      <article class="pp-specs-tvg__card">
        <span class="pp-specs-tvg__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <ellipse cx="11" cy="12" rx="7" ry="3.5"/>
            <path d="M4 12c0 2.5 3 4.5 7 4.5s7-2 7-4.5" stroke-linecap="round"/>
            <path d="M18 12h3M21 12l-1.5-1.5M21 12l-1.5 1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        <h3 class="pp-specs-tvg__card-title"><?php echo esc_html__('Chảo tay quay điện', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-tvg__card-desc"><?php echo esc_html__('Nấu lượng lớn nhanh chóng, vận hành nhẹ nhàng cho nhân sự bếp học đường ít người.', 'saigonhoreca'); ?></p>
        <span class="pp-specs-tvg__tag pp-specs-tvg__tag--mint"><?php echo esc_html__('Công suất lớn', 'saigonhoreca'); ?></span>
      </article>

      <article class="pp-specs-tvg__card">
        <span class="pp-specs-tvg__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <rect x="4" y="3" width="16" height="18" rx="2"/>
            <path d="M8 8h8M8 12h8M8 16h5" stroke-linecap="round"/>
          </svg>
        </span>
        <h3 class="pp-specs-tvg__card-title"><?php echo esc_html__('Tủ hấp công suất cao', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-tvg__card-desc"><?php echo esc_html__('Hấp chín đồng đều khối lượng lớn, tiết kiệm thời gian và sức lao động trong giờ cao điểm.', 'saigonhoreca'); ?></p>
        <span class="pp-specs-tvg__tag pp-specs-tvg__tag--mint"><?php echo esc_html__('Tiết kiệm nhân sự', 'saigonhoreca'); ?></span>
      </article>

      <article class="pp-specs-tvg__card">
        <span class="pp-specs-tvg__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M3 10h18v2a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" stroke-linejoin="round"/>
            <path d="M6 15v5M18 15v5M3 10l2-4h14l2 4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        <h3 class="pp-specs-tvg__card-title"><?php echo esc_html__('Bàn chia bố trí thuận tay', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-tvg__card-desc"><?php echo esc_html__('Phân khu — chia suất chính xác, nâng cao tốc độ phục vụ cho 500 phần ăn cùng thời điểm.', 'saigonhoreca'); ?></p>
        <span class="pp-specs-tvg__tag pp-specs-tvg__tag--gold"><?php echo esc_html__('Chia nhanh', 'saigonhoreca'); ?></span>
      </article>

      <article class="pp-specs-tvg__card">
        <span class="pp-specs-tvg__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <rect x="4" y="3" width="16" height="18" rx="2"/>
            <circle cx="12" cy="13" r="3.5"/>
            <path d="M8 6h2" stroke-linecap="round"/>
          </svg>
        </span>
        <h3 class="pp-specs-tvg__card-title"><?php echo esc_html__('Máy rửa chén & kệ inox 3 tầng', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-tvg__card-desc"><?php echo esc_html__('Tối ưu không gian thẳng đứng, hạn chế bề mặt tiếp xúc — dễ vệ sinh, sạch sẽ tuyệt đối.', 'saigonhoreca'); ?></p>
        <span class="pp-specs-tvg__tag pp-specs-tvg__tag--mint"><?php echo esc_html__('Inox SUS 304', 'saigonhoreca'); ?></span>
      </article>

      <article class="pp-specs-tvg__card">
        <span class="pp-specs-tvg__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M5 13a7 7 0 0 1 14 0" stroke-linecap="round"/>
            <path d="M4 13h16v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z" stroke-linejoin="round"/>
            <path d="M9 7c0-1 1-1.5 1-2.5M14 7c0-1 1-1.5 1-2.5" stroke-linecap="round"/>
          </svg>
        </span>
        <h3 class="pp-specs-tvg__card-title"><?php echo esc_html__('An toàn điện – gas', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-tvg__card-desc"><?php echo esc_html__('Thiết bị từ thương hiệu uy tín, đạt chuẩn an toàn, dễ dàng bảo trì sau bàn giao.', 'saigonhoreca'); ?></p>
        <span class="pp-specs-tvg__tag pp-specs-tvg__tag--gold"><?php echo esc_html__('Đạt chuẩn', 'saigonhoreca'); ?></span>
      </article>

      <article class="pp-specs-tvg__card">
        <span class="pp-specs-tvg__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M4 21V8l8-5 8 5v13" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 21v-6h6v6M9 11h.01M15 11h.01" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        <h3 class="pp-specs-tvg__card-title"><?php echo esc_html__('Công thái học vận hành', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-tvg__card-desc"><?php echo esc_html__('Độ cao bàn, vị trí ổ điện, hướng mở cửa tủ — tính toán theo thực tế vận hành, không chỉ trên bản vẽ.', 'saigonhoreca'); ?></p>
        <span class="pp-specs-tvg__tag pp-specs-tvg__tag--mint"><?php echo esc_html__('An toàn lao động', 'saigonhoreca'); ?></span>
      </article>
    </div>
  </div>

  <!-- Parallax Clip-Path — sáng bừng, sạch bóng, position:fixed bg + clip-path (không JS, không overlay đen) -->
  <div class="pp-specs-tvg__parallax" role="img" aria-label="<?php echo esc_attr__('Khu sơ chế thực tế của bếp Trường Mầm Non Trinh Vương', 'saigonhoreca'); ?>">
    <div class="pp-specs-tvg__parallax-bg" style="background-image:url('<?php echo sgh_img('bep-an-truong-mam-non-tu-thuc-trinh-vuong/bep-an-truong-mam-non-tu-thuc-trinh-vuong-day-ban-soan-chia-inox-chu-l.webp'); ?>');" aria-hidden="true"></div>
    <div class="pp-specs-tvg__parallax-overlay" aria-hidden="true"></div>
    <div class="pp-specs-tvg__parallax-caption">
      <span class="pp-specs-tvg__parallax-tag"><?php echo esc_html__('Khu sơ chế thực tế', 'saigonhoreca'); ?></span>
      <p><?php echo esc_html__('Mọi thiết bị, bề mặt, đường dẫn khí – nước – rác đều được thiết kế để giảm tiếp xúc, dễ làm sạch, dễ kiểm soát.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
