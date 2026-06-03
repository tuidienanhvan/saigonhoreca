# Rollback Plan — T-20260517-007

Most of T-018 was experimental and already reverted before archiving. Only two
small "kept" changes remain in the codebase, both targeted at hero image LCP:

1. `template-parts/home/hero-carousel.php` — `decoding="async"` on all slides.
2. `inc/core/enqueue.php` — hero `wp_head` preload uses `imagesrcset` +
   `imagesizes` + `fetchpriority="high"`.

## When to roll back

These are best-practice changes that shouldn't cause regressions. Roll back
only if:

- The first hero slide flashes blank for > 500 ms on first paint (the
  async decode might trip up some browsers — unlikely but possible).
- Lighthouse `preload-lcp-image` audit reports a mismatch between the
  preload `<link>` and the rendered `<img>` source — that means the
  `imagesrcset` doesn't match the `<img>`'s srcset attribute. Verify with
  `curl saigonhouse.local/ | grep 'rel="preload" as="image"'`.

## How to roll back

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"

# Restore hero-carousel.php (decoding sync on first 2 slides)
sed -i 's|$decoding_attr = .async.;|$decoding_attr = ($index <= 1) ? "sync" : "async";|' \
   themes/saigonhouse-theme/template-parts/home/hero-carousel.php

# Restore enqueue.php hero preload (basic href only)
# Manual edit:
#  - Replace the new `add_action('wp_head', ...)` block (lines ~205-285) with
#    the original simpler block — see this dossier's `before/` snapshot path
#    in T-20260517-004's changes/ folder (T-017 had the previous version).
```

## Validation

```bash
curl -s -I http://saigonhouse.local/                # 200 OK
curl -s http://saigonhouse.local/ | grep -oE '<img[^>]*sh-hero__bg-img[^>]*>' | head -1
# Verify the rendered <img> has decoding="sync" on first slide if reverted

npx --yes lighthouse http://saigonhouse.local/ --only-categories=performance --quiet --chrome-flags="--headless"
# Expect Perf ~0.78-0.83 median (same as T-017).
```

## All other experiments

The four reverted experiments left no artifacts in the working tree. Their
files (`_imports-critical.css`, `style-critical.css`, `style-components.css`,
`dist/theme-critical.css`, `dist/theme-components.css`) were deleted as part
of the revert. No partial-state remains.
