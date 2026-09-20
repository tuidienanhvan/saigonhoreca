<?php
/**
 * single.php part — khối mục lục (TOC) của bài viết.
 * Render container #toc-container rỗng; danh sách được JS đổ vào lúc runtime.
 * Chỉ dùng cho post (nhánh !$is_product trong single.php).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;
$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
$toc_titles = [
    'vi' => 'Nội dung bài viết',
    'en' => 'Table of Contents',
    'ja' => '目次',
];
$toc_toggles = [
    'vi' => '[Thu gọn]',
    'en' => '[Collapse]',
    'ja' => '[閉じる]',
];
?>
<div id="toc-container" class="sh-single__toc hidden">
    <div class="sh-single__toc-header">
        <h3 class="sh-single__toc-title">
            <?php echo sh_icon('list', 'sh-single__toc-icon'); ?> <?php echo esc_html($toc_titles[$lang] ?? $toc_titles['vi']); ?>
        </h3>
        <button id="toc-toggle" class="sh-single__toc-toggle"><?php echo esc_html($toc_toggles[$lang] ?? $toc_toggles['vi']); ?></button>
    </div>
    <ul id="toc-list" class="sh-single__toc-list"></ul>
</div>
