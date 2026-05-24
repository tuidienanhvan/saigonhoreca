---
id: T-20260512-002
owner: codex
state: drafted
priority: P1
risk: low
estimated_minutes: 25
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Gemini, Claude]
created: 2026-05-12 00:40
updated: 2026-05-12 00:40
---

# 📋 T-20260512-002 | Refactor & Rename HomeStore to EcosystemMarketplace

## 0. 📝 Ý định gốc của người dùng / User Original Intent
- **Verbatim**: "Làm 1 cái mô tả về pi-dashboard thôi, đọc full pi-dashboard r lên plan lại cho phần homestore này, đổi tên thành gì cho hợp lý r làm @[/pi-task-handoffs]"
- **Context**: Người dùng nhận thấy `HomeStore` hiện tại rời rạc và dùng dữ liệu mock, muốn đổi tên và cấu trúc lại cho khớp với hệ sinh thái Pi Dashboard.

## 1. 📚 Tài liệu tham khảo / Required Reading
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Tech stack và kiến trúc.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu.

## 2. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `pi-store-webapp/src/pages/public/HomePage.jsx`
- 📁 `pi-store-webapp/src/components/home/HomeStore.jsx` (Rename to EcosystemMarketplace.jsx)
- 📁 `pi-store-webapp/src/components/home/HomeStore.css` (Rename to EcosystemMarketplace.css)

## 3. 🚫 Ngoài phạm vi xử lý / Out Of Scope
- ❌ **API Backend**: Không sửa `pi-api` hay DB.
- ❌ **Shared Components**: Không sửa `Button.jsx`, `Badge.jsx` trừ khi lỗi logic.
- ❌ **Styling**: Không thay đổi Design System gốc, chỉ đổi namespace CSS.

## 4. 🛠️ Các giai đoạn thực hiện / Phases of Execution
1.  **Giai đoạn 1: Chuẩn bị / Audit & Baseline** 🔍
    - Xác nhận dữ liệu trong `catalog.generated.json` để map field.
2.  **Giai đoạn 2: Đổi tên & Di chuyển / Refactor & Move** 🛠️
    - Đổi tên file `HomeStore.jsx` -> `EcosystemMarketplace.jsx`.
    - Đổi tên file `HomeStore.css` -> `EcosystemMarketplace.css`.
    - Cập nhật import trong `HomePage.jsx`.
3.  **Giai đoạn 3: Logic Dữ liệu / Data Integration** ⚙️
    - Xóa `MOCK_PRODUCTS`.
    - Map dữ liệu từ `products` prop sang grid.
4.  **Giai đoạn 4: Kiểm tra / Verification** 🧪
    - Chạy `lint` và `build`.

## 5. 🔍 Lệnh kiểm tra bắt buộc / Verification Commands
```powershell
cd "pi-store-webapp"
npm run lint
npm run build
```

## 6. ✅ Tiêu chí nghiệm thu / Acceptance Criteria
- [ ] Component mới mang tên `EcosystemMarketplace`.
- [ ] Dữ liệu hiển thị đúng từ `catalog.generated.json`.
- [ ] CSS Namespace được cập nhật thành `.ecosystem-marketplace`.
- [ ] Build & Lint PASS.

## 7. 📋 Mẫu lệnh cho Worker / Copy-Paste Prompt
```text
# 🚀 UNIVERSAL AGENT PROMPT v3.0 — INFINITY EDITION (PROMAX)
Bạn là codex, Senior Surgeon tại Pi Ecosystem.

## 1. 🎯 KHỞI TẠO NGỮ CẢNH
Đọc: .task-handoffs/SKILL.md, .task-handoffs/project/PROJECT.md, active/T-20260512-002-codex-refactor-homestore.md.

## 2. 📝 CHI TIẾT TASK: T-20260512-002
Mục tiêu: Đổi tên HomeStore -> EcosystemMarketplace & kết nối dữ liệu thật từ catalog.generated.json.

## 3. 🛡️ QUY TẮC
- Scope: HomePage.jsx, HomeStore.jsx/css.
- Cấm: Sửa file ngoài scope.

## 4. 📤 BÁO CÁO
Theo format chuẩn REPORT BLOCK v3.1.
```

## 8. 📊 Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | Production build success. |
| **Lint Gate** | 🧹 `pending` | | Zero new errors. |
| **Scope Gate** | 📂 `pending` | | No drift from Allowed Scope. |
| **Logic Gate** | 🎯 `pending` | | Requirements met 100%. |

---
## 📑 LỊCH SỬ THAY ĐỔI / CHANGE LOG
- **2026-05-12 00:40**: Khởi tạo Dossier theo yêu cầu của user.
