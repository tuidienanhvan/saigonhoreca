<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #6: gallery — metro image grid + equipment feature list + closing block.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hwa pp-gallery-section-hwa scroll-reveal">
  <!-- Lưới kỹ thuật chìm của bản vẽ CAD -->
  <div class="pp-section-hwa__decor-grid" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.08" opacity="0.06" style="position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none;">
      <path d="M 0 5 H 100 M 0 10 H 100 M 0 15 H 100 M 0 20 H 100 M 0 25 H 100 M 0 30 H 100 M 0 35 H 100 M 0 40 H 100 M 0 45 H 100 M 0 50 H 100 M 0 55 H 100 M 0 60 H 100 M 0 65 H 100 M 0 70 H 100 M 0 75 H 100 M 0 80 H 100 M 0 85 H 100 M 0 90 H 100 M 0 95 H 100" />
      <path d="M 5 0 V 100 M 10 0 V 100 M 15 0 V 100 M 20 0 V 100 M 25 0 V 100 M 30 0 V 100 M 35 0 V 100 M 40 0 V 100 M 45 0 V 100 M 50 0 V 100 M 55 0 V 100 M 60 0 V 100 M 65 0 V 100 M 70 0 V 100 M 75 0 V 100 M 80 0 V 100 M 85 0 V 100 M 90 0 V 100 M 95 0 V 100" />
    </svg>
  </div>
  
  <div class="pp-container-shared">
    <div class="pp-gallery-hwa__header scroll-reveal">
      <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('BẢN VẼ KỸ THUẬT', 'saigonhoreca'); ?></div>
      <h2 class="pp-text-hwa__title"><?php echo esc_html__('Mặt Bằng Bố Trí Thiết Bị Bếp Omakase', 'saigonhoreca'); ?></h2>
      <p class="pp-gallery-hwa__subtitle"><?php echo esc_html__('Sơ đồ bố trí tối ưu công thái học dòng chảy vận hành thực tế tại Heiwa Sushi.', 'saigonhoreca'); ?></p>
    </div>

    <!-- Sơ đồ Blueprint Vector SVG tinh xảo, thể hiện tay nghề kỹ thuật của Saigon Horeca -->
    <div class="pp-gallery-hwa__blueprint-wrapper scroll-reveal">
      <div class="pp-frame-corner pp-frame-corner--tl"></div>
      <div class="pp-frame-corner pp-frame-corner--tr"></div>
      <div class="pp-frame-corner pp-frame-corner--bl"></div>
      <div class="pp-frame-corner pp-frame-corner--br"></div>
      
      <div class="pp-gallery-hwa__blueprint-container">
        <svg viewBox="0 0 1200 650" fill="none" class="pp-gallery-hwa__blueprint-svg" xmlns="http://www.w3.org/2000/svg">
          <!-- BẢNG ĐỊNH NGHĨA GLOW VÀ GRADIENT -->
          <defs>
            <!-- Marker đầu mũi tên sắc nét -->
            <marker id="cad-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
              <path d="M 0 1.5 L 8 5 L 0 8.5 Z" fill="var(--hwa-gold, #d4af37)" />
            </marker>
            
            <!-- Glow nhẹ cho các luồng vận hành -->
            <filter id="glow-flow" x="-20%" y="-20%" width="140%" height="140%">
              <feGaussianBlur stdDeviation="3" result="blur" />
              <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>

            <!-- Glow đỏ hồng cho than Binchotan -->
            <filter id="glow-ember" x="-30%" y="-30%" width="160%" height="160%">
              <feGaussianBlur stdDeviation="4" result="blur" />
              <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>

            <!-- Pattern sọc chéo chìm cho các bề mặt thiết bị cơ khí -->
            <pattern id="cad-hatch" width="10" height="10" patternTransform="rotate(45 0 0)" patternUnits="userSpaceOnUse">
              <line x1="0" y1="0" x2="0" y2="10" stroke="rgba(255, 255, 255, 0.03)" stroke-width="1" />
            </pattern>
            
            <!-- Pattern chấm tổ ong cho lưới nướng -->
            <pattern id="grill-mesh" width="8" height="8" patternUnits="userSpaceOnUse">
              <path d="M 0 0 L 8 8 M 8 0 L 0 8" stroke="rgba(255, 255, 255, 0.25)" stroke-width="0.5" />
            </pattern>
          </defs>

          <!-- KHUNG VIỀN CAD TIÊU CHUẨN KỸ THUẬT -->
          <rect x="15" y="15" width="1170" height="620" rx="6" class="cad-border-outer" stroke="var(--hwa-line, rgba(212, 175, 55, 0.2))" stroke-width="1" stroke-dasharray="8 4"/>
          <rect x="25" y="25" width="1150" height="600" rx="4" class="cad-border-inner" stroke="var(--hwa-line, rgba(212, 175, 55, 0.35))" stroke-width="1.5"/>
          
          <!-- CHỮ THẬP ĐỊNH VỊ GÓC (CROSSHAIRS) -->
          <path d="M 20 35 H 50 M 35 20 V 50" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" opacity="0.3"/>
          <path d="M 1150 35 H 1180 M 1165 20 V 50" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" opacity="0.3"/>
          <path d="M 20 615 H 50 M 35 600 V 630" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" opacity="0.3"/>
          <path d="M 1150 615 H 1180 M 1165 600 V 630" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" opacity="0.3"/>

          <!-- HỆ TRỤC ĐỊNH VỊ CHÌM (CAD GRID SYSTEM) -->
          <g class="cad-grid-lines" stroke="rgba(255, 255, 255, 0.05)" stroke-width="0.5" stroke-dasharray="2 8">
            <!-- Trục dọc -->
            <line x1="100" y1="25" x2="100" y2="625" />
            <line x1="200" y1="25" x2="200" y2="625" />
            <line x1="300" y1="25" x2="300" y2="625" />
            <line x1="400" y1="25" x2="400" y2="625" />
            <line x1="500" y1="25" x2="500" y2="625" />
            <line x1="600" y1="25" x2="600" y2="625" />
            <line x1="700" y1="25" x2="700" y2="625" />
            <line x1="800" y1="25" x2="800" y2="625" />
            <line x1="900" y1="25" x2="900" y2="625" />
            <line x1="1000" y1="25" x2="1000" y2="625" />
            <line x1="1100" y1="25" x2="1100" y2="625" />
            <!-- Trục ngang -->
            <line x1="25" y1="80" x2="1175" y2="80" />
            <line x1="25" y1="190" x2="1175" y2="190" />
            <line x1="25" y1="300" x2="1175" y2="300" />
            <line x1="25" y1="410" x2="1175" y2="410" />
            <line x1="25" y1="520" x2="1175" y2="520" />
          </g>

          <!-- TƯỜNG BAO KHÔNG GIAN BẾP CHUYÊN NGHIỆP -->
          <g class="cad-walls" stroke="rgba(255, 255, 255, 0.4)" stroke-width="3">
            <!-- Viền tường chính -->
            <path d="M 50 50 L 1150 50 L 1150 500 L 50 500 Z" fill="rgba(255, 255, 255, 0.005)" />
            <!-- Hatch tường cách điệu -->
            <path d="M 45 45 L 1155 45 L 1155 505 L 45 505 Z" stroke="rgba(255, 255, 255, 0.1)" stroke-width="1" fill="none" stroke-dasharray="1 5"/>
          </g>
          
          <!-- HỆ THỐNG HÚT MÙI TRÊN CAO (OVERHEAD EXHAUST HOOD) -->
          <g class="cad-hood" opacity="0.45">
            <rect x="180" y="65" width="740" height="155" stroke="var(--hwa-gold, #d4af37)" stroke-width="1.25" stroke-dasharray="6 4" fill="none"/>
            <rect x="185" y="70" width="730" height="145" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" stroke-dasharray="2 2" fill="none" opacity="0.5"/>
            
            <rect x="440" y="100" width="220" height="24" rx="4" fill="var(--b2, #0d0d0d)" stroke="var(--hwa-gold, #d4af37)" stroke-width="1" opacity="0.9"/>
            <text x="550" y="116" fill="var(--hwa-gold, #d4af37)" font-size="10" font-family="monospace" text-anchor="middle" font-weight="bold" letter-spacing="0.05em">OVERHEAD EXHAUST HOOD SYSTEM</text>
          </g>

          <!-- KHU VỰC THIẾT BỊ BẾP CHUẨN BỊ (LINE A) -->
          
          <!-- 1. BẾP NƯỚNG THAN BINCHOTAN (CHARCOAL GRILL) -->
          <g transform="translate(190, 70)" class="cad-eq-group">
            <!-- Vỏ ngoài thiết bị bếp -->
            <rect width="170" height="130" rx="4" class="cad-eq-body" fill="rgba(255,255,255,0.02)" stroke="#ffffff" stroke-width="2"/>
            <rect x="5" y="5" width="160" height="120" rx="2" stroke="rgba(255,255,255,0.3)" stroke-width="1" fill="url(#cad-hatch)"/>
            
            <!-- Lòng bếp nướng và than hồng cách điệu -->
            <rect x="25" y="15" width="120" height="90" rx="3" stroke="#ffffff" stroke-width="1.5" fill="rgba(20,20,20,0.9)"/>
            <!-- Các đốm hồng hồng của than Binchotan rực lửa -->
            <g filter="url(#glow-ember)" opacity="0.8">
              <ellipse cx="50" cy="60" rx="12" ry="4" fill="#ff4d00" />
              <ellipse cx="85" cy="50" rx="15" ry="5" fill="#ff7a00" />
              <ellipse cx="120" cy="65" rx="10" ry="4" fill="#ff4d00" />
              <circle cx="70" cy="70" r="6" fill="#ff3300" />
              <circle cx="100" cy="65" r="5" fill="#ffaa00" />
            </g>
            <!-- Lưới nướng phủ lên trên -->
            <rect x="25" y="15" width="120" height="90" rx="3" fill="url(#grill-mesh)" stroke="rgba(255,255,255,0.5)" stroke-width="1"/>
            
            <!-- Khay kéo tro bếp ở mặt trước -->
            <line x1="45" y1="129" x2="125" y2="129" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
            
            <!-- Label thông tin được bọc nền che chắn cực kỳ rõ ràng -->
            <g transform="translate(85, 60)">
              <rect x="-65" y="-22" width="130" height="38" rx="4" fill="rgba(10,10,10,0.92)" stroke="var(--hwa-line, rgba(212, 175, 55, 0.2))" stroke-width="1" />
              <text x="0" y="-8" fill="#ffffff" font-size="9.5" font-family="monospace" text-anchor="middle" font-weight="bold" letter-spacing="0.05em">CHARCOAL GRILL</text>
              <text x="0" y="8" fill="var(--hwa-gold, #d4af37)" font-size="8.5" font-family="monospace" text-anchor="middle">(BẾP NƯỚNG THAN)</text>
            </g>
          </g>

          <!-- 2. BÀN LẠNH THAO TÁC HOSHIZAKI (HOSHIZAKI UNDERCOUNTER FRIDGE) -->
          <g transform="translate(380, 70)" class="cad-eq-group">
            <rect width="320" height="130" rx="4" class="cad-eq-body" fill="rgba(255,255,255,0.02)" stroke="#ffffff" stroke-width="2"/>
            <rect x="5" y="5" width="310" height="120" rx="2" stroke="rgba(255,255,255,0.3)" stroke-width="1" fill="none"/>
            
            <!-- 3 Ngăn tủ lạnh với tay nắm âm -->
            <!-- Cánh 1 -->
            <rect x="15" y="15" width="90" height="90" rx="2" stroke="rgba(255,255,255,0.5)" stroke-width="1" fill="none"/>
            <line x1="25" y1="25" x2="95" y2="25" stroke="var(--hwa-gold, #d4af37)" stroke-width="2.5" stroke-linecap="round" />
            <!-- Cánh 2 -->
            <rect x="115" y="15" width="90" height="90" rx="2" stroke="rgba(255,255,255,0.5)" stroke-width="1" fill="none"/>
            <line x1="125" y1="25" x2="195" y2="25" stroke="var(--hwa-gold, #d4af37)" stroke-width="2.5" stroke-linecap="round" />
            <!-- Cánh 3 -->
            <rect x="215" y="15" width="90" height="90" rx="2" stroke="rgba(255,255,255,0.5)" stroke-width="1" fill="none"/>
            <line x1="225" y1="25" x2="295" y2="25" stroke="var(--hwa-gold, #d4af37)" stroke-width="2.5" stroke-linecap="round" />
            
            <!-- Bảng điều khiển điện tử LED đỏ tinh tế -->
            <rect x="270" y="112" width="22" height="10" fill="#222" stroke="rgba(255,255,255,0.2)" stroke-width="0.5"/>
            <text x="281" y="120" fill="#ff3300" font-size="7.5" font-family="monospace" text-anchor="middle" font-weight="bold">3.0°C</text>
            
            <!-- Label thông tin được bọc nền che chắn cực kỳ rõ ràng -->
            <g transform="translate(160, 60)">
              <rect x="-105" y="-22" width="210" height="38" rx="4" fill="rgba(10,10,10,0.92)" stroke="var(--hwa-line, rgba(212, 175, 55, 0.2))" stroke-width="1" />
              <text x="0" y="-8" fill="#ffffff" font-size="9.5" font-family="monospace" text-anchor="middle" font-weight="bold" letter-spacing="0.03em">HOSHIZAKI WORKTABLE FRIDGE</text>
              <text x="0" y="8" fill="var(--hwa-gold, #d4af37)" font-size="8.5" font-family="monospace" text-anchor="middle">(BÀN MÁT HOSHIZAKI 1.8M)</text>
            </g>
          </g>

          <!-- 3. CHẬU RỬA INOX CÓ VÒI RÚT ERGONOMIC (PREP SINK) -->
          <g transform="translate(720, 70)" class="cad-eq-group">
            <rect width="180" height="130" rx="4" class="cad-eq-body" fill="rgba(255,255,255,0.02)" stroke="#ffffff" stroke-width="2"/>
            <rect x="5" y="5" width="170" height="120" rx="2" stroke="rgba(255,255,255,0.3)" stroke-width="1" fill="none"/>
            
            <!-- Lòng chậu rửa nghiêng dốc thoát nước -->
            <rect x="20" y="20" width="140" height="90" rx="6" stroke="#ffffff" stroke-width="1.5" fill="rgba(255,255,255,0.01)"/>
            <!-- Các nét kẻ thể hiện độ dốc dồn nước về hố rác -->
            <line x1="20" y1="20" x2="90" y2="65" stroke="rgba(255,255,255,0.15)" stroke-width="0.75" />
            <line x1="160" y1="20" x2="90" y2="65" stroke="rgba(255,255,255,0.15)" stroke-width="0.75" />
            <line x1="20" y1="110" x2="90" y2="65" stroke="rgba(255,255,255,0.15)" stroke-width="0.75" />
            <line x1="160" y1="110" x2="90" y2="65" stroke="rgba(255,255,255,0.15)" stroke-width="0.75" />
            
            <!-- Hố rác rốn xả inox -->
            <circle cx="90" cy="65" r="14" stroke="#ffffff" stroke-width="1" fill="rgba(10,10,10,0.8)"/>
            <circle cx="90" cy="65" r="6" stroke="rgba(255,255,255,0.5)" stroke-width="0.75" fill="none"/>
            <circle cx="90" cy="65" r="2" fill="#ffffff"/>
            
            <!-- Vòi nước cổ ngỗng (Gooseneck Faucet) cong nghệ thuật vẽ bằng Bezier -->
            <path d="M 90 5 L 90 22 C 90 28, 80 28, 80 22" stroke="var(--hwa-gold, #d4af37)" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <circle cx="90" cy="5" r="3.5" fill="var(--hwa-gold, #d4af37)"/>
            
            <!-- Label thông tin được bọc nền che chắn cực kỳ rõ ràng -->
            <g transform="translate(90, 75)">
              <rect x="-65" y="-12" width="130" height="24" rx="4" fill="rgba(10,10,10,0.92)" stroke="var(--hwa-line, rgba(212, 175, 55, 0.2))" stroke-width="1" />
              <text x="0" y="3" fill="#ffffff" font-size="9" font-family="monospace" text-anchor="middle" font-weight="bold">PREP SINK (CHẬU RỬA)</text>
            </g>
          </g>

          <!-- DÒNG CHẢY HÀNH TRÌNH VẬN HÀNH (ERGONOMICS OPERATION FLOW) -->
          <g stroke="var(--hwa-gold, #d4af37)" stroke-width="1.75" fill="none" opacity="0.85" filter="url(#glow-flow)" stroke-dasharray="6 4">
            <!-- Hướng đầu bếp di chuyển thao tác -->
            <!-- 1. Từ Bàn mát tủ mát -> Thớt chuẩn bị và biểu diễn -->
            <path d="M 540 215 C 540 290, 480 290, 480 375" marker-end="url(#cad-arrow)" />
            <!-- 2. Từ Chậu rửa nguyên liệu sạch -> Thớt và tủ lạnh -->
            <path d="M 810 215 C 810 295, 680 295, 680 375" marker-end="url(#cad-arrow)" />
            <!-- 3. Từ Bếp nướng than -> Quầy biểu diễn nóng hổi phục vụ -->
            <path d="M 280 215 C 280 295, 360 295, 360 375" marker-end="url(#cad-arrow)" />
          </g>

          <!-- QUẦY BIỂU DIỄN OMAKASE NGHỆ THUẬT (CHEF COUNTER - LINE B) -->
          <g transform="translate(140, 400)" class="cad-eq-group">
            <!-- Mặt quầy đá/gỗ cong nghệ thuật sang trọng -->
            <path d="M 50 0 L 850 0 C 910 0, 950 35, 950 90 L 950 120 L 870 120 L 870 80 C 870 55, 845 30, 800 30 L 50 30 Z" fill="rgba(212, 175, 55, 0.03)" stroke="#ffffff" stroke-width="2.5"/>
            <!-- Chỉ viền đá bo cạnh nghệ thuật -->
            <path d="M 50 5 L 845 5 C 895 5, 940 35, 940 85 L 940 120" stroke="rgba(255, 255, 255, 0.2)" stroke-width="0.75" fill="none"/>
            
            <!-- Thớt gỗ Sashimi chuyên dụng của Chef trước các ghế ăn -->
            <rect x="180" y="5" width="80" height="20" rx="1" stroke="rgba(255,255,255,0.4)" stroke-width="1" fill="rgba(255,255,255,0.05)"/>
            <rect x="500" y="5" width="80" height="20" rx="1" stroke="rgba(255,255,255,0.4)" stroke-width="1" fill="rgba(255,255,255,0.05)"/>
            
            <!-- GHẾ NGỒI THỰC KHÁCH (STOOLS) VẼ CHI TIẾT ĐỒNG TÂM ELEGANT -->
            <!-- Ghế 1 -->
            <g transform="translate(120, 75)">
              <circle cx="0" cy="0" r="23" stroke="var(--hwa-gold, #d4af37)" stroke-width="1.5" stroke-dasharray="3 2" fill="rgba(10,10,10,0.6)"/>
              <circle cx="0" cy="0" r="18" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" fill="none"/>
              <text x="0" y="3" fill="#ffffff" font-size="8.5" font-family="monospace" text-anchor="middle" font-weight="bold">STOOL</text>
            </g>
            
            <!-- Ghế 2 -->
            <g transform="translate(280, 75)">
              <circle cx="0" cy="0" r="23" stroke="var(--hwa-gold, #d4af37)" stroke-width="1.5" stroke-dasharray="3 2" fill="rgba(10,10,10,0.6)"/>
              <circle cx="0" cy="0" r="18" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" fill="none"/>
              <text x="0" y="3" fill="#ffffff" font-size="8.5" font-family="monospace" text-anchor="middle" font-weight="bold">STOOL</text>
            </g>
            
            <!-- Ghế 3 -->
            <g transform="translate(440, 75)">
              <circle cx="0" cy="0" r="23" stroke="var(--hwa-gold, #d4af37)" stroke-width="1.5" stroke-dasharray="3 2" fill="rgba(10,10,10,0.6)"/>
              <circle cx="0" cy="0" r="18" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" fill="none"/>
              <text x="0" y="3" fill="#ffffff" font-size="8.5" font-family="monospace" text-anchor="middle" font-weight="bold">STOOL</text>
            </g>
            
            <!-- Ghế 4 -->
            <g transform="translate(600, 75)">
              <circle cx="0" cy="0" r="23" stroke="var(--hwa-gold, #d4af37)" stroke-width="1.5" stroke-dasharray="3 2" fill="rgba(10,10,10,0.6)"/>
              <circle cx="0" cy="0" r="18" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" fill="none"/>
              <text x="0" y="3" fill="#ffffff" font-size="8.5" font-family="monospace" text-anchor="middle" font-weight="bold">STOOL</text>
            </g>
            
            <!-- Ghế 5 -->
            <g transform="translate(760, 75)">
              <circle cx="0" cy="0" r="23" stroke="var(--hwa-gold, #d4af37)" stroke-width="1.5" stroke-dasharray="3 2" fill="rgba(10,10,10,0.6)"/>
              <circle cx="0" cy="0" r="18" stroke="var(--hwa-gold, #d4af37)" stroke-width="0.75" fill="none"/>
              <text x="0" y="3" fill="#ffffff" font-size="8.5" font-family="monospace" text-anchor="middle" font-weight="bold">STOOL</text>
            </g>

            <!-- Quầy gỗ thao tác trực tiếp phía trong của Chef -->
            <rect x="50" y="-45" width="770" height="45" fill="rgba(255,255,255,0.01)" stroke="rgba(255, 255, 255, 0.4)" stroke-dasharray="3 3" stroke-width="1.25"/>
            
            <!-- Nhãn quầy bọc nền cực đẹp -->
            <g transform="translate(435, -22)">
              <rect x="-240" y="-12" width="480" height="24" rx="4" fill="rgba(10,10,10,0.92)" stroke="var(--hwa-line, rgba(212, 175, 55, 0.2))" stroke-width="1"/>
              <text x="0" y="4" fill="#ffffff" font-size="10.5" font-family="monospace" text-anchor="middle" font-weight="bold" letter-spacing="0.02em">CHEF'S OPEN SHOWCASE TABLE (QUẦY BIỂU DIỄN OMAKASE CHẾ TÁC THỦ CÔNG)</text>
            </g>
          </g>

          <!-- HỆ ĐƯỜNG KÍCH THƯỚC KỸ THUẬT RÕ RÀNG (TECHNICAL DIMENSION LINES) -->
          <g stroke="var(--hwa-gold, #d4af37)" stroke-width="1" opacity="0.8">
            <!-- 1. Kích thước ngang tổng quầy Omakase (8800 mm) -->
            <line x1="190" y1="545" x2="1010" y2="545" />
            <!-- Vạch giới hạn hai đầu kiểu CAD kiến trúc -->
            <line x1="190" y1="538" x2="190" y2="552" />
            <line x1="190" y1="540" x2="198" y2="548" stroke-width="1.5" /> <!-- Chéo kiến trúc -->
            <line x1="1010" y1="538" x2="1010" y2="552" />
            <line x1="1002" y1="540" x2="1010" y2="548" stroke-width="1.5" />
            
            <!-- Chữ số kích thước bọc nền -->
            <g transform="translate(600, 545)">
              <rect x="-35" y="-10" width="70" height="20" rx="3" fill="var(--b2, #0d0d0d)" stroke="var(--hwa-line, rgba(212, 175, 55, 0.2))" stroke-width="0.75"/>
              <text x="0" y="4" fill="var(--hwa-gold, #d4af37)" font-size="10.5" font-family="monospace" text-anchor="middle" font-weight="bold">8800 mm</text>
            </g>

            <!-- 2. Kích thước rộng lối đi kỹ thuật (1100 mm) -->
            <line x1="540" y1="205" x2="540" y2="350" />
            <line x1="533" y1="205" x2="547" y2="205" />
            <line x1="536" y1="201" x2="544" y2="209" stroke-width="1.5" />
            <line x1="533" y1="350" x2="547" y2="350" />
            <line x1="536" y1="346" x2="544" y2="354" stroke-width="1.5" />
            
            <!-- Chữ số kích thước bọc nền -->
            <g transform="translate(540, 277)">
              <rect x="-10" y="-30" width="20" height="60" rx="3" fill="var(--b2, #0d0d0d)" stroke="var(--hwa-line, rgba(212, 175, 55, 0.2))" stroke-width="0.75"/>
              <!-- Xoay text đứng đúng chuẩn CAD -->
              <text x="0" y="4" fill="var(--hwa-gold, #d4af37)" font-size="10.5" font-family="monospace" text-anchor="middle" font-weight="bold" transform="rotate(-90 0 0)">1100 mm</text>
            </g>
          </g>

          <!-- TIÊU ĐỀ BẢN VẼ & KHUNG TÊN TIÊU CHUẨN (TITLE BLOCK) -->
          <g transform="translate(930, 70)">
            <!-- Nền khung tên ngăn cách lưới CAD chìm -->
            <rect width="210" height="240" fill="var(--b2, #0d0d0d)" stroke="var(--hwa-line, rgba(212, 175, 55, 0.4))" stroke-width="1.5" opacity="0.95"/>
            <!-- Các đường phân cách ngăn ô -->
            <line x1="0" y1="48" x2="210" y2="48" stroke="var(--hwa-line, rgba(212, 175, 55, 0.3))" stroke-width="1" />
            <line x1="0" y1="96" x2="210" y2="96" stroke="var(--hwa-line, rgba(212, 175, 55, 0.3))" stroke-width="1" />
            <line x1="0" y1="144" x2="210" y2="144" stroke="var(--hwa-line, rgba(212, 175, 55, 0.3))" stroke-width="1" />
            <line x1="0" y1="192" x2="210" y2="192" stroke="var(--hwa-line, rgba(212, 175, 55, 0.3))" stroke-width="1" />

            <!-- Nội dung chi tiết trong khung tên -->
            <!-- Dự án -->
            <text x="12" y="22" fill="rgba(255,255,255,0.4)" font-size="7.5" font-family="monospace">PROJECT:</text>
            <text x="12" y="36" fill="#ffffff" font-size="10.5" font-family="monospace" font-weight="bold" letter-spacing="0.03em">HEIWA OMAKASE</text>
            
            <!-- Tên bản vẽ -->
            <text x="12" y="70" fill="rgba(255,255,255,0.4)" font-size="7.5" font-family="monospace">DWG TITLE:</text>
            <text x="12" y="84" fill="#ffffff" font-size="10.5" font-family="monospace" font-weight="bold" letter-spacing="0.03em">KITCHEN PLAN</text>
            
            <!-- Tỷ lệ và Đơn vị -->
            <text x="12" y="114" fill="rgba(255,255,255,0.4)" font-size="7.5" font-family="monospace">SCALE:</text>
            <text x="70" y="114" fill="#ffffff" font-size="9" font-family="monospace" font-weight="bold">1 : 50</text>
            <text x="12" y="130" fill="rgba(255,255,255,0.4)" font-size="7.5" font-family="monospace">UNIT:</text>
            <text x="70" y="130" fill="#ffffff" font-size="9" font-family="monospace" font-weight="bold">METRIC (mm)</text>

            <!-- Ngày tháng & Người vẽ -->
            <text x="12" y="162" fill="rgba(255,255,255,0.4)" font-size="7.5" font-family="monospace">DATE:</text>
            <text x="70" y="162" fill="#ffffff" font-size="9" font-family="monospace" font-weight="bold">2026-06-02</text>
            <text x="12" y="178" fill="rgba(255,255,255,0.4)" font-size="7.5" font-family="monospace">DRAWN BY:</text>
            <text x="70" y="178" fill="#ffffff" font-size="9" font-family="monospace" font-weight="bold">SGH ENG DEPT</text>
            
            <!-- Thương hiệu Saigon Horeca -->
            <text x="105" y="218" fill="var(--hwa-gold, #d4af37)" font-size="12" font-family="monospace" font-weight="bold" text-anchor="middle" letter-spacing="0.15em">SAIGON HORECA</text>
          </g>
        </svg>
      </div>
    </div>
  </div>
</section>

<section class="pp-section-hwa scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-gallery-hwa__feature-card">
      <div class="pp-hwa-ornament pp-hwa-ornament--right" aria-hidden="true"></div>
      <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('KỸ THUẬT VẬN HÀNH', 'saigonhoreca'); ?></div>
      <h2 class="pp-text-hwa__title"><?php echo esc_html__('Hệ Thống Thiết Bị Hoshizaki & Cơ Khí Inox Cao Cấp', 'saigonhoreca'); ?></h2>
      <div class="pp-text-hwa__body-grid">
        <div class="pp-text-hwa__desc">
          <p><?php echo esc_html__('Trái tim vận hành của bếp Heiwa Sushi nằm ở các giải pháp thiết bị lạnh chất lượng cao từ thương hiệu Hoshizaki Nhật Bản. Tủ đông và tủ mát bảo quản nguyên liệu tươi sống được tính toán vị trí lắp đặt chính xác, tối ưu hóa tối đa lối đi và tầm với của đầu bếp trong giờ cao điểm.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Hệ thống giá treo tường, chậu rửa, bàn inox 304 cao cấp do Saigon Horeca gia công sản xuất riêng cho dự án đều đạt tiêu chuẩn cơ khí hoàn mỹ. Mỗi góc chấn ép, vết hàn khí trơ đều được xử lý mài bóng mịn, triệt tiêu khe hở tích tụ vi khuẩn để đảm bảo tiêu chí Omakase Fresh khắt khe.', 'saigonhoreca'); ?></p>
        </div>
        <div class="pp-gallery-hwa__features-list">
          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon pp-gallery-hwa__feature-icon--svg" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="pp-gallery-hwa__svg-icon">
                <!-- Thân tủ chính -->
                <rect x="9" y="4" width="22" height="32" rx="1.5" />
                <!-- Đường phân chia 4 cánh (cột dọc giữa và nét ngang chia đôi) -->
                <line x1="20" y1="4" x2="20" y2="36" />
                <line x1="9" y1="20" x2="31" y2="20" />
                <!-- Bản lề (bên trái và phải) -->
                <rect x="9" y="8" width="0.75" height="2" fill="currentColor" stroke="none" />
                <rect x="9" y="24" width="0.75" height="2" fill="currentColor" stroke="none" />
                <rect x="30.25" y="8" width="0.75" height="2" fill="currentColor" stroke="none" />
                <rect x="30.25" y="24" width="0.75" height="2" fill="currentColor" stroke="none" />
                <!-- Tay nắm chìm dạng thanh dài sát đường dọc giữa -->
                <line x1="18.5" y1="8" x2="18.5" y2="16" />
                <line x1="21.5" y1="8" x2="21.5" y2="16" />
                <line x1="18.5" y1="24" x2="18.5" y2="32" />
                <line x1="21.5" y1="24" x2="21.5" y2="32" />
                <!-- Màn hình điện tử hiển thị LED nhỏ ở trên cùng -->
                <rect x="17" y="5.5" width="6" height="2.5" rx="0.5" fill="currentColor" stroke="none" opacity="0.3" />
                <circle cx="20" cy="6.75" r="0.4" fill="var(--gold)" stroke="none" />
                <!-- Chân tủ cơ khí tăng chỉnh chiều cao -->
                <line x1="13" y1="36" x2="13" y2="38.5" stroke-width="1.75" />
                <line x1="27" y1="36" x2="27" y2="38.5" stroke-width="1.75" />
                <line x1="11" y1="38.5" x2="15" y2="38.5" />
                <line x1="25" y1="38.5" x2="29" y2="38.5" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Tủ Lạnh Hoshizaki', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Bảo quản hải sản sashimi ở nhiệt độ tối ưu nhất.', 'saigonhoreca'); ?></span>
            </span>
          </div>
          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon pp-gallery-hwa__feature-icon--svg" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="pp-gallery-hwa__svg-icon">
                <!-- Mặt bàn 3D góc isometric -->
                <polygon points="6,15 22,9 34,15 18,21" />
                <!-- Độ dày mặt bàn (chấn gấp) -->
                <line x1="6" y1="15" x2="6" y2="17.5" />
                <line x1="18" y1="21" x2="18" y2="23.5" />
                <line x1="34" y1="15" x2="34" y2="17.5" />
                <polygon points="6,17.5 18,23.5 34,17.5 18,21" fill="currentColor" opacity="0.1" stroke="none" />
                <!-- Thành chắn sau (backsplash) ngăn vết bẩn -->
                <polygon points="6,15 22,9 22,7 6,13" fill="currentColor" opacity="0.25" />
                <line x1="6" y1="13" x2="6" y2="15" />
                <line x1="22" y1="7" x2="22" y2="9" stroke-width="1.5" />
                <line x1="6" y1="13" x2="22" y2="7" />
                <!-- Chân bàn giằng tròn cơ khí -->
                <!-- Chân trước trái -->
                <line x1="8" y1="17.5" x2="8" y2="32.5" />
                <!-- Chân trước phải -->
                <line x1="18" y1="23.5" x2="18" y2="34.5" />
                <!-- Chân sau phải -->
                <line x1="32" y1="17.5" x2="32" y2="31.5" />
                <!-- Chân sau trái (nét đứt vì bị che khuất) -->
                <line x1="22" y1="10" x2="22" y2="24" stroke-dasharray="2 2" />
                <!-- Thanh giằng dưới chịu lực dạng chữ H -->
                <line x1="11" y1="27.5" x2="27.5" y2="22" />
                <!-- Tấm đệm chân bàn tròn xoay tăng giảm chiều cao -->
                <circle cx="8" cy="33.5" r="0.75" fill="currentColor" stroke="none" />
                <circle cx="18" cy="35.5" r="0.75" fill="currentColor" stroke="none" />
                <circle cx="32" cy="32.5" r="0.75" fill="currentColor" stroke="none" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Inox 304 Chuẩn Sạch', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Kháng khuẩn tuyệt đối, chống gỉ sét và dễ vệ sinh.', 'saigonhoreca'); ?></span>
            </span>
          </div>
          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon pp-gallery-hwa__feature-icon--svg" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="pp-gallery-hwa__svg-icon">
                <!-- Tường (mặt phẳng lưng làm nền bằng 2 nét gạch chéo rất nhẹ) -->
                <line x1="4" y1="24" x2="8" y2="24" opacity="0.3" />
                <line x1="32" y1="24" x2="36" y2="24" opacity="0.3" />
                <!-- Ke giá đỡ chéo chịu lực (Brackets) bắt vít vào tường -->
                <polygon points="10,14 10,24 13.5,14" fill="currentColor" opacity="0.15" />
                <polygon points="30,14 30,24 26.5,14" fill="currentColor" opacity="0.15" />
                <!-- Mặt giá treo nằm ngang (dày 2 lớp) -->
                <rect x="6" y="11.5" width="28" height="2.5" rx="0.5" />
                <line x1="6" y1="14" x2="34" y2="14" />
                <!-- Thanh treo ống inox phía dưới -->
                <line x1="8" y1="18.5" x2="32" y2="18.5" stroke-dasharray="1.5 1.5" />
                <!-- Móc chữ S treo nồi chảo cách điệu -->
                <!-- Móc 1 + xoong sâu lòng -->
                <path d="M 13,18.5 C 13,20.5 12,21.5 11,21.5 C 10,21.5 10,19.5 11,18.5 C 12,17.5 12,16.5 11,16.5" />
                <path d="M 11,21.5 L 11,24" />
                <circle cx="11" cy="27" r="3" fill="currentColor" opacity="0.2" />
                <line x1="6" y1="27" x2="11" y2="27" /> <!-- Tay cầm chảo -->
                <!-- Móc 2 + nồi chảo inox tròn -->
                <path d="M 20,18.5 C 20,20.5 19,21.5 18,21.5 C 17,21.5 17,19.5 18,18.5 C 19,17.5 19,16.5 18,16.5" />
                <path d="M 18,21.5 L 18,25" />
                <rect x="15" y="25" width="6" height="5" rx="1" />
                <line x1="14" y1="27" x2="15" y2="27" />
                <line x1="21" y1="27" x2="22" y2="27" />
                <!-- Móc 3 + muôi/vá inox -->
                <path d="M 27,18.5 C 27,20.5 26,21.5 25,21.5 C 24,21.5 24,19.5 25,18.5 C 26,17.5 26,16.5 25,16.5" />
                <path d="M 25,21.5 L 25,28" />
                <path d="M 23.5,28 C 23.5,29.5 26.5,29.5 26.5,28 Z" fill="currentColor" opacity="0.3" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Giá Treo Tiện Lợi', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Tối ưu hóa không gian, tăng diện tích thao tác thực tế.', 'saigonhoreca'); ?></span>
            </span>
          </div>
          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon pp-gallery-hwa__feature-icon--svg" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="pp-gallery-hwa__svg-icon">
                <!-- Viền mặt trên của chậu rửa phẳng -->
                <rect x="6" y="15" width="28" height="19" rx="1.5" />
                <!-- Lòng chậu âm chấn gấp sâu (3D chìm nhẹ bằng bóng đổ đứt nét) -->
                <rect x="9" y="18" width="22" height="13" rx="0.5" />
                <line x1="9" y1="18" x2="6" y2="15" stroke-dasharray="2 2" />
                <line x1="31" y1="18" x2="34" y2="15" stroke-dasharray="2 2" />
                <!-- Lỗ rốn xả nước ở giữa lòng chậu -->
                <ellipse cx="20" cy="24.5" rx="3" ry="1.5" />
                <circle cx="20" cy="24.5" r="1" fill="currentColor" stroke="none" />
                <!-- Vòi nước inox cổ ngỗng cao cấp xoay 360 độ -->
                <path d="M 13,18 L 13,9 C 13,5 18,5 18,8.5 L 18,10.5" stroke-width="1.75" />
                <rect x="12" y="15.5" width="2" height="1" rx="0.2" fill="currentColor" stroke="none" />
                <!-- Van vặn khóa nước dạng gạt -->
                <line x1="10" y1="16.5" x2="12" y2="15" />
                <!-- Dòng nước nhỏ chảy xuống lấp lánh (nét đứt mảnh) -->
                <line x1="18" y1="11" x2="18" y2="24" stroke-dasharray="2 3" opacity="0.75" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Hệ Chậu Rửa Ergonomics', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Thiết kế sâu lòng thông minh ngăn văng nước bẩn ra ngoài.', 'saigonhoreca'); ?></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php /* Khối đúc kết / lời kết đặc thù dự án — đặt cuối gallery (KHÔNG tạo section riêng). */ ?>
<section class="pp-section-hwa pp-section-hwa--alt pp-gallery-hwa__closing scroll-reveal">
  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--bl" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-gallery-hwa__closing-centered pp-story-card-hwa">
      <div class="pp-hwa-watermark-num" aria-hidden="true">04</div>
      <div class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></div>
      <div class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></div>
      <div class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></div>
      <div class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></div>
      <div class="pp-hwa-ornament" aria-hidden="true"></div>
      <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('HOÀN THIỆN THỰC TẾ', 'saigonhoreca'); ?></div>
      <h2 class="pp-text-hwa__title"><?php echo esc_html__('Kiến Tạo Chuẩn Xác Từng Chi Tiết Cơ Khí', 'saigonhoreca'); ?></h2>
      <div class="pp-text-hwa__body">
        <p><?php echo esc_html__('Tại Heiwa Sushi, sự hoàn mỹ không chỉ xuất hiện trên các đĩa thức ăn tuyệt hảo của người nghệ sĩ Omakase, mà được đảm bảo xuyên suốt từ cốt lõi hạ tầng kỹ thuật. Saigon Horeca cam kết thi công thực tế bám sát 100% bản vẽ kỹ thuật kỹ lưỡng nhất.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Quy trình lắp ráp thiết bị, căn chỉnh độ phẳng mặt bàn inox, tối ưu hóa các đường đi ống thoát sàn và hệ thống hút mùi đều được giám sát khắt khe bởi các kỹ sư dày dặn kinh nghiệm, mang đến một gian bếp an toàn, hiệu năng vượt trội.', 'saigonhoreca'); ?></p>
      </div>
      <blockquote class="pp-gallery-hwa__quote">
        <p><?php echo esc_html__('“Sự tinh tế không nằm ở những gì hào nhoáng lộ thiên, mà ẩn sâu trong sự hoàn hảo của những chi tiết cơ khí âm thầm nâng bước người đầu bếp sáng tạo nghệ thuật.”', 'saigonhoreca'); ?></p>
        <cite class="pp-gallery-hwa__quote-author"><?php echo esc_html__('Đội Ngũ Kỹ Sư Saigon Horeca', 'saigonhoreca'); ?></cite>
      </blockquote>
    </div>
  </div>
</section>
