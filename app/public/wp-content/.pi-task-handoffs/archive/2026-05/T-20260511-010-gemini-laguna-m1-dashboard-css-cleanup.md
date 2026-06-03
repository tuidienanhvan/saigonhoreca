---
id: T-20260511-010
owner: gemini
state: drafted
priority: P1
risk: medium
estimated_minutes: 45
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Claude, Codex]
created: 2026-05-11 15:52
updated: 2026-05-11 15:58
---

# 📋 T-20260511-010-gemini-laguna-m1-dashboard-css-cleanup — Detailed Task Specification

## 0. User Original Intent (Verbatim)
> "rất rất nhiều file dính @appy hay @layout tào lao"
> "tách ra 010 và 011, mỗi tk 1 project"

## 1. 📊 Frontmatter Fields & Risk Matrix

| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260511-010` | 🆔 Dashboard CSS Refactor & @apply Purge. |
| `owner` | gemini | 👤 Orchestrator drafting for Laguna M1. |
| `state` | drafted | 🔄 Phase A: Planning & Drafting. |
| `priority` | P1 | 🚥 High priority: Technical debt cleanup. |
| `risk` | low | ⚠️ Low risk: Focused on Dashboard only. |

---

## 2. 🎯 Goal & Strategic Objective
Eradicate redundant and "nonsense" CSS patterns (specifically excessive `@apply` and manual resets) across the `pi-dashboard-webapp`. Transition to a minimalist CSS architecture leveraging Tailwind v4 native capabilities and the new shorthand variable system (`--p`, `--b1`, etc.).

**Technical Goals:**
- **Purge @apply**: Replace `@apply` in base layers and global tags with raw CSS or Tailwind v4 variables.
- **Enforce Shorthand Variables**: Use `--p`, `--b1`, `--bc` etc. as defined in `index.css`.
- **Remove Redundant Resets**: Delete manual CSS resets covered by Preflight.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md` (v3.1): Role ID & Technical Integrity.
- 🏗️ `pi-dashboard-webapp/src/index.css`: Reference for new variable tokens.

---

## 4. 🚧 Allowed Scope (Strict)
- 📂 `pi-dashboard-webapp/src/**/*.css`
- 📄 `pi-dashboard-webapp/src/index.css`

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Store Webapp**: Task `T-20260511-011` handles the store.
- ❌ **Global CSS Config**: DO NOT modify `index.css` or `animations.css`. These are already synchronized and standardized.
- ❌ **JSX Logic**: No component logic changes.

---

## 6. 🛠️ Phases of Execution

### Phase 1: Audit 🧪
- Search for `@apply` in `pi-dashboard-webapp/src`.

### Phase 2: Cleanup 🧹
- Refactor `Login.css`, `TipTapEditor.css`, `Toolbar.css`, etc.
- Replace utility-only `@apply` with raw CSS or specific token variables.

### Phase 3: Verification 🔍
- `cd pi-dashboard-webapp && npm run lint && npm run build`

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
cd "pi-dashboard-webapp"
npm run lint
npm run build
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] **CSS Purity**: No utility `@apply` on global tags or simple containers.
- [ ] **Token Alignment**: Uses shorthand variables (`--p`, `--b1`, etc.).
- [ ] **Build Stability**: Dashboard builds successfully.

---

## 9. 📋 Copy-Paste Prompt (Worker Instructions)
(Bạn là Laguna M.1. Hãy thực hiện refactor CSS cho **Dashboard Webapp** theo Dossier `T-20260511-010`. Dùng hệ thống biến mới `--p`, `--b1`... và xóa bỏ `@apply` tào lao.)

---

## 10. 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-11 15:52**: Dossier created.
- **2026-05-11 15:58**: Split tasks. This dossier is now Dashboard-only.
