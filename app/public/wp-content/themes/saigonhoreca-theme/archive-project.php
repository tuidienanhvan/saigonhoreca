<?php
/**
 * CPT `project` archive template — /du-an/
 *
 * Saigonhouse-theme pattern: archive template orchestrates template-parts.
 * Section content extracted directly from the production saigonhoreca.vn
 * /du-an/ scrape (3 Elementor blocks: hero, trust, projects-grid).
 *
 * The projects-grid block contains the production HTML for the grid; over
 * time it can be replaced with a dynamic WP_Query over the imported CPT
 * `project` posts (which are already in wp_posts after Phase 2 data
 * migration) — see the inline note at the bottom of the file.
 *
 * @package SaigonHoreca
 */

get_header(); ?>

<main id="primary" class="sh-archive sh-archive--project" tabindex="-1">
    <?php
    // 1. Hero — featured projects banner image strip
    get_template_part('template-parts/archive-project/hero');

    // 2. Trust — Uy Tín và Chất Lượng
    get_template_part('template-parts/archive-project/trust');

    // 3. Projects grid — Những Dự Án nổi bật của Saigon Horeca
    //
    // The current implementation renders the production-scraped grid
    // verbatim so the page matches saigonhoreca.vn pixel-for-pixel.
    // To switch to dynamic rendering once styling is locked, replace
    // this template-part with a WP_Query over `project` CPT — see
    // template-parts/components/project-card.php for the card markup.
    get_template_part('template-parts/archive-project/projects-grid');
    ?>
</main>

<?php get_footer();
