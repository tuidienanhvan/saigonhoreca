---
id: T-20260512-003
owner: codex
state: drafted
priority: P1
risk: low
estimated_minutes: 30
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Gemini, Claude]
created: 2026-05-12 00:50
updated: 2026-05-12 00:50
---

# 📋 T-20260512-003 | Refactor HomeStore to ProductOfferings & Fix Catalog Mojibake

## 0. 📝 Ý định gốc của người dùng / User Original Intent
- **Verbatim**: "Chợ là sao? giới thiệu về pi-api và pi-dashboard đi, chỉ bán pi-dashboard qua pi-api và các gói token thêm mà? có đọc full pi-backend chưa"
- **Context**: HomeStore hiện tại bị sai business logic. Cần đổi tên sang ProductOfferings, cấu trúc lại để hiển thị đúng 2 nhóm sản phẩm: Subscription Tiers (Dashboard) và Token Packs. Đồng thời sửa lỗi Mojibake trong file JSON.

## 1. 📚 Tài liệu tham khảo / Required Reading
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Tech stack và kiến trúc.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu.
- 📄 `plugins/pi-api/DOCS.md`: Mô hình 3 tầng subscription.
- 📄 `pi-backend/DOCS.md`: Logic License và Token.

## 2. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `pi-store-webapp/src/data/catalog.generated.json`
- 📄 `pi-store-webapp/src/pages/public/HomePage.jsx`
- 📁 `pi-store-webapp/src/components/home/HomeStore.jsx` (Rename to ProductOfferings.jsx)
- 📁 `pi-store-webapp/src/components/home/HomeStore.css` (Rename to ProductOfferings.css)

## 3. 🚫 Ngoài phạm vi xử lý / Out Of Scope
- ❌ **Themes/Plugins lẻ**: Loại bỏ hoàn toàn ý tưởng bán lẻ các module "Saigon House Theme", "Chatbot"... trong Store.
- ❌ **Backend Logic**: Không can thiệp vào cách tính token hay verify license.

## 4. 🛠️ Các giai đoạn thực hiện / Phases of Execution
1.  **Giai đoạn 1: Data Cleanup** 🧹
    - Khôi phục Tiếng Việt chuẩn cho `catalog.generated.json`.
2.  **Giai đoạn 2: Component Refactoring** 🛠️
    - Đổi tên `HomeStore` -> `ProductOfferings`.
    - Xóa logic lọc theo category (Themes/AI/...).
    - Chia làm 2 khu vực: "Pi Ecosystem Tiers" và "AI Token Packs".
3.  **Giai đoạn 3: Integration** ⚙️
    - Map dữ liệu từ file JSON vào các card tương ứng.
4.  **Giai đoạn 4: Verification** 🧪
    - Chạy `lint` và `build`.

## 5. 🔍 Lệnh kiểm tra bắt buộc / Verification Commands
```powershell
cd "pi-store-webapp"
npm run lint
npm run build
```

## 6. ✅ Tiêu chí nghiệm thu / Acceptance Criteria
- [ ] Tên component là `ProductOfferings`.
- [ ] Text Tiếng Việt chuẩn, không Mojibake.
- [ ] Chỉ hiển thị Subscription Plans và Token Packs.
- [ ] Build & Lint PASS.

---
## 📑 LỊCH SỬ THAY ĐỔI / CHANGE LOG
- **2026-05-12 00:50**: Chuyển đổi từ T-20260512-002 sang T-20260512-003 sau khi nhận feedback của user về business model.
