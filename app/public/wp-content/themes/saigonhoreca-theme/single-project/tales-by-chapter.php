<?php
/**
 * Single Project Template — Tales by Chapter
 * Thumbnail: 2025/04/du-an-sheh-fung.jpg
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/tales-by-chapter/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--tbc" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/tales-by-chapter/hero');
    get_template_part('template-parts/project-pillar/tales-by-chapter/intro');
    get_template_part('template-parts/project-pillar/tales-by-chapter/concept');
    get_template_part('template-parts/project-pillar/tales-by-chapter/partnership');
    get_template_part('template-parts/project-pillar/tales-by-chapter/specs');
    get_template_part('template-parts/project-pillar/tales-by-chapter/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
