---
id: T-20260510-005
owner: antigravity
state: archived
priority: high
risk: medium
created: 2026-05-10T20:10:00Z
updated: 2026-05-10T20:10:00Z
---

# T-20260510-005: Milestone 1 — Full Layout Synchronization (M1)

## 0. User Original Intent
"lên plan đi cho langua M1 sửa full layout" - 2026-05-10T20:09:33+07:00 via Chat.

## 1. Context & Scope
Sau khi hoàn thành đợt migration ban đầu sang Tailwind v4, hệ thống layout vẫn còn tồn tại các "Legacy Utilities" và một số trang chưa được đồng bộ hóa hoàn toàn về độ rộng container và semantic tokens. Milestone 1 nhằm mục tiêu dọn dẹp triệt để và ổn định hóa toàn bộ UI.

**Allowed Scope:**
- `pi-store-webapp/src/**/*`
- `pi-store-webapp/src/styles/index.css`

## 2. Acceptance Criteria
- [ ] 100% các file `.jsx` sử dụng `grid-cols-x` thay vì `cols-x` (hoặc chuẩn hóa class `.cols-x`).
- [ ] Header, Hero, Bento, và Footer đồng bộ độ rộng `max-w-[1400px]`.
- [ ] Sidebar Dashboard ổn định ở 280px, không bị "bóp" layout.
- [ ] User Menu dropdown có thiết kế premium, đầy đủ icon.
- [ ] Build thành công không lỗi CSS/JS.

## 3. Implementation Plan
- **Phase 1: Audit & Fix Utilities**: Quét toàn bộ project để thay thế `cols-` sang `grid-cols-`.
- **Phase 2: Layout Synchronization**: Áp dụng container chuẩn cho các trang Public và Dashboard.
- **Phase 3: Semantic Refinement**: Rà soát lại các biến màu sắc, đảm bảo 100% dùng token.

## 4. Evidence
(To be updated during execution)

## 5. Review & Gates
- Build Gate: [ ]
- Lint Gate: [ ]
- Scope Gate: [ ]
- Logic Gate: [ ]
