<?php
/**
 * Template Name: Project — SPICE WORLD HOT POT
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/spice-world-hotpot/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/spice-world-hotpot/hero');
    get_template_part('template-parts/project-pillar/spice-world-hotpot/intro');
    get_template_part('template-parts/project-pillar/spice-world-hotpot/concept');
    get_template_part('template-parts/project-pillar/spice-world-hotpot/partnership');
    get_template_part('template-parts/project-pillar/spice-world-hotpot/specs');
    get_template_part('template-parts/project-pillar/spice-world-hotpot/gallery');
    get_template_part('template-parts/project-pillar/spice-world-hotpot/related');
    get_template_part('template-parts/project-pillar/spice-world-hotpot/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
