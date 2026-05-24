<?php
/**
 * Convert all sgh_t() calls in pillar files to __() and extract .po entries.
 * Run from theme root: php scripts/convert-sgh-t-to-__.php
 *
 * Transformations:
 *   esc_html(sgh_t('VN', 'EN'))    → esc_html__('VN', 'saigonhoreca')
 *   esc_attr(sgh_t('VN', 'EN'))    → esc_attr__('VN', 'saigonhoreca')
 *   sgh_t('VN', 'EN')             → __('VN', 'saigonhoreca')
 */

$theme_dir = 'C:/Users/Administrator/Local Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme';
$pillar_dir = $theme_dir . '/template-parts/project-pillar';
$languages_dir = $theme_dir . '/languages';

// Skip reference projects
$skip = ['amdang-typhoon', 'godmother-friendship', 'grand-marble-thuong-hieu-banh-cao-cap-nhat-ban', 'bambino-saigonhoreca'];

$pot_entries = [];
$total_replaced = 0;
$files_changed = 0;

$dirs = glob($pillar_dir . '/*', GLOB_ONLYDIR);
foreach ($dirs as $dir) {
    $slug = basename($dir);
    if (in_array($slug, $skip)) continue;

    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $original = $content;
        $file_ref = str_replace($theme_dir . '/', '', $file);
        $changed = false;

        // Pattern 1: esc_html(sgh_t('VN', 'EN'))
        $content = preg_replace_callback(
            "/esc_html\(\s*sgh_t\(\s*'((?:[^'\\\\]|\\\\.)*)'\s*,\s*'((?:[^'\\\\]|\\\\.)*)'\s*\)\s*\)/s",
            function ($m) use ($file_ref, &$pot_entries, &$changed) {
                $changed = true;
                $pot_entries[$m[1]] = $m[2];
                return "esc_html__('" . $m[1] . "', 'saigonhoreca')";
            },
            $content
        );

        // Pattern 2: esc_attr(sgh_t('VN', 'EN'))
        $content = preg_replace_callback(
            "/esc_attr\(\s*sgh_t\(\s*'((?:[^'\\\\]|\\\\.)*)'\s*,\s*'((?:[^'\\\\]|\\\\.)*)'\s*\)\s*\)/s",
            function ($m) use ($file_ref, &$pot_entries, &$changed) {
                $changed = true;
                $pot_entries[$m[1]] = $m[2];
                return "esc_attr__('" . $m[1] . "', 'saigonhoreca')";
            },
            $content
        );

        // Pattern 3: plain sgh_t('VN', 'EN') — bare, not inside esc_html/esc_attr
        $content = preg_replace_callback(
            "/sgh_t\(\s*'((?:[^'\\\\]|\\\\.)*)'\s*,\s*'((?:[^'\\\\]|\\\\.)*)'\s*\)/s",
            function ($m) use ($file_ref, &$pot_entries, &$changed) {
                $changed = true;
                $pot_entries[$m[1]] = $m[2];
                return "__('" . $m[1] . "', 'saigonhoreca')";
            },
            $content
        );

        if ($content !== $original) {
            file_put_contents($file, $content);
            $files_changed++;
            $total_replaced += ($changed ? 1 : 0);
        }
    }
}

echo "Files changed: $files_changed\n";
echo "Unique strings extracted: " . count($pot_entries) . "\n\n";

// Generate merged .pot (combine with T-001 strings)
$existing_pot = $languages_dir . '/saigonhoreca.pot';
$existing_po  = $languages_dir . '/en_US.po';

// Write new .pot with pillar entries
$header = '# Copyright (C) 2026 Saigon Horeca
msgid ""
msgstr ""
"Project-Id-Version: Saigon Horeca 1.0\\\\n"
"Report-Msgid-Bugs-To: https://saigonhoreca.vn\\\\n"
"POT-Creation-Date: ' . date('Y-m-d H:iO') . '\\\\n"
"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\\\\n"
"Last-Translator: Saigon Horeca Team <contact@saigonhoreca.com>\\\\n"
"Language-Team: English <contact@saigonhoreca.com>\\\\n"
"Language: en_US\\\\n"
"MIME-Version: 1.0\\\\n"
"Content-Type: text/plain; charset=UTF-8\\\\n"
"Content-Transfer-Encoding: 8bit\\\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\\\n"
"X-Generator: Manual\\\\n"
';

// Read existing T-001 .po entries
$existing_translations = [];
if (file_exists($existing_po)) {
    $po = file_get_contents($existing_po);
    preg_match_all('/msgid "(.*)"\s+msgstr "(.*)"/Us', $po, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        if (!empty($m[1])) {
            $existing_translations[$m[1]] = $m[2];
        }
    }
}

// Merge: existing translations take priority, new pillar entries added
foreach ($pot_entries as $vi => $en) {
    if (!isset($existing_translations[$vi])) {
        $existing_translations[$vi] = $en;
    }
}

// Write merged en_US.po
$po_content = '';
foreach ($existing_translations as $vi => $en) {
    // Escape quotes in po format
    $vi_escaped = addcslashes($vi, '"');
    $en_escaped = addcslashes($en, '"');
    $po_content .= "\n#: pillar-content\n";
    $po_content .= 'msgid "' . $vi_escaped . "\"\n";
    $po_content .= 'msgstr "' . $en_escaped . "\"\n";
}

$full_po = str_replace('\\\\n', '\\n', $header) . $po_content;
file_put_contents($languages_dir . '/en_US.po', $full_po);
echo "en_US.po updated with " . count($pot_entries) . " new pillar entries\n";
echo "Total entries in en_US.po: " . count($existing_translations) . "\n";

// Compile .mo
$wp_root = dirname(dirname(dirname($theme_dir)));
require_once $wp_root . '/wp-includes/pomo/po.php';
require_once $wp_root . '/wp-includes/pomo/mo.php';

$po = new PO();
$po->import_from_file($languages_dir . '/en_US.po');
$mo = new MO();
$mo->merge_with($po);
if ($mo->export_to_file($languages_dir . '/en_US.mo')) {
    echo "en_US.mo compiled successfully (" . filesize($languages_dir . '/en_US.mo') . " bytes)\n";
} else {
    echo "ERROR: MO compile failed\n";
}
