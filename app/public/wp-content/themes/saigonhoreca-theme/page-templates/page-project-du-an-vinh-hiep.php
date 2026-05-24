<?php
/**
 * Template Name: Project — Dự án Vĩnh Hiệp – Coffee Lab
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/du-an-vinh-hiep/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/hero');
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/intro');
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/concept');
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/partnership');
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/specs');
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/gallery');
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/related');
    get_template_part('template-parts/project-pillar/du-an-vinh-hiep/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
