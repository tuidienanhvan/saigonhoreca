<?php
/**
 * Template Name: Liên Hệ
 *
 * Page template for the "Liên Hệ" page (slug `lien-he`). Orchestrates
 * template-parts/contact/* — sections extracted directly from the
 * production saigonhoreca.vn /lien-he/ scrape.
 *
 * @package SaigonHoreca
 */

get_header(); ?>

<main id="primary" class="sh-contact" tabindex="-1">
    <?php
    // 1. Hero — Liên hệ Sài Gòn Horeca title block
    get_template_part('template-parts/contact/hero');

    // 2. Form — contact info + form (block 2 of /lien-he/ scrape includes both)
    get_template_part('template-parts/contact/form');

    // 3. Map — Google Maps embed
    get_template_part('template-parts/contact/map');
    ?>
</main>

<?php get_footer();
