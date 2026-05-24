<?php
/**
 * Extract all __() / esc_html__() / esc_attr__() strings and merge into en_US.po.
 * Only adds NEW entries (preserves existing translations).
 */

$theme_dir = 'C:/Users/Administrator/Local Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme';
$languages_dir = $theme_dir . '/languages';

// 1. Read existing .po to preserve translations
$existing_po = $languages_dir . '/en_US.po';
$existing = [];
if (file_exists($existing_po)) {
    $po_content = file_get_contents($existing_po);
    preg_match_all('/msgid "(.*)"\s+msgstr "(.*)"/Us', $po_content, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        if (!empty($m[1])) {
            $existing[stripslashes($m[1])] = stripslashes($m[2]);
        }
    }
}
echo "Existing en_US.po entries: " . count($existing) . "\n";

// 2. Scan all pillar files (skip 4 reference) for __() calls
$pillar_dir = $theme_dir . '/template-parts/project-pillar';
$skip = ['amdang-typhoon', 'godmother-friendship', 'grand-marble-thuong-hieu-banh-cao-cap-nhat-ban', 'bambino-saigonhoreca'];
$new_found = 0;

$dirs = glob($pillar_dir . '/*', GLOB_ONLYDIR);
foreach ($dirs as $dir) {
    $slug = basename($dir);
    if (in_array($slug, $skip)) continue;
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        // Match: __(...), esc_html__(...), esc_attr__(...)
        preg_match_all("/(?:_|esc_html__|esc_attr__)\s*\(\s*'((?:[^'\\\\]|\\\\.)*)'\s*,\s*'saigonhoreca'\s*\)/", $content, $calls);
        foreach ($calls[1] as $str) {
            $str = stripslashes($str);
            if ($str === '') continue;
            if (!isset($existing[$str])) {
                $existing[$str] = $str; // Default: untranslated (same as source)
                $new_found++;
            }
        }
    }
}

echo "New entries added: $new_found\n";
echo "Total en_US.po entries: " . count($existing) . "\n";

// 3. Write merged .po
$header = 'msgid ""
msgstr ""
"Project-Id-Version: Saigon Horeca 1.0\\n"
"Report-Msgid-Bugs-To: https://saigonhoreca.vn\\n"
"POT-Creation-Date: ' . date('Y-m-d H:iO') . '\\n"
"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\\n"
"Last-Translator: Saigon Horeca Team <contact@saigonhoreca.com>\\n"
"Language-Team: English <contact@saigonhoreca.com>\\n"
"Language: en_US\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\n"
"X-Generator: Manual\\n"
';

$po_content = '';
foreach ($existing as $vi => $en) {
    $vi_escaped = addcslashes($vi, '"');
    $en_escaped = addcslashes($en, '"');
    $po_content .= "\n#: pillar-content\n";
    $po_content .= 'msgid "' . $vi_escaped . "\"\n";
    $po_content .= 'msgstr "' . $en_escaped . "\"\n";
}

file_put_contents($existing_po, $header . $po_content);
echo "en_US.po written.\n";

// 4. Compile .mo
$wp_root = dirname(dirname(dirname($theme_dir)));
require_once $wp_root . '/wp-includes/pomo/po.php';
require_once $wp_root . '/wp-includes/pomo/mo.php';

$po = new PO();
$po->import_from_file($existing_po);
$mo = new MO();
$mo->merge_with($po);
if ($mo->export_to_file($languages_dir . '/en_US.mo')) {
    echo "en_US.mo compiled (" . filesize($languages_dir . '/en_US.mo') . " bytes)\n";
}
