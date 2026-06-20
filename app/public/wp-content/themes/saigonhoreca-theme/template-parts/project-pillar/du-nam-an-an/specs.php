<?php
/**
 * Project Pillar — du-nam-an-an
 * Section #5 (route) — specs: smoke/odor handling + hygiene standards.
 * Reverse split + blueprint-style media frame. @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$nan_specs = [
  [
    'tag'   => 'AIR',
    'title' => __('Hệ thống xử lý khói & mùi', 'saigonhoreca'),
    'desc'  => __('Trong môi trường Nursing Home, khói và mùi không đơn thuần là vấn đề kỹ thuật, mà là yếu tố ảnh hưởng trực tiếp đến chất lượng sống của người cao tuổi. Hệ thống được tính toán kỹ lưỡng theo công suất thực tế, đảm bảo không gian bếp luôn thông thoáng, hạn chế ám mùi sang khu vực sinh hoạt.', 'saigonhoreca'),
  ],
  [
    'tag'   => 'HYGIENE',
    'title' => __('An toàn & vệ sinh trong từng chi tiết', 'saigonhoreca'),
    'desc'  => __('Saigon Horeca đã tư vấn và triển khai hệ thống thoát nước, lọc rác và xử lý nước thải ngay tại khu vực bếp, giúp giảm thiểu nguy cơ ô nhiễm và duy trì môi trường sạch sẽ liên tục. Trong môi trường chăm sóc người cao tuổi, an toàn thực phẩm không chỉ là yêu cầu kỹ thuật, mà còn là cam kết nhân văn.', 'saigonhoreca'),
  ],
];
?>
<section class="pp-section-nan pp-specs-nan scroll-reveal">
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-split-nan pp-split-nan--reverse">

      <div class="pp-split-nan__media">
        <div class="pp-specs-nan__frame">
          <span class="pp-specs-nan__coord pp-specs-nan__coord--tl" aria-hidden="true">SYS / NURSING_HOME_KITCHEN</span>
          <span class="pp-specs-nan__coord pp-specs-nan__coord--br" aria-hidden="true">SAIGON HORECA</span>
          <div class="pp-image-container-shared">
            <img src="<?php echo esc_url(sgh_img('du-nam-an-an/nam-an-voi-xit-rua-ap-luc-bon-rua-inox.webp')); ?>" alt="<?php echo esc_attr__('Vòi xịt rửa áp lực tiền rửa lò xo inox tại khu bồn rửa chén bếp công nghiệp Nam An', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Vòi xịt tiền rửa và bồn rửa inox – thoát nước, vệ sinh tích hợp', 'saigonhoreca'); ?></div>
          </div>
        </div>
      </div>

      <div class="pp-split-nan__body">
        <header>
          <span class="pp-badge-nan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
              <path d="M9 3v2m6-2v2M5 8h14M6 8l1 11a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-11M10 12v5m4-5v5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php echo esc_html__('Tiêu chuẩn kỹ thuật', 'saigonhoreca'); ?>
          </span>
          <h2 class="pp-text-nan__title"><?php echo esc_html__('Yếu tố không thể thỏa hiệp', 'saigonhoreca'); ?></h2>
          <span class="pp-text-nan__divider" aria-hidden="true"></span>
        </header>

        <div class="pp-specs-nan__features">
          <?php foreach ($nan_specs as $spec): ?>
            <article class="pp-specs-nan__feature">
              <span class="pp-specs-nan__feature-tag"><?php echo esc_html($spec['tag']); ?></span>
              <h3><?php echo esc_html($spec['title']); ?></h3>
              <p><?php echo esc_html($spec['desc']); ?></p>
            </article>
          <?php endforeach; ?>
        </div>

        <p class="pp-specs-nan__note"><?php echo esc_html__('Với Nam An, Saigon Horeca chứng minh rằng: thiết kế bếp công nghiệp không chỉ là kỹ thuật, mà còn là sự thấu hiểu con người và mô hình.', 'saigonhoreca'); ?></p>
      </div>

    </div>
  </div>
</section>
