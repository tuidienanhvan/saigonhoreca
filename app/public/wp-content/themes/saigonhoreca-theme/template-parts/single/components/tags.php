<?php
/**
 * single.php part — khối tag (.sh-single__tags) của bài viết.
 * Tự lấy tag từ bài hiện tại bằng get_the_tags(), không cần truyền biến.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$tags = get_the_tags(); if ($tags) : ?>
    <div class="sh-single__tags">
        <?php echo sh_icon('tag', 'sh-single__tags-icon'); ?>
        <?php foreach ($tags as $tag) : ?>
            <a href="<?php echo get_tag_link($tag->term_id); ?>" class="sh-single__tag">#<?php echo $tag->name; ?></a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
