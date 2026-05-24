<?php
/**
 * Term archive for taxonomy `product_brand` (URL: /thuong-hieu/<slug>/).
 *
 * Reuses the same orchestration as archive-product.php — hero + sidebar +
 * grid + pagination. The native CPT archive template doesn't run for
 * taxonomy archives, so we delegate explicitly.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

include get_template_directory() . '/archive-product.php';
