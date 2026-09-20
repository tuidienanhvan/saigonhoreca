<?php
/**
 * single.php part — hộp tác giả (.sh-single__author).
 * Tên hiển thị nhận qua $args['author'] (custom author meta đã resolve ở single.php).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$lang          = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
$custom_author = isset($args['author']) ? $args['author'] : (get_the_author() ?: 'Saigon Horeca');

$author_prefixes = [
    'vi' => 'Tác giả:',
    'en' => 'Author:',
    'ja' => '著者:',
];

$default_bios = [
    'vi' => 'Đội ngũ Saigon Horeca — chuyên tư vấn thiết bị bếp công nghiệp, quầy bar và giải pháp horeca cho nhà hàng, khách sạn.',
    'en' => 'The Saigon Horeca Team — specialized in commercial kitchen equipment, bar engineering, and turnkey hospitality solutions.',
    'ja' => 'Saigon Horeca 専門チーム — 業務用厨房設備、バー設計、ホテル・飲食施設向けソリューションをご提案。',
];

$all_posts_labels = [
    'vi' => 'Xem tất cả bài viết',
    'en' => 'View all articles',
    'ja' => 'すべての記事を見る',
];
?>
<div class="sh-single__author">
    <div class="sh-single__author-avatar">
        <?php echo get_avatar(get_the_author_meta('ID'), 200, '', '', ['class' => 'sh-single__author-img']); ?>
    </div>
    <div class="sh-single__author-info">
        <h4 class="sh-single__author-name"><?php echo esc_html($author_prefixes[$lang] ?? $author_prefixes['vi']); ?> <?php echo esc_html($custom_author); ?></h4>
        <p class="sh-single__author-bio"><?php echo get_the_author_meta('description') ?: esc_html($default_bios[$lang] ?? $default_bios['vi']); ?></p>
        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="sh-single__author-link"><?php echo esc_html($all_posts_labels[$lang] ?? $all_posts_labels['vi']); ?></a>
    </div>
</div>
