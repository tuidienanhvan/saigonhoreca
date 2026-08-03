<?php
/**
 * Project Pillar — bep-an-truong-mam-non-tu-thuc-trinh-vuong
 * Section #3: concept (Quy Trình Một Chiều — Timeline Spec Panel · milk-glass cards)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-concept-tvg">
  <div class="pp-concept-tvg__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <header class="pp-concept-tvg__head">
      <div class="pp-concept-tvg__badge">
        <span class="pp-concept-tvg__badge-dot" aria-hidden="true"></span>
        <?php echo esc_html__('Quy hoạch tổng thể', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-concept-tvg__title"><?php echo esc_html__('Luồng vận hành một chiều khép kín', 'saigonhoreca'); ?></h2>
      <p class="pp-concept-tvg__lead"><?php echo esc_html__('Dựa trên layout tổng thể, SGH bố trí 150m² không gian bếp thành các khu vực công năng tách biệt, vận hành theo nguyên tắc một chiều — hạn chế tối đa việc giao chéo giữa nguyên liệu sống và thực phẩm chín, yếu tố quan trọng hàng đầu trong bếp học đường.', 'saigonhoreca'); ?></p>
    </header>

    <ol class="pp-concept-tvg__timeline">

      <li class="pp-concept-tvg__step" style="--tvg-i:0">
        <div class="pp-concept-tvg__card">
          <span class="pp-concept-tvg__num">01</span>
          <span class="pp-concept-tvg__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M3 7h13v9H3z" stroke-linejoin="round"/>
              <path d="M16 10h3.5L22 13v3h-6z" stroke-linejoin="round"/>
              <circle cx="7" cy="18" r="1.8"/><circle cx="18" cy="18" r="1.8"/>
            </svg>
          </span>
          <h3 class="pp-concept-tvg__step-title"><?php echo esc_html__('Nhập hàng', 'saigonhoreca'); ?></h3>
          <p class="pp-concept-tvg__step-desc"><?php echo esc_html__('Nguyên liệu tươi sống được tiếp nhận tại cửa riêng, kiểm soát đầu vào trước khi đưa vào dây chuyền.', 'saigonhoreca'); ?></p>
        </div>
      </li>

      <li class="pp-concept-tvg__step" style="--tvg-i:1">
        <div class="pp-concept-tvg__card">
          <span class="pp-concept-tvg__num">02</span>
          <span class="pp-concept-tvg__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M6 3v7a3 3 0 0 0 6 0V3" stroke-linecap="round"/>
              <path d="M9 13v8" stroke-linecap="round"/>
              <path d="M17 3c-1.5 1-2 3-2 5s.5 4 2 5v8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          <h3 class="pp-concept-tvg__step-title"><?php echo esc_html__('Sơ chế', 'saigonhoreca'); ?></h3>
          <p class="pp-concept-tvg__step-desc"><?php echo esc_html__('Khu sơ chế tách biệt theo nhóm thực phẩm, giảm tiếp xúc, dễ làm sạch và dễ kiểm soát vệ sinh.', 'saigonhoreca'); ?></p>
        </div>
      </li>

      <li class="pp-concept-tvg__step" style="--tvg-i:2">
        <div class="pp-concept-tvg__card">
          <span class="pp-concept-tvg__num">03</span>
          <span class="pp-concept-tvg__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M4 13h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z" stroke-linejoin="round"/>
              <path d="M8 9c0-1.5 1-2 1-3.5M12 9c0-1.5 1-2 1-3.5M16 9c0-1.5 1-2 1-3.5" stroke-linecap="round"/>
            </svg>
          </span>
          <h3 class="pp-concept-tvg__step-title"><?php echo esc_html__('Nấu', 'saigonhoreca'); ?></h3>
          <p class="pp-concept-tvg__step-desc"><?php echo esc_html__('Chảo tay quay điện và tủ hấp công suất cao nấu lượng lớn, tiết kiệm nhân sự, đúng giờ mỗi ngày.', 'saigonhoreca'); ?></p>
        </div>
      </li>

      <li class="pp-concept-tvg__step" style="--tvg-i:3">
        <div class="pp-concept-tvg__card">
          <span class="pp-concept-tvg__num">04</span>
          <span class="pp-concept-tvg__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <rect x="3" y="9" width="18" height="11" rx="2"/>
              <path d="M3 13h18M9 9V6h6v3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          <h3 class="pp-concept-tvg__step-title"><?php echo esc_html__('Chia', 'saigonhoreca'); ?></h3>
          <p class="pp-concept-tvg__step-desc"><?php echo esc_html__('Bàn chia bố trí thuận tay giúp phân khu — chia suất nhanh, chính xác cho 500 phần ăn cùng lúc.', 'saigonhoreca'); ?></p>
        </div>
      </li>

      <li class="pp-concept-tvg__step" style="--tvg-i:4">
        <div class="pp-concept-tvg__card">
          <span class="pp-concept-tvg__num">05</span>
          <span class="pp-concept-tvg__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <rect x="4" y="3" width="16" height="18" rx="2"/>
              <circle cx="12" cy="13" r="4"/>
              <path d="M8 6h2" stroke-linecap="round"/>
            </svg>
          </span>
          <h3 class="pp-concept-tvg__step-title"><?php echo esc_html__('Rửa', 'saigonhoreca'); ?></h3>
          <p class="pp-concept-tvg__step-desc"><?php echo esc_html__('Máy rửa chén và kệ inox 3 tầng tối ưu không gian thẳng đứng, hạn chế bề mặt tiếp xúc, sạch sẽ tuyệt đối.', 'saigonhoreca'); ?></p>
        </div>
      </li>

    </ol>
  </div>
</section>
