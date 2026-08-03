<?php
/**
 * Single Project Template — Grand Marble
 * Thumbnail: grand-marble/grand-marble-thumbnail-project-cover.webp
 *
 * Render cho CPT project slug `grand-marble` qua single_template filter
 * (inc/engine/pillar-routes.php). Slug dai cu 301 ve day.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--gmarb" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/grand-marble/hero');
    get_template_part('template-parts/project-pillar/grand-marble/intro');
    get_template_part('template-parts/project-pillar/grand-marble/concept');
    get_template_part('template-parts/project-pillar/grand-marble/partnership');
    get_template_part('template-parts/project-pillar/grand-marble/specs');
    get_template_part('template-parts/project-pillar/grand-marble/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
