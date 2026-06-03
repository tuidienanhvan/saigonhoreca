# Archive Index — T-014 Tailwind Migration

Standalone archive folder cho T-014 (sweep lớn, 122 files).

## Task

| Field | Value |
|---|---|
| ID | T-20260502-014 |
| Owner | gemini |
| Scope | V1-controls + Custom CSS Tailwind v4 migration |
| Result | pass |
| Bundle delta | 581.6 KB → 247.9 KB (-57%) |
| Tests | 122/122 pass |
| Files modified | ~125 (122 components + migrate-css.cjs + index.css + App.jsx) |
| Files deleted | 12+ legacy CSS (v1-controls, components, panels, app, NotificationCenter) |

## Files in this folder

- `T-20260502-014-gemini-v1-controls-tailwind-migration.md` — full dossier
- `applied-summary.md` — outcome summary (verify + bundle delta + root cause)
- `INDEX.md` — this file

## Root Cause Documented

Migration script `scripts/migrate-css.cjs` had logic bug ở Template Literal handling: biến `lastEnd` không update đúng position → mỗi run nhân bản chuỗi class cũ. Fixed + thêm dedup automation.

## References

- LEADERBOARD entry: `2026-05-02 | T-20260502-014 | gemini | css-migration | pass | 1 | ...`
- STATUS.md row: Recently Archived section
