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
$blueprint_img = sgh_img('2024/02/swh-ban-ve-tong-the-khu-bep.webp');
$showcase_img  = sgh_img('2024/02/The-Brix-05-1-768x432.webp');$intro_items = [
    [
        'number' => '01',
        'subtitle' => '',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-mission-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="var(--ph)" />
                        </linearGradient>
                      </defs>
                      <circle class="svg-orbit-dashed" cx="24" cy="24" r="20" stroke="url(#grad-mission-luxe)" stroke-width="1" stroke-dasharray="4 4" stroke-opacity="0.4"/>
                      <circle cx="24" cy="24" r="16" stroke="currentColor" stroke-width="1" stroke-opacity="0.15"/>
                      <circle cx="24" cy="24" r="8" stroke="url(#grad-mission-luxe)" stroke-width="1" stroke-opacity="0.5"/>
                      <path class="svg-axis" d="M24 5V12M24 36V43M5 24H12M36 24H43" stroke="url(#grad-mission-luxe)" stroke-width="1.5" stroke-linecap="round" stroke-opacity="0.7"/>
                      <path class="svg-star" d="M24 13L27.5 20.5L35 24L27.5 27.5L24 35L20.5 27.5L13 24L20.5 20.5L24 13Z" fill="url(#grad-mission-luxe)"/>
                      <circle cx="24" cy="24" r="2" fill="#ffffff"/>
                    </svg>',
        'title'  => __('Sự mệnh', 'saigonhoreca'),
        'desc'   => 'Nâng cao chất lượng và hiệu suất ngành ẩm thực bằng các giải pháp <strong>thiết bị bếp công nghiệp</strong> và dịch vụ tối ưu, đồng hành bền vững cùng sự phát triển của doanh nghiệp F&B.',
    ],
    [
        'number' => '02',
        'subtitle' => '',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-products-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="#ffd166" />
                        </linearGradient>
                      </defs>
                      <path class="svg-box-top" d="M24 5L40 13L24 21L8 13L24 5Z" fill="url(#grad-products-luxe)" fill-opacity="0.85" stroke="url(#grad-products-luxe)" stroke-width="1.5"/>
                      <path class="svg-box-mid" d="M8 13V20L24 28L40 20V13" stroke="url(#grad-products-luxe)" stroke-width="1.5" stroke-linejoin="round"/>
                      <path d="M8 22L24 30L40 22" stroke="currentColor" stroke-width="1.5" stroke-opacity="0.6" stroke-linejoin="round"/>
                      <path d="M8 25V27L24 35L40 27V25" stroke="currentColor" stroke-width="1.5" stroke-opacity="0.3" stroke-linejoin="round"/>
                      <path d="M8 31L24 39L40 31" stroke="url(#grad-products-luxe)" stroke-width="1" stroke-opacity="0.4" stroke-linejoin="round"/>
                      <circle cx="13" cy="8" r="1.5" fill="var(--p)"/>
                      <circle cx="35" cy="36" r="1.5" fill="currentColor" fill-opacity="0.4"/>
                    </svg>',
        'title'  => __('Sản phẩm đa dạng', 'saigonhoreca'),
        'desc'   => 'Từ toàn bộ hệ thống <strong>thiết bị bếp inox</strong>, tủ lạnh công nghiệp, máy rửa chén đến đồ dùng quầy bar chuyên dụng — cam kết đáp ứng trọn vẹn nhu cầu của ngành HORECA.',
    ],
    [
        'number' => '03',
        'subtitle' => '',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-quality-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="var(--ph)" />
                        </linearGradient>
                      </defs>
                      <path d="M24 2V5M24 43V46M2 24H5M43 24H46" stroke="currentColor" stroke-width="1.5" stroke-opacity="0.2" stroke-linecap="round"/>
                      <path class="svg-shield" d="M24 8C30 8 37 9 37 9V23C37 31 30 38 24 41C18 38 11 31 11 23V9C11 9 18 8 24 8Z" fill="url(#grad-quality-luxe)" fill-opacity="0.12" stroke="url(#grad-quality-luxe)" stroke-width="2" stroke-linejoin="round"/>
                      <path d="M24 12V14" stroke="url(#grad-quality-luxe)" stroke-width="1.5" stroke-linecap="round"/>
                      <path class="svg-check" d="M17 23L22 28L31 19" stroke="url(#grad-quality-luxe)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <circle cx="24" cy="33" r="2" fill="currentColor" fill-opacity="0.5"/>
                    </svg>',
        'title'  => __('Chất lượng đảm bảo', 'saigonhoreca'),
        'desc'   => 'Sản phẩm nhập khẩu chính hãng từ các <strong>thương hiệu quốc tế</strong> hàng đầu. Quy trình kiểm định chất lượng nghiêm ngặt trước khi lắp đặt và vận hành thực tế.',
    ],
    [
        'number' => '04',
        'subtitle' => '',
        'icon'   => '<svg class="sh-about-intro__svg-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <defs>
                        <linearGradient id="grad-team-luxe" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stop-color="var(--p)" />
                          <stop offset="100%" stop-color="#ffd166" />
                        </linearGradient>
                      </defs>
                      <path d="M24 4C35.0457 4 44 12.9543 44 24C44 35.0457 35.0457 44 24 44C12.9543 44 4 35.0457 4 24" stroke="currentColor" stroke-width="1" stroke-dasharray="3 3" stroke-opacity="0.2"/>
                      <circle class="svg-inner-circle" cx="24" cy="24" r="14" stroke="url(#grad-team-luxe)" stroke-width="1" stroke-opacity="0.3"/>
                      <circle class="svg-team-head" cx="24" cy="18" r="4.5" fill="url(#grad-team-luxe)"/>
                      <path class="svg-team-body" d="M14 31C14 26.5 18.5 25 24 25C29.5 25 34 26.5 34 31V33H14V31Z" fill="url(#grad-team-luxe)" fill-opacity="0.85"/>
                      <circle cx="12" cy="23" r="3" fill="currentColor" fill-opacity="0.4"/>
                      <path d="M6 31C6 28.5 9 27.5 12 27.5C13.8 27.5 15.5 27.9 16.5 28.5" stroke="currentColor" stroke-width="1.2" stroke-opacity="0.5" stroke-linecap="round"/>
                      <circle cx="36" cy="23" r="3" fill="currentColor" fill-opacity="0.4"/>
                      <path d="M42 31C42 28.5 39 27.5 36 27.5C34.2 27.5 32.5 27.9 31.5 28.5" stroke="currentColor" stroke-width="1.2" stroke-opacity="0.5" stroke-linecap="round"/>
                      <path d="M24 18V25" stroke="url(#grad-team-luxe)" stroke-width="1.5" stroke-opacity="0.5"/>
                    </svg>',
        'title'  => __('Đội ngũ chuyên nghiệp', 'saigonhoreca'),
        'desc'   => 'Đội ngũ kỹ sư, chuyên viên giàu kinh nghiệm thực chiến. Tư vấn đo đạc **khảo sát thực địa**, dựng layout 2D/3D tối ưu công năng đến thi công bàn giao trọn gói.',
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
                    <img class="sh-about-intro__showcase-img" src="<?php echo esc_url($showcase_img); ?>" alt="Thiết kế bếp nhà hàng cao cấp Saigon Horeca thực hiện" loading="eager" fetchpriority="high" />
                    
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
                            <span class="sh-about-intro__badge-tag">THUONG HIEU UY TIN</span>
                            <span class="sh-about-intro__badge-year">TU NAM 2018</span>
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
                        
                        <div class="sh-about-intro__panel-header">
                            <!-- Left: Number & Subtitle -->
                            <div class="sh-about-intro__panel-number-wrap">
                                <span class="sh-about-intro__panel-num"><?php echo esc_html($item['number']); ?></span>
                                <span class="sh-about-intro__panel-subtag"><?php echo esc_html($item['subtitle']); ?></span>
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
                        <div class="sh-about-intro__panel-content">
                            <div class="sh-about-intro__panel-content-inner">
                                <p class="sh-about-intro__panel-desc"><?php echo $item['desc']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>
