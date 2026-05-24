<?php
/**
 * Template Name: Project — TRƯỜNG MẦM NON TRINH VƯƠNG
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/pho-24/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/pho-24/hero');
    get_template_part('template-parts/project-pillar/pho-24/intro');
    get_template_part('template-parts/project-pillar/pho-24/concept');
    get_template_part('template-parts/project-pillar/pho-24/partnership');
    get_template_part('template-parts/project-pillar/pho-24/specs');
    get_template_part('template-parts/project-pillar/pho-24/gallery');
    get_template_part('template-parts/project-pillar/pho-24/related');
    get_template_part('template-parts/project-pillar/pho-24/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
