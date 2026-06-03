# Decision Record — T-20260517-003

## Why this approach?

T-014 restored the inline-CSS pattern from T-012, but the helper sat dormant because the bundle had grown from 15 KB to 465 KB between the two tasks (the `_imports.css` aggregator was added to bundle 87 component CSS files via Tailwind `@apply`). At 465 KB inline, Lighthouse complained about HTML size; at 465 KB external render-blocking, Lighthouse scored 72.

T-015's job: split the bundle so the inline-CSS strategy actually pays off, and address the three audits that scored 50 in T-013's report (`uses-long-cache-ttl`, `modern-image-formats`, `dom-size`).

## Alternatives considered

1. **Per-component `<link>` enqueue (originally drafted in T-014's spawn task)**
   - Drop `_imports.css` entirely, restore per-page `wp_enqueue_style` for each component CSS file.
   - Blocker: component CSS files use Tailwind `@apply` — they MUST be processed by Tailwind to compile to real CSS. Loading them raw as `<link rel="stylesheet">` would leave layouts collapsed because browsers ignore `@apply`.
   - Would need either (a) a build step per component or (b) hand-rewriting 87 files to not use `@apply` — both order-of-magnitude bigger than T-015's budget.
   - **Rejected.**

2. **Multiple Tailwind build outputs (one per page template)**
   - `npm run build:home`, `build:pricing`, `build:about`, … each emitting its own dist.
   - Would minimize per-page CSS bytes but each output would duplicate ~16 KB of Tailwind utilities (utilities are scanned from all PHP files regardless of the input entry).
   - Estimated ~8–10 build targets needed, each ~50 KB. Net wire bytes saved per page: maybe 100 KB vs single bundle.
   - High complexity for moderate gain. **Deferred to a future task if simpler approach proves insufficient.**

3. **Critical / lazy two-bundle split (CHOSEN)**
   - One always-loaded "critical" chunk (≤ 150 KB raw / ≤ 18 KB gzip): header, footer, global, shared, above-the-fold home.
   - One async-loaded "rest" chunk: page-specific styles + below-the-fold home + all pricing/contact/about/single/archive.
   - `critical-css.php` inlines critical, async-rewrites rest via `<link rel="preload" as="style" onload>`.
   - Pros: simplest architecture that delivers on the inline-CSS premise. Helper already supports this from T-014 — just needs the bundle to fit the cap.
   - Cons: critical bundle 122 KB raw is larger than ideal (T-012's 15 KB). HTML payload bloats. Gzip on wire keeps it tolerable.

## Trade-offs

| Decision | Cost | Benefit |
|---|---|---|
| Critical includes hero-carousel + service-links | +6 KB on every non-home page (CSS not visually used there) | Prevents CLS 0.1+ on homepage when rest async-loads |
| Cap 150 KB instead of T-012's 30 KB | HTML response ~285 KB instead of ~165 KB | Inline path activates; otherwise helper would no-op |
| `.htaccess` Brotli added | None on Apache (mod_brotli optional, no-ops when not loaded) | Score 100 on `uses-text-compression` when prod Apache has mod_brotli |
| SVG header collapsed to `<path>` | Lose individual element styling potential | -64 DOM nodes, smaller HTML |

## What we explicitly did not do

- Did not touch any page template (`page-templates/*.php`) — out of scope.
- Did not edit any component CSS file content — only restructured imports.
- Did not add npm dependencies.
- Did not fix the hero image LCP (separate problem, deferred to T-20260517-004).
- Did not reduce mega-menu / marquee DOM nodes (UX-blocking, needs design call).

## Production risk

Low-medium. The `<noscript>` fallback ensures CSS still arrives if JS is disabled. The async filter only fires for handles `theme-tokens` and `theme-rest`, so other stylesheets (Google Fonts, per-page CSS from T-013) load normally. SVG simplification is purely visual — same skyline silhouette, fewer DOM nodes.

Rollback procedure documented in dossier §XV.
