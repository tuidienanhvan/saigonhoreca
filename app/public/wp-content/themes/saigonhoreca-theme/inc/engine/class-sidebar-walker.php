<?php
/**
 * Sidebar Menu Walker — renders the `sidebar_menu` location as BEM CTA items.
 *
 * How icons are resolved (in priority order):
 *   1. Menu item "Description" field — exact icon name (e.g. "home", "hammer")
 *   2. Menu item "CSS Classes" field — any class prefixed with `icon-`
 *      (e.g. `icon-home` → icon name `home`)
 *   3. Fallback — uses the `arrow-right` icon
 *
 * Admin enables the Description/CSS Classes fields in:
 *   Appearance → Menus → Screen Options (top-right dropdown).
 *
 * Depth is restricted to 1 level — sub-menus are ignored.
 *
 * @package SaigonHoreca
 */

class SaigonHoreca_Sidebar_Walker extends Walker_Nav_Menu {

    /** Default icon when no icon is specified on the menu item. */
    private const DEFAULT_ICON = 'arrow-right';

    /** No opening <ul>/<div> — wrapper comes from wp_nav_menu's items_wrap. */
    public function start_lvl(&$output, $depth = 0, $args = null) {}
    public function end_lvl(&$output, $depth = 0, $args = null) {}

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if ($depth > 0) return; // flat — ignore nested items

        $icon    = $this->resolveIcon($item);
        $classes = $this->resolveClasses($item);
        $url     = !empty($item->url) ? $item->url : '#';
        $label   = apply_filters('the_title', $item->title, $item->ID);

        $target  = !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $rel     = !empty($item->xfn)    ? ' rel="' . esc_attr($item->xfn) . '"'    : '';

        $output .= sprintf(
            '<a href="%s"%s%s class="%s">',
            esc_url($url),
            $target,
            $rel,
            esc_attr($classes)
        );
        $output .= '<div class="sh-sidebar__cta-icon">';
        $output .= sh_icon($icon, 'sh-sidebar__cta-svg');
        $output .= '</div>';
        $output .= '<span class="sh-sidebar__cta-label">' . esc_html($label) . '</span>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth > 0) return;
        $output .= '</a>';
    }

    /** Extract icon name from menu item description or CSS class prefix. */
    private function resolveIcon(object $item): string {
        // 1. Description field — trimmed (admin pastes "home" there)
        $desc = isset($item->description) ? trim((string) $item->description) : '';
        if ($desc !== '' && preg_match('/^[a-z0-9\-]+$/i', $desc)) {
            return $desc;
        }

        // 2. CSS class prefixed with `icon-`
        if (!empty($item->classes) && is_array($item->classes)) {
            foreach ($item->classes as $class) {
                if (strpos($class, 'icon-') === 0) {
                    $name = substr($class, 5);
                    if ($name !== '') return $name;
                }
            }
        }

        return self::DEFAULT_ICON;
    }

    /** Merge default BEM class with any admin-supplied classes (excluding icon-* markers). */
    private function resolveClasses(object $item): string {
        $base = ['sh-sidebar__cta-item'];

        if (!empty($item->classes) && is_array($item->classes)) {
            foreach ($item->classes as $class) {
                $class = trim((string) $class);
                if ($class === '' || strpos($class, 'icon-') === 0) continue;
                // Skip WP's auto-generated classes that aren't useful here
                if (in_array($class, ['menu-item', 'menu-item-type-custom', 'menu-item-object-custom'], true)) continue;
                $base[] = $class;
            }
        }

        return implode(' ', array_unique($base));
    }
}
