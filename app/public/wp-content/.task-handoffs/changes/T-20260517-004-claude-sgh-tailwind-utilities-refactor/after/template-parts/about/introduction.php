<?php
/**
 * Template Part: Introduction (Why Choose Us)
 */
$_pid = get_queried_object_id();
$_i_tag   = get_post_meta($_pid, '_sgh_intro_tag', true) ?: 'Về Chúng Tôi';
$_i_title = get_post_meta($_pid, '_sgh_intro_title', true) ?: 'Kiến Tạo Không Gian sống <span class="text-primary">Đẳng Cấp</span> & Bền Vững';
$_i_lead  = get_post_meta($_pid, '_sgh_intro_lead', true) ?: 'Saigon House tự hào là đơn vị hàng đầu trong lĩnh vực thiết kế và thi công nội thất, kiến trúc. Chúng tôi không chỉ xây dựng những ngôi nhà, mà còn kiến tạo nên những tổ ấm, nơi lưu giữ những giá trị sống đích thực cho mỗi gia đình Việt.';
$_stats = [
    ['num' => get_post_meta($_pid, '_sgh_intro_stat1', true) ?: '500+', 'label' => get_post_meta($_pid, '_sgh_intro_stat1_label', true) ?: 'Dự Án Hoàn Thành'],
    ['num' => get_post_meta($_pid, '_sgh_intro_stat2', true) ?: '98%',  'label' => get_post_meta($_pid, '_sgh_intro_stat2_label', true) ?: 'Khách Hàng Hài Lòng'],
    ['num' => get_post_meta($_pid, '_sgh_intro_stat3', true) ?: '50+',  'label' => get_post_meta($_pid, '_sgh_intro_stat3_label', true) ?: 'Kiến Trúc Sư'],
];
?>
<section class="about-intro">

    <div class="about-intro__container">
        <div class="about-intro__grid">
            <?php
            $about_imgs = [
                sh_section_image('biet thu', '', 0),
                sh_section_image('noi that', '', 1),
                sh_section_image('thi cong', '', 2),
                sh_section_image('kien truc', '', 3),
            ];
            ?>
            <div class="about-intro__images" data-aos="fade-right">
                <div class="about-intro__img-grid">
                    <div class="about-intro__img-col about-intro__img-col--offset">
                        <img src="<?php echo esc_url($about_imgs[0]); ?>" alt="Biệt thự tráng lệ" class="about-intro__img about-intro__img--tall" loading="lazy" decoding="async">
                        <img src="<?php echo esc_url($about_imgs[1]); ?>" alt="Nội thất sang trọng" class="about-intro__img about-intro__img--short" loading="lazy" decoding="async">
                    </div>
                    <div class="about-intro__img-col">
                        <img src="<?php echo esc_url($about_imgs[2]); ?>" alt="Không gian sống xanh" class="about-intro__img about-intro__img--short" loading="lazy" decoding="async">
                        <img src="<?php echo esc_url($about_imgs[3]); ?>" alt="Kiến trúc hiện đại" class="about-intro__img about-intro__img--tall" loading="lazy" decoding="async">
                    </div>
                </div>
                <div class="about-intro__badge">
                    <div class="about-intro__badge-ring"></div>
                    <span class="about-intro__badge-number" id="counter-years">15+</span>
                    <span class="about-intro__badge-label">Năm Kinh Nghiệm</span>
                </div>
            </div>

            <div class="about-intro__content" data-aos="fade-left">
                <div>
                    <span class="about-intro__tag"><?php echo esc_html($_i_tag); ?></span>
                    <h2 class="about-intro__title"><?php echo wp_kses_post($_i_title); ?></h2>
                    <p class="about-intro__lead"><?php echo esc_html($_i_lead); ?></p>
                </div>

                <div class="about-intro__features">
                    <div class="about-intro__feature">
                        <div class="about-intro__feature-icon">
                            <?php echo sh_icon('check-circle', 'about-intro__feature-svg'); ?>
                        </div>
                        <div>
                            <h4 class="about-intro__feature-title">Chất Lượng Vượt Trội</h4>
                            <p class="about-intro__feature-desc">Cam kết vật liệu chính hãng, thi công chuẩn kỹ thuật.</p>
                        </div>
                    </div>
                    <div class="about-intro__feature">
                        <div class="about-intro__feature-icon">
                            <?php echo sh_icon('help-circle', 'about-intro__feature-svg'); ?>
                        </div>
                        <div>
                            <h4 class="about-intro__feature-title">Tiến Độ Cam Kết</h4>
                            <p class="about-intro__feature-desc">Bàn giao đúng hạn, quy trình làm việc rõ ràng.</p>
                        </div>
                    </div>
                    <div class="about-intro__feature">
                        <div class="about-intro__feature-icon">
                            <?php echo sh_icon('dollar-sign', 'about-intro__feature-svg'); ?>
                        </div>
                        <div>
                            <h4 class="about-intro__feature-title">Chi Phí Tối Ưu</h4>
                            <p class="about-intro__feature-desc">Giải pháp tài chính phù hợp, không phát sinh.</p>
                        </div>
                    </div>
                    <div class="about-intro__feature">
                        <div class="about-intro__feature-icon">
                            <?php echo sh_icon('phone-call', 'about-intro__feature-svg'); ?>
                        </div>
                        <div>
                            <h4 class="about-intro__feature-title">Hỗ Trợ 24/7</h4>
                            <p class="about-intro__feature-desc">Đội ngũ tư vấn tận tâm, bảo hành dài hạn.</p>
                        </div>
                    </div>
                </div>

                <div class="about-intro__stats">
                    <?php foreach ($_stats as $stat): ?>
                    <div class="about-intro__stat">
                        <span class="about-intro__stat-number"><?php echo esc_html($stat['num']); ?></span>
                        <span class="about-intro__stat-label"><?php echo esc_html($stat['label']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
