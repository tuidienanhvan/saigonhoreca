<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #3: concept — split layout with design concept.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-concept-yzo scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-concept-yzo__grid">
      
      <!-- Cột chứa hình ảnh lớn (Trái) -->
      <div class="pp-concept-yzo__visual scroll-reveal">
        <div class="pp-image-container-shared">
          <div class="pp-concept-yzo__img-wrapper">
            <img src="<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-nghe-nhan-sushi-quay-bieu-dien.webp')); ?>" alt="<?php echo esc_attr__('Nghệ nhân biểu diễn chế tác nigiri tại quầy bếp mở Yuzu Omakase', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-concept-yzo__img-border"></div>
          </div>
          <div class="pp-image-caption-shared">
            <?php echo esc_html__('Đôi bàn tay tài hoa của nghệ nhân sushi nâng niu từng hạt cơm và lát cá, kết tinh tình yêu ẩm thực truyền thống trong một không gian đương đại.', 'saigonhoreca'); ?>
          </div>
        </div>
      </div>

      <!-- Cột văn bản dạng Glassmorphism Card cao cấp (Phải) -->
      <div class="pp-concept-yzo__card">
        <div class="pp-concept-yzo__card-content">
          <div class="pp-concept-yzo__kicker-group">
            <span class="pp-concept-yzo__tag"><?php echo esc_html__('Giải Pháp Thiết Kế', 'saigonhoreca'); ?></span>
            <span class="pp-concept-yzo__kicker"><?php echo esc_html__('Design Concept', 'saigonhoreca'); ?></span>
          </div>
          
          <h2 class="pp-concept-yzo__title">
            Ý Tưởng Tổ Chức 
            <span class="pp-concept-yzo__title-accent"><?php echo esc_html__('Không Gian Bếp', 'saigonhoreca'); ?></span>
          </h2>
          
          <div class="pp-concept-yzo__divider" aria-hidden="true"></div>
          
          <div class="pp-concept-yzo__body">
            <p><?php echo esc_html__('Dựa trên kinh nghiệm chuyên sâu về F&B cao cấp, Saigon Horeca thiết kế hệ thống bếp công nghiệp tối giản nhưng tối ưu tối đa về mặt công năng và vệ sinh khép kín.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Hệ thống thiết bị bếp inox được gia công và đo đạc chuẩn xác theo từng centimet, bố trí so le thông minh giữa khu vực sơ chế thô và khu vực phục vụ trực tiếp của Chef, đảm bảo luồng di chuyển không giao nhau và tối đa hóa năng suất.', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-concept-yzo__notes" aria-label="<?php echo esc_attr__('Điểm nhấn thiết kế', 'saigonhoreca'); ?>">
            <span class="pp-concept-yzo__note-item"><?php echo esc_html__('Tối ưu hóa line bếp', 'saigonhoreca'); ?></span>
            <span class="pp-concept-yzo__note-item"><?php echo esc_html__('Thiết bị inox tùy chỉnh', 'saigonhoreca'); ?></span>
            <span class="pp-concept-yzo__note-item"><?php echo esc_html__('Hỗ trợ vận hành nhịp độ cao', 'saigonhoreca'); ?></span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
