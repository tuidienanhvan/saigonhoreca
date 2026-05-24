<?php
/**
 * Template Name: Project — Yuzu Omakase
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/yuzu-omakase/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/yuzu-omakase/hero');
    get_template_part('template-parts/project-pillar/yuzu-omakase/intro');
    get_template_part('template-parts/project-pillar/yuzu-omakase/concept');
    get_template_part('template-parts/project-pillar/yuzu-omakase/partnership');
    get_template_part('template-parts/project-pillar/yuzu-omakase/specs');
    get_template_part('template-parts/project-pillar/yuzu-omakase/gallery');
    get_template_part('template-parts/project-pillar/yuzu-omakase/related');
    get_template_part('template-parts/project-pillar/yuzu-omakase/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
