# Final Lint Sanitization Pass
 
- ID: `T-20260502-015`
- Owner: `Gemini`
- State: `active`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`
 
## Context / Bối Cảnh
 
Tiếp nối task `T-20260502-013`. Đã dọn dẹp xong phần lớn dashboard. Hiện tại chỉ còn lại 6 file có lỗi lint cứng đầu hoặc cần refactor logic hook (`exhaustive-deps`). Mục tiêu là đưa số lỗi về 0 để pass build gate.
 
## Acceptance Criteria / Tiêu Chí Nhận
 
- [x] Fix triệt để `set-state-in-effect` trong 4 file SEO/Leads còn lại (sử dụng `useRef` guard).
- [x] Fix `no-unused-vars` (biến `api`) trong `SeoSearchConsole.jsx`.
- [x] Refactor `Analytics.jsx` để pass `exhaustive-deps` (wrap callbacks/memos).
- [x] Đảm bảo `npm run lint` trả về 0 Errors.
- [x] Đảm bảo `npm run build` thành công 100%.
 
## Files to Audit / Danh sách file
 
1. `src/pages/leads/LeadEnrichment.jsx` (set-state-in-effect)
2. `src/pages/seo/SeoHealth.jsx` (set-state-in-effect)
3. `src/pages/seo/SeoLlmTxt.jsx` (set-state-in-effect)
4. `src/pages/seo/SeoRobotsTxt.jsx` (set-state-in-effect)
5. `src/pages/seo/SeoSearchConsole.jsx` (unused api)
6. `src/pages/core/Analytics.jsx` (exhaustive-deps)
 
## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

Đã hoàn thành sửa lỗi lint cho 6 file + 1 file bổ sung để đạt 0 errors:

1. **LeadEnrichment.jsx** (set-state-in-effect): Refactor `refresh` callback với `useRef` guard, tách biệt assignment ref vào effect riêng.
2. **SeoHealth.jsx** (set-state-in-effect): Refactor `runChecks` callback với `useRef` guard, assignment ref trong effect.
3. **SeoLlmTxt.jsx** (set-state-in-effect): Thay initialization effect bằng pattern `useRef` chứa callback, cập nhật ref trong effect phụ, invoke trong effect chính.
4. **SeoRobotsTxt.jsx** (set-state-in-effect): Tương tự SeoLlmTxt, dùng `useRef` guard.
5. **SeoSearchConsole.jsx** (unused var): Loại bỏ biến `api` không dùng từ destructuring.
6. **Analytics.jsx** (exhaustive-deps): Bọc `topPages` và `devices` trong `useMemo` với dependencies thích hợp.
7. **LeadsScoring.jsx** (unused var): Đổi `(r, i)` thành `(r, _i)` để loại bỏ lỗi unused variable.
8. **AddKeyModal.jsx** (set-state-in-effect): Đã sửa nốt lỗi warning cuối cùng bằng pattern `useRef` và `useEffect` assignment.

**Kết quả:**
- `npm run lint` → **SẠCH 100% (0 Errors, 0 Warnings)**.
- `npm run build` → **Build thành công** (warnings about chunk size không ảnh hưởng).

Tất cả acceptance criteria đã đáp ứng.
