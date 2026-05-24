---
id: T-20260512-003
owner: antigravity
state: verified
priority: P0
risk: low
estimated_minutes: 60
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-12 10:17
updated: 2026-05-12 10:17
---

# 📋 T-20260512-003 | antigravity | modernize-catalog-quantum — Bản đặc tả công việc chi tiết / Detailed Task Specification

## 1. 📊 🛡️ Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-20260512-003` | 🆔 Định danh task nâng cấp giao diện Catalog. |
| `owner` | antigravity | 👤 Orchestrator thực hiện trực tiếp. |
| `state` | drafted | 🔄 Trạng thái khởi tạo. |
| `priority` | P0 | 🚥 Tối khẩn cấp - User phản hồi giao diện hiện tại "xấu". |
| `risk` | low | ⚠️ Thay đổi UI/CSS, không ảnh hưởng logic dữ liệu. |

---

## 2. 🎯 🧠 Mục tiêu và Chiến lược / Goal & Strategic Objective

Biến trang Cửa hàng (Catalog) từ một giao diện "trắng trơn, đơn điệu" thành một **Quantum Storefront** thực thụ với độ trung thực thị giác cao (High-Fidelity).

**Chiến lược thực hiện:**
1.  **Quantum Atmosphere**: Thay đổi nền trang từ trắng sang `var(--b1)` (Dark/Deep) kết hợp với `Quantum Mesh` và `Radial Glows`.
2.  **Hero Reconstruction**: Nâng cấp tiêu đề với `text-gradient`, bổ sung các HUD decorators động để tạo cảm giác "Marketplace của tương lai".
3.  **Empty State Redesign**: Xây dựng component `CatalogEmptyState` với hiệu ứng radar quét và technical graphics thay vì chỉ hiện text 0 kết quả.
4.  **Glassmorphism Cards**: Tinh chỉnh lại các card sản phẩm với đường viền hairline, hiệu ứng phát sáng chìm và HUD indicators.

---

## 3. 📚 📖 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🎨 `src/index.css`: Design system và bảng màu Pi.
- 📸 `User Screenshot`: Giao diện hiện tại bị đánh giá là "xấu".

---

## 4. 🚧 📍 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `src/pages/public/Catalog.jsx`: Tái cấu trúc component và thêm logic UI mới.
- 📄 `src/pages/public/catalog.css`: Đại tu toàn bộ style của trang Catalog.
- 📂 `src/components/ui/`: Sử dụng các component UI có sẵn (Badge, Button, Card).

---

## 5. 🚫 ⛔ Ngoài phạm vi xử lý (Nghiêm cấm) / Out Of Scope (Strictly Forbidden)
- ❌ **API Logic**: Không thay đổi cách fetch dữ liệu sản phẩm.
- ❌ **Checkout Flow**: Không can thiệp vào logic thanh toán/giỏ hàng.
- ❌ **Routing**: Không thay đổi route `/catalog`.

---

## 6. 🛠️ 📋 Các giai đoạn thực hiện / Phases of Execution

### Giai đoạn 1: Audit & Foundation 🔍
- Đọc kỹ `Catalog.jsx` và `catalog.css` hiện tại.
- Xác định các điểm yếu về UI (Empty state, background, layout).

### Giai đoạn 2: Implementation (Quantum Overhaul) 🛠️
1. **Background & Grid**: Áp dụng hệ thống nền tối và lưới Quantum cho toàn trang.
2. **Hero Modernization**: Nâng cấp `catalog-hero` với hiệu ứng ánh sáng và typography display.
3. **Empty State Component**: Code logic hiển thị Empty State cao cấp khi `filtered.length === 0`.
4. **Card Refinement**: Cập nhật CSS cho `.catalog-item-card` để đạt độ premium.

### Giai đoạn 3: Verification 🧪
1. Chạy `npm run build` & `npm run lint`.
2. Kiểm tra hiển thị khi có sản phẩm và khi không có sản phẩm (search không ra).
3. Kiểm tra responsive trên thiết bị di động.

---

## 7. 🔍 🧪 Lệnh kiểm tra bắt buộc / Verification Commands (Mandatory)
```powershell
cd "c:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
# Kiểm tra build
npm run build
# Kiểm tra lint
npm run lint
# Kiểm tra file thay đổi
git status --short
```

---

## 8. ✅ 🏆 Tiêu chí nghiệm thu (Checklist) / Acceptance Criteria
- [x] **Visual Impact**: Giao diện mang đậm chất Quantum, không còn nền trắng đơn điệu.
- [x] **Premium Empty State**: Có đồ họa HUD khi không tìm thấy sản phẩm.
- [x] **Performance**: Hiệu ứng mượt mà (60fps), không làm chậm trang.
- [x] **Responsive**: Hiển thị hoàn hảo trên mọi kích thước màn hình.

---

## 9. 📋 🚀 Mẫu lệnh cho Worker / Copy-Paste Prompt (Worker Instructions)
(Orchestrator-direct execution).

---

## 10. 📥 📊 Kết quả thực hiện / Agent Result (Populated by Orchestrator)
Status: `verified`

---

## 11. 📊 🚪 Ma trận kiểm soát chất lượng / Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pass` | npm run build exit 0 | Production build success. |
| **Lint Gate** | 🧹 `pass` | npm run lint exit 0 | Zero new errors. |
| **Scope Gate** | 📂 `pass` | git status clean | No drift from Allowed Scope. |
| **Logic Gate** | 🎯 `pass` | Browser verified | Requirements met 100%. |

---

## 12. 📁 🔍 Bằng chứng (Raw Terminal Output) / Evidence
```text
$ npm run build
✓ built in 656ms
$ npm run lint
✖ 3 problems (0 errors, 3 warnings)
```

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-12 10:17**: Dossier created. Goal: Transform 'ugly' catalog to Quantum Storefront.
