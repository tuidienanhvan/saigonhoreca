<?php
/**
 * Template Name: Project — GodMother Friendship
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/godmother-friendship/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/godmother-friendship/hero');
    get_template_part('template-parts/project-pillar/godmother-friendship/intro');
    get_template_part('template-parts/project-pillar/godmother-friendship/concept');
    get_template_part('template-parts/project-pillar/godmother-friendship/partnership');
    get_template_part('template-parts/project-pillar/godmother-friendship/specs');
    get_template_part('template-parts/project-pillar/godmother-friendship/gallery');
    get_template_part('template-parts/project-pillar/godmother-friendship/related');
    get_template_part('template-parts/project-pillar/godmother-friendship/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
