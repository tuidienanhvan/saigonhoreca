---
id: T-20260511-005-antigravity-css-purge-tailwind-v4
owner: antigravity
state: archived
priority: high
risk: medium
estimated_minutes: 30
parent: null
children: []
depends_on: []
parallelization_ok: false
created: 2026-05-11T04:43:00Z
updated: 2026-05-11T04:43:00Z
---
# 🛡️ DOSSIER: CSS Purge & Tailwind v4 Standardization

## 0. User Original Intent
"chỉ nên sạch nhất, lưu trữ theme thôi, còn lại dùng tailwindv4 hết"

## 1. Allowed Scope
- `pi-store-webapp/src/styles/index.css`
- `pi-store-webapp/src/components/**/*`
- `pi-store-webapp/src/pages/**/*`

## 2. Out Of Scope
- No changes to business logic.
- No changes to non-CSS files unless for class replacement.

## 3. Phases

### Phase A: Audit
- [ ] Find all usages of `.container` and `.page-shell` in `pi-store-webapp`.
- [ ] Identify other custom utility classes in `index.css` that can be replaced.

### Phase B: Implementation
- [ ] Replace custom classes with Tailwind v4 utilities in components.
- [ ] Clean up `index.css` to only contain theme tokens and base styles.

### Phase C: Verification
- [ ] `npm run lint`
- [ ] `npm run build`
- [ ] Visual verification.

## 4. Evidence
- (To be populated)
