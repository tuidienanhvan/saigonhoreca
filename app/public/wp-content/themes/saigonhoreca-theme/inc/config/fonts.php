<?php
/**
 * Font & color-token configuration — saigonhoreca
 *
 * Nguồn duy nhất cho font family + palette CSS variables đang inline vào
 * mọi trang qua wp_add_inline_style('theme-tokens', ...) trong enqueue.php.
 *
 * fonts.mode: 'google' (mặc định, đang chạy) | 'self' (Phase 8 — woff2
 * subset self-host, A/B đo Lighthouse trước khi đổi mặc định). Đọc qua
 * sgh_config('fonts.mode', 'google') khi loader.php sẵn sàng.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_fonts_mode')) {
    /**
     * 'google' (mặc định, ĐANG CHẠY — không đổi mặc định khi chưa có đo A/B
     * thật trên production) | 'self' (woff2 mirror chính xác của Google,
     * assets/css/fonts-self-host.css, đã tạo sẵn ở Phase 8 — bật thử bằng
     * filter `sgh_fonts_mode` hoặc override tại đây, rồi đo Lighthouse/PSI
     * trước khi đổi mặc định).
     *
     * KHÔNG preload font ở bất kỳ mode nào — xem docs/PERF-DECISIONS.md §3.
     */
    function sgh_fonts_mode() {
        return apply_filters('sgh_fonts_mode', 'google');
    }
}

if (!function_exists('sgh_font_families')) {
    function sgh_font_families() {
        return [
            'body'    => '"Be Vietnam Pro", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            'heading' => '"Be Vietnam Pro", "Lexend", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            'serif'   => '"Lora", Georgia, serif',
        ];
    }
}

if (!function_exists('sgh_color_palette')) {
    /**
     * Legacy Astra/Elementor global-color CSS var names — giữ nguyên tên
     * biến (ast-global-color-*, e-global-color-astglobalcolor*) vì nhiều
     * pillar CSS cũ vẫn tham chiếu chúng; đừng đổi tên khi không cần thiết.
     */
    function sgh_color_palette() {
        return [
            '--ast-global-color-0'                  => '#f9c349',
            '--ast-global-color-1'                  => '#ffb100',
            '--ast-global-color-2'                  => '#000000',
            '--ast-global-color-3'                  => '#242525',
            '--ast-global-color-4'                  => '#f9fafb',
            '--ast-global-color-5'                  => '#ffffff',
            '--ast-global-color-6'                  => '#e2e8f0',
            '--ast-global-color-7'                  => '#cbd5e1',
            '--ast-global-color-8'                  => '#94a3b8',
            '--e-global-color-astglobalcolor0'      => '#f9c349',
            '--e-global-color-astglobalcolor1'      => '#ffb100',
            '--e-global-color-astglobalcolor2'      => '#000000',
            '--e-global-color-astglobalcolor3'      => '#242525',
            '--e-global-color-astglobalcolor4'      => '#f9fafb',
            '--e-global-color-astglobalcolor5'      => '#ffffff',
            '--e-global-color-astglobalcolor6'      => '#e2e8f0',
            '--e-global-color-astglobalcolor7'      => '#cbd5e1',
            '--e-global-color-astglobalcolor8'      => '#94a3b8',
        ];
    }
}

if (!function_exists('sgh_render_font_and_palette_css')) {
    /** Render CSS custom properties block cho wp_add_inline_style('theme-tokens', ...). */
    function sgh_render_font_and_palette_css() {
        $f = sgh_font_families();
        $vars = "--font-body: {$f['body']};\n            --font-heading: {$f['heading']};\n            --font-serif: {$f['serif']};";
        foreach (sgh_color_palette() as $name => $value) {
            $vars .= "\n            {$name}: {$value};";
        }
        return ":root {\n            {$vars}\n        }\n        body { font-family: var(--font-body); }\n        h1, h2, h3, h4, h5, h6, .font-sans { font-family: var(--font-heading) !important; }\n        .e-font-icon-svg { display: inline-block; width: 1em; height: 1em; fill: currentColor; }";
    }
}
