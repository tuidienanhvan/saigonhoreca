<?php
/**
 * Category Archive Template
 */
get_header();
?>

<?php sh_breadcrumbs(); ?>

<main id="primary" class="sh-archive" tabindex="-1">
    <section class="category-hero-bg sh-archive__hero">
        <div class="sh-archive__hero-inner">
            <span class="sh-archive__badge">Chuyên mục</span>
            <h1 class="sh-archive__title"><?php single_cat_title(); ?></h1>
            <?php if (category_description()) : ?>
                <div class="sh-archive__desc"><?php echo wp_kses_post(category_description()); ?></div>
            <?php endif; ?>
            <p class="sh-archive__count">
                <?php
                global $wp_query;
                printf(_n('Có tổng cộng <strong>%d</strong> bài viết', 'Có tổng cộng <strong>%d</strong> bài viết', $wp_query->found_posts, 'saigonhoreca'), $wp_query->found_posts);
                ?>
            </p>
        </div>
        <?php get_template_part('template-parts/components/wave-divider'); ?>
    </section>

    <div class="sh-archive__content">
        <?php if (have_posts()) : ?>
            <div class="sh-archive__grid">
                <?php
                $post_index = 0;
                while (have_posts()) : the_post();
                    $loading = ($post_index === 0) ? 'eager' : 'lazy';
                    get_template_part('template-parts/components/post-card', null, [
                        'loading' => $loading
                    ]);
                    $post_index++;
                endwhile;
                ?>
            </div>
            <div class="sgh-pagination">
                <?php echo paginate_links([
                    'mid_size'  => 2,
                    'prev_text' => sh_icon('chevron-left', '') . '<span style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0;">Trang trước</span>',
                    'next_text' => sh_icon('chevron-right', '') . '<span style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0;">Trang sau</span>',
                    'type'      => 'plain'
                ]); ?>
            </div>
        <?php else : ?>
            <div class="sh-archive__empty">
                <div class="sh-archive__empty-icon"><?php echo sh_icon('folder-open', ''); ?></div>
                <h2 class="sh-archive__empty-title"><?php _e('Thư mục trống', 'saigonhoreca'); ?></h2>
                <p class="sh-archive__empty-desc"><?php _e('Hiện tại chưa có bài viết nào trong chuyên mục này.', 'saigonhoreca'); ?></p>
                <a href="<?php echo home_url('/'); ?>" class="sh-archive__empty-btn"><?php echo sh_icon('home', ''); ?> <?php _e('Về trang chủ', 'saigonhoreca'); ?></a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
