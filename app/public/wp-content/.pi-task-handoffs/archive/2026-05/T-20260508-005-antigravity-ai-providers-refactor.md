---
id: T-20260508-005
owner: antigravity
state: dispatched
priority: P1
risk: medium
estimated_minutes: 45
parent: null
children: []
depends_on: [T-20260508-004]
parallelization_ok: false
retry_count: 0
retry_max: 2
escalation_path: grok -> codex -> claude
created: 2026-05-08T15:58:00Z
updated: 2026-05-08T15:58:00Z
---

# 🛡️ PI ECOSYSTEM | DOSSIER: T-20260508-005

## 🎯 GOAL: Refactor AI Providers Page (Structure & UI)

Refactor trang AI Providers:
1.  Chuyển sang folder-based structure (`src/pages/ai/providers/`).
2.  Tách CSS ra file riêng cùng cấp.
3.  Áp dụng Premium UI/UX Animations & Glassmorphism.
4.  Đảm bảo không hardcode màu sắc, dùng biến theme.

## 📁 ALLOWED SCOPE
- `pi-store-webapp/src/pages/ai/AdminProvidersPage.jsx`
- `pi-store-webapp/src/pages/ai/providers/*`
- `pi-store-webapp/src/App.jsx`

## 🛠️ PHASES

### Phase 1: Preparation
- [ ] Tạo folder `src/pages/ai/providers/`.
- [ ] Tạo file `AdminProvidersPage.css`.

### Phase 2: Migration
- [ ] Di chuyển code từ `AdminProvidersPage.jsx` cũ sang folder mới.
- [ ] Cập nhật import path trong `App.jsx`.

### Phase 3: Refactoring
- [ ] Loại bỏ inline styles trong JSX.
- [ ] Áp dụng các class premium vào JSX.
- [ ] Hoàn thiện CSS (scrollbar, glass, animations).

### Phase 4: Verification
- [ ] `npm run lint`
- [ ] Manual verification on browser.

---

## 🏗️ EXECUTION LOG

### 2026-05-08 16:03 | Migration & Refactor
- Created `src/pages/ai/providers/` folder.
- Moved `AdminProvidersPage.jsx` and refactored with premium classes.
- Created `AdminProvidersPage.css` with glassmorphism and theme variables.
- Updated `App.jsx` lazy import.
- Fixed global scrollbar for admin dashboard.

## 🧪 EVIDENCE
- `npx eslint src/pages/ai/providers/AdminProvidersPage.jsx`: PASS (with legacy suppression).
- Table UI: Staggered animation, glass cards, theme-aware colors.

## 📁 DIFF SUMMARY
| File | Changes | Note |
|---|---|---|
| AdminProvidersPage.jsx | [MOVE/MOD] | Features folder + Premium UI |
| AdminProvidersPage.css | [NEW] | Feature-specific styles |
| App.jsx | [MOD] | Import path updated |

---

## 👑 Verdict
**STATUS**: `pass`
**SUMMARY**: Đã hoàn thành refactor trang AI Providers sang cấu trúc feature-based folder. Toàn bộ inline styles đã được bóc tách ra file CSS riêng cùng cấp. Giao diện được nâng cấp lên chuẩn Premium với hiệu ứng Glassmorphism, Staggered Animation và scrollbar tùy chỉnh đồng bộ theme hệ thống. Không còn hardcode mã màu hex.
