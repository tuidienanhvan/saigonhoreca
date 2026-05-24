---
id: T-20260512-002
owner: antigravity
state: verified
priority: P1
risk: low
estimated_minutes: 60
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-12 10:11
updated: 2026-05-12 10:11
---

# 📋 T-20260512-002 | antigravity | build-quantum-docs-center — Bản đặc tả công việc chi tiết / Detailed Task Specification

## 1. 📊 🛡️ Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-20260512-002` | 🆔 Định danh duy nhất cho task xây dựng trang Docs. |
| `owner` | antigravity | 👤 Orchestrator trực tiếp thực thi (Orchestrator-direct mode). |
| `state` | drafted | 🔄 Trạng thái khởi tạo. |
| `priority` | P1 | 🚥 Mức độ ưu tiên cao do ảnh hưởng trực tiếp đến trải nghiệm người dùng. |
| `risk` | low | ⚠️ Tác động thấp đến core logic, chủ yếu là UI/UX và I18n. |

---

## 2. 🎯 🧠 Mục tiêu và Chiến lược / Goal & Strategic Objective

Mục tiêu tối thượng là xây dựng một **Trung tâm Tài liệu (Documentation Center)** đẳng cấp Quantum cho Pi Store, giải quyết triệt để tình trạng "trống rỗng" và lỗi font hiện tại.

**Chiến lược thực hiện:**
1.  **Dữ liệu hóa (I18n)**: Khôi phục cấu trúc dữ liệu `docs` trong `messages.js` bị thiếu.
2.  **Quantum Modernization**: Tái định nghĩa ngôn ngữ thiết kế của trang Docs theo chuẩn Pi Ecosystem (Glassmorphism, HUD elements, Text-gradients).
3.  **Linguistic Integrity**: Xóa bỏ hoàn toàn lỗi Mojibake trong code, đảm bảo tiếng Việt chuẩn UTF-8.

---

## 3. 📚 📖 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `pi-store-webapp/README.md`: Cấu trúc dự án Store.
- 🎨 `src/index.css`: Design system và CSS variables của Pi.

---

## 4. 🚧 📍 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `src/i18n/messages.js`: Bổ sung keys `docs` cho `vi` và `en`.
- 📄 `src/pages/public/DocsPage.jsx`: Refactor layout và fix font.
- 📄 `src/components/docs/DocsHero.jsx` & `DocsHero.css`: Nâng cấp UI Hero.
- 📄 `src/components/docs/DocsGrid.jsx` & `DocsGrid.css`: Nâng cấp UI Grid/Cards.

---

## 5. 🚫 ⛔ Ngoài phạm vi xử lý (Nghiêm cấm) / Out Of Scope (Strictly Forbidden)
- ❌ **Cấu hình Vite**: Không chỉnh sửa `vite.config.js`.
- ❌ **Routing**: Không thay đổi route `/docs` trong `App.jsx`.
- ❌ **Dependencies**: Không cài thêm bất kỳ npm package nào.

---

## 6. 🛠️ 📋 Các giai đoạn thực hiện / Phases of Execution

### Giai đoạn 1: Audit & Content Infrastructure 🔍
1. Kiểm tra sự tồn tại của các file trong `Allowed Scope`.
2. Bổ sung nội dung tài liệu chuẩn vào `src/i18n/messages.js` (Getting Started, API Reference, Guides).

### Giai đoạn 2: UI Engineering (Quantum Style) 🛠️
1. **DocsHero**: Triển khai `DocsHero.jsx` với HUD decorators, `text-gradient` và thanh search ảo diệu.
2. **DocsGrid**: Triển khai `DocsGrid.jsx` với các glass-panels, HUD index và hiệu ứng hover mượt mà.
3. **DocsPage**: Tổng hợp layout, sử dụng `container-fixed` và sửa lỗi mã hóa tiếng Việt.

### Giai đoạn 3: Verification & Polish 🧪
1. Chạy `npm run lint` để verify code quality.
2. Chạy `npm run build` để verify build stability.
3. Kiểm tra visual trên browser (Mobile & Desktop).

---

## 7. 🔍 🧪 Lệnh kiểm tra bắt buộc / Verification Commands (Mandatory)
```powershell
cd "c:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
# Kiểm tra Audit
npm run lint
# Kiểm tra Đóng gói
npm run build
# Kiểm tra Trạng thái Git
git status --short
```

---

## 8. ✅ 🏆 Tiêu chí nghiệm thu (Checklist) / Acceptance Criteria
- [x] **Dữ liệu đầy đủ**: Trang Docs không còn trống, hiển thị đúng nội dung từ `dict.docs`.
- [ ] **Giao diện Quantum**: Đạt độ phân giải cao, có HUD decorators và hiệu ứng glassmorphism.
- [ ] **Bảo toàn mã hóa**: Không còn ký tự lạ, tiếng Việt hiển thị hoàn hảo.
- [ ] **Build & Lint Pass**: 100% không lỗi build/lint.

---

## 9. 📋 🚀 Mẫu lệnh cho Worker / Copy-Paste Prompt (Worker Instructions)
(Nhiệm vụ này do Orchestrator-direct thực hiện, không cần delegate cho worker khác).

---

## 10. 📥 📊 Kết quả thực hiện / Agent Result (Populated by Orchestrator)
Status: `in-progress`

---

## 11. 📊 🚪 Ma trận kiểm soát chất lượng / Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | Production build success. |
| **Lint Gate** | 🧹 `pending` | | Zero new errors. |
| **Scope Gate** | 📂 `pending` | | No drift from Allowed Scope. |
| **Logic Gate** | 🎯 `pending` | | Requirements met 100%. |

---

## 12. 📁 🔍 Bằng chứng (Raw Terminal Output) / Evidence
(Sẽ được cập nhật sau khi thực thi)

---

## 13. 📉 📑 Tóm tắt thay đổi / Diff Summary (Calculated)
(Sẽ được cập nhật sau khi thực thi)

---

## 14. 🛡️ ⚖️ Phê duyệt của Orchestrator / Orchestrator Review & Final Decision
Status: `pending`

---

## 15. 🆘 🪜 Xử lý lỗi và Hoàn tác / Escalation, Errors & Rollback
- **Loại lỗi / Failure Type**: None
- **Quy trình hoàn tác / Rollback Procedure**: 
  1. `git checkout -- .`
- **Next Step**: N/A

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-12 10:11**: Dossier created with Quantum Specs.
