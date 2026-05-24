---
id: T-20260509-001
owner: gemini
state: archived
priority: P1
risk: medium
estimated_minutes: 60
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Claude]
created: 2026-05-09 12:05
updated: 2026-05-09 12:05
---

# 📋 T-20260509-001-gemini-unify-pi-api — Detailed Task Specification

## 0. User Original Intent
> "LÊN KẾ HOẠCH @[/pi-task-handoffs] KẾT NỐI PI-API RÕ RÀNG, NỘI DUNG PHẢI HỢP LOGIC"
- Timestamp: 2026-05-09 12:04
- Context: Browser shows user at /admin/usage and /tools pages.

---

## 1. 📊 Frontmatter Fields & Risk Matrix
| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260509-001` | Unique ID for API Unification task. |
| `owner` | gemini | Assigned to Gemini (Antigravity). |
| `state` | drafted | Initial planning phase. |
| `priority` | P1 | High priority as it affects all features. |
| `risk` | medium | Refactoring core API client might cause regression in 32+ pages. |

---

## 2. 🎯 Goal & Strategic Objective
Chuẩn hóa kiến trúc kết nối API giữa Frontend (Store & Dashboard) và Pi Backend. Mục tiêu là loại bỏ sự hỗn loạn trong `client.js` hiện tại của Dashboard, tách bạch rõ ràng giữa WordPress REST API và Pi Backend v1 API, đồng thời áp dụng pattern service-based đã thành công ở Storefront.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`
- 🏗️ `.task-handoffs/project/PROJECT.md`
- 👤 `pi-backend/app/main.py` (API Surface)
- 👤 `pi-dashboard-webapp/src/api/client.js` (Target for refactor)
- 👤 `pi-store-webapp/src/lib/api-client.js` (Pattern reference)

---

## 4. 🚧 Allowed Scope (Strict)
- 📄 `pi-dashboard-webapp/src/api/client.js`
- 📄 `pi-dashboard-webapp/src/api/useApi.js`
- 📄 `pi-store-webapp/src/lib/api-client.js`
- 📄 `pi-store-webapp/src/api/billing.js`

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Backend Changes**: Không sửa code Python trong `pi-backend/`.
- ❌ **WP Plugin Changes**: Không sửa code PHP trong `plugins/pi-api/`.
- ❌ **UI Changes**: Không sửa giao diện các trang đang sử dụng API.

---

## 6. 🛠️ Phases of Execution
1.  **Phase 1: Audit & Pattern Sync** 🔍
    - Phân tích các endpoint `v1` đang dùng trong `pi-store-webapp`.
    - Đối chiếu với các endpoint đang gọi thủ công trong `pi-dashboard-webapp`.
2.  **Phase 2: Dashboard Client Refactor** 🛠️
    - Rewrite `createApi` trong `pi-dashboard-webapp/src/api/client.js`.
    - Triển khai cấu trúc service-based: `api.admin.*`, `api.keys.*`, `api.license.*`.
    - Đảm bảo Backward Compatibility cho hook `useApi`.
3.  **Phase 3: Storefront Client Alignment** 🛠️
    - Đồng bộ hóa logic Auth interceptor giữa 2 app.
4.  **Phase 4: Verification** 🧪
    - Run build & lint trên cả 2 webapp.
    - Test login/fetch flows.

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
# Dashboard checks
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-dashboard-webapp"
npm run lint
npm run build

# Store checks
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
npm run lint
npm run build
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [x] Tách bạch rõ `piClient` (Backend) và `wpClient` (WordPress).
- [x] Dashboard API Client sử dụng pattern service-based (`api.domain.method`).
- [x] Hook `useApi` vẫn hoạt động bình thường mà không cần sửa code ở 32 trang.
- [x] Thống nhất biến môi trường `VITE_PI_API_URL`.
- [x] Không còn lỗi 401 loop khi token hết hạn.

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-09 12:05**: Dossier created by Gemini.
- **2026-05-09 12:20**: API Client refactor completed.
- **2026-05-09 12:25**: Build gates passed for both webapps.
- **2026-05-09 12:26**: Task closed.
