<?php
/**
* Project Pillar — godmother-friendship
* Section #1: hero
* @package SaigonHoreca
*/
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-gmf">
<div class="pp-hero-gmf__media" style="background-image:url('<?php echo sgh_img('godmother-friendship/godmother-friendship-phoi-canh-3d-tong-the-noi-that.jpg'); ?>');"></div>
<div class="pp-hero-gmf__overlay" aria-hidden="true"></div>
<div class="pp-ambient-glow-gmf pp-ambient-glow-gmf--top-left" aria-hidden="true"></div>
<div class="pp-ambient-glow-gmf pp-ambient-glow-gmf--bottom-right" aria-hidden="true"></div>

<!-- AutoCAD Crosshairs decoration -->
<div class="pp-cad-crosshair-gmf pp-cad-crosshair-gmf--tl" aria-hidden="true">
<svg viewBox="0 0 100 100"><line x1="0" y1="50" x2="100" y2="50" stroke="currentColor" stroke-width="0.5"/><line x1="50" y1="0" x2="50" y2="100" stroke="currentColor" stroke-width="0.5"/><circle cx="50" cy="50" r="10" fill="none" stroke="currentColor" stroke-width="0.5"/></svg>
</div>
<div class="pp-cad-crosshair-gmf pp-cad-crosshair-gmf--tr" aria-hidden="true">
<svg viewBox="0 0 100 100"><line x1="0" y1="50" x2="100" y2="50" stroke="currentColor" stroke-width="0.5"/><line x1="50" y1="0" x2="50" y2="100" stroke="currentColor" stroke-width="0.5"/><circle cx="50" cy="50" r="10" fill="none" stroke="currentColor" stroke-width="0.5"/></svg>
</div>
<div class="pp-cad-crosshair-gmf pp-cad-crosshair-gmf--br" aria-hidden="true">
<svg viewBox="0 0 100 100"><line x1="0" y1="50" x2="100" y2="50" stroke="currentColor" stroke-width="0.5"/><line x1="50" y1="0" x2="50" y2="100" stroke="currentColor" stroke-width="0.5"/><circle cx="50" cy="50" r="10" fill="none" stroke="currentColor" stroke-width="0.5"/></svg>
</div>

<!-- Background Technical Compass -->
<div class="pp-cad-compass-gmf" aria-hidden="true">
<svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="0.5">
<circle cx="100" cy="100" r="80" stroke-dasharray="2 4"/>
<circle cx="100" cy="100" r="90"/>
<circle cx="100" cy="100" r="60" stroke-dasharray="10 5"/>
<line x1="100" y1="0" x2="100" y2="200"/>
<line x1="0" y1="100" x2="200" y2="100"/>
<path d="M100 10 L105 25 L95 25 Z" fill="currentColor"/>
<text x="100" y="40" font-size="8" fill="currentColor" text-anchor="middle" font-family="monospace">N 35°</text>
</svg>
</div>

<div class="pp-hero-gmf__content scroll-reveal">
<div class="pp-hero-gmf__badge" aria-hidden="true">
<svg viewBox="0 0 24 24" fill="currentColor">
<path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
</svg>
</div>
<p class="pp-hero-gmf__subhead">Brunch &bull; Cocktail &bull; Saigon</p>
<h1 class="pp-hero-gmf__title">
GodMother Friendship
<span class="pp-hero-gmf__title-sub">Brunch reimagined in the heart of Saigon</span>
</h1>
<div class="pp-hero-gmf__divider" aria-hidden="true"></div>
</div>
</section>
