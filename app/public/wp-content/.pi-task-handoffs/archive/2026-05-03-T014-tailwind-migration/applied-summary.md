# T-014 Applied — V1 controls Tailwind migration (Gemini)

**Date:** 2026-05-03
**AI:** Gemini / Antigravity
**Outcome:** ✅ Applied + verified

## Verify
- Build: 247.9 KB main bundle (limit 520 KB) ✅
- Tests: 122/122 ✅
- Legacy CSS files: removed (v1-controls, components, panels, app, NotificationCenter)
- No legacy class refs (v1-btn, v1-card, v1-input, pi-btn-primary) còn trong src/
- `--pi-card-*` CSS tokens giữ lại (design tokens, không phải legacy class)

## Bundle delta
- Before: 581.6 KB
- After:  247.9 KB
- **Reduction: -57%** ✅

## Root cause fix
Migration script `migrate-css.cjs` có lỗi logic ở Template Literal handling: biến `lastEnd` không update đúng position → mỗi run nhân bản chuỗi class cũ. Fixed + thêm dedup automation.

## Files modified
- `scripts/migrate-css.cjs` — fix lastEnd + dedup logic
- `src/index.css` — consolidated tokens
- `src/App.jsx` — class cleanup
- `src/components/shared/NotificationCenter.jsx` — 100% Tailwind, level mapping clean
- `src/components/shared/NotificationCenter.css` — DELETED
- 122 component files — auto-cleaned duplicate classes
- 12+ legacy CSS files — DELETED

## Outcome
Pi Dashboard webapp giờ:
- Pure Tailwind v4 utility-first
- No legacy `.v1-*` / `.pi-btn-*` class
- Bundle size healthy (247 KB main, code-split route-level)
- Design tokens consolidated trong index.css
- Visual không regression
