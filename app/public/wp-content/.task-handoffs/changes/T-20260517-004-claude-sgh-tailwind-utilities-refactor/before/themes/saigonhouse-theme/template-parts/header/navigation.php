<?php
/**
 * Template Part: Desktop Navigation
 *
 * @package SaigonHouse
 */
?>
<nav class="sh-nav" aria-label="Main navigation">
    <?php
    if (has_nav_menu('primary')) {
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'sh-nav__list',
            'item_spacing'   => 'discard',
            'walker'         => new SaigonHouse_Desktop_Walker(),
        ));
    }
    ?>
</nav>
