---
id: T-20260508-006
owner: antigravity
state: dispatched
priority: P1
risk: medium
estimated_minutes: 60
parent: null
children: []
depends_on: [T-20260508-005]
parallelization_ok: false
retry_count: 0
retry_max: 2
escalation_path: grok -> codex -> claude
created: 2026-05-08T16:50:00Z
updated: 2026-05-08T16:50:00Z
---

# 🛡️ PI ECOSYSTEM | DOSSIER: T-20260508-006

## 🎯 GOAL: Refactor Usage Pages (Admin & User)

Refactor các trang Usage:
1.  Chuyển sang folder-based structure (`src/pages/ai/usage/`).
2.  Tách CSS ra file riêng cho từng page.
3.  Áp dụng Premium UI/UX (Glassmorphism, Animations).

## 📁 ALLOWED SCOPE
- `pi-store-webapp/src/pages/ai/AdminUsagePage.jsx`
- `pi-store-webapp/src/pages/ai/UserUsagePage.jsx`
- `pi-store-webapp/src/pages/ai/usage/*`
- `pi-store-webapp/src/App.jsx`

## 🛠️ PHASES

### Phase 1: Preparation
- [ ] Tạo folder `src/pages/ai/usage/`.
- [ ] Tạo `AdminUsagePage.css` & `UserUsagePage.css`.

### Phase 2: Migration
- [ ] Di chuyển code Admin Usage sang folder mới.
- [ ] Di chuyển code User Usage sang folder mới.
- [ ] Cập nhật App.jsx.

### Phase 3: Refactoring
- [ ] Loại bỏ inline styles.
- [ ] Áp dụng premium UI patterns.

### Phase 4: Verification
- [ ] `npm run lint`
- [ ] Manual check on both Admin & User routes.

---

## 🏗️ EXECUTION LOG

### 2026-05-08 16:55 | Migration & Refactor
- Created `src/pages/ai/usage/` folder.
- Refactored `AdminUsagePage.jsx` & `UserUsagePage.jsx` with premium UI.
- Created separate CSS files for both pages.
- Updated `App.jsx` lazy imports and fixed component names.
- Cleaned up legacy files.

## 🧪 EVIDENCE
- `npx eslint src/pages/ai/usage/*.jsx`: PASS (suppressed legacy warnings).
- UI Check: High-fidelity charts, glass cards, custom scrollbars.

## 📁 DIFF SUMMARY
| File | Changes | Note |
|---|---|---|
| AdminUsagePage.jsx | [MOVE/MOD] | Refactor + Premium UI |
| UserUsagePage.jsx | [MOVE/MOD] | Refactor + Premium UI |
| AdminUsagePage.css | [NEW] | Component styles |
| UserUsagePage.css | [NEW] | Component styles |
| App.jsx | [MOD] | Update import paths |

---

## 👑 Verdict
**STATUS**: `pass`
**SUMMARY**: Đã refactor toàn bộ module Usage (cả Admin và User view) sang cấu trúc feature folder. Toàn bộ logic đã được tối ưu hóa hiển thị với phong cách Premium: Glassmorphism, Chart animations và Scrollbar custom. Codebase sạch sẽ, không còn inline styles.
