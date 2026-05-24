---
id: T-20260511-003
owner: Gemini (Antigravity)
state: archived
priority: high
risk: low
estimated_minutes: 90
created: 2026-05-11T10:05:00Z
updated: 2026-05-11T10:05:00Z
---

# 🛡️ DOSSIER: T-20260511-003-gemini-customer-profile

## 0. User Original Intent
> "@[/pi-task-handoffs]vậy lên kế hoạch thêm 1 trang profile khách hàng trong pi-store-admin lưu trữ lại application password và site đang dùng" (Captured at 2026-05-11T09:57:14+07:00)

## 1. Goal Description
Xây dựng trang chi tiết hồ sơ khách hàng (User Profile) trong Dashboard Admin để quản trị viên có thể lưu trữ và quản lý các thông tin kỹ thuật nhạy cảm: **Application Password** (dùng cho kết nối API/WP) và **Site URL** (địa chỉ website khách hàng đang vận hành).

## 2. Allowed Scope
- `pi-backend/app/shared/auth/models.py`
- `pi-backend/app/shared/auth/schemas.py`
- `pi-backend/app/api/v1/endpoints/users.py`
- `pi-backend/migrations/`
- `pi-store-webapp/src/App.jsx`
- `pi-store-webapp/src/pages/system/AdminUserProfilePage.jsx`
- `pi-store-webapp/src/pages/system/AdminUsersPage.jsx`

## 3. Out Of Scope
- Không thay đổi cơ chế mã hóa mật khẩu đăng nhập chính.
- Không thay đổi logic quản lý License hiện có.

## 4. Proposed Changes

### 🏛️ Backend (pi-backend)
#### [MODIFY] `app/shared/auth/models.py`
- Thêm cột `application_password`: String(255), Nullable.
- Thêm cột `site_url`: String(500), Nullable.

#### [NEW] Migration
- Chạy `alembic revision --autogenerate` để tạo script cập nhật DB.

#### [MODIFY] User API Endpoints
- Cập nhật schemas và endpoints để hỗ trợ 2 field mới.

---

### 🎨 Frontend (pi-store-webapp)
#### [MODIFY] `src/App.jsx`
- Đăng ký route: `<Route path="users/:id" element={<Suspense fallback={<AdminTableSkeleton />}><AdminUserProfilePage /></Suspense>} />` trong nhóm `/admin`.

#### [NEW] `src/pages/system/AdminUserProfilePage.jsx`
- Trang giao diện chi tiết hồ sơ khách hàng dành cho Admin.

#### [MODIFY] `src/pages/system/AdminUsersPage.jsx`
- Đổi cột "Hồ sơ" thành dạng link (hoặc nút bấm) chuyển hướng sang `/admin/users/:id`.

## 5. Verification Plan
### Quality Gates
- [ ] Build: `cd pi-store-webapp && npm run build`
- [ ] Lint: `cd pi-store-webapp && npm run lint`
- [ ] Logic: Kiểm tra CRUD dữ liệu qua giao diện mới.

## 6. Open Questions
> [!IMPORTANT]
> **Bảo mật**: Mặc định em sẽ để `Application Password` hiển thị dạng Masked (***) và có nút "Show" để Admin copy. Anh có đồng ý không?
