<?php
/**
 * Single Project Template — G.CUP COFFEE & BISTRO
 * Thumbnail: g-cup-coffee-bistro/g-cup-coffee-bistro-quay-bar-da-trang-van-may.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/g-cup-coffee-bistro/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--gcb" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/g-cup-coffee-bistro/hero');
    get_template_part('template-parts/project-pillar/g-cup-coffee-bistro/intro');
    get_template_part('template-parts/project-pillar/g-cup-coffee-bistro/concept');
    get_template_part('template-parts/project-pillar/g-cup-coffee-bistro/partnership');
    get_template_part('template-parts/project-pillar/g-cup-coffee-bistro/specs');
    get_template_part('template-parts/project-pillar/g-cup-coffee-bistro/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
