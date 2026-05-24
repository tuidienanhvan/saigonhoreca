<?php
/**
 * Template Name: Project — THE ROYAL - ALL DAY DINING
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/the-royal-all-day-dining/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/hero');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/intro');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/concept');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/partnership');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/specs');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/gallery');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/related');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
