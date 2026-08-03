<?php
/**
 * Single Project Template — MÂM MÂM EATERY & LOUNGE
 * Thumbnail: mam-mam-eatery-lounge-nang-tam-mam-com-viet/mam-mam-eatery-lounge-nang-tam-mam-com-viet-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/mam-mam-eatery-lounge-nang-tam-mam-com-viet/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/mam-mam-eatery/hero');
    get_template_part('template-parts/project-pillar/mam-mam-eatery/intro');
    get_template_part('template-parts/project-pillar/mam-mam-eatery/concept');
    get_template_part('template-parts/project-pillar/mam-mam-eatery/partnership');
    get_template_part('template-parts/project-pillar/mam-mam-eatery/specs');
    get_template_part('template-parts/project-pillar/mam-mam-eatery/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
