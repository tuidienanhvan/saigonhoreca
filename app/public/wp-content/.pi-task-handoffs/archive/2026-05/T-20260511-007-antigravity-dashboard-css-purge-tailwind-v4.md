---
id: T-20260511-007-antigravity-dashboard-css-purge-tailwind-v4
owner: antigravity
state: archived
priority: high
risk: medium
estimated_minutes: 45
parent: null
children: []
depends_on: []
parallelization_ok: false
created: 2026-05-11T05:03:00Z
updated: 2026-05-11T05:03:00Z
---
# 🛡️ DOSSIER: Dashboard CSS Purge & Tailwind v4 Standardization

## 0. User Original Intent
"lên thêm 1 plan nữa cho laguna khác nma về index.css của pi-dashboard"

## 1. Allowed Scope
- `pi-dashboard-webapp/src/styles/index.css`
- `pi-dashboard-webapp/src/**/*`

## 2. Out Of Scope
- Modifying backend API.
- Changing core business logic.

## 3. Phases

### Phase A: Global Encoding Restoration (Auditor)
- [ ] Convert all dashboard source files to UTF-8 to prevent Mojibake.

### Phase B: Tailwind v4 Migration (Worker: Laguna)
- [ ] Replace custom utility classes (`.container`, `.page-shell`, etc.) with pure Tailwind v4 utilities.
- [ ] Align layout containers to 1400px (max-w-[1400px]) with proper padding (px-12).

### Phase C: CSS Purge (Worker: Laguna)
- [ ] Strip `pi-dashboard-webapp/src/styles/index.css` to only theme tokens and base layer.

### Phase D: Verification (Auditor: Gemini)
- [ ] `npm run lint` & `npm run build` in `pi-dashboard-webapp`.
- [ ] Visual audit of the dashboard.

## 4. Evidence
- (To be populated)
