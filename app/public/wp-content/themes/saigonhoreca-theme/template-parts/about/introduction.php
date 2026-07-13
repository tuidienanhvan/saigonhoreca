<?php
/**
 * About Page Section — Introduction
 *
 * Redesigned into a Luxury Architectural Concept Board.
 * Asymmetric 2-column layout (min-width: 1024px).
 * Left Column: Storytelling + Visual Collage (Blueprint + Real Project Photo + Glass Badge).
 * Right Column: Premium Vertical Interactive Accordion Panels (01 - 04).
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$uri = get_template_directory_uri();
$blueprint_img = sgh_img('spice-world-hotpot/swh-ban-ve-tong-the-khu-bep.png');
$showcase_img  = sgh_img('the-brix/the-brix-he-thong-bep-nau-chinh-inox.webp');
$intro_items = [
    [
        'number' => '01',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-mission-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="#ffd166" />
                        </linearGradient>
                        <filter id="glow-luxe" x="-50%" y="-50%" width="200%" height="200%">
                          <feGaussianBlur stdDeviation="1.5" result="blur" />
                          <feComposite in="SourceGraphic" in2="blur" operator="over" />
                        </filter>
                      </defs>
                      <circle class="svg-orbit-dashed" cx="24" cy="24" r="21" stroke="url(#grad-mission-luxe)" stroke-width="1.2" stroke-dasharray="6 3" stroke-opacity="0.5"/>
                      <circle class="svg-inner-circle" cx="24" cy="24" r="16" stroke="currentColor" stroke-width="0.8" stroke-dasharray="2 4" stroke-opacity="0.3"/>
                      <circle cx="24" cy="24" r="18.5" stroke="url(#grad-mission-luxe)" stroke-width="0.8" stroke-opacity="0.2"/>
                      <line x1="24" y1="2" x2="24" y2="46" stroke="url(#grad-mission-luxe)" stroke-width="0.8" stroke-opacity="0.3" />
                      <line x1="2" y1="24" x2="46" y2="24" stroke="url(#grad-mission-luxe)" stroke-width="0.8" stroke-opacity="0.3" />
                      <path d="M24 6V10M24 38V42M6 24H10M38 24H42" stroke="url(#grad-mission-luxe)" stroke-width="1" stroke-linecap="round"/>
                      <path class="svg-star" d="M24 8C24 16 16 24 8 24C16 24 24 32 24 40C24 32 32 24 40 24C32 24 24 16 24 8Z" fill="url(#grad-mission-luxe)" filter="url(#glow-luxe)"/>
                      <circle cx="24" cy="24" r="2.5" fill="#ffffff" filter="url(#glow-luxe)"/>
                    </svg>',
        'title'  => __('Sự mệnh', 'saigonhoreca'),
        'desc'   => 'Nâng cao chất lượng và hiệu suất ngành ẩm thực bằng các giải pháp <strong>thiết bị bếp công nghiệp</strong> và dịch vụ tối ưu, đồng hành bền vững cùng sự phát triển của doanh nghiệp F&B.',
    ],
    [
        'number' => '02',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-products-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="#ffd166" />
                        </linearGradient>
                      </defs>
                      <path d="M8 24L24 16L40 24L24 32L8 24Z" stroke="currentColor" stroke-width="0.5" stroke-opacity="0.15"/>
                      <path d="M24 6V42" stroke="currentColor" stroke-width="0.5" stroke-opacity="0.1"/>
                      <g class="svg-box-top">
                        <path d="M24 6L40 14L24 22L8 14L24 6Z" fill="url(#grad-products-luxe)" fill-opacity="0.8" stroke="url(#grad-products-luxe)" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M24 11.5L34.5 16.5" stroke="#ffffff" stroke-width="0.8" stroke-opacity="0.5"/>
                      </g>
                      <g class="svg-box-mid">
                        <path d="M24 15L40 23L24 31L8 23L24 15Z" fill="url(#grad-products-luxe)" fill-opacity="0.35" stroke="url(#grad-products-luxe)" stroke-width="1.2" stroke-linejoin="round"/>
                        <path d="M8 23V25.5L24 33.5L40 25.5V23" stroke="url(#grad-products-luxe)" stroke-width="1.2" stroke-linejoin="round"/>
                      </g>
                      <g class="svg-box-bottom" style="transition: transform 0.4s ease;">
                        <path d="M24 24L40 32L24 40L8 32L24 24Z" fill="url(#grad-products-luxe)" fill-opacity="0.15" stroke="url(#grad-products-luxe)" stroke-width="1" stroke-linejoin="round"/>
                        <path d="M8 32V36.5L24 44.5L40 36.5V32" stroke="url(#grad-products-luxe)" stroke-width="1" stroke-linejoin="round"/>
                      </g>
                      <line class="svg-axis" x1="24" y1="6" x2="24" y2="40" stroke="#ffffff" stroke-width="1" stroke-dasharray="2 3" stroke-opacity="0.4"/>
                    </svg>',
        'title'  => __('Sản phẩm đa dạng', 'saigonhoreca'),
        'desc'   => 'Từ toàn bộ hệ thống <strong>thiết bị bếp inox</strong>, tủ lạnh công nghiệp, máy rửa chén đến đồ dùng quầy bar chuyên dụng — cam kết đáp ứng trọn vẹn nhu cầu của ngành HORECA.',
    ],
    [
        'number' => '03',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-quality-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="#ffd166" />
                        </linearGradient>
                        <filter id="glow-shield" x="-50%" y="-50%" width="200%" height="200%">
                          <feGaussianBlur stdDeviation="1.5" result="blur" />
                          <feComposite in="SourceGraphic" in2="blur" operator="over" />
                        </filter>
                      </defs>
                      <circle cx="24" cy="24" r="18" stroke="currentColor" stroke-width="0.5" stroke-dasharray="1 5" stroke-opacity="0.2" />
                      <path class="svg-shield" d="M24 6C29.5 6 36.5 7 36.5 7V22C36.5 30 30.5 37.5 24 41C17.5 37.5 11.5 30 11.5 22V7C11.5 7 18.5 6 24 6Z" fill="url(#grad-quality-luxe)" fill-opacity="0.15" stroke="url(#grad-quality-luxe)" stroke-width="2.2" stroke-linejoin="round" filter="url(#glow-shield)"/>
                      <path d="M24 9.5C28 9.5 33 10.3 33 10.3V21.5C33 27.5 28.5 33.5 24 36.5C19.5 33.5 15 27.5 15 21.5V10.3C15 10.3 20 9.5 24 9.5Z" stroke="url(#grad-quality-luxe)" stroke-width="0.8" stroke-opacity="0.4" stroke-dasharray="3 2"/>
                      <path class="svg-check" d="M17.5 23L22 27.5L30.5 18" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" filter="url(#glow-shield)" stroke-dasharray="25" stroke-dashoffset="25" style="transition: stroke-dashoffset 0.6s cubic-bezier(0.25, 1, 0.5, 1);"/>
                      <circle cx="24" cy="32.5" r="1.8" fill="url(#grad-quality-luxe)"/>
                    </svg>',
        'title'  => __('Chất lượng đảm bảo', 'saigonhoreca'),
        'desc'   => 'Sản phẩm nhập khẩu chính hãng từ các <strong>thương hiệu quốc tế</strong> hàng đầu. Quy trình kiểm định chất lượng nghiêm ngặt trước khi lắp đặt và vận hành thực tế.',
    ],
    [
        'number' => '04',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-team-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="#ffd166" />
                        </linearGradient>
                      </defs>
                      <circle class="svg-inner-circle" cx="24" cy="24" r="21" stroke="url(#grad-team-luxe)" stroke-width="1.2" stroke-dasharray="5 3" stroke-opacity="0.4"/>
                      <path class="svg-axis" d="M13 24H35M24 13V35M16 16L32 32M16 32L32 16" stroke="currentColor" stroke-width="0.6" stroke-opacity="0.15" stroke-dasharray="2 2" />
                      <g class="svg-team-body">
                        <circle class="svg-team-head" cx="24" cy="18" r="4.5" fill="url(#grad-team-luxe)"/>
                        <path d="M14 31C14 26 19 25 24 25C29 25 34 26 34 31V34H14V31Z" fill="url(#grad-team-luxe)" fill-opacity="0.9" stroke="url(#grad-team-luxe)" stroke-width="0.8"/>
                      </g>
                      <g class="svg-team-left" style="transition: transform 0.4s ease;">
                        <circle cx="12.5" cy="23.5" r="3.5" fill="currentColor" fill-opacity="0.75"/>
                        <path d="M6 32C6 28.5 9.5 28 12.5 28C15.5 28 19 28.5 19 32V34H6V32Z" fill="currentColor" fill-opacity="0.5"/>
                      </g>
                      <g class="svg-team-right" style="transition: transform 0.4s ease;">
                        <circle cx="35.5" cy="23.5" r="3.5" fill="currentColor" fill-opacity="0.75"/>
                        <path d="M29 32C29 28.5 32.5 28 35.5 28C38.5 28 42 28.5 42 32V34H29V32Z" fill="currentColor" fill-opacity="0.5"/>
                      </g>
                      <circle cx="24" cy="25" r="1.5" fill="#ffffff" />
                      <circle cx="12.5" cy="28" r="1.2" fill="url(#grad-team-luxe)" />
                      <circle cx="35.5" cy="28" r="1.2" fill="url(#grad-team-luxe)" />
                    </svg>',
        'title'  => __('Đội ngũ chuyên nghiệp', 'saigonhoreca'),
        'desc'   => 'Đội ngũ kỹ sư, chuyên viên giàu kinh nghiệm thực chiến. Tư vấn đo đạc <strong>khảo sát thực địa</strong>, dựng layout 2D/3D tối ưu công năng đến thi công bàn giao trọn gói.',
    ],
];
?>
<section class="sh-about-intro" aria-label="<?php esc_attr_e('Giới thiệu Saigon Horeca', 'saigonhoreca'); ?>">
    <div class="sh-about-intro__inner">
        
        <!-- CỘT TRÁI: STORYTELLING & VISUAL COLLAGE -->
        <div class="sh-about-intro__left-col">
            <div class="sh-about-intro__lead">
                <span class="sh-about-intro__eyebrow"><?php esc_html_e('Chúng tôi là ai', 'saigonhoreca'); ?></span>
                <h2 class="sh-about-intro__heading">
                    <?php esc_html_e('Saigon Horeca — Giải pháp bếp công nghiệp &amp; quầy bar chuyên nghiệp', 'saigonhoreca'); ?>
                </h2>
                <div class="sh-about-intro__rule">
                    <span class="sh-about-intro__rule-line"></span>
                    <span class="sh-about-intro__rule-dot"></span>
                    <span class="sh-about-intro__rule-line"></span>
                </div>
                <p class="sh-about-intro__body">
                    Saigon Horeca tự hào là đơn vị hàng đầu chuyên thiết kế, sản xuất và phân phối
                    <a class="sh-about-intro__link" href="<?php echo esc_url(home_url('/danh-muc-san-pham/thiet-bi-bep-cong-nghiep-sgh/')); ?>">thiết bị bếp công nghiệp</a>
                    chất lượng cao cùng giải pháp tổng thể cho ngành ẩm thực, quầy bar, nhà hàng, khách sạn. 
                    Chúng tôi đồng hành kiến tạo nên những không gian vận hành bếp đỉnh cao và tối ưu năng suất.
                </p>
            </div>

            <!-- Visual Collage Board -->
            <div class="sh-about-intro__collage">
                <!-- Background Technical Blueprint -->
                <div class="sh-about-intro__blueprint" style="background-image: url('<?php echo esc_url($blueprint_img); ?>');" aria-hidden="true"></div>
                
                <!-- Orbit Ring Micro-animation -->
                <div class="sh-about-intro__orbit-container" aria-hidden="true">
                    <div class="sh-about-intro__orbit-ring"></div>
                </div>

                <!-- Showcase Premium Real Image -->
                <div class="sh-about-intro__showcase-frame">
                    <img class="sh-about-intro__showcase-img" src="<?php echo esc_url($showcase_img); ?>" alt="Thiết kế bếp nhà hàng cao cấp Saigon Horeca thực hiện" width="800" height="500" loading="lazy" />
                    
                    <!-- Glassmorphic Badge -->
                    <div class="sh-about-intro__glass-badge">
                        <div class="sh-about-intro__badge-target" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="var(--p)" stroke-width="1" stroke-dasharray="2 2" />
                                <circle cx="12" cy="12" r="6" stroke="var(--p)" stroke-width="1.5" />
                                <path d="M12 2V22M2 12H22" stroke="var(--p)" stroke-width="1" />
                                <circle cx="12" cy="12" r="1.5" fill="#ffffff" />
                            </svg>
                        </div>
                        <div class="sh-about-intro__badge-text">
                            <span class="sh-about-intro__badge-tag">THƯƠNG HIỆU UY TÍN</span>
                            <span class="sh-about-intro__badge-year">TỪ NĂM 2018</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: INTERACTIVE ACCORDION PANELS -->
        <div class="sh-about-intro__right-col">
            <div class="sh-about-intro__accordion">
                <?php foreach ($intro_items as $index => $item): ?>
                    <div class="sh-about-intro__panel" data-index="<?php echo $index; ?>">
                        <!-- Border line indicator top -->
                        <div class="sh-about-intro__panel-border-top"></div>
                        
                        <div class="sh-about-intro__panel-header" role="button" tabindex="0" aria-expanded="false" aria-controls="about-panel-<?php echo $index; ?>">
                            <!-- Left: Number -->
                            <div class="sh-about-intro__panel-number-wrap">
                                <span class="sh-about-intro__panel-num"><?php echo esc_html($item['number']); ?></span>
                            </div>

                            <!-- Center: Title -->
                            <div class="sh-about-intro__panel-title-wrap">
                                <h3 class="sh-about-intro__panel-title"><?php echo esc_html($item['title']); ?></h3>
                            </div>

                            <!-- Right: Icon and Interactive Chevron/Plus -->
                            <div class="sh-about-intro__panel-meta">
                                <div class="sh-about-intro__panel-icon">
                                    <?php echo $item['icon']; ?>
                                </div>
                                <div class="sh-about-intro__panel-trigger" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4V20M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Expandable Content Box -->
                        <div class="sh-about-intro__panel-content" id="about-panel-<?php echo $index; ?>">
                            <div class="sh-about-intro__panel-content-inner">
                                <p class="sh-about-intro__panel-desc"><?php echo $item['desc']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
            /* Accessible disclosure: panels were :hover-only (keyboard/touch couldn't open). */
            (function(){
              document.querySelectorAll('.sh-about-intro__panel-header[role="button"]').forEach(function(h){
                function toggle(){
                  var p = h.closest('.sh-about-intro__panel');
                  var open = h.getAttribute('aria-expanded') === 'true';
                  h.setAttribute('aria-expanded', open ? 'false' : 'true');
                  if (p) p.classList.toggle('is-open', !open);
                }
                h.addEventListener('click', toggle);
                h.addEventListener('keydown', function(e){
                  if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') { e.preventDefault(); toggle(); }
                });
              });
            })();
            </script>
        </div>

    </div>
</section>
