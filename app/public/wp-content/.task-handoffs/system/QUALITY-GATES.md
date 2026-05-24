# 🏆 Technical Quality Gates — v4.3

This document defines the mandatory 4 Gates a task must clear before it can be marked as `verified`.

> **Note (v4.3)**: Consolidated from 5 gates to 4. Encoding check is now embedded in Lint Gate.

---

## I. 🏗️ Gate 1: The Build Gate (Production Readiness)
The code must be able to compile into a production-ready bundle.

- **Check**: `npm run build` (relevant webapp)
- **Criteria**:
# 🏆 Cổng kiểm soát chất lượng / Technical Quality Gates — v3.1

Mọi task PHẢI vượt qua 4 cổng này trước khi Orchestrator set `state: verified`.

---

## I. 🏗️ Cổng 1: Đóng gói / Build Gate
- **Lệnh / Command**: `npm run build` (trong webapp tương ứng).
- **Tiêu chuẩn / Criteria**:
  - Exit code PHẢI là 0.
  - Không có lỗi nghiêm trọng trong quá trình minify/bundle.
  - Kích thước bundle không tăng đột biến (>10%) trừ khi có tính năng mới.

---

## II. 🧹 Cổng 2: Cú pháp / Lint Gate
- **Lệnh / Command**: `npm run lint`.
- **Tiêu chuẩn / Criteria**:
  - 0 lỗi (Zero Errors).
  - Các cảnh báo (Warnings) phải được giải trình rõ trong báo cáo.
  - Tuân thủ quy tắc Prettier/ESLint của dự án.

---

## III. 📂 Cổng 3: Phạm vi / Scope Gate
- **Lệnh / Command**: `git status --short`.
- **Tiêu chuẩn / Criteria**:
  - Tuyệt đối không có file nào bị chỉnh sửa nằm ngoài `Allowed Scope`.
  - Không có file rác, file tạm, hoặc artifact dư thừa (`.log`, `.tmp`).
  - Không thay đổi các file cấu hình hệ thống (`package.json`, `vite.config.js`) nếu không được yêu cầu.

---

## IV. 🎯 Cổng 4: Logic và Hiển thị / Logic & UI Gate
- **Lệnh / Command**: Manual review / Browser MCP / Unit test.
- **Tiêu chuẩn / Criteria**:
  - Đáp ứng 100% ý định gốc của người dùng (User Intent).
  - Không làm hỏng các tính năng cũ (No Regressions).
  - Khôi phục hoàn hảo font chữ Tiếng Việt (Zero Mojibake).
  - UI/UX mượt mà, đúng thiết kế (nếu có).

---

## VI. 🛡️ Ghi chú quan trọng / Critical Notice
Orchestrator có quyền từ chối (Reject) bất kỳ task nào nếu Evidence không đầy đủ hoặc có dấu hiệu "lách luật" qua các cổng kiểm soát.

---

## V. 🛡️ Enforcement Protocol
- **Fail**: If any gate returns a hard error, the task is marked `blocked`.
- **Warning**: If a gate returns a minor warning, the Orchestrator may accept if documented in `Remaining Warnings`.
- **Verification**: The Orchestrator MUST populate the `Quality Gate Verification Matrix` in the dossier for every task.
- **Evidence**: Raw terminal output MUST be pasted — paraphrased reports are rejected.

---

## VIII. 📜 Changelog
- **v3.1** (2026-05-10): Consolidated 5 gates → 4 (Encoding merged into Lint). Aligned with WORKFLOW.md.
- **v2.0** (2026-05): Initial 5-gate version.
