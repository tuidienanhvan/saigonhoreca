<?php
/**
 * Template Part: About Story / Introduction
 */
$_pid = get_queried_object_id();
$_s_tag      = get_post_meta($_pid, '_sgh_story_tag', true) ?: 'Tầm Nhìn & Sứ Mệnh';
$_s_title    = get_post_meta($_pid, '_sgh_story_title', true) ?: 'KIẾN TẠO';
$_s_accent   = get_post_meta($_pid, '_sgh_story_accent', true) ?: 'Di Sản';
$_s_subtitle = get_post_meta($_pid, '_sgh_story_subtitle', true) ?: 'Cho Thế Hệ Tương Lai';
$_s_quote    = get_post_meta($_pid, '_sgh_story_quote', true) ?: '"Tại <strong class="text-primary">Saigon House</strong>, chúng tôi không chỉ xây dựng những ngôi nhà vô tri, mà chúng tôi kiến tạo những <span class="text-primary" style="font-size:1.25rem;font-weight:700">Tổ Ấm</span>."';
$_s_body     = get_post_meta($_pid, '_sgh_story_body', true) ?: 'Hơn 10 năm hành trình, sứ mệnh của chúng tôi là hiện thực hóa giấc mơ về một không gian sống đẳng cấp, bền vững. Mỗi công trình là một bản giao hưởng giữa kỹ thuật chính xác và nghệ thuật kiến trúc, nơi lưu giữ những khoảnh khắc hạnh phúc nhất của gia đình Việt.';
$_s_badge    = get_post_meta($_pid, '_sgh_story_badge', true) ?: 'Cam Kết Chất Lượng';
$_s_badge_sub= get_post_meta($_pid, '_sgh_story_badge_sub', true) ?: 'Bảo hành kết cấu 10 năm';
?>
<section class="about-story">

    <div class="about-story__orb"></div>
    <div class="about-story__line"></div>

    <div class="about-story__container">
        <div class="about-story__grid">
            <div class="about-story__content" data-aos="fade-up">
                <div class="about-story__tag-row">
                    <span class="about-story__tag-line"></span>
                    <span class="about-story__tag"><?php echo esc_html($_s_tag); ?></span>
                </div>

                <h2 class="about-story__heading">
                    <?php echo esc_html($_s_title); ?> <br>
                    <span class="about-story__heading-gradient"><?php echo esc_html($_s_accent); ?></span>
                    <span class="about-story__subheading"><?php echo esc_html($_s_subtitle); ?></span>
                </h2>

                <div class="about-story__body">
                    <p class="about-story__quote"><?php echo wp_kses_post($_s_quote); ?></p>
                    <p class="about-story__desc"><?php echo esc_html($_s_body); ?></p>

                    <div class="about-story__signature">
                        <span class="about-story__sign-text">Trân Trọng,</span>
                        <div class="about-story__sign-meta">
                             <p class="about-story__sign-role">Tổng Giám Đốc</p>
                             <p class="about-story__sign-org">SAIGONHOUSE CORP</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-story__image-side" data-aos="fade-up" data-aos-delay="200">
                <div class="about-story__frame"></div>
                <div class="about-story__corner-tr"></div>
                <div class="about-story__corner-bl"></div>

                <div class="about-story__img-wrap">
                    <img src="<?php echo esc_url(sh_section_image('xay dung', '', 2)); ?>" class="about-story__img" alt="Construction Site" loading="lazy" decoding="async">
                </div>

                <div class="about-story__badge">
                    <div class="about-story__badge-icon">
                        <?php echo sh_icon('shield', 'about-story__badge-svg'); ?>
                    </div>
                    <div>
                        <p class="about-story__badge-title"><?php echo esc_html($_s_badge); ?></p>
                        <p class="about-story__badge-copy"><?php echo esc_html($_s_badge_sub); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
