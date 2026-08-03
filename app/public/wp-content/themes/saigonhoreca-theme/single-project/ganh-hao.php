<?php
/**
 * Single Project Template — Gành Hào
 * Thumbnail: ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/ganh-hao-mui-da-toan-canh-tu-tren-cao-mai-cong-luon-song.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/ganh-hao/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--ganh" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/ganh-hao/hero');
    get_template_part('template-parts/project-pillar/ganh-hao/intro');
    get_template_part('template-parts/project-pillar/ganh-hao/concept');
    get_template_part('template-parts/project-pillar/ganh-hao/partnership');
    get_template_part('template-parts/project-pillar/ganh-hao/specs');
    get_template_part('template-parts/project-pillar/ganh-hao/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
