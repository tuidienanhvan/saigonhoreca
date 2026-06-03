<?php
/**
 * Template Part: Latest News � Grid of 3 latest posts.
 * @package SaigonHouse
 */
?>
<?php $fp_id = (int) get_option('page_on_front'); ?>
<section class="sh-news sgh-cv-auto">
    <div class="sh-news__container">
        <div class="sh-news__header" data-aos="slide-up">
            <div>
                <span class="sh-news__label">C?m Nang X�y D?ng</span>
                <h2 class="sh-news__title">Tin T?c & <span class="sh-news__title-accent">S? Ki?n</span></h2>
            </div>
            <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="sh-news__view-all sh-news__view-all--desktop">
                Xem t?t c?
                <?php echo sh_icon('arrow-right', 'sh-news__arrow-icon'); ?>
            </a>
        </div>

        <div class="sh-news__grid">
            <?php
            // T-016: cap to 1 post (was 3) — each `post-card` template
            // ships ~25 DOM nodes. "Xem tất cả" link drives readers to
            // the blog archive for the rest.
            $news_query = new WP_Query([
                'post_type'           => 'post',
                'posts_per_page'      => 1,
                'ignore_sticky_posts' => 1,
                'no_found_rows'       => true,
                'orderby'             => 'date',
                'order'               => 'DESC',
            ]);

            $news_index = 0;
            if ($news_query->have_posts()) :
                while ($news_query->have_posts()) : $news_query->the_post();
            ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $news_index * 100; ?>">
                    <?php get_template_part('template-parts/components/post-card'); ?>
                </div>
            <?php
                $news_index++;
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <div class="sh-news__view-all-mobile">
            <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="sh-news__view-all-btn">
                Xem t?t c? tin t?c
                <?php echo sh_icon('arrow-right', 'sh-news__arrow-icon'); ?>
            </a>
        </div>
    </div>
</section>
