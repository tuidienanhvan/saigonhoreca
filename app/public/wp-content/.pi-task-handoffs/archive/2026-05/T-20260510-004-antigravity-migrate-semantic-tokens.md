---
id: T-20260510-004
owner: antigravity
state: verified
priority: P1
risk: medium
estimated_minutes: 60
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-10 18:18
updated: 2026-05-10 18:18
---

# 📋 T-20260510-004-antigravity-migrate-semantic-tokens — Detailed Task Specification

## 0. User Original Intent
"lên plan chi tiết, áp dụng cho các components và pàes"
(Tạm dịch: Lập kế hoạch chi tiết và áp dụng hệ thống token mới cho toàn bộ các component và trang của ứng dụng).

## 1. 📊 Frontmatter Fields & Risk Matrix
| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260510-004` | 🆔 Mã định danh duy nhất cho nhiệm vụ migrate design system. |
| `owner` | antigravity | 👤 Gemini-family agent chịu trách nhiệm thực thi. |
| `state` | drafted | 🔄 Trạng thái hiện tại: Đang lập hồ sơ. |
| `priority` | P1 | 🚥 Ưu tiên cao: Ảnh hưởng đến tính nhất quán của toàn bộ UI. |
| `risk` | medium | ⚠️ Rủi ro trung bình: Thay đổi class hàng loạt có thể gây lỗi visual nếu không cẩn thận. |

---

## 2. 🎯 Goal & Strategic Objective
Chuyển đổi toàn bộ hệ thống class CSS từ dạng Legacy (text-text-1, bg-surface...) sang hệ thống Semantic Token chuẩn DaisyUI (text-base-content, bg-base-200...) trong dự án `pi-dashboard-webapp`. Việc này giúp code sạch hơn, dễ bảo trì và chuẩn hóa theo các hệ thống thiết kế hiện đại mà vẫn giữ được phong cách Premium Glassmorphism.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Core operational guidelines.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Project overview.
- 🎨 `src/index.css`: Nơi định nghĩa các Token mới.

---

## 4. 🚧 Allowed Scope (Strict)
Toàn bộ mã nguồn frontend của Dashboard App:
- 📂 `src/components/**/*`
- 📂 `src/pages/**/*`
- 📂 `src/layouts/**/*`
- 📂 `src/styles/**/*`
- 📄 `src/App.jsx`

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **WP Backend**: Không động vào plugin `pi-api` hay core WordPress.
- ❌ **Store Webapp**: Không động vào dự án `pi-store-webapp`.
- ❌ **Dependencies**: Không cài thêm package npm nào (như DaisyUI).
- ❌ **Logic Change**: Không thay đổi logic nghiệp vụ của các component.

---

## 6. 🛠️ Phases of Execution
1.  **Phase 1: Audit & Mapping** 🔍
    - Xác định tất cả các file chứa class cũ.
2.  **Phase 2: Core Migration** 🛠️
    - Cập nhật các Layout chính và Component dùng chung (`shared/`).
3.  **Phase 3: Page Migration** 📄
    - Cập nhật các trang theo từng module (Content, SEO, Leads, System).
4.  **Phase 4: Style Migration** 🎨
    - Cập nhật các file `.css` trong thư mục `styles/`.
5.  **Phase 5: Final Audit & Verification** 🧪
    - Run build và check regressions.

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
cd "c:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-dashboard-webapp"
# Kiểm tra số lượng class cũ còn sót lại
grep -r "text-text-1" src/ | wc -l
# Kiểm tra build
npm run build
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] **Consistency**: 100% các class legacy đã được chuyển sang semantic token.
- [ ] **Visual Integrity**: Giao diện không bị thay đổi màu sắc hay bố cục so với trước khi migrate.
- [ ] **Zero Drift**: Không sửa đổi bất kỳ file nào ngoài scope cho phép.
- [ ] **Clean Code**: Xóa các comment thừa hoặc legacy code nếu không còn cần thiết.

---

## 9. 📋 Copy-Paste Prompt (Worker Instructions)
(Sẽ được cập nhật khi chuyển trạng thái sang dispatched)

---

## 10. 📥 Agent Result (Populated by Orchestrator)
Status: `completed`
Result: Đã migrate 100% legacy tokens và hardcoded colors (white/black patterns) sang Semantic Tokens. Build production thành công.

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-10 18:18**: Dossier created by Antigravity.
