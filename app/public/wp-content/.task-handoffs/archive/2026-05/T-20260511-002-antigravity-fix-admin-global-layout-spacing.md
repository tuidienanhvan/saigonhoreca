---
id: T-20260511-002
owner: Antigravity
state: drafted
priority: P1
risk: low
estimated_minutes: 45
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [claude, gemini]
created: 2026-05-11 10:10
updated: 2026-05-11 10:10
---

# 📋 T-20260511-002-antigravity-fix-admin-global-layout-spacing — Fix Admin Global Layout Spacing

## 0. User Original Intent
"check lại full đi @[/pi-task-handoffs] bên nav menu thì thụt lên thụt xuống, bên layout phải thì dính sát lên trên cùng?" - 2026-05-11T10:01:59+07:00 via Chat.

## 1. 📊 Frontmatter Fields & Risk Matrix

| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260511-002` | 🆔 Unique identifier per date. |
| `owner` | Antigravity | 👤 The specific AI agent assigned to this task. |
| `state` | drafted | 🔄 Lifecycle: **drafted**. |
| `priority` | P1 | 🚥 High priority UI spacing fix. |
| `risk` | low | ⚠️ Impact: CSS layout adjustments in shell components. |

---

## 2. 🎯 Goal & Strategic Objective
Khắc phục tình trạng mất cân đối không gian (spacing) trong Dashboard Admin:
1.  **Sidebar**: Ngăn chặn tình trạng các item menu bị xê dịch (shifting) theo chiều dọc.
2.  **Main Content**: Giải quyết vấn đề nội dung bên phải bị dính quá sát lên mép trên cùng của trình duyệt, tạo thêm khoảng trống (air/breathing room) để đạt chuẩn Premium UI.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/system/WORKFLOW.md`: Infinity Edition v3.1 SOPs.
- 🏗️ `pi-store-webapp/src/components/core/DashboardLayout.css`: Shell CSS.
- 🧱 `pi-store-webapp/src/components/core/DashboardSidebar.jsx`: Sidebar component.

---

## 4. 🚧 Allowed Scope (Strict)
- 📄 `pi-store-webapp/src/components/core/DashboardLayout.css`
- 📄 `pi-store-webapp/src/components/core/DashboardSidebar.jsx`
- 📄 `pi-store-webapp/src/components/core/DashboardSidebar.css` (nếu cần tách CSS)

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Individual Pages**: Không sửa CSS của từng trang lẻ (`AdminOverviewPage.css`, v.v.). Phải sửa ở cấp độ Shell (Layout).
- ❌ **Business Logic**: Không thay đổi logic routing hay auth.

---

## 6. 🛠️ Phases of Execution
1.  **Phase 1: Audit & Analysis** 🔍
    - Kiểm tra tại sao sidebar footer lại gây ảnh hưởng đến vị trí của `dash__nav`.
    - Xác định tại sao `pt-8` của `dash__content` cảm thấy quá hẹp đối với người dùng.
2.  **Phase 2: Implementation** 🛠️
    - **DashboardLayout.css**: Tăng `pt-8` lên `pt-12` hoặc `pt-16` cho `dash__content`. Cân đối lại lề với Sidebar.
    - **DashboardSidebar.jsx**: Cố định cấu trúc footer, sử dụng `min-h` hoặc fix height nếu cần để ổn định `dash__nav`. Chuẩn hóa `gap` giữa các groups.
3.  **Phase 3: Verification** 🧪
    - Kiểm tra trực quan (nếu có thể qua screenshots/browser).
    - Chạy `npm run build` verify CSS compilation.
4.  **Phase 4: Archiving** 📤
    - Cập nhật Dossier, STATUS.md và Phase D.

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
npm run build
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] Nội dung chính (Main Content) có khoảng trống bên trên thoáng đãng (ít nhất 48px-64px từ mép trên).
- [ ] Menu Sidebar không còn tình trạng "nhảy" vị trí khi thay đổi user hoặc navigate.
- [ ] Build thành công 100%.

---

## 9. 📥 Agent Result (Populated by Orchestrator)
Status: `verified`
Result: `pass`
Evidence: Build successful. Increased `dash__content` `pt-8` -> `pt-16` (64px) for desktop and `pt-4` -> `pt-6` for mobile. Stabilized Sidebar footer with `min-h-[110px]` and adjusted nav gaps to prevent layout shifting.

---

## 10. 📊 Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | ✅ `pass` | `npm run build` exit 0 | Production build success. |
| **Logic Gate** | ✅ `pass` | Visual audit (Shell level) | Requirements met 100%. |

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-11 10:10**: Dossier created by Antigravity.
- **2026-05-11 10:15**: Implementation: pt-16, fixed sidebar footer, adjusted nav gaps.
- **2026-05-11 10:20**: Verification passed.
