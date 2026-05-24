<?php
/**
 * Template Part: Contact Form - Compact Layout
 * Left: Contact info | Right: Form
 *
 * @package SaigonHouse
 */
$contact = saigonhouse_get_contact_info();
?>
<section class="sh-contact-form">

    <div class="sh-contact-form__container">
        <div class="sh-contact-form__card">

            <div class="sh-contact-form__left" data-aos="fade-right">
                <div class="sh-contact-form__pattern">
                    <svg xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid)"/></svg>
                </div>
                <div class="sh-contact-form__house">
                    <svg width="280" height="280" viewBox="0 0 200 200" fill="white"><path d="M100 20L20 80V180H80V130H120V180H180V80L100 20Z"/><rect x="85" y="100" width="30" height="40" rx="2"/><rect x="40" y="100" width="25" height="25" rx="2"/><rect x="135" y="100" width="25" height="25" rx="2"/><polygon points="100,5 15,75 25,75 100,15 175,75 185,75"/></svg>
                </div>

                <div class="sh-contact-form__info">
                    <span class="sh-contact-form__badge">
                        <span class="sh-contact-form__badge-dot"></span>
                        Saigon House
                    </span>
                    <h2 class="sh-contact-form__title">Liên Hệ <span class="sh-contact-form__title-accent">Tư Vấn</span></h2>
                    <p class="sh-contact-form__subtitle">Đội ngũ KTS giàu kinh nghiệm sẵn sàng lắng nghe và hiện thực hóa mọi ý tưởng.</p>
                </div>

                <div class="sh-contact-form__contacts">
                    <a href="tel:<?php echo esc_attr($contact['hotline_raw']); ?>" class="sh-contact-form__contact-item">
                        <div class="sh-contact-form__contact-icon"><?php echo sh_icon('phone', ''); ?></div>
                        <span class="sh-contact-form__hotline"><?php echo esc_html($contact['hotline']); ?></span>
                    </a>
                    <div class="sh-contact-form__contact-item">
                        <div class="sh-contact-form__contact-icon"><?php echo sh_icon('map-pin', ''); ?></div>
                        <span class="sh-contact-form__address"><?php echo esc_html($contact['address_short'] ?? $contact['address']); ?></span>
                    </div>
                    <a href="mailto:<?php echo esc_attr($contact['email_primary']); ?>" class="sh-contact-form__contact-item">
                        <div class="sh-contact-form__contact-icon"><?php echo sh_icon('mail', ''); ?></div>
                        <span class="sh-contact-form__email"><?php echo esc_html($contact['email_primary']); ?></span>
                    </a>

                    <div class="sh-contact-form__social">
                        <?php $theme_uri = get_template_directory_uri(); ?>
                        <a href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="sh-contact-form__social-btn" title="Facebook">
                            <img src="<?php echo esc_url($theme_uri . '/assets/images/facebook-icon.webp'); ?>" alt="Facebook" class="sh-contact-form__social-img" loading="lazy">
                        </a>
                        <a href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" class="sh-contact-form__social-btn" title="Zalo">
                            <img src="<?php echo esc_url($theme_uri . '/assets/images/zalo-icon.webp'); ?>" alt="Zalo" class="sh-contact-form__social-img" loading="lazy">
                        </a>
                        <a href="<?php echo esc_url($contact['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="sh-contact-form__social-btn" title="YouTube">
                            <img src="<?php echo esc_url($theme_uri . '/assets/images/youtube-icon.webp'); ?>" alt="YouTube" class="sh-contact-form__social-img" loading="lazy">
                        </a>
                    </div>
                </div>
            </div>

            <div class="sh-contact-form__right" data-aos="fade-left">
                <div class="sh-contact-form__form-tag">
                    <span class="sh-contact-form__form-line"></span>
                    <span class="sh-contact-form__form-label">Đăng Ký Tư Vấn</span>
                </div>
                <h3 class="sh-contact-form__form-title">Gửi yêu cầu của bạn</h3>
                <p class="sh-contact-form__form-desc">KTS sẽ liên hệ lại trong vòng 30 phút.</p>

                <form id="saigonhouse-contact-form" class="sh-contact-form__form" data-track="contact_form_submit">
                    <?php wp_nonce_field('sh_contact_submit', 'sh_contact_nonce'); ?>
                    <div style="position:absolute;left:-9999px;" aria-hidden="true">
                        <input type="text" name="sh_website_url" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <div class="sh-contact-form__row">
                        <div>
                            <label for="contact_name" class="sh-contact-form__label">Họ và tên <span class="sh-contact-form__label-req">*</span></label>
                            <input type="text" name="contact_name" id="contact_name" required class="sh-contact-form__input" placeholder="Nguyễn Văn A">
                        </div>
                        <div>
                            <label for="contact_phone" class="sh-contact-form__label">Số điện thoại <span class="sh-contact-form__label-req">*</span></label>
                            <input type="tel" name="contact_phone" id="contact_phone" required class="sh-contact-form__input" placeholder="0961 xxx xxx">
                        </div>
                    </div>

                    <div class="sh-contact-form__row">
                        <div>
                            <label for="contact_email" class="sh-contact-form__label">Email <span class="sh-contact-form__label-opt">(tùy chọn)</span></label>
                            <input type="email" name="contact_email" id="contact_email" class="sh-contact-form__input" placeholder="email@example.com">
                        </div>
                        <div>
                            <label for="contact_service" class="sh-contact-form__label">Dịch vụ quan tâm</label>
                            <select name="contact_service" id="contact_service" class="sh-contact-form__select">
                                <option value="">-- Chọn --</option>
                                <option value="thiet-ke-kien-truc">Thiết kế kiến trúc</option>
                                <option value="thiet-ke-noi-that">Thiết kế nội thất</option>
                                <option value="xay-nha-phan-tho">Xây nhà phần thô</option>
                                <option value="xay-nha-tron-goi">Xây nhà trọn gói</option>
                                <option value="tu-van-bao-gia">Tư vấn báo giá</option>
                                <option value="khac">Khác</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="contact_message" class="sh-contact-form__label">Nội dung <span class="sh-contact-form__label-opt">(tùy chọn)</span></label>
                        <textarea name="contact_message" id="contact_message" rows="2" class="sh-contact-form__textarea" placeholder="Diện tích đất, số tầng, ngân sách dự kiến..."></textarea>
                    </div>

                    <button type="submit" class="sh-contact-form__submit">
                        <span>Gửi Yêu Cầu Tư Vấn</span>
                        <?php echo sh_icon('arrow-right', 'sh-contact-form__submit-icon'); ?>
                    </button>

                    <p class="sh-contact-form__trust">
                        <?php echo sh_icon('shield', 'sh-contact-form__trust-icon'); ?>
                        Bảo mật 100% · Tư vấn miễn phí
                    </p>
                </form>
            </div>

        </div>
    </div>
</section>
