<?php
/**
 * Template Part: Product Hero — Modern B2B Luxury Showcase for CPT `product`.
 *
 * 2-Column Split Hero Layout:
 *   Left (52%): Large 4:3 Studio Showcase Card + Trust Badges Strip (CO/CQ, Warranty, Installation)
 *   Right (48%): Brand/Category Pills + H1 Title + Quick Specs + Sapo + High-Conversion CTAs + B2B Commitments
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$post_id    = $args['post_id'] ?? get_the_ID();
$sku        = $args['sku'] ?? get_post_meta($post_id, '_sku', true);
$source_url = $args['source_url'] ?? get_post_meta($post_id, '_source_url', true);
$brands     = $args['brands'] ?? wp_get_post_terms($post_id, 'product_brand');
$pcats      = $args['pcats'] ?? wp_get_post_terms($post_id, 'product_category');

$brand_name = (!is_wp_error($brands) && !empty($brands)) ? $brands[0]->name : '';
$brand_url  = (!is_wp_error($brands) && !empty($brands)) ? get_term_link($brands[0]) : '';
$cat_name   = (!is_wp_error($pcats) && !empty($pcats)) ? $pcats[0]->name : '';
$cat_url    = (!is_wp_error($pcats) && !empty($pcats)) ? get_term_link($pcats[0]) : '';

$lang          = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
$hotline       = get_theme_mod('saigonhouse_hotline', '0901 304 365');
$hotline_clean = preg_replace('/[^0-9+]/', '', (string) $hotline);
$zalo_url      = 'https://zalo.me/' . $hotline_clean;
$consult_url   = ($lang === 'vi') ? $zalo_url : ('tel:' . $hotline_clean);
$consult_sub   = ($lang === 'vi') 
    ? ('Hotline / Zalo: ' . $hotline)
    : (($lang === 'ja') ? ('ホットライン: ' . $hotline) : ('Hotline / Phone: ' . $hotline));

// Clean Sapo / Excerpt
$raw_excerpt = get_the_excerpt($post_id);
if (empty($raw_excerpt)) {
    $post_obj = get_post($post_id);
    $raw_excerpt = $post_obj ? wp_trim_words(strip_shortcodes($post_obj->post_content), 40) : '';
}
// Chuẩn hóa lỗi ngoặc kép: (Đức (Germany)) -> (Đức / Germany)
$clean_excerpt = str_replace(
    ['(Đức (Germany))', '(Đức (Germany)', '(Germany))', '((', '))'],
    ['(Đức - Germany)', '(Đức - Germany)', '(Germany)', '(', ')'],
    $raw_excerpt
);

// Trích xuất quốc gia xuất xứ từ content hoặc brand
$origin = sgh_t('product_origin_default');
if (stripos($raw_excerpt, 'Đức') !== false || stripos($raw_excerpt, 'Germany') !== false) {
    $origin = ['vi' => 'Đức (Germany)', 'en' => 'Germany', 'ja' => 'ドイツ (Germany)'][$lang] ?? 'Germany';
} elseif (stripos($raw_excerpt, 'Nhật') !== false || stripos($raw_excerpt, 'Japan') !== false) {
    $origin = ['vi' => 'Nhật Bản (Japan)', 'en' => 'Japan', 'ja' => '日本 (Japan)'][$lang] ?? 'Japan';
} elseif (stripos($raw_excerpt, 'Tây Ban Nha') !== false || stripos($raw_excerpt, 'Spain') !== false) {
    $origin = ['vi' => 'Tây Ban Nha (Spain)', 'en' => 'Spain', 'ja' => 'スペイン (Spain)'][$lang] ?? 'Spain';
} elseif (stripos($raw_excerpt, 'Ý') !== false || stripos($raw_excerpt, 'Italy') !== false) {
    $origin = ['vi' => 'Ý (Italy)', 'en' => 'Italy', 'ja' => 'イタリア (Italy)'][$lang] ?? 'Italy';
} elseif (stripos($raw_excerpt, 'Thụy Sĩ') !== false || stripos($raw_excerpt, 'Switzerland') !== false) {
    $origin = ['vi' => 'Thụy Sĩ (Switzerland)', 'en' => 'Switzerland', 'ja' => 'スイス (Switzerland)'][$lang] ?? 'Switzerland';
} elseif (stripos($raw_excerpt, 'Mỹ') !== false || stripos($raw_excerpt, 'USA') !== false) {
    $origin = ['vi' => 'Hoa Kỳ (USA)', 'en' => 'United States (USA)', 'ja' => 'アメリカ (USA)'][$lang] ?? 'USA';
}
?>

<div class="sh-product-hero">
    <!-- ═══ CỘT TRÁI: SHOWCASE ẢNH STUDIO & HUY HIỆU CAM KẾT ═══ -->
    <div class="sh-product-hero__media-col">
        <div class="sh-product-hero__gallery">
            <?php if (has_post_thumbnail($post_id)) : ?>
                <div class="sh-product-hero__image-wrap">
                    <?php echo get_the_post_thumbnail($post_id, 'large', [
                        'class'         => 'sh-product-hero__img',
                        'loading'       => 'eager',
                        'fetchpriority' => 'high',
                        'itemprop'      => 'image',
                        'alt'           => esc_attr(get_the_title($post_id)),
                    ]); ?>
                    <?php if (!empty($brand_name)) : ?>
                        <span class="sh-product-hero__brand-tag"><?php echo esc_html($brand_name); ?></span>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="sh-product-hero__placeholder">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo-header.webp" alt="Saigon Horeca" class="sh-product-hero__placeholder-logo">
                </div>
            <?php endif; ?>
        </div>

        <!-- Trust Badges Strip (3 cam kết cốt lõi) -->
        <div class="sh-product-hero__trust-strip">
            <div class="sh-product-hero__trust-item">
                <div class="sh-product-hero__trust-icon-box">
                    <svg class="sh-product-hero__trust-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="22" height="22" aria-hidden="true">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <div class="sh-product-hero__trust-text">
                    <strong><?php echo esc_html(sgh_t('product_trust_genuine_title')); ?></strong>
                    <span><?php echo esc_html(sgh_t('product_trust_genuine_desc')); ?></span>
                </div>
            </div>
            <div class="sh-product-hero__trust-item">
                <div class="sh-product-hero__trust-icon-box">
                    <svg class="sh-product-hero__trust-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="22" height="22" aria-hidden="true">
                        <circle cx="12" cy="8" r="6"/>
                        <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                        <path d="m9 8 2 2 4-4"/>
                    </svg>
                </div>
                <div class="sh-product-hero__trust-text">
                    <strong><?php echo esc_html(sgh_t('product_trust_warranty_title')); ?></strong>
                    <span><?php echo esc_html(sgh_t('product_trust_warranty_desc')); ?></span>
                </div>
            </div>
            <div class="sh-product-hero__trust-item">
                <div class="sh-product-hero__trust-icon-box">
                    <svg class="sh-product-hero__trust-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="22" height="22" aria-hidden="true">
                        <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                </div>
                <div class="sh-product-hero__trust-text">
                    <strong><?php echo esc_html(sgh_t('product_trust_install_title')); ?></strong>
                    <span><?php echo esc_html(sgh_t('product_trust_install_desc')); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ CỘT PHẢI: THÔNG TIN SẢN PHẨM & BỘ NÚT CHUYỂN ĐỔI ═══ -->
    <div class="sh-product-hero__info-col">
        <!-- Badge điều hướng / Brand & Category -->
        <div class="sh-product-hero__badge-row">
            <?php if (!empty($cat_name)) : ?>
                <a href="<?php echo esc_url($cat_url); ?>" class="sh-product-hero__pill sh-product-hero__pill--cat">
                    <?php echo esc_html($cat_name); ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($brand_name)) : ?>
                <a href="<?php echo esc_url($brand_url); ?>" class="sh-product-hero__pill sh-product-hero__pill--brand">
                    <?php echo esc_html($brand_name); ?>
                </a>
            <?php endif; ?>
            <span class="sh-product-hero__pill sh-product-hero__pill--origin">
                <?php echo esc_html($origin); ?>
            </span>
        </div>

        <!-- Tiêu đề sản phẩm H1 -->
        <h1 class="sh-product-hero__title" itemprop="name">
            <?php the_title(); ?>
        </h1>

        <!-- Bảng thông tin nhanh dạng Card Grid -->
        <div class="sh-product-hero__quick-specs">
            <?php if (!empty($sku)) : ?>
                <div class="sh-product-hero__spec-box">
                    <span class="sh-product-hero__spec-label"><?php echo esc_html(sgh_t('product_spec_sku')); ?></span>
                    <strong class="sh-product-hero__spec-val" itemprop="sku"><?php echo esc_html($sku); ?></strong>
                </div>
            <?php endif; ?>
            <div class="sh-product-hero__spec-box">
                <span class="sh-product-hero__spec-label"><?php echo esc_html(sgh_t('product_spec_brand')); ?></span>
                <strong class="sh-product-hero__spec-val">
                    <?php if (!empty($brand_name)) : ?>
                        <a href="<?php echo esc_url($brand_url); ?>"><?php echo esc_html($brand_name); ?></a>
                    <?php else : ?>
                        <?php echo esc_html(sgh_t('product_trust_genuine_title')); ?>
                    <?php endif; ?>
                </strong>
            </div>
            <div class="sh-product-hero__spec-box">
                <span class="sh-product-hero__spec-label"><?php echo esc_html(sgh_t('product_spec_condition')); ?></span>
                <strong class="sh-product-hero__spec-val text-success"><?php echo esc_html(sgh_t('product_spec_condition_val')); ?></strong>
            </div>
            <div class="sh-product-hero__spec-box">
                <span class="sh-product-hero__spec-label"><?php echo esc_html(sgh_t('product_spec_origin')); ?></span>
                <strong class="sh-product-hero__spec-val"><?php echo esc_html($origin); ?></strong>
            </div>
        </div>

        <!-- Đoạn Sapo / Lead giới thiệu ứng dụng -->
        <?php if (!empty($clean_excerpt)) : ?>
            <div class="sh-product-hero__sapo" itemprop="description">
                <p><?php echo esc_html($clean_excerpt); ?></p>
            </div>
        <?php endif; ?>

        <!-- Bộ Nút Hành Động Kêu Gọi Chuyển Đổi (High-Conversion CTAs) -->
        <div class="sh-product-hero__actions">
            <a href="tel:<?php echo esc_attr($hotline_clean); ?>" class="sh-product-hero__cta-btn sh-product-hero__cta-btn--primary">
                <span class="sh-product-hero__cta-icon"><?php echo sh_icon('calculator', 'w-5 h-5'); ?></span>
                <span class="sh-product-hero__cta-content">
                    <strong><?php echo esc_html(sgh_t('product_cta_quote_title')); ?></strong>
                    <small><?php echo esc_html(sgh_t('product_cta_quote_subtitle')); ?></small>
                </span>
            </a>

            <a href="<?php echo esc_url($consult_url); ?>" <?php echo ($lang === 'vi') ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="sh-product-hero__cta-btn sh-product-hero__cta-btn--secondary">
                <span class="sh-product-hero__cta-icon"><?php echo sh_icon('phone-call', 'w-5 h-5'); ?></span>
                <span class="sh-product-hero__cta-content">
                    <strong><?php echo esc_html(sgh_t('product_cta_consult_title')); ?></strong>
                    <small><?php echo esc_html($consult_sub); ?></small>
                </span>
            </a>

            <?php if (!empty($source_url)) : ?>
                <a href="<?php echo esc_url($source_url); ?>" target="_blank" rel="nofollow noopener noreferrer" class="sh-product-hero__btn-link">
                    <?php echo sh_icon('file-text', 'w-4 h-4'); ?>
                    <span><?php echo esc_html(sgh_t('product_cta_catalogue')); ?></span>
                    <?php echo sh_icon('arrow-right', 'w-3.5 h-3.5'); ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- Hộp Cam Kết Dịch Vụ Dự Án B2B -->
        <div class="sh-product-hero__b2b-box">
            <div class="sh-product-hero__b2b-header">
                <?php echo sh_icon('check', 'w-4 h-4 text-primary'); ?>
                <span><?php echo esc_html(sgh_t('product_b2b_header')); ?></span>
            </div>
            <ul class="sh-product-hero__b2b-list">
                <li><?php echo esc_html(sgh_t('product_b2b_item1')); ?></li>
                <li><?php echo esc_html(sgh_t('product_b2b_item2')); ?></li>
                <li><?php echo esc_html(sgh_t('product_b2b_item3')); ?></li>
            </ul>
        </div>
    </div>
</div>
