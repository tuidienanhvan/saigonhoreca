<?php
/**
 * Template Name: Project — Tales by Chapter
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/tales-by-chapter/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/tales-by-chapter/hero');
    get_template_part('template-parts/project-pillar/tales-by-chapter/intro');
    get_template_part('template-parts/project-pillar/tales-by-chapter/concept');
    get_template_part('template-parts/project-pillar/tales-by-chapter/partnership');
    get_template_part('template-parts/project-pillar/tales-by-chapter/specs');
    get_template_part('template-parts/project-pillar/tales-by-chapter/gallery');
    get_template_part('template-parts/project-pillar/tales-by-chapter/related');
    get_template_part('template-parts/project-pillar/tales-by-chapter/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
