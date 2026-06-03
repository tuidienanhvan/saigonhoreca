<?php
/**
 * Single Project Template — Amdang Typhoon
 * Thumbnail: amdang-typhoon/amdang-typhoon-loi-vao-nhahang.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/amdang-typhoon/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--adt" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/amdang-typhoon/hero');
    get_template_part('template-parts/project-pillar/amdang-typhoon/intro');
    get_template_part('template-parts/project-pillar/amdang-typhoon/concept');
    get_template_part('template-parts/project-pillar/amdang-typhoon/partnership');
    get_template_part('template-parts/project-pillar/amdang-typhoon/specs');
    get_template_part('template-parts/project-pillar/amdang-typhoon/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
