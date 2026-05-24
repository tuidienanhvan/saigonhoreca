<?php
/**
 * Template Name: Project — Skyloft by Glow
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/skyloft-by-glow/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/skyloft-by-glow/hero');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/intro');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/concept');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/partnership');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/specs');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/gallery');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/related');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
