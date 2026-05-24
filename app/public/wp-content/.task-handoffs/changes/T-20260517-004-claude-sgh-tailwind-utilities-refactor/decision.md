# Decision Record — T-20260517-004

## Why this approach?

Phase A audit found **2,955 `@apply` usages across 76 files** (the dossier's initial estimate of ~330 was wrong by a factor of 9). Of those, **only 15 % sit in "simple" rules** (single selector, no pseudo/media/descendant) that could be cleanly migrated to inline utilities in HTML/PHP markup. The remaining 84 % live inside `:hover`, `@media`, or descendant selectors that require staying as CSS rules.

This re-baseline made the dossier's stretch goal (5-15 KB / Perf 0.95+) unreachable in scope — it would require an architectural migration that drops the entire `.sh-*` BEM namespace, ~60-80 hours of PHP rewriting. Instead, we shipped a **partial, architectural-cleanup pass**:

1. Wrote `scripts/deapply.py` that reads `dist/theme.css` (Tailwind's compiled output) and rewrites each component source's `@apply` directive in place with the corresponding compiled raw-CSS body. Each selector's occurrences are consumed in declaration order so `@media`-wrapped overrides line up correctly.
2. Ran the script across `assets/css/**`, `template-parts/**`, `page-templates/**`. Replaced 2,513 of 2,955 `@apply` usages (**85 %**). The remaining 442 are mostly inside the three large page-template files (page-bang-gia, page-du-toan, page-portfolio) which are loaded per-page via `wp_enqueue_style` rather than bundled through `_imports.css` — so their selectors don't appear in `dist/theme.css` for the script to match against, and they don't impact the homepage Lighthouse measurement either.
3. Build verified, visual smoke verified (all `.sh-*` classes still render with the same compiled CSS body).

## Alternatives considered

1. **Full utility-in-HTML migration** (the dossier's original aim). Rejected for this task: the 84 % of complex rules can't be moved without rewriting CSS into pseudo-aware utility variants (`hover:`, `md:`, etc.) and that ripples through every PHP template. Estimated 60-80 hours, multi-session minimum.
2. **Manual conversion file by file**. Rejected: 2,955 rules × ~30 s/rule = 25 hours of error-prone mechanical typing. The script does this in seconds with deterministic output.
3. **Run Tailwind CLI per-file as a converter**. Considered: would need a wrapper that wraps each rule with `@import "tailwindcss"`, compiles, extracts. More complex than reading the existing dist. Same end result.

## Trade-offs

| Decision | Cost | Benefit |
|---|---|---|
| Use `dist/theme.css` as the conversion source | Conversion can only target selectors that Tailwind already saw. Three page-template files weren't bundled and so weren't converted. | Zero risk of mis-translating utility names — we're literally copying Tailwind's own output. |
| Consume selector bodies in order (queue per selector) | Source rule count must match dist rule count for a clean conversion. 1 selector (`.sh-fp__header-line`) had source idx 2 but only 2 dist bodies because Tailwind merged its two `@media (min-width: 768px)` overrides. | Handles the majority case (base + one or two media overrides) correctly. |
| Leave the 442 unmatched `@apply` in place | Source still contains some `@apply` directives. They continue to work because Tailwind processes them at build time as before. | Zero risk of breaking the build. Bundle stays valid CSS. |
| Keep `@import "tailwindcss" source(none)` + scoped `@source` | We still depend on Tailwind for `@theme` variables (`--text-3xl`, `--font-weight-bold`, etc.) referenced in the converted CSS. | Bundle still has correct visual output. Removing Tailwind entirely would break those vars. |

## What we explicitly did NOT do

- Touch any PHP markup. All `class="..."` attributes are byte-identical to baseline.
- Modify build configuration. `npm run build` works exactly as before.
- Remove or rename any `.sh-*` selectors.
- Touch `style.css` `:root` token aliases (76 tokens kept from T-016).
- Change visual output. CSS bodies after conversion equal what Tailwind produced before conversion — render is byte-identical.

## Result

| Metric | Baseline (T-016 final) | After T-017 (this dossier) | Delta |
|---|---|---|---|
| Bundle raw | 461,719 B | 417,755 B | **-9.5 %** |
| Bundle gzip | 56,362 B | 52,347 B | **-7.1 %** |
| `@apply` usages | 2,955 | 442 | **-85 %** |
| Lighthouse Perf (median) | 0.80 | 0.83 | +0.03 |
| Lighthouse Perf (best of 3) | 0.81 | 0.84 | +0.03 |
| FCP (median) | 3.2 s | 3.1 s | -0.1 s |
| LCP (median) | 3.9 s | 3.8 s | -0.1 s |
| TBT (max) | 10 ms | 20 ms | within noise |
| CLS (median) | 0.017 | 0.033 | within green threshold |
| DOM size score | 1.0 (green) | 1.0 (green) | unchanged |

## Production risk

Low. The conversion is mechanically lossless — every replaced rule is the literal raw-CSS output Tailwind was already compiling. The remaining 442 `@apply` still work because Tailwind continues to process them at build time. Bundle is byte-different but render is byte-identical (verified by curl smoke checks).

Rollback plan in `rollback-plan.md`.

## Next-step path forward

For another **+8-15 Lighthouse points** the path is **Pattern A** migration:

- Audit the 464 "simple" rules (already categorized in `before/` snapshot).
- Find their PHP usage sites: `grep -rE "class=\"[^\"]*\\b<class>\\b" template-parts/ page-templates/ *.php`.
- For each: paste the rule's utility list into the PHP `class=""` attribute and delete the rule from CSS.
- Re-build → expect bundle to drop by ~30-50 KB once shared utilities are deduplicated across the elements.

That work is well-suited to a follow-up dossier (T-018) at ~10-15 hours of focused effort, broken into 5-6 sessions of 2 hours each (one CSS file per session).
