---
id: T-20260511-003
owner: 🏗️ Antigravity
state: archived
priority: 🔴 P1 HIGH
risk: 🟡 MED
estimated_minutes: 45
parent: null
children: []
depends_on: []
parallelization_ok: false
created: 2026-05-11T10:56:00Z
updated: 2026-05-11T10:56:00Z
---

# 🛡️ DOSSIER: CSS REFACTOR & LAYOUT FIX RETRY

## 0. User Original Intent
- **Verbatim**: "đọc xong r thì so sánh @[pi-dashboard-webapp/src/index.css] và @[pi-store-webapp/src/styles/index.css] sao lại cần -boder,-radius,...? chỉ cần bộ màu theme thôi Cái gì có của tailwind thì đừng làm lại"
- **Follow-up**: User reporting previous fixes for "dính sát lề trên", "menu thụt lên thụt xuống", "đè chữ ở Table" still persist ("vẫn vậy").
- **Timestamp**: 2026-05-11 10:33:02 / 10:55:41
- **Context**: Refactoring CSS foundation to be minimal (only theme colors) and fixing regressions.

## 1. Allowed Scope
- `pi-store-webapp/src/styles/index.css`
- `pi-dashboard-webapp/src/index.css`
- `pi-store-webapp/src/components/core/DashboardLayout.css`
- `pi-store-webapp/src/components/ui/Table.css`

## 2. Out Of Scope
- Modifying `package.json` or dependencies.
- Global refactoring of other component styles unless directly related to the layout regressions.
- Logic changes in JS/JSX files (except minor CSS class adjustments if needed).

## 3. Phases

### 🏗️ Phase 1: Audit & Analysis
- [x] Compare `index.css` files and identify all redundant Tailwind-overlapping tokens.
- [ ] Investigate why `pt-32` in `DashboardLayout.css` isn't solving the "sticking to top" issue.
- [ ] Analyze sidebar footer shifting behavior.

### 🛠️ Phase 2: Implementation (Atomic)
- [ ] Clean up `index.css` (Store & Dashboard): Keep only color tokens, fonts, and core glow effects. Remove redundant radius, shadow, and transition tokens.
- [ ] Remove `@layer utilities` that recreate Tailwind.
- [ ] Fix `DashboardLayout.css`: Ensure stable padding and fixed footer height.
- [ ] Fix `Table.css`: Enforce `min-width` and `whitespace-nowrap`.

### 🧪 Phase 3: Verification (Quality Gate)
- [ ] `npm run build` in both apps.
- [ ] Visual check of the dashboard layout (top space, sidebar stability).
- [ ] Visual check of tables with long content.

### 📁 Phase 4: Archiving
- [ ] Update `STATUS.md`.
- [ ] Final report with evidence.

## 4. Evidence (Build Logs & Diffs)
*To be filled during execution.*

---
**Mantra**: "Code in English, Think in Logic, Record in Dossier, Speak in Vietnamese."
