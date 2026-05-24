<?php
/**
 * Default Page Template
 *
 * Layout: 2-column (content + sidebar) on ≥1024px, stacked below.
 * Sidebar content comes from the shared `saigonhouse-sidebar` widget area
 * (see sidebar.php + functions.php). The old hardcoded "Dịch vụ nổi bật"
 * card has been moved to a default widget — admin can edit it at
 * Appearance → Widgets → Sidebar — Widget Area.
 */
get_header();
?>

<?php sh_breadcrumbs(); ?>

<div class="sh-page" data-aos="fade-up">

    <div class="sh-page__layout sh-with-sidebar">
        <main id="primary" class="sh-with-sidebar__main" data-aos="fade-up" data-aos-delay="80">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header>
                        <h1 class="sh-page__title" data-aos="fade-right" data-aos-delay="120"><?php the_title(); ?></h1>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="sh-page__thumbnail" data-aos="zoom-in" data-aos-delay="160"><?php the_post_thumbnail('large'); ?></div>
                        <?php endif; ?>
                    </header>
                    <div class="sh-page__content entry-content" data-aos="fade-up" data-aos-delay="200"><?php the_content(); ?></div>
                </article>
            <?php endwhile; ?>
        </main>

        <aside class="sh-with-sidebar__aside" data-aos="fade-left" data-aos-delay="140">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>

<?php get_footer(); ?>
