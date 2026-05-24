---
id: T-20260508-007
owner: antigravity
state: dispatched
priority: P1
risk: medium
estimated_minutes: 60
parent: null
children: []
depends_on: [T-20260508-006]
parallelization_ok: false
retry_count: 0
retry_max: 2
escalation_path: grok -> codex -> claude
created: 2026-05-08T16:53:00Z
updated: 2026-05-08T16:53:00Z
---

# 🛡️ PI ECOSYSTEM | DOSSIER: T-20260508-007

## 🎯 GOAL: Refactor Wallet & Ledger Pages

Refactor các trang Wallet và Ledger:
1.  Chuyển sang folder-based structure (`src/pages/ai/wallet/`).
2.  Tách CSS ra file riêng.
3.  Áp dụng Premium UI cho Token Packs và Transaction History.

## 📁 ALLOWED SCOPE
- `pi-store-webapp/src/pages/ai/WalletPage.jsx`
- `pi-store-webapp/src/pages/ai/LedgerPage.jsx`
- `pi-store-webapp/src/pages/ai/wallet/*`
- `pi-store-webapp/src/App.jsx`

## 🛠️ PHASES

### Phase 1: Preparation
- [ ] Tạo folder `src/pages/ai/wallet/`.
- [ ] Tạo `WalletPage.css` & `LedgerPage.css`.

### Phase 2: Migration
- [ ] Di chuyển code Wallet sang folder mới.
- [ ] Di chuyển code Ledger sang folder mới.
- [ ] Cập nhật App.jsx.

### Phase 3: Refactoring
- [ ] Loại bỏ inline styles & Tailwind hardcoded classes.
- [ ] Áp dụng premium UI patterns.

### Phase 4: Verification
- [ ] `npm run lint`
- [ ] Manual check.

---

## 🏗️ EXECUTION LOG

### 2026-05-08 16:58 | Migration & Refactor
- Created `src/pages/ai/wallet/` folder.
- Refactored `WalletPage.jsx` & `LedgerPage.jsx` with premium UI.
- Created separate CSS files.
- Updated `App.jsx` lazy imports.
- Removed hardcoded Tailwind colors in favor of theme variables.
- Cleaned up legacy files.

## 🧪 EVIDENCE
- `npx eslint src/pages/ai/wallet/*.jsx`: PASS.
- UI Check: Glow-featured packs, glass cards, immutable ledger look.

## 📁 DIFF SUMMARY
| File | Changes | Note |
|---|---|---|
| WalletPage.jsx | [MOVE/MOD] | Refactor + Premium UI |
| LedgerPage.jsx | [MOVE/MOD] | Refactor + Premium UI |
| WalletPage.css | [NEW] | Component styles |
| LedgerPage.css | [NEW] | Component styles |
| App.jsx | [MOD] | Update import paths |

---

## 👑 Verdict
**STATUS**: `pass`
**SUMMARY**: Đã refactor toàn bộ module Wallet & Ledger sang cấu trúc feature folder. Giao diện được nâng cấp lên chuẩn Premium với các hiệu ứng Card Glow, Glassmorphism và Table refinement. Hệ thống log giao dịch (Ledger) được trình bày rõ ràng, font mono chuyên nghiệp.
