<?php
/**
 * Project Pillar — bambino-saigonhoreca
 * Section #3: split — Bespoke Cyberpunk Neon Glow Edition
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bb pp-concept-bam">
  <div class="pp-container-shared">
    <div class="pp-concept-bam__grid">
      
      <div class="pp-concept-bam__text-col">
        <div class="pp-concept-bam__panels">
          <!-- Cột 1: Ý Tưởng Thiết Kế (Bản vẽ SVG chìm hình học sáng tạo) -->
          <div class="pp-concept-bam__panel pp-concept-bam__panel--primary">
            <!-- SVG hình vẽ kiến trúc định hướng hình học sáng tạo -->
            <div class="panel-svg-bg">
              <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="150" cy="150" r="45" stroke="rgba(212, 175, 55, 0.12)" stroke-width="0.75" />
                <circle cx="150" cy="150" r="28" stroke="rgba(212, 175, 55, 0.08)" stroke-width="0.75" />
                <line x1="150" y1="80" x2="150" y2="200" stroke="rgba(212, 175, 55, 0.08)" stroke-width="0.5" stroke-dasharray="2 2" />
                <line x1="80" y1="150" x2="200" y2="150" stroke="rgba(212, 175, 55, 0.08)" stroke-width="0.5" stroke-dasharray="2 2" />
                <path d="M 105 150 A 45 45 0 0 1 150 105" stroke="var(--gold)" stroke-width="1" stroke-opacity="0.25" />
                <rect x="125" y="125" width="50" height="50" stroke="rgba(212, 175, 55, 0.05)" stroke-width="0.5" />
              </svg>
            </div>
            
            <div class="panel-content-wrapper">
              <header class="panel-header">
                <span class="panel-tag">
                  <svg class="tag-gold-icon" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; width:10px; height:10px; margin-right:4px; vertical-align:middle;">
                    <circle cx="6" cy="6" r="5" stroke="var(--gold)" stroke-width="1"/>
                    <circle cx="6" cy="6" r="2" fill="var(--gold)"/>
                  </svg>
                  Ý Tưởng Thiết Kế
                </span>
                <h3 class="panel-title">Ý Tưởng Chủ Đạo Thiết Kế của Bambino</h3>
                <div class="panel-divider"></div>
              </header>
              <div class="panel-desc">
                <p>Qua trao đổi về concept nhà hàng kiểu Ý, Saigon Horeca đã tư vấn thiết kế nhà bếp cho Bambino với những nét đặc trưng cùng những thiết bị đầy đủ công năng và hiện đại.</p>
              </div>
            </div>
          </div>

          <!-- Cột 2: Giải Pháp Tối Ưu (Bản vẽ SVG chìm quy trình luồng công việc) -->
          <div class="pp-concept-bam__panel pp-concept-bam__panel--secondary">
            <!-- SVG hình vẽ luồng công việc một chiều tinh xảo (Đã bỏ toàn bộ tiếng Anh) -->
            <div class="panel-svg-bg" style="width:240px; height:120px; top:auto; bottom:0;">
              <svg viewBox="0 0 200 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M 10 60 Q 55 15 100 60 T 190 60" stroke="rgba(212, 175, 55, 0.12)" stroke-width="1" stroke-dasharray="3 3" />
                <circle cx="10" cy="60" r="3.5" fill="var(--gold)" fill-opacity="0.3" />
                <circle cx="100" cy="60" r="3.5" fill="var(--gold)" fill-opacity="0.3" />
                <circle cx="190" cy="60" r="3.5" fill="var(--gold)" fill-opacity="0.3" />
                <path d="M 184 56 L 190 60 L 184 64" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1.2" />
              </svg>
            </div>

            <div class="panel-content-wrapper">
              <span class="solution-tag">Giải Pháp Tối Ưu</span>
              <p>Với sự kết hợp giữa thiết bị tự sản xuất và nhập khẩu trực tiếp từ Italy, không gian làm việc bên trong nhà bếp đã phản ánh rõ nét tinh thần nước Ý một cách mạnh mẽ.</p>
              
              <div class="solution-chips">
                <span class="chip-item">Inox 304 cao cấp</span>
                <span class="chip-item">Thiết bị Italy nhập khẩu</span>
                <span class="chip-item">Quy trình bếp một chiều</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột 3: Khung ảnh Premium Single Frame được vẽ bằng SVG kỹ thuật chính xác bao quanh -->
      <div class="pp-concept-bam__media-col">
        <div class="pp-concept-bam__single-frame">
          <!-- Hào quang hào hoa phía sau -->
          <div class="premium-glow-circle"></div>
          
          <!-- SVG Khung viền kiến trúc tinh xảo bao quanh -->
          <svg class="concept-svg-frame" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Nét đứt định vị ngoài cùng -->
            <rect x="1.5" y="1.5" width="97" height="97" rx="5" stroke="rgba(212, 175, 55, 0.3)" stroke-width="0.35" stroke-dasharray="1.5 1.5" />
            <!-- Khung chỉ số kỹ thuật liền -->
            <rect x="4.5" y="4.5" width="91" height="91" rx="4" stroke="rgba(212, 175, 55, 0.15)" stroke-width="0.5" />
            
            <!-- Bốn góc L-bracket vẽ tinh vi bằng vector -->
            <path d="M 0 16 L 0 0 L 16 0" stroke="rgba(212, 175, 55, 0.75)" stroke-width="1.2" />
            <path d="M 100 16 L 100 0 L 84 0" stroke="rgba(212, 175, 55, 0.75)" stroke-width="1.2" />
            <path d="M 0 84 L 0 100 L 16 100" stroke="rgba(212, 175, 55, 0.75)" stroke-width="1.2" />
            <path d="M 100 84 L 100 100 L 84 100" stroke="rgba(212, 175, 55, 0.75)" stroke-width="1.2" />
            
            <!-- Tâm nhắm kỹ thuật tinh tế ở 4 góc -->
            <circle cx="4.5" cy="4.5" r="1.2" fill="var(--gold)" />
            <circle cx="95.5" cy="4.5" r="1.2" fill="var(--gold)" />
            <circle cx="4.5" cy="95.5" r="1.2" fill="var(--gold)" />
            <circle cx="95.5" cy="95.5" r="1.2" fill="var(--gold)" />
            
            <!-- Vạch thước đo kỹ thuật chi tiết ở các cạnh -->
            <line x1="50" y1="0" x2="50" y2="3.5" stroke="var(--gold)" stroke-width="0.5" />
            <line x1="25" y1="0" x2="25" y2="2" stroke="rgba(212, 175, 55, 0.5)" stroke-width="0.5" />
            <line x1="75" y1="0" x2="75" y2="2" stroke="rgba(212, 175, 55, 0.5)" stroke-width="0.5" />
            
            <line x1="50" y1="100" x2="50" y2="96.5" stroke="var(--gold)" stroke-width="0.5" />
            <line x1="25" y1="100" x2="25" y2="98" stroke="rgba(212, 175, 55, 0.5)" stroke-width="0.5" />
            <line x1="75" y1="100" x2="75" y2="98" stroke="rgba(212, 175, 55, 0.5)" stroke-width="0.5" />
            
            <line x1="0" y1="50" x2="3.5" y2="50" stroke="var(--gold)" stroke-width="0.5" />
            <line x1="100" y1="50" x2="96.5" y2="50" stroke="var(--gold)" stroke-width="0.5" />
          </svg>
          
          <!-- Container ảnh chính -->
          <div class="pp-concept-bam__image-container pp-image-container-shared">
            <img src="<?php echo sgh_img('bambino/bambino-khong-gian-am-thuc-y-sang-trong.jpg'); ?>" alt="<?php echo esc_attr__('Thiết kế không gian ẩm thực Ý sang trọng tại Bambino', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Không gian sảnh tiệc chính sang trọng và ấm cúng tại nhà hàng Bambino', 'saigonhoreca'); ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
