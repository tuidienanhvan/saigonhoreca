<?php
/**
 * Desktop Menu Walker — Pure CSS classes
 *
 * T-016: depth-0 dropdowns wrapped in `<template>` so their ~80 dropdown
 * nodes don't count toward Lighthouse `dom-size`. JS hydrates each
 * template on first hover/focus of the parent nav item — see
 * `main-modern.js` "Desktop dropdown lazy-hydrate" block.
 */
class SaigonHoreca_Desktop_Walker extends Walker_Nav_Menu {

    private $is_megamenu = false;
    private $should_flip_right = false;

    function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $mod = $this->is_megamenu ? 'sh-dropdown--mega' : 'sh-dropdown--standard';
            $pos = $this->should_flip_right ? 'sh-dropdown--right' : 'sh-dropdown--left';

            // Wrap the dropdown panel in <template>. Browser keeps the
            // template's contents in an off-DOM document fragment that
            // Lighthouse does NOT count toward `dom-size`. JS clones it
            // into the parent <li> on first hover.
            $output .= '<template class="sh-dropdown-tpl">';
            $output .= '<div class="sh-dropdown ' . $mod . ' ' . $pos . '">';

            $list_mod = $this->is_megamenu ? 'sh-dropdown__list--grid' : 'sh-dropdown__list--stack';
            $output .= '<ul class="sh-dropdown__list ' . $list_mod . '">';
        } else {
            $dir = $this->is_megamenu ? 'sh-flyout--left' : 'sh-flyout--right';
            $output .= '<ul class="sh-flyout ' . $dir . '">';
        }
    }

    function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</ul></div></template>';
            $this->is_megamenu = false;
            $this->should_flip_right = false;
        } else {
            $output .= "</ul>";
        }
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes);

        if ( $depth === 0 ) {
            $is_long_menu = (strpos($item->title, 'Thiết Kế') !== false || in_array('mega-menu', $classes));
            if ($is_long_menu) $this->is_megamenu = true;

            $should_flip = (strpos($item->title, 'Cẩm Nang') !== false || strpos($item->title, 'Thiết Kế') !== false || strpos($item->title, 'Liên Hệ') !== false);
            if ($should_flip) $this->should_flip_right = true;

            // T-016: mark items that own a dropdown so the JS lazy-hydrate
            // handler in main-modern.js can find them on hover/focus.
            $dropdown_attr = $has_children ? ' data-sgh-dropdown="lazy"' : '';
            $output .= '<li class="sh-nav-item"' . $dropdown_attr . '>';
            $output .= '<a href="' . esc_url($item->url) . '" class="sh-nav-item__link">';
            $output .= esc_html($item->title);

            if ($has_children) {
                $output .= '<span class="sh-nav-item__chevron">' . sh_icon('chevron-down', 'sh-nav-item__chevron-icon') . '</span>';
            }

            $output .= '</a>';
        } else {
            $li_class = $this->is_megamenu ? 'sh-dropdown-item' : 'sh-dropdown-item sh-dropdown-item--bordered';
            $output .= '<li class="' . $li_class . '">';
            $output .= '<a href="' . esc_url($item->url) . '" class="sh-dropdown-item__link">';
            $output .= esc_html($item->title);
            $output .= '</a>';
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>";
    }
}
