<?php
/**
 * Template Part: Social Share Buttons
 * @package SaigonHoreca
 */

$permalink   = get_permalink();
$share_url   = rawurlencode($permalink);
$share_title = rawurlencode(get_the_title());
$theme_uri   = get_template_directory_uri();
$fb_url      = 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url;
$zalo_url    = 'https://zalo.me/share?url=' . $share_url . '&text=' . $share_title;
$lang        = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
$share_labels = [
    'vi' => 'Chia sẻ:',
    'en' => 'Share:',
    'ja' => 'シェア:',
];
$fb_labels = [
    'vi' => 'Chia sẻ lên Facebook',
    'en' => 'Share on Facebook',
    'ja' => 'Facebookでシェア',
];
$zalo_labels = [
    'vi' => 'Chia sẻ qua Zalo',
    'en' => 'Share via Zalo',
    'ja' => 'Zaloでシェア',
];
?>

<div class="sh-share">
    <div class="sh-share__inner">
        <span class="sh-share__label">
            <?php echo sh_icon('share', 'sh-share__label-icon'); ?> <?php echo esc_html($share_labels[$lang] ?? $share_labels['vi']); ?>
        </span>

        <a href="<?php echo esc_url($fb_url); ?>" data-share-action="popup" rel="nofollow noopener noreferrer" class="sh-share__btn sh-share__btn--facebook" aria-label="<?php echo esc_attr($fb_labels[$lang] ?? $fb_labels['vi']); ?>">
            <img src="<?php echo esc_url($theme_uri . '/assets/images/facebook-icon.webp'); ?>" alt="" width="18" height="18" class="sh-share__btn-img" loading="lazy">
            Facebook
        </a>

        <a href="<?php echo esc_url($zalo_url); ?>" target="_blank" rel="nofollow noopener noreferrer" class="sh-share__btn sh-share__btn--zalo" aria-label="<?php echo esc_attr($zalo_labels[$lang] ?? $zalo_labels['vi']); ?>">
            <img src="<?php echo esc_url($theme_uri . '/assets/images/zalo-icon.webp'); ?>" alt="" width="18" height="18" class="sh-share__btn-img" loading="lazy">
            Zalo
        </a>

        <button data-share-action="copy" data-share-url="<?php echo esc_attr($permalink); ?>" class="sh-share__btn sh-share__btn--copy sh-copy-btn" aria-label="Copy link">
            <?php echo sh_icon('share', 'sh-share__copy-icon'); ?>
            <span>Copy link</span>
        </button>
    </div>
</div>
