---
id: T-20260511-011
owner: gemini
state: drafted
priority: P1
risk: high
estimated_minutes: 90
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Claude, Codex]
created: 2026-05-11 15:58
updated: 2026-05-11 15:58
---

# 📋 T-20260511-011-gemini-laguna-m1-store-css-cleanup — Detailed Task Specification

## 0. User Original Intent (Verbatim)
> "rất rất nhiều file dính @appy hay @layout tào lao"
> "tách ra 010 và 011, mỗi tk 1 project"

## 1. 📊 Frontmatter Fields & Risk Matrix

| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260511-011` | 🆔 Store CSS Refactor & @apply Purge. |
| `owner` | gemini | 👤 Orchestrator drafting for Laguna M1. |
| `state` | drafted | 🔄 Phase A: Planning & Drafting. |
| `priority` | P1 | 🚥 High priority: Significant technical debt in Store. |
| `risk` | high | ⚠️ High risk: Many files, potential visual breakage. |

---

## 2. 🎯 Goal & Strategic Objective
Eradicate redundant and "nonsense" CSS patterns across the `pi-store-webapp`. This project currently has the highest volume of `@apply` misuse.

**Technical Goals:**
- **Purge @apply**: Replace utility-only `@apply` with raw CSS or Tailwind v4 variables.
- **Remove Redundant Resets**: Delete manual CSS resets.
- **Enforce UI consistency**: Ensure refactored CSS maintains the 1400px layout standard.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md` (v3.1): Role ID & Technical Integrity.
- 🏗️ `pi-store-webapp/src/styles/index.css`: Theme reference.

---

## 4. 🚧 Allowed Scope (Strict)
- 📂 `pi-store-webapp/src/**/*.css`
- 📄 `pi-store-webapp/src/styles/index.css`

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Dashboard Webapp**: Handled by task `T-20260511-010`.
- ❌ **Global CSS Config**: DO NOT modify `index.css` or `animations.css`. These are already synchronized and standardized.
- ❌ **JSX Logic**: No logic changes.

---

## 6. 🛠️ Phases of Execution

### Phase 1: Global Audit 🧪
- Identify all CSS files in `pi-store-webapp` using `@apply`.

### Phase 2: UI Component Refactor 🧹
- Refactor files in `src/components/ui/` (Button, Card, Input, etc.).
- Convert `@apply` to raw CSS where appropriate.

### Phase 3: Page Layout Refactor 🏗️
- Refactor page-level CSS (AdminUsersPage, HomePage, etc.).

### Phase 4: Verification 🔍
- `cd pi-store-webapp && npm run lint && npm run build`

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
cd "pi-store-webapp"
npm run lint
npm run build
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] **CSS Cleanliness**: Storefront CSS is minimalist and utility-free where possible.
- [ ] **Build Stability**: Store builds successfully.
- [ ] **Layout Parity**: 1400px maximum width is preserved.

---

## 9. 📋 Copy-Paste Prompt (Worker Instructions)
(Bạn là Laguna M.1. Hãy thực hiện refactor CSS cho **Store Webapp** theo Dossier `T-20260511-011`. Đây là đợt dọn dẹp lớn, hãy cẩn thận tránh làm vỡ layout.)

---

## 10. 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-11 15:58**: Task created as part of the project split.
