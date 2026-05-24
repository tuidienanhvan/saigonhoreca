<?php
/**
 * Mobile Menu Walker — Accordion style, pure CSS classes
 */
class SaigonHoreca_Mobile_Walker extends Walker_Nav_Menu {

    function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="sub-menu sh-mob-submenu">';
    }

    function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= "</ul>";
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes);

        if ( $depth === 0 ) {
            $output .= '<li class="sh-mob-item">';
            $output .= '<div class="sh-mob-item__row">';
            $output .= '<a href="' . esc_url($item->url) . '" class="sh-mob-item__link">' . esc_html($item->title) . '</a>';

            if ($has_children) {
                $output .= '<button class="mobile-submenu-toggle sh-mob-item__toggle">';
                $output .= sh_icon('chevron-down', 'sh-mob-item__toggle-icon');
                $output .= '</button>';
            }

            $output .= '</div>';
        } else {
            $output .= '<li class="sh-mob-sub-item">';
            $output .= '<a href="' . esc_url($item->url) . '" class="sh-mob-sub-item__link">';
            $output .= '<span class="sh-mob-sub-item__dot"></span>';
            $output .= '<span class="sh-mob-sub-item__text">' . esc_html($item->title) . '</span>';
            $output .= '</a>';
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}
