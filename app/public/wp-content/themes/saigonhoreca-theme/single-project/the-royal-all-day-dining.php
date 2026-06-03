<?php
/**
 * Single Project Template — THE ROYAL - ALL DAY DINING
 * Thumbnail: the-royal-all-day-dining/the-royal-all-day-dining-khu-so-che-chau-rua.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/the-royal-all-day-dining/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--trd" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/hero');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/intro');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/concept');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/partnership');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/specs');
    get_template_part('template-parts/project-pillar/the-royal-all-day-dining/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
