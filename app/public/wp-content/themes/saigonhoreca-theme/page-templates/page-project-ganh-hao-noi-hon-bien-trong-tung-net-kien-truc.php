<?php
/**
 * Template Name: Project — Gành Hào
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/hero');
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/intro');
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/concept');
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/partnership');
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/specs');
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/gallery');
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/related');
    get_template_part('template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
