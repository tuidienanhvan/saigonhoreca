<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #4: specs — specifications & staggered technical layout with CAD overlays.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$spec_items = [
  [
    'title' => esc_html__('Hệ Thống Hút Khói Eco-Ventilation', 'saigonhoreca'),
    'desc' => esc_html__('Tích hợp bộ lọc mùi than hoạt tính và cấp gió tươi tuần hoàn, đảm bảo quầy phục vụ Omakase khép kín luôn trong lành và không ám khói bụi.', 'saigonhoreca'),
    'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M17 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2zM9 9h6M9 13h6" stroke-linecap="round" stroke-linejoin="round"/></svg>'
  ],
  [
    'title' => esc_html__('Thiết Bị Lạnh Precision Hoshizaki', 'saigonhoreca'),
    'desc' => esc_html__('Hệ thống tủ lạnh trưng bày và bàn mát dưới quầy từ Hoshizaki Nhật Bản, giữ nhiệt độ lý tưởng tuyệt đối cho hải sản tươi sống.', 'saigonhoreca'),
    'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9h18M3 15h18M5 3v18M19 3v18" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2"/></svg>'
  ],
  [
    'title' => esc_html__('Quầy Inox Gia Công SUS304 Premium', 'saigonhoreca'),
    'desc' => esc_html__('Inox 304 dày 1.2mm xước hairline cao cấp chống bám bẩn, bo góc R10 thẩm mỹ, tích hợp hệ thống chậu rửa cảm ứng thông minh.', 'saigonhoreca'),
    'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16v16H4zM4 9h16M9 4v16" stroke-linecap="round" stroke-linejoin="round"/></svg>'
  ]
];
?>
<section class="pp-specs-yzo scroll-reveal">
  <!-- Ambient light glow for depth -->
  <div class="pp-specs-yzo__ambient-glow" aria-hidden="true"></div>
  
  <div class="pp-container-shared">
    <div class="pp-specs-yzo__grid">
      
      <!-- Left Column: Specs List -->
      <div class="pp-specs-yzo__content">
        <div class="pp-specs-yzo__header">
          <span class="pp-specs-yzo__eyebrow"><?php echo esc_html__('Artisanal Engineering Specs', 'saigonhoreca'); ?></span>
          <h2 class="pp-specs-yzo__title"><?php echo esc_html__('Hệ Thiết Bị M&E Đạt Chuẩn Tối Tân', 'saigonhoreca'); ?></h2>
          <div class="pp-specs-yzo__title-line"></div>
        </div>
        
        <div class="pp-specs-yzo__list">
          <?php foreach ($spec_items as $i => $item): ?>
            <div class="pp-specs-yzo__item">
              <div class="pp-specs-yzo__icon-wrapper">
                <div class="pp-specs-yzo__icon" aria-hidden="true">
                  <?php echo $item['icon']; ?>
                </div>
                <div class="pp-specs-yzo__icon-pulse"></div>
              </div>
              <div class="pp-specs-yzo__info">
                <h3 class="pp-specs-yzo__item-title">
                  <span class="pp-specs-yzo__item-num">0<?php echo $i + 1; ?>.</span>
                  <?php echo esc_html($item['title']); ?>
                </h3>
                <p class="pp-specs-yzo__item-desc"><?php echo esc_html($item['desc']); ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Right Column: Drafting Canvas with 3D Layering -->
      <div class="pp-specs-yzo__visual">
        <div class="pp-specs-yzo__sketchbook">
          
          <!-- Layer 1: Blueprint grid backdrop -->
          <div class="pp-specs-yzo__grid-overlay" aria-hidden="true">
            <svg viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Coordinate Grid lines -->
              <g opacity="0.05" stroke="var(--yzo-gold)" stroke-width="0.5">
                <path d="M50 0v400M100 0v400M150 0v400M200 0v400M250 0v400M300 0v400M350 0v400M400 0v400M450 0v400"/>
                <path d="M0 50h500M0 100h500M0 150h500M0 200h500M0 250h500M0 300h500M0 350h500"/>
              </g>
              
              <!-- Compass and angle measurement curves -->
              <g opacity="0.1" stroke="var(--yzo-gold)" stroke-width="0.8">
                <circle cx="250" cy="200" r="140" stroke-dasharray="3 6"/>
                <circle cx="250" cy="200" r="90"/>
                <circle cx="250" cy="200" r="30" stroke-dasharray="1 3"/>
                <path d="M70 200h360M250 20v360" stroke-dasharray="2 2"/>
                <path d="M144 94l212 212M144 306l212-212" stroke-width="0.5" stroke-dasharray="3 3"/>
              </g>
              
              <!-- Subtle Japanese Calligraphy watermark -->
              <text x="30" y="370" fill="var(--yzo-gold)" font-family="serif" font-size="26" opacity="0.06" letter-spacing="8">柚子極み</text>
              <text x="450" y="70" fill="var(--yzo-gold)" font-family="serif" font-size="18" opacity="0.04" transform="rotate(90 450 70)">こだわり</text>
            </svg>
          </div>

          <!-- Architect's signature metadata block -->
          <div class="pp-specs-yzo__blueprint-meta" aria-hidden="true">
            <div class="pp-specs-yzo__meta-row">
              <span class="pp-specs-yzo__meta-label">SYS_COORD:</span>
              <span class="pp-specs-yzo__meta-val">10.7769° N, 106.6994° E</span>
            </div>
            <div class="pp-specs-yzo__meta-row">
              <span class="pp-specs-yzo__meta-label">DWG_REF:</span>
              <span class="pp-specs-yzo__meta-val">SGH-YZO-ME-V4.0</span>
            </div>
            <div class="pp-specs-yzo__meta-row">
              <span class="pp-specs-yzo__meta-label">SCALE:</span>
              <span class="pp-specs-yzo__meta-val">1:20 (CRAFT_LAYOUT)</span>
            </div>
          </div>

          <!-- SVG Flow connector drawing line between base and overlay image targets -->
          <div class="pp-specs-yzo__flow-connector" aria-hidden="true">
            <svg viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M220 180 C 270 180, 290 120, 360 120" stroke="var(--yzo-gold)" stroke-width="1.2" stroke-dasharray="3 3" opacity="0.5"/>
              <circle cx="220" cy="180" r="3.5" fill="var(--yzo-gold)" opacity="0.7"/>
              <circle cx="360" cy="120" r="3.5" fill="var(--yzo-gold)" opacity="0.7"/>
              <text x="245" y="145" fill="var(--yzo-gold)" font-family="monospace" font-size="6" opacity="0.4" letter-spacing="1">PROCESS_LINK</text>
            </svg>
          </div>

          <!-- Layered Collage stack - Redesigned to stack cleanly without overlapping -->
          <div class="pp-specs-yzo__collage">
            
            <!-- Base Image: Caviar Container -->
            <div class="pp-specs-yzo__wrapper pp-specs-yzo__wrapper--base scroll-reveal">
              <figure class="pp-specs-yzo__fig">
                
                <!-- Dimension overlay overlaid directly on base image -->
                <div class="pp-specs-yzo__image-dimension" aria-hidden="true">
                  <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25 25h350" stroke="var(--yzo-gold)" stroke-width="0.8" stroke-dasharray="3 3" opacity="0.5"/>
                    <path d="M25 20v10M375 20v10" stroke="var(--yzo-gold)" stroke-width="1.2" opacity="0.7"/>
                    <text x="200" y="16" fill="var(--yzo-gold)" font-family="monospace" font-size="7" text-anchor="middle" letter-spacing="1" opacity="0.7">L = 800mm</text>
                    
                    <path d="M20 30v240" stroke="var(--yzo-gold)" stroke-width="0.8" stroke-dasharray="3 3" opacity="0.5"/>
                    <path d="M15 30h10M15 270h10" stroke="var(--yzo-gold)" stroke-width="1.2" opacity="0.7"/>
                    <text x="11" y="150" fill="var(--yzo-gold)" font-family="monospace" font-size="7" text-anchor="middle" transform="rotate(-90 11 150)" letter-spacing="1" opacity="0.7">H = 600mm</text>
                    
                    <circle cx="365" cy="265" r="5" stroke="var(--yzo-gold)" stroke-width="0.8" opacity="0.4"/>
                    <path d="M365 258v14M358 265h14" stroke="var(--yzo-gold)" stroke-width="0.5" opacity="0.4"/>
                    <text x="348" y="253" fill="var(--yzo-gold)" font-family="monospace" font-size="5" opacity="0.4">SEC_A-A</text>
                  </svg>
                </div>

                <img src="<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-hop-son-mai-trung-ca-tam-caviar.webp')); ?>" alt="<?php echo esc_attr__('Hộp sơn mài xanh ngọc đựng trứng cá tầm Caviar hảo hạng', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              </figure>
              
              <!-- Architect Note Card (Bottom) -->
              <div class="pp-specs-yzo__note-card-new">
                <div class="pp-specs-yzo__note-header-new">
                  <span class="pp-specs-yzo__note-title-new"><?php echo esc_html__('SPEC_FILE // CAVIAR_BOX_01', 'saigonhoreca'); ?></span>
                  <span class="pp-specs-yzo__note-status-new pp-specs-yzo__note-status-new--approved"><?php echo esc_html__('APPROVED', 'saigonhoreca'); ?></span>
                </div>
                <p class="pp-specs-yzo__note-desc-new">
                  <strong><?php echo esc_html__('Ngăn bảo ôn lạnh chuyên dụng:', 'saigonhoreca'); ?></strong>
                  <?php echo esc_html__('Hộp sơn mài xanh ngọc đựng trứng cá tầm Caviar được tích hợp ngăn bảo ôn lạnh chuyên dụng của Saigon Horeca, duy trì độ lạnh ổn định từ -2°C đến 2°C giúp bảo tồn trọn vẹn hương vị tinh khiết.', 'saigonhoreca'); ?>
                </p>
              </div>
            </div>
            
            <!-- Staggered Overlay Image: Sushi -->
            <div class="pp-specs-yzo__wrapper pp-specs-yzo__wrapper--overlay scroll-reveal">
              <figure class="pp-specs-yzo__fig">
                
                <!-- SVG Lens focus crosshair overlaid on overlay image -->
                <div class="pp-specs-yzo__lens-target" aria-hidden="true">
                  <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="25" stroke="var(--yzo-gold)" stroke-width="0.8" stroke-dasharray="2 3" opacity="0.6"/>
                    <circle cx="50" cy="50" r="8" stroke="var(--yzo-gold)" stroke-width="0.5" opacity="0.4"/>
                    <path d="M50 15v70M15 50h70" stroke="var(--yzo-gold)" stroke-width="0.5" opacity="0.5" stroke-dasharray="1 1"/>
                    <rect x="47" y="47" width="6" height="6" stroke="var(--yzo-gold)" stroke-width="0.8" opacity="0.7"/>
                    <text x="58" y="45" fill="var(--yzo-gold)" font-family="monospace" font-size="6" font-weight="bold" opacity="0.7">TGT_01</text>
                  </svg>
                </div>

                <img src="<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-can-canh-mon-an-nigiri-sushi.webp')); ?>" alt="<?php echo esc_attr__('Nigiri sushi cao cấp chế tác thủ công', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              </figure>
              
              <!-- Architect Note Card (Bottom) -->
              <div class="pp-specs-yzo__note-card-new">
                <div class="pp-specs-yzo__note-header-new">
                  <span class="pp-specs-yzo__note-title-new"><?php echo esc_html__('SPEC_FILE // NIGIRI_PLATE_02', 'saigonhoreca'); ?></span>
                  <span class="pp-specs-yzo__note-status-new pp-specs-yzo__note-status-new--ready"><?php echo esc_html__('READY', 'saigonhoreca'); ?></span>
                </div>
                <p class="pp-specs-yzo__note-desc-new">
                  <strong><?php echo esc_html__('Bày biện nghệ thuật trên đĩa đá:', 'saigonhoreca'); ?></strong>
                  <?php echo esc_html__('Sự sắp đặt tinh tế của tác phẩm Nigiri sushi phủ nhũ vàng và trứng cá Caviar cao cấp trên đĩa đá bazan đen tự nhiên, tạo điểm nhấn thị giác đẳng cấp hoàng gia.', 'saigonhoreca'); ?>
                </p>
              </div>
            </div>

          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>
