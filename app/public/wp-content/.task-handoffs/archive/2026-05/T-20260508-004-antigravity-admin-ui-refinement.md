---
id: T-20260508-004
owner: antigravity
state: dispatched
priority: P0
risk: low
estimated_minutes: 30
parent: T-20260508-003
children: []
depends_on: []
parallelization_ok: false
retry_count: 1
retry_max: 2
escalation_path: grok -> codex -> claude
created: 2026-05-08T15:52:00Z
updated: 2026-05-08T15:52:00Z
---

# 🛡️ PI ECOSYSTEM | DOSSIER: T-20260508-004

## 🎯 GOAL: Refine Admin Overview UI/UX - Fix Hardcoding & Scrollbars

Người dùng tiếp tục không hài lòng với UI, yêu cầu:
1.  Loại bỏ hoàn toàn hardcoded colors, sử dụng biến theme.
2.  Sửa lỗi 2 thanh scrollbar không đúng theme.
3.  Tối ưu hóa màu sắc cho hài hòa hơn.

## 📁 ALLOWED SCOPE
- `pi-store-webapp/src/pages/core/AdminOverviewPage.jsx`
- `pi-store-webapp/src/pages/core/AdminOverviewPage.css`

## 🛠️ PHASES

### Phase 1: Audit
- [ ] Xác định các vị trí đang dùng hex code trong `AdminOverviewPage.css`.
- [ ] Xác định các container đang bị hiện scrollbar mặc định.

### Phase 2: Implementation
- [ ] Thay thế hex code bằng `var(--brand)`, `var(--success)`, v.v.
- [ ] Style lại scrollbar cho `no-scrollbar` hoặc custom scrollbar đồng bộ theme.
- [ ] Cân chỉnh lại độ bão hòa (saturation) của các gradient để hài hòa với dark mode.

### Phase 3: Verification
- [ ] `npm run lint`
- [ ] Kiểm tra scrollbar trên Browser.

### Phase 4: Reporting
- [ ] Ghi lại các thay đổi.

---

## 🏗️ EXECUTION LOG

### 2026-05-08 15:56 | Implementation Refined
- Replaced all hex codes with `var(--brand)`, `color-mix`, etc.
- Added global scrollbar overrides for `.dash__sidebar` and `.dash__main`.
- Toned down gradients and badges for a more professional "Premium" look.
- Fixed `text-white` usage in JSX.

## 🧪 EVIDENCE
- `npx eslint src/pages/core/AdminOverviewPage.jsx`: PASS.
- Browser: Scrollbars now match dark theme (thin, subtle).

## 📁 DIFF SUMMARY
| File | Changes | Note |
|---|---|---|
| AdminOverviewPage.css | Refactored | Theme variables & Scrollbars |
| AdminOverviewPage.jsx | Refactored | Semantic classes |

---

## 👑 Verdict
**STATUS**: `pass`
**SUMMARY**: Đã sửa triệt để vấn đề hardcoded màu sắc. Toàn bộ style giờ đây sử dụng biến theme và `color-mix` để đảm bảo tính nhất quán. Đã fix lỗi scrollbar trắng/mặc định trên sidebar và main content bằng cách override CSS global trong scope của trang. Giao diện giờ đây hài hòa và chuyên nghiệp hơn.
