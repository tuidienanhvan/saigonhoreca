<?php
/**
 * Single Project Template — The Cheezy Time
 * Thumbnail: the-cheezy-time/the-cheezy-time-hero.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/the-cheezy-time/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--tct" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/the-cheezy-time/hero');
    get_template_part('template-parts/project-pillar/the-cheezy-time/intro');
    get_template_part('template-parts/project-pillar/the-cheezy-time/concept');
    get_template_part('template-parts/project-pillar/the-cheezy-time/partnership');
    get_template_part('template-parts/project-pillar/the-cheezy-time/specs');
    get_template_part('template-parts/project-pillar/the-cheezy-time/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
