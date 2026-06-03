<?php
/**
 * Project Pillar — godmother-friendship
 * Section #4: specs
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmf pp-specs-gmf scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-grid-gmf">
      <div class="pp-grid-gmf__text">
        <div class="pp-glass-card-gmf pp-specs-card-gmf">
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
            Quy chuẩn kỹ thuật
          </div>
          
          <h2 class="pp-text-gmf__title">Thông số bếp nóng chuyên dụng</h2>
          <div class="pp-text-gmf__divider" aria-hidden="true"></div>
          
          <div class="pp-text-gmf__body">
            <p>Hệ thống line bếp nóng tại Friendship Tower được thiết kế và sản xuất 100% bằng inox 304 cao cấp, đáp ứng cường độ vận hành liên tục và các quy chuẩn khắt khe về an toàn vệ sinh thực phẩm.</p>
            
            <ul class="pp-specs-list-gmf">
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>Chất liệu chính:</strong> Inox 304 dày 1.0mm - 1.2mm gia công chấn gấp thủy lực thẩm mỹ cao.</span>
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>Bếp Âu Cobra:</strong> Tích hợp line bếp gas công nghiệp 6 họng Cobra cùng lò nướng Salamander treo tường tiện dụng.</span>
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>Module bảo quản:</strong> Hệ thống bàn đông, bàn mát kết hợp bàn sơ chế/soạn chia thực phẩm dưới quầy tiện lợi.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      
      <div class="pp-grid-gmf__media">
        <div class="pp-image-container-shared pp-specs-img-container-gmf">
          <!-- Bounding box SVG decoration with CAD indicators -->
          <svg class="pp-cad-bbox-gmf" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.5" preserveAspectRatio="none" aria-hidden="true">
            <rect x="1.5" y="1.5" width="97" height="97" stroke-dasharray="3 3"/>
            <line x1="0" y1="10" x2="5" y2="10"/>
            <line x1="10" y1="0" x2="10" y2="5"/>
            <line x1="90" y1="0" x2="90" y2="5"/>
            <line x1="95" y1="10" x2="100" y2="10"/>
            <text x="4" y="94" font-size="3.5" fill="currentColor" font-family="monospace">SYS_M&E / SCALE 1:25</text>
            <text x="58" y="8" font-size="3.5" fill="currentColor" font-family="monospace">COORD_GMF_Y18</text>
          </svg>
          
          <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-phoi-canh-3d-bep-nong-inox.webp'); ?>" alt="<?php echo esc_attr__('Bản vẽ phối cảnh 3D hệ thống bếp nóng inox công nghiệp tại GodMother Friendship', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ phối cảnh 3D hệ thống bếp nóng trung tâm bằng inox 304, bố trí khoa học theo tiêu chuẩn vận hành một chiều chuyên nghiệp.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>
