<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #3: text & Image Comparison (Before-After)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-concept-section">
  <div class="pp-concept-container">
    
    <!-- BLOCK 1: Title & Paragraph 1 & Slider 1 -->
    <div class="sgh-concept-row">
      <div class="sgh-concept-col sgh-concept-col--text">
        <h2 class="sgh-concept-title">
          <span class="sgh-concept-title__accent">//</span>
          <?php echo esc_html__('Thách thức & Khó khăn', 'saigonhoreca'); ?>
        </h2>
        <div class="sgh-concept-body">
          <p class="sgh-concept-paragraph sgh-concept-paragraph--dropcap">
            <?php echo esc_html__('Khi tiếp nhận thông tin và yêu cầu từ chủ đầu tư, Saigon Horeca đã đối mặt với nhiều khó khăn và thử thách. Việc cải tạo lại khu bếp hiện hữu rộng hơn 70m2 yêu cầu phải bố trí lại các thiết bị đang còn hoạt động và phải đảm bảo đúng công năng hoạt động, quy trình chế biến của toàn bộ khu bếp.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
      <div class="sgh-concept-col sgh-concept-col--slider">
        <!-- BEFORE-AFTER SLIDER 1: Bếp Cũ vs Bếp Mới -->
        <div class="sgh-compare">
          <div class="sgh-compare__after">
            <img src="<?php echo sgh_img('2024/06/Sol0D7-03.jpg'); ?>" alt="After - Khu bếp sau khi cải tạo hoàn thiện bởi Saigon Horeca" loading="lazy" decoding="async">
          </div>
          <div class="sgh-compare__before" style="width: 50%;">
            <img src="<?php echo sgh_img('2024/06/Sol0D7-02.jpg'); ?>" alt="Before - Hiện trạng khu bếp cũ trước khi cải tạo" loading="lazy" decoding="async">
          </div>
          <div class="sgh-compare__line" style="left: 50%;"></div>
          <div class="sgh-compare__handle" style="left: 50%;"></div>
          <input type="range" min="0" max="100" value="50" class="sgh-compare__slider" aria-label="Compare Before and After images">
        </div>
        <span class="sgh-compare-label"><?php echo esc_html__('Khu vực nấu chính (Bếp cũ & Bếp mới sau cải tạo)', 'saigonhoreca'); ?></span>
      </div>
    </div>

    <!-- BLOCK 2: Slider 2 & Paragraph 2 (Reversed Grid) -->
    <div class="sgh-concept-row sgh-concept-row--reverse">
      <div class="sgh-concept-col sgh-concept-col--text">
        <div class="sgh-concept-body">
        <blockquote class="sgh-concept-quote">
            <?php echo esc_html__('Khảo sát tỉ mỉ từng chi tiết, đánh giá tối ưu công năng để tích hợp thiết bị cũ và mới vào một hệ sinh thái vận hành trơn tru nhất.', 'saigonhoreca'); ?>
          </blockquote>
          <p class="sgh-concept-paragraph">
            <?php echo esc_html__('Để làm được điều này, đội ngũ kỹ thuật của Saigon Horeca đã phải tiến hành khảo sát tỉ mỉ và chi tiết qua nhiều ngày, đánh giá từng thiết bị và lập kế hoạch chi tiết để tích hợp các thiết bị cũ vào hệ thống mới một cách hợp lý và hiệu quả nhất.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>

      <div class="sgh-concept-col sgh-concept-col--slider">
        <!-- BEFORE-AFTER SLIDER 2: Khu Rửa & Lưu Trữ Cũ vs Mới -->
        <div class="sgh-compare">
          <div class="sgh-compare__after">
            <img src="<?php echo sgh_img('2024/06/Sol0D7-05.jpg'); ?>" alt="After - Lắp đặt tủ đông mát và thiết bị inox khu rửa hoàn thiện" loading="lazy" decoding="async">
          </div>
          <div class="sgh-compare__before" style="width: 50%;">
            <img src="<?php echo sgh_img('2024/06/Sol0D7-04.jpg'); ?>" alt="Before - Hiện trạng khu rửa và lưu trữ cũ" loading="lazy" decoding="async">
          </div>
          <div class="sgh-compare__line" style="left: 50%;"></div>
          <div class="sgh-compare__handle" style="left: 50%;"></div>
          <input type="range" min="0" max="100" value="50" class="sgh-compare__slider" aria-label="Compare Before and After images">
        </div>
        <span class="sgh-compare-label"><?php echo esc_html__('Khu sơ chế và hệ thống rửa, lưu trữ lạnh', 'saigonhoreca'); ?></span>
      </div>
    </div>

  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.sgh-compare__slider').forEach(function(slider) {
    slider.addEventListener('input', function(e) {
      var value = e.target.value;
      var container = e.target.closest('.sgh-compare');
      var beforeImg = container.querySelector('.sgh-compare__before');
      var handle = container.querySelector('.sgh-compare__handle');
      var line = container.querySelector('.sgh-compare__line');
      
      if (beforeImg) beforeImg.style.width = value + '%';
      if (handle) handle.style.left = value + '%';
      if (line) line.style.left = value + '%';
    });
  });
});
</script>
