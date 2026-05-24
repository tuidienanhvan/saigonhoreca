<?php
/**
 * Tag Archive Template
 */
get_header();
?>

<main id="primary" class="sh-archive" tabindex="-1">
    <div class="sh-archive__content sh-with-sidebar">
        <div class="sh-with-sidebar__main">
            <div class="sh-archive__header">
                <h1 class="sh-archive__title"><?php single_tag_title(__('Tag: ', 'saigonhoreca')); ?></h1>
                <?php if (tag_description()) : ?>
                    <div class="sh-archive__desc"><?php echo tag_description(); ?></div>
                <?php endif; ?>
                <p class="sh-archive__count">
                    <?php
                    global $wp_query;
                    printf(_n('Tìm thấy %d bài viết', 'Tìm thấy %d bài viết', $wp_query->found_posts, 'saigonhoreca'), $wp_query->found_posts);
                    ?>
                </p>
            </div>

            <?php if (have_posts()) : ?>
                <div class="sh-archive__grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/components/post-card'); ?>
                    <?php endwhile; ?>
                </div>
                <div class="sh-archive__pagination">
                    <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '« Trước', 'next_text' => 'Sau »']); ?>
                </div>
            <?php else : ?>
                <div class="sh-archive__empty">
                    <div class="sh-archive__empty-emoji">📝</div>
                    <h2 class="sh-archive__empty-title"><?php _e('Không có bài viết nào', 'saigonhoreca'); ?></h2>
                    <a href="<?php echo home_url(); ?>" class="sh-archive__empty-btn"><?php _e('Về trang chủ', 'saigonhoreca'); ?></a>
                </div>
            <?php endif; ?>
        </div>
        <aside class="sh-with-sidebar__aside">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</main>

<?php get_footer(); ?>
