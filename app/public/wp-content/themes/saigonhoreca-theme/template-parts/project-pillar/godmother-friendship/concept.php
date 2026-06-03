<?php
/**
 * Project Pillar — godmother-friendship
 * Section #3: concept
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmf pp-section-gmf--alt pp-concept-gmf scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-grid-gmf pp-grid-gmf--reverse">
      <div class="pp-grid-gmf__text">
        <div class="pp-glass-card-gmf pp-concept-card-gmf">
          <!-- CAD decorations inside card -->
          <div class="pp-corner-ornament-gmf--tl" aria-hidden="true"></div>
          <div class="pp-corner-ornament-gmf--tr" aria-hidden="true"></div>
          <div class="pp-corner-ornament-gmf--bl" aria-hidden="true"></div>
          <div class="pp-corner-ornament-gmf--br" aria-hidden="true"></div>
          
          <svg class="pp-cad-bbox-card-gmf" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.3" preserveAspectRatio="none" aria-hidden="true">
            <rect x="1" y="1" width="98" height="98" stroke-dasharray="2 2"/>
          </svg>
          
          <div class="pp-badge-gmf">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 0.85rem; height: 0.85rem; margin-right: 0.25rem;"><path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/></svg>
            Concept thiết kế
          </div>
          
          <h2 class="pp-text-gmf__title">Gỗ & Terrazzo — Sự giao thoa chất liệu</h2>
          <div class="pp-text-gmf__divider" aria-hidden="true"></div>
          
          <div class="pp-text-gmf__body">
            <p>Vẫn giữ nguyên phong cách trẻ trung và hiện đại của cơ sở Lò Sũ, tuy nhiên tại Friendship Tower, chúng tôi bổ sung những đường nét thiết kế mềm mại hơn bằng gỗ sồi ấm áp và mặt bàn terrazzo tinh tế.</p>
            <p>Sự kết hợp này tạo nên một không gian vừa sang trọng, tinh tế vừa mang lại cảm giác thân thuộc cho giới văn phòng năng động tại Quận 1.</p>
          </div>
        </div>
      </div>
      
      <div class="pp-grid-gmf__media">
        <div class="pp-image-container-shared pp-concept-img-container-gmf">
          <!-- CAD Grid running mờ mờ ở nền của ảnh concept -->
          <div class="pp-cad-grid-bg-gmf" aria-hidden="true">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <pattern id="conceptGrid" width="40" height="40" patternUnits="userSpaceOnUse">
                  <rect width="40" height="40" fill="none"/>
                  <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="0.5" stroke-opacity="0.1"/>
                </pattern>
              </defs>
              <rect width="100%" height="100%" fill="url(#conceptGrid)" />
            </svg>
          </div>
          
          <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-noi-that-quay-bar-go-va-ban-terrazzo.jpg'); ?>" alt="<?php echo esc_attr__('Không gian quầy bar gỗ và bàn đá terrazzo tại GodMother Friendship', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          
          <div class="pp-image-caption-shared"><?php echo esc_html__('Không gian quầy bar kết hợp hài hòa giữa chất liệu gỗ ấm áp và mặt bàn đá terrazzo hiện đại, tạo nên điểm nhấn kiến trúc độc bản.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>
