<?php
$wp_root = 'C:/Users/Administrator/Local Sites/saigonhoreca/app/public';
$theme_dir = $wp_root . '/wp-content/themes/saigonhoreca-theme';

require $wp_root . '/wp-includes/pomo/po.php';
require $wp_root . '/wp-includes/pomo/mo.php';

$po_file = $theme_dir . '/languages/en_US.po';
$mo_file = $theme_dir . '/languages/en_US.mo';

if (!file_exists($po_file)) {
    exit("ERROR: $po_file not found.\n");
}

$po = new PO();
if (!$po->import_from_file($po_file)) {
    exit("ERROR: Failed to import from $po_file\n");
}

$mo = new MO();
$mo->merge_with($po);

if ($mo->export_to_file($mo_file)) {
    echo "MO compiled successfully.\n";
    echo "File: $mo_file\n";
    echo "Size: " . filesize($mo_file) . " bytes\n";
} else {
    echo "MO compile failed.\n";
    exit(1);
}
