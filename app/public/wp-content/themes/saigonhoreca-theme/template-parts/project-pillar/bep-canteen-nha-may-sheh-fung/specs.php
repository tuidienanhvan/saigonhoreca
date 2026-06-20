<?php
/**
 * Project Pillar — bep-canteen-nha-may-sheh-fung
 * Section #5: specs — closing differentiator statement + capability grid
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-shf pp-specs-shf">
  <div class="pp-ambient-glow-shf pp-ambient-glow-shf--right" aria-hidden="true"></div>

  <div class="pp-container-shared">

    <header class="pp-specs-shf__header">
      <span class="pp-badge-shf">
        <span class="pp-badge-shf__dot" aria-hidden="true"></span>
        <?php echo esc_html__('Năng lực Saigon Horeca', 'saigonhoreca'); ?>
      </span>
      <h2 class="pp-text-shf__title"><?php echo esc_html__('Sự khác biệt làm nên đẳng cấp', 'saigonhoreca'); ?></h2>
      <span class="pp-text-shf__divider" aria-hidden="true"></span>
      <div class="pp-specs-shf__lead">
        <p><?php echo esc_html__('Sự khác biệt của Saigon Horeca nằm ở khả năng kết hợp giữa thiết kế tối ưu và công nghệ hiện đại, đáp ứng mọi tiêu chuẩn khắt khe về an toàn, vệ sinh và hiệu suất vận hành. Với bề dày kinh nghiệm trong lĩnh vực tư vấn và triển khai các hệ thống bếp công nghiệp cao cấp, Saigon Horeca không chỉ mang đến các giải pháp kỹ thuật xuất sắc mà còn đảm bảo từng công trình đạt độ hoàn thiện cao nhất.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Từ bếp nhà hàng, canteen cho đến quầy bar và coffee shop, Saigon Horeca luôn là sự lựa chọn hàng đầu của các chủ đầu tư, nhờ vào sự uy tín, chuyên nghiệp và tận tâm trong từng dự án. Sheh Fung không chỉ có một nhà máy hiện đại mà còn sở hữu một không gian bếp tối ưu, góp phần nâng cao hiệu quả làm việc và sự hài lòng của nhân viên.', 'saigonhoreca'); ?></p>
      </div>
    </header>

    <div class="pp-specs-shf__grid">

      <article class="pp-specs-shf__card">
        <div class="pp-specs-shf__card-top">
          <span class="pp-specs-shf__card-num">01</span>
          <span class="pp-specs-shf__card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M3 9l9-6 9 6v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9 21V12h6v9" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
        </div>
        <h3 class="pp-specs-shf__card-title"><?php echo esc_html__('Thiết kế tối ưu', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-shf__card-text"><?php echo esc_html__('Kết hợp thiết kế tối ưu cùng công nghệ hiện đại, đáp ứng mọi tiêu chuẩn khắt khe về an toàn, vệ sinh và hiệu suất vận hành.', 'saigonhoreca'); ?></p>
      </article>

      <article class="pp-specs-shf__card">
        <div class="pp-specs-shf__card-top">
          <span class="pp-specs-shf__card-num">02</span>
          <span class="pp-specs-shf__card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M12 3l8 4v5c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V7l8-4z" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
        </div>
        <h3 class="pp-specs-shf__card-title"><?php echo esc_html__('Hoàn thiện cao nhất', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-shf__card-text"><?php echo esc_html__('Bề dày kinh nghiệm tư vấn và triển khai hệ thống bếp công nghiệp cao cấp, đảm bảo từng công trình đạt độ hoàn thiện cao nhất.', 'saigonhoreca'); ?></p>
      </article>

      <article class="pp-specs-shf__card">
        <div class="pp-specs-shf__card-top">
          <span class="pp-specs-shf__card-num">03</span>
          <span class="pp-specs-shf__card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M3 21V8a1 1 0 011-1h4V3h8v4h4a1 1 0 011 1v13" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
        </div>
        <h3 class="pp-specs-shf__card-title"><?php echo esc_html__('Đa dạng mô hình F&B', 'saigonhoreca'); ?></h3>
        <p class="pp-specs-shf__card-text"><?php echo esc_html__('Từ bếp nhà hàng, canteen cho đến quầy bar và coffee shop – lựa chọn hàng đầu của các chủ đầu tư nhờ uy tín và sự tận tâm.', 'saigonhoreca'); ?></p>
      </article>

    </div>
  </div>
</section>
