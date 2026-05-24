---
id: T-20260510-006
owner: antigravity
state: archived
priority: P1
risk: high
estimated_minutes: 120
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Claude, Codex]
created: 2026-05-10 20:18
updated: 2026-05-10 20:18
---

# 📋 T-20260510-006-antigravity-full-flow-optimization — Detailed Task Specification

## 0. User Original Intent
- **Message**: "lên plan phân tích quy trình và tối ưu toàn diện, tôi muốn khi bạn xong t có thể giả lập user và mua và xài token,..."
- **Timestamp**: 2026-05-10T20:15:46+07:00
- **Translation**: "create a plan for comprehensive process analysis and optimization, I want to be able to simulate a user buying and using tokens when you're done,..."

## 1. 📊 Frontmatter Fields & Risk Matrix

| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260510-006` | 🆔 Unique identifier. |
| `owner` | `antigravity` | 👤 Google Deepmind Agent. |
| `state` | `drafted` | 🔄 Lifecycle: Currently in planning phase. |
| `priority` | `P1` | 🚥 High priority for system completion. |
| `risk` | `high` | ⚠️ Cross-codebase changes affecting core revenue flow. |

---

## 2. 🎯 Goal & Strategic Objective
Phân tích, chuẩn hóa và tối ưu hóa toàn bộ quy trình từ lúc người dùng mua gói tại **Pi Store**, kích hoạt tại **WordPress (Pi API)** cho đến khi sử dụng token trên **Pi Dashboard**. Mục tiêu cuối cùng là tạo ra một luồng "End-to-End" hoàn chỉnh, cho phép người dùng (hoặc admin giả lập) trải nghiệm trọn vẹn quy trình mua và sử dụng AI Cloud.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Core operational guidelines.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Project overview.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Quality requirements.

---

## 4. 🚧 Allowed Scope (Strict)
- 📂 `pi-store-webapp/*`
- 📂 `pi-dashboard-webapp/*`
- 📂 `pi-backend/*`
- 📂 `plugins/pi-api/*`

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Third-party APIs**: Không thực hiện thanh toán thật qua Stripe (chế độ Test/Mock only).
- ❌ **WordPress Core**: Không thay đổi file hệ thống WordPress.

---

## 6. 🛠️ Phases of Execution
1.  **Phase 1: Flow Audit** 🔍
    - Kiểm tra logic `checkout` tại Pi Store.
    - Kiểm tra logic `registerSiteCredentials` tại Pi Backend.
    - Kiểm tra logic `handshake` tại Pi API plugin.
    - Kiểm tra logic `token deduction` tại Pi Backend.
2.  **Phase 2: Planning (Implementation Plan)** 🛠️
    - Tạo `implementation_plan.md` chi tiết.
3.  **Phase 3: Optimization & Fixes** 🛠️
    - Sửa lỗi/tối ưu các thành phần còn thiếu hoặc yếu.
4.  **Phase 4: Simulation Tooling** 🧪
    - Tạo các script hoặc UI test để giả lập quy trình mua -> xài.
5.  **Phase 5: Final Verification** 👑
    - Chạy full flow audit.

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
# Sẽ cập nhật chi tiết sau khi có Implementation Plan
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] User có thể giả lập mua một gói MAX tại Pi Store.
- [ ] Plugin Pi API tự động tạo App Password và sync về Backend thành công.
- [ ] User có thể vào Dashboard và sử dụng một tính năng AI (ví dụ: Viết bài).
- [ ] Tokens được trừ chính xác trong database Pi Backend.
- [ ] Không có lỗi CORS hoặc 500 phát sinh trong suốt quá trình.

---

## 9. 📋 Copy-Paste Prompt (Worker Instructions)
(Sẽ cập nhật khi Dispatch)

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-10 20:18**: Dossier created by antigravity.
