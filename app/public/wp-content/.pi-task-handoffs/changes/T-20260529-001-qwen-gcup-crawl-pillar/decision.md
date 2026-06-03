# Decision Record — T-20260529-001 Phase 7: Quality Upgrade

## Date: 2026-05-29
## Agent: qwen (Qwen3.7-Max-Preview)

## Root Cause Analysis
Hero section rendering PHẲNG was caused by **selector mismatch**: `hero.css` used generic `.pp-hero` selectors while `hero.php` markup used `pp-hero-gcb` class. All hero CSS (Ken-Burns, ornaments, gradients, ambient glow) was silently not applying.

## Changes Made (Phase 7)

### CSS Fixes
1. **hero.css** — Replaced all `.pp-hero` → `.pp-hero-gcb` selectors. Added `pp-ambient-glow-gcb` class. Added scroll-reveal staggered entry animation. Renamed `pp-hero-gcb-breathe` → `pp-hero-gcb-ken-burns` for clarity. Replaced `var(--gold)` → `var(--p)` for token compliance.
2. **intro.css** — Added `.scroll-reveal` base + `.animate-in`/`.is-visible` trigger styles. Added `prefers-reduced-motion` fallback. Added watermark selector for `.pp-container-shared`.
3. **concept.css** — Added `.pp-image-container-shared` aspect-ratio for split media. Replaced `var(--gold)` → `var(--p)` in espresso-trio dots.
4. **partnership.css** — Added `.scroll-reveal` support + `prefers-reduced-motion` fallback.
5. **specs.css** — Rewrote to use `.pp-container-shared` instead of `.pp__container`. Replaced `var(--gold)` → `var(--p)`.
6. **gallery.css** — Populated with gallery section styles (was empty placeholder).
7. **related.css** — Added conclusion emphasis styling.
8. **cta.css** — Cleaned placeholder comment.
9. **single-project/g-cup-coffee-bistro.css** — Added `.pp .scroll-reveal` base + transition + `prefers-reduced-motion` fallback.

### PHP Fixes
1. **hero.php** — Added `pp-ambient-glow-gcb` div, `scroll-reveal` classes on content children.
2. **intro.php** — Replaced `.pp__container` → `.pp-container-shared`. Added `pp-image-container-shared` + `pp-image-caption-shared` on gallery images. Added `scroll-reveal`. Improved alt text.
3. **concept.php** — Added `pp-image-container-shared` + `pp-image-caption-shared` on split media. Added `scroll-reveal`. Replaced `.pp__container` → `.pp-container-shared`.
4. **partnership.php** — Added `pp-container-shared` wrapper. Added `scroll-reveal`.
5. **specs.php** — Added `pp-container-shared` + `pp-image-container-shared` + `pp-image-caption-shared` on all images. Added `scroll-reveal`. Improved alt text.
6. **gallery.php** — Rewrote as proper gallery section (was duplicate of specs). 5 images with captions.
7. **related.php** — Added `pp-container-shared` + `scroll-reveal` + title + divider.
8. **single-project/g-cup-coffee-bistro.php** — Added `pp--gcb` modifier class on `<main>`.

## Quality Bar Checklist (§B)
- [x] ZERO hardcode hex → all `var(--p)`, `var(--bc)`, `color-mix(...)`
- [x] `pp-container-shared` applied (7/8 sections — cta.php is empty placeholder)
- [x] `pp-image-caption-shared` applied (intro, concept, specs, gallery)
- [x] ≥6/9 CSS techniques:
  - [x] Ken-Burns hero
  - [x] Responsive grid split (concept, specs)
  - [x] Glassmorphism card (gallery badges via backdrop-filter)
  - [x] Staggered entry rise (hero, gallery tiles)
  - [x] Pseudo-element ornaments (hero coffee-cup, divider swirls, L-brackets)
  - [x] Ambient glow (hero center, section pseudo-elements)
  - [x] Fluid typography clamp() everywhere
  - [x] Hover micro-interactions (gallery lift, border brighten, caption float)
  - [x] Scroll-reveal
- [x] `prefers-reduced-motion` fallback in hero.css, intro.css, partnership.css, single-project.css
- [x] Build pass + route 200
- [x] Scope: only G-Cup files modified
