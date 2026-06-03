# Decision Record — T-20260517-007

## Goal vs reality

Asked to push saigonhouse-theme Lighthouse Performance from 0.83 → 100. Ran four architectural experiments + one targeted LCP fix. Result: **no architecture on LocalWP nginx achieves Perf 100 while keeping the page visually correct**. Best stable result is the unchanged render-blocking-external baseline (median 0.76-0.83). The 100 target requires production Apache + smaller hero image source — both outside the scope of a code refactor.

## Experiments run

### 1. Hero image LCP fix (kept)

Changed `decoding="sync"` → `decoding="async"` on every carousel slide, and rewrote the `wp_head` preload to emit `imagesrcset` + `imagesizes` + `fetchpriority="high"` so the browser preloads the EXACT image picked by `<img>` srcset.

Effect: best-practice, eliminates a Lighthouse `preload-lcp-image` warning. No dramatic Perf jump on LocalWP — the LCP element is a 640×640 image upscaled to ~1200 px viewport, and re-encoding it via async decode doesn't change the underlying download time.

**Kept** in final state. Production deploys will see the benefit.

### 2. Inline-full bundle (reverted)

Raised `SGH_INLINE_CSS_HARD_CAP` to 500 KB so the helper inlines the entire 418 KB bundle.

Result: Perf 0.74-0.78. HTML response grew from ~136 KB to ~554 KB. TTFB and FCP regressed more than the saved CSS round-trip improved them.

**Reverted** — cap back to 30 KB so the bundle stays as render-blocking external.

### 3. Hand-built critical split (reverted)

Built a separate `dist/theme-critical.css` (53 KB raw, 9.5 KB gzip) containing tokens + header + hero + service-links. Inlined that. Switched the main 418 KB bundle to `<link rel="preload" as="style" onload>`.

Result: Perf 0.75-0.78, FCP 2.7-3.1 s, LCP 4.3-5.0 s, CLS 0.051-0.057, TBT 170 ms. The TBT spike comes from the onload-swap re-flow firing across hundreds of nodes simultaneously.

**Reverted** — the critical split is bigger than the inline cap and doesn't beat the simple render-blocking external.

### 4. Tiny critical (utility-only) + async component bundle (reverted)

Stripped `@import "./_imports.css"` from `style.css`. Bundle became 16 KB raw / 4.5 KB gzip. Critical-css.php inlines it. Added a SECOND build target `dist/theme-components.css` (413 KB) loaded via preload+onload swap.

Result: Perf 0.54-0.57, FCP 1.1 s (excellent), LCP 4.8 s, TBT 130-230 ms, **CLS 0.962** (catastrophic).

What happened: first paint renders with only Tailwind utilities — no `.sh-*` selectors styled. Hero, header, service cards, every section render with default block flow, stacking text/images naked. When the async component bundle arrives ~500 ms later, every element snaps to its real layout → full-page reflow → CLS through the roof.

**Reverted** — this is the same failure mode T-015 split-mode hit. Component CSS needed at first paint, no way around it.

### 5. Drop `@import "_imports.css"` entirely (Perf 0.97 but visually broken)

Same as experiment 4 but without the async component bundle. Page renders with utility-only styles, no `.sh-*` classes.

Result: Perf **0.97** all three runs, FCP 0.9 s, LCP 2.6 s, CLS 0.

This proved the bundle IS the perf ceiling. With no component CSS load required, Lighthouse measures the page as essentially instant. The cost: the page is unusable — header, hero, cards, footer all render unstyled (default browser layout).

**Reverted** — Lighthouse doesn't check visual correctness; users do.

## What this proves

On LocalWP nginx, with the current `.sh-*` BEM architecture and current hero image source, **the achievable ceiling is Perf ~0.83**. Hitting 100 requires one of:

1. **Production Apache + HTTP/2 + Brotli + Cache-Control headers** (the `.htaccess` is already configured from T-015). Brotli alone shaves ~20 % off the bundle wire size. HTTP/2 multiplexes the CSS + image fetches in parallel. Cache-Control 1y immutable makes repeat visits free. Expected Perf in production: **0.90-0.95**.
2. **Bundle reduction below 30 KB** — would auto-activate the inline path in `critical-css.php` and eliminate the render-blocking link entirely. Requires either:
   - Pattern A migration (move 464 simple `.sh-*` rules' utilities into PHP `class=""`) — saves ~30-50 KB.
   - Drop the `.sh-*` BEM namespace entirely (60-80 h architectural rewrite).
3. **Smaller hero image source** — current `thumbnail.webp` is 640×640. Browsers upscale to 1200 px+ viewports → blurry + slow LCP. Re-uploading the source at 1600×1600 (and regenerating sub-sizes via WP-CLI or admin Regenerate Thumbnails plugin) drops LCP from ~3.8 s to ~2 s independently of the CSS work.

None of those are addressable in a CSS-only refactor task on LocalWP. Production deploy + an admin-side hero image regen are the practical path to 100.

## Files kept after revert

- `themes/saigonhouse-theme/template-parts/home/hero-carousel.php` — `decoding="async"` change kept.
- `themes/saigonhouse-theme/inc/core/enqueue.php` — hero preload with `imagesrcset` + `imagesizes` + `fetchpriority="high"` kept; legacy single-handle enqueue restored.
- `themes/saigonhouse-theme/assets/css/style.css` — back to T-017 state (`@import "tailwindcss" source(none)` + `@import "./_imports.css"` + scoped `@source`).
- `themes/saigonhouse-theme/package.json` — single `build` script.
- `themes/saigonhouse-theme/inc/core/critical-css.php` — single-bundle logic, 30 KB inline cap.

## Files deleted (T-018 experiment scaffolding)

- `themes/saigonhouse-theme/assets/css/_imports-critical.css`
- `themes/saigonhouse-theme/assets/css/style-critical.css`
- `themes/saigonhouse-theme/assets/css/style-components.css`
- `themes/saigonhouse-theme/assets/css/dist/theme-critical.css`
- `themes/saigonhouse-theme/assets/css/dist/theme-components.css`

## Recommendation

Stop optimizing on LocalWP. Deploy to production Apache and measure there. If the production median is still under 0.95, spawn T-019 for the hero image regen + Pattern A migration combo. Stop chasing 100 on a development server that uses HTTP/1.1, no Brotli, no cache headers, and a 640×640 hero source — those are environment problems, not code problems.
