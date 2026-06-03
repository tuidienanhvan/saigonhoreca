<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #3: concept — challenges + interactive Before/After comparison sliders.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-solq7 pp-section-solq7--alt pp-concept-solq7 scroll-reveal">
  <div class="pp-solq7-ambient-glow pp-solq7-ambient-glow--bl pp-solq7-ambient-glow--ember" aria-hidden="true"></div>

  <div class="pp-container-shared">

    <div class="pp-split-solq7 pp-concept-solq7__row scroll-reveal">
      <div class="pp-split-solq7__body">
        <div class="pp-solq7-ornament" aria-hidden="true"></div>
        <span class="pp-text-solq7__tag"><span class="pp-text-solq7__tag-accent">//</span> <?php echo esc_html__('THÁCH THỨC', 'saigonhoreca'); ?></span>
        <div class="pp-text-solq7__divider pp-text-solq7__divider--dots" aria-hidden="true"></div>
        <h2 class="pp-text-solq7__title"><?php echo esc_html__('Thách thức & Khó khăn', 'saigonhoreca'); ?></h2>
        <div class="pp-text-solq7__body">
          <p class="pp-text-solq7__lead"><?php echo esc_html__('Khi tiếp nhận thông tin và yêu cầu từ chủ đầu tư, Saigon Horeca đã đối mặt với nhiều khó khăn và thử thách. Việc cải tạo lại khu bếp hiện hữu rộng hơn 70m2 yêu cầu phải bố trí lại các thiết bị đang còn hoạt động và phải đảm bảo đúng công năng hoạt động, quy trình chế biến của toàn bộ khu bếp.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-split-solq7__media scroll-reveal">
        <div class="pp-compare-solq7" data-compare>
          <div class="pp-compare-solq7__after">
            <img src="<?php echo sgh_img('renovate-sol-kitchen-bar-quan-7/Sol0D7-03.jpg'); ?>" alt="<?php echo esc_attr__('Khu bếp sau khi cải tạo hoàn thiện bởi Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <span class="pp-compare-solq7__chip pp-compare-solq7__chip--after"><?php echo esc_html__('SAU', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-compare-solq7__before" style="width:50%;">
            <img src="<?php echo sgh_img('renovate-sol-kitchen-bar-quan-7/Sol0D7-02.jpg'); ?>" alt="<?php echo esc_attr__('Hiện trạng khu bếp cũ trước khi cải tạo', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <span class="pp-compare-solq7__chip pp-compare-solq7__chip--before"><?php echo esc_html__('TRƯỚC', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-compare-solq7__line" style="left:50%;" aria-hidden="true"></div>
          <div class="pp-compare-solq7__handle" style="left:50%;" aria-hidden="true"></div>
          <input type="range" min="0" max="100" value="50" class="pp-compare-solq7__slider" aria-label="<?php echo esc_attr__('So sánh bếp trước và sau cải tạo', 'saigonhoreca'); ?>">
        </div>
        <span class="pp-compare-solq7__caption"><?php echo esc_html__('Khu vực nấu chính (Bếp cũ & Bếp mới sau cải tạo)', 'saigonhoreca'); ?></span>
      </div>
    </div>

    <div class="pp-split-solq7 pp-split-solq7--reverse pp-concept-solq7__row scroll-reveal">
      <div class="pp-split-solq7__body">
        <div class="pp-text-solq7__body">
          <blockquote class="pp-text-solq7__quote"><?php echo esc_html__('Khảo sát tỉ mỉ từng chi tiết, đánh giá tối ưu công năng để tích hợp thiết bị cũ và mới vào một hệ sinh thái vận hành trơn tru nhất.', 'saigonhoreca'); ?></blockquote>
          <p><?php echo esc_html__('Để làm được điều này, đội ngũ kỹ thuật của Saigon Horeca đã phải tiến hành khảo sát tỉ mỉ và chi tiết qua nhiều ngày, đánh giá từng thiết bị và lập kế hoạch chi tiết để tích hợp các thiết bị cũ vào hệ thống mới một cách hợp lý và hiệu quả nhất.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-split-solq7__media scroll-reveal">
        <div class="pp-compare-solq7" data-compare>
          <div class="pp-compare-solq7__after">
            <img src="<?php echo sgh_img('renovate-sol-kitchen-bar-quan-7/Sol0D7-05.jpg'); ?>" alt="<?php echo esc_attr__('Lắp đặt tủ đông mát và thiết bị inox khu rửa hoàn thiện', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <span class="pp-compare-solq7__chip pp-compare-solq7__chip--after"><?php echo esc_html__('SAU', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-compare-solq7__before" style="width:50%;">
            <img src="<?php echo sgh_img('renovate-sol-kitchen-bar-quan-7/Sol0D7-04.jpg'); ?>" alt="<?php echo esc_attr__('Hiện trạng khu rửa và lưu trữ cũ', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <span class="pp-compare-solq7__chip pp-compare-solq7__chip--before"><?php echo esc_html__('TRƯỚC', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-compare-solq7__line" style="left:50%;" aria-hidden="true"></div>
          <div class="pp-compare-solq7__handle" style="left:50%;" aria-hidden="true"></div>
          <input type="range" min="0" max="100" value="50" class="pp-compare-solq7__slider" aria-label="<?php echo esc_attr__('So sánh khu rửa trước và sau cải tạo', 'saigonhoreca'); ?>">
        </div>
        <span class="pp-compare-solq7__caption"><?php echo esc_html__('Khu sơ chế và hệ thống rửa, lưu trữ lạnh', 'saigonhoreca'); ?></span>
      </div>
    </div>

  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.pp-compare-solq7__slider').forEach(function (slider) {
    slider.addEventListener('input', function (e) {
      var v = e.target.value, box = e.target.closest('.pp-compare-solq7');
      var before = box.querySelector('.pp-compare-solq7__before');
      var handle = box.querySelector('.pp-compare-solq7__handle');
      var line = box.querySelector('.pp-compare-solq7__line');
      if (before) before.style.width = v + '%';
      if (handle) handle.style.left = v + '%';
      if (line) line.style.left = v + '%';
    });
  });
});
</script>
