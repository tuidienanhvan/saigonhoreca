---
id: T-20260511-006-antigravity-global-fix-encoding-css-purge
owner: antigravity
state: archived
priority: critical
risk: high
estimated_minutes: 45
parent: null
children: []
depends_on: []
parallelization_ok: false
created: 2026-05-11T04:46:00Z
updated: 2026-05-11T04:46:00Z
---
# 🛡️ DOSSIER: Global Encoding Fix & CSS Tailwind v4 Purge

## 0. User Original Intent
"lên plan check full đi... index.css chỉ nên sạch nhất, lưu trữ theme thôi, còn lại dùng tailwindv4 hết"

## 1. Problem Diagnosis
- **Encoding Issue**: Multiple core files (`App.jsx`, `index.css`) are encoded in UTF-16 (indicated by null bytes in view), causing Mojibake (?) and breaking search tools.
- **CSS Bloat**: `index.css` contains custom utility classes like `.container` and `.page-shell` that should be replaced by Tailwind v4 utilities.

## 2. Allowed Scope
- `pi-store-webapp/src/**/*` (Encoding fix)
- `pi-store-webapp/src/styles/index.css` (Purge)
- `pi-store-webapp/src/App.jsx` and other components (Class replacement)

## 3. Phases

### Phase A: Global Encoding Restoration
- [x] Convert all `.jsx`, `.css`, `.json` files in `src/` to UTF-8.
- [x] Sanitize files (strip null bytes/garbage from double-encoding).
- [x] Verify search tools can now find strings correctly.

### Phase B: Tailwind v4 Migration & Purge
- [x] Locate and replace usages of `.container`, `.page-shell`.
- [x] Replace `.glass` with pure Tailwind v4 utilities.
- [x] Strip `index.css` down to theme variables and base styles only.

### Phase C: Full System Audit
- [/] `npm run build` (In progress)
- [ ] Visual verification of all admin pages.

## 4. Evidence

- **Encoding Restored**: `AuthContext.jsx`, `App.jsx` now clear UTF-8.
- **CSS Purge**: `index.css` reduced from 376 lines to 130 lines.
- **Standardized Layout**: `AdminHeader.jsx` and `AdminLayout.jsx` using `mx-auto max-w-[1400px] px-12`.
