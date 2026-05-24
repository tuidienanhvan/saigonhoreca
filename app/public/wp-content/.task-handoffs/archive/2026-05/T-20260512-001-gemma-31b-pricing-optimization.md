---
id: T-20260512-001
owner: gemma-31b
state: drafted
priority: P1
risk: medium
estimated_minutes: 20
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Gemini, Claude]
created: 2026-05-12 00:20
updated: 2026-05-12 00:35
---

# 📋 T-20260512-001 | Tối ưu gói Pricing & Sửa lỗi hiển thị / Pricing Package Optimization & Mojibake Fix

## 1. 📊 🛡️ Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả / Description |
|---|---|---|
| `priority` | P1 | Mức độ ưu tiên cao để đồng bộ Storefront. |
| `risk` | medium | Ảnh hưởng đến tầng UI Pricing của khách hàng. |
| `state` | drafted | Đang chờ phân phối cho Worker. |

---

## 2. 🎯 🧠 Mục tiêu và Chiến lược / Goal & Strategic Objective
Thu gọn các gói dịch vụ hiển thị từ 4 xuống 3 (Free, Pro, Enterprise) để tối ưu UX và sửa triệt để lỗi hiển thị ký tự Tiếng Việt (Mojibake) trong toàn bộ file `PricingPage.jsx`.

---

## 3. 📚 📖 Tài liệu tham khảo bắt buộc / Required Reading
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành Agent.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Cổng kiểm soát chất lượng.

---

## 4. 🚧 📍 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `c:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp\src\pages\public\PricingPage.jsx`

---

## 5. 🚫 ⛔ Ngoài phạm vi xử lý (Nghiêm cấm) / Out Of Scope (Strictly Forbidden)
- ❌ Không sửa `PricingGrid.jsx`, `PricingCard.jsx` hoặc các file CSS.
- ❌ Không thay đổi logic thanh toán hay tích hợp Stripe.

---

## 6. 🛠️ 📋 Các giai đoạn thực hiện / Phases of Execution
1.  **Giai đoạn 1: Kiểm tra & Đánh giá / Audit & Baseline** 🔍
    - Xác nhận file hiện tại có 4 gói và các lỗi font Tiếng Việt.
2.  **Giai đoạn 2: Triển khai / Implementation** 🛠️
    - Loại bỏ gói `max` khỏi hàm `getTiers`.
    - Khôi phục font chữ Tiếng Việt chuẩn UTF-8 cho toàn bộ text trong file.
3.  **Giai đoạn 3: Kiểm tra nội bộ / Internal Verification** 🧪
    - Chạy `lint`, `build` và script kiểm tra lỗi font.
4.  **Giai đoạn 4: Báo cáo / Standardized Reporting** 📤
    - Hoàn thiện báo cáo theo mẫu chuẩn.

---

## 7. 🔍 🧪 Lệnh kiểm tra bắt buộc / Verification Commands
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
npm run lint
npm run build
cd ".."
$patterns = @(([char]0x00C3 + '.'), ([char]0x00C2 + '.'), ([char]0x00E2 + [char]0x20AC), ([char]0x00F0 + [char]0x0178), ('T' + [char]0x00E1 + [char]0x00BB), ([char]0x00C4 + [char]0x2018), ([char]0x00C4 + [char]0x0090), ([char]0x00C6))
Select-String -Path "pi-store-webapp\src\pages\public\PricingPage.jsx" -Pattern ($patterns -join "|") -CaseSensitive
```

---

## 8. ✅ 🏆 Tiêu chí nghiệm thu (Checklist) / Acceptance Criteria
- [ ] **Đúng Protocol**: Cung cấp RAW output cho toàn bộ lệnh kiểm tra.
- [ ] **Đúng Scope**: 0% thay đổi ngoài file `PricingPage.jsx`.
- [ ] **Chất lượng**: Build và Lint PASS 100%.
- [ ] **Logic**: Chỉ hiển thị 3 gói (Free, Pro, Enterprise).

---

## 9. 📋 🚀 Mẫu lệnh cho Worker / Copy-Paste Prompt
(Sử dụng mẫu v3.0 trong `system/templates/PROMPT.md`)

---

## 10. 📥 📊 Kết quả thực hiện / Agent Result
Status: `not-started`

---

## 11. 📊 🚪 Ma trận kiểm soát chất lượng / Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | Production build success. |
| **Lint Gate** | 🧹 `pending` | | Zero new errors. |
| **Scope Gate** | 📂 `pending` | | No drift from Allowed Scope. |
| **Logic Gate** | 🎯 `pending` | | Requirements met 100%. |

---

## 📁 🔍 Bằng chứng (Raw Terminal Output) / Evidence
```text
(Chờ Worker điền bằng chứng sau khi thực hiện)
```

---

## 📉 📑 Tóm tắt thay đổi / Diff Summary (Calculated)
| File | +Lines | -Lines | Type |
|---|---|---|---|
| PricingPage.jsx | | | Modified |

---

## 🛡️ ⚖️ Phê duyệt của Orchestrator / Orchestrator Review & Final Decision
Status: `pending`

---

## 🆘 🪜 Xử lý lỗi và Hoàn tác / Escalation, Errors & Rollback
- **Rollback**: `git checkout -- pi-store-webapp/src/pages/public/PricingPage.jsx`

---

## 📑 📜 LỊCH SỬ THAY ĐỔI / CHANGE LOG
- **2026-05-12 00:20**: Khởi tạo Dossier.
- **2026-05-12 00:35**: Hoàn thiện định dạng song ngữ + Emoji + Quality Matrix.
