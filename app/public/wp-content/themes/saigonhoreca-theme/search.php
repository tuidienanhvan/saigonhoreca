<?php
/**
 * Search Results Template
 * @package SaigonHoreca
 */
get_header();
?>

<?php sh_breadcrumbs(); ?>

<main id="primary" class="sh-search" tabindex="-1">
    <!-- Hero -->
    <section class="sh-search__hero search-hero-bg">
        <div class="sh-search__hero-inner">
            <span class="sh-search__badge">Kết quả tìm kiếm</span>
            <h1 class="sh-search__title">"<?php echo esc_html(get_search_query()); ?>"</h1>
            <p class="sh-search__count">
                <?php
                $total_results = $wp_query->found_posts;
                printf(_n('Tìm thấy <strong>%d</strong> kết quả phù hợp', 'Tìm thấy <strong>%d</strong> kết quả phù hợp', $total_results, 'saigonhoreca'), $total_results);
                ?>
            </p>

            <!-- Floating Search Form -->
            <div class="sh-search__form-wrap">
                <form role="search" method="get" class="sh-search__form" action="<?php echo home_url('/'); ?>">
                    <div class="sh-search__form-icon">
                        <?php echo sh_icon('search', 'sh-search__form-svg'); ?>
                    </div>
                    <input type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="Nhập từ khóa cần tìm..." class="sh-search__input" required />
                    <button type="submit" class="sh-search__submit">Tìm kiếm</button>
                </form>
            </div>
        </div>

        <?php get_template_part('template-parts/components/wave-divider'); ?>
    </section>

    <div class="sh-search__content sh-with-sidebar">
        <div class="sh-with-sidebar__main">
            <?php if (have_posts()) : ?>
                <div class="sh-search__grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/components/post-card'); ?>
                    <?php endwhile; ?>
                </div>

                <div class="sgh-pagination">
                    <?php
                    echo paginate_links([
                        'mid_size'  => 2,
                        'prev_text' => sh_icon('chevron-left', 'sh-search__page-icon'),
                        'next_text' => sh_icon('chevron-right', 'sh-search__page-icon'),
                        'type'      => 'plain'
                    ]);
                    ?>
                </div>

            <?php else : ?>
                <div class="sh-search__empty">
                    <div class="sh-search__empty-bar"></div>
                    <div class="sh-search__empty-icon-wrap">
                        <div class="sh-search__empty-ping"></div>
                        <?php echo sh_icon('search', 'sh-search__empty-icon'); ?>
                    </div>
                    <h2 class="sh-search__empty-title"><?php _e('Không tìm thấy kết quả', 'saigonhoreca'); ?></h2>
                    <p class="sh-search__empty-desc"><?php _e('Rất tiếc, không có bài viết nào khớp với từ khóa của bạn. Vui lòng thử lại bằng một từ khóa ngắn gọn hoặc chung chung hơn.', 'saigonhoreca'); ?></p>
                    <a href="<?php echo home_url('/'); ?>" class="sh-search__empty-btn">
                        <?php echo sh_icon('home', 'sh-search__empty-btn-icon'); ?>
                        <?php _e('Về trang chủ', 'saigonhoreca'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <aside class="sh-with-sidebar__aside">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</main>

<?php get_footer(); ?>
