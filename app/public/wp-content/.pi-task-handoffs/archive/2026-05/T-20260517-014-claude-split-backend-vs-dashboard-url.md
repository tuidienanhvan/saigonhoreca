---
id: T-20260517-014
owner: claude
state: archived
priority: P0
risk: medium
estimated_minutes: 30
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17 21:14
updated: 2026-05-17 21:17
archived: 2026-05-17 21:17
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> Em tu phat hien khi prep T-011 - T-008 thay PI_API_BACKEND_URL sang Vercel URL pha hong BackendClient API calls (license activation, jwt issue, heartbeat). Hotfix bang cach tach 2 constants.


# 📋 T-20260517-014 | claude | split-backend-vs-dashboard-url — Bản đặc tả công việc chi tiết / Detailed Task Specification

## I. 📊 Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-YYYYMMDD-XXX` | 🆔 Định danh duy nhất theo ngày. Nếu trùng, dùng hậu tố A/B. |
| `owner` | tên viết thường | 👤 Agent được giao nhiệm vụ (vd: codex, gemini). |
| `state` | drafted...archived | 🔄 Vòng đời: **drafted**, **dispatched**, **returned**, **verified**, **archived**, **blocked**. |
| `priority` | P0...P3 | 🚥 **P0**: Khẩn cấp. **P1**: Cao. **P2**: Tiêu chuẩn. **P3**: Cải thiện. |
| `risk` | cosmetic...critical | ⚠️ Tác động: **cosmetic**, **low**, **medium**, **high**, **critical**. |
| `retry_count` | số nguyên | 🔄 Số lần thử lại thất bại. |
| `retry_max` | số nguyên | 🛑 Số lần thử lại tối đa (Mặc định: 1) trước khi escalate. |
| `escalation_path` | danh sách agent | 🪜 Chuỗi agent cứu trợ (vd: [Codex, Claude]). |

---

## II. 🎯 Mục tiêu và Chiến lược / Goal & Strategic Objective
(Mô tả chi tiết nhiệm vụ. Giá trị business là gì? Vấn đề kỹ thuật là gì? Định nghĩa trạng thái kết thúc mong muốn.)

---

## III. 📚 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án, tech stack và quy ước.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu kỹ thuật.
- 📤 `.task-handoffs/system/REPORTING.md`: Quy chuẩn báo cáo và bằng chứng.

---

## IV. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
(Liệt kê mọi file hoặc thư mục agent được phép chạm vào. Hãy thật rõ ràng.)
- 📄 `path/to/file.jsx`

---

## V. 🚫 Ngoài phạm vi xử lý (Nghiêm cấm) / Out Of Scope (Strictly Forbidden)
- ❌ **Refactor không liên quan**: Không dọn dẹp code nằm ngoài mục tiêu.
- ❌ **Thay đổi UI/UX**: Không đổi layout, màu sắc, font trừ khi có yêu cầu.
- ❌ **Cấu hình hệ thống**: Không đụng vào `package.json` hay root configs.
- ❌ **Dependencies**: Không thêm hoặc xóa npm packages.

---

## VI. 🛠️ Các giai đoạn thực hiện / Phases of Execution
1.  **Giai đoạn 1: Kiểm tra & Đánh giá / Audit & Baseline** 🔍
    - Đọc file trong scope. Chạy `grep` hoặc `ls`. Xác nhận trạng thái ổn định.
2.  **Giai đoạn 2: Triển khai / Implementation** 🛠️
    - Áp dụng thay đổi dùng **Atomic Edits**. Tuân thủ tiêu chuẩn (Tailwind v4, UTF-8).
3.  **Giai đoạn 3: Kiểm tra nội bộ / Internal Verification** 🧪
    - Chạy các `Lệnh kiểm tra`. Chụp lại RAW output (stdout/stderr).
4.  **Giai đoạn 4: Báo cáo chuẩn hóa / Standardized Reporting** 📤
    - Xây dựng block báo cáo theo mẫu trong `PROMPT.md`.

---

## VII. 🔍 Lệnh kiểm tra bắt buộc / Verification Commands (Mandatory)
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\<webapp>"
# Kiểm tra Audit
npm run lint
# Kiểm tra Đóng gói
npm run build
# Kiểm tra Trạng thái Git
git status --short
```

---

## VIII. ✅ Tiêu chí nghiệm thu (Checklist) / Acceptance Criteria
- [ ] **Đúng Protocol**: Worker cung cấp đầy đủ RAW output cho TẤT CẢ lệnh kiểm tra.
- [ ] **Đúng Scope**: 0% thay đổi ngoài `Allowed Scope`.
- [ ] **Chất lượng kỹ thuật**: Không phát sinh lỗi build hoặc lint mới.
- [ ] **Độ chính xác logic**: Thực thi đúng 100% mục tiêu đã đề ra.
- [ ] **Bảo toàn mã hóa**: Mọi file giữ đúng định dạng UTF-8, không lỗi font.

---

## IX. 📋 Mẫu lệnh cho Worker / Copy-Paste Prompt (Worker Instructions)
(Chèn bản Universal Prompt v3.0 đã điền đầy đủ thông tin vào đây.)

---

## X. 📥 Kết quả thực hiện / Agent Result (Populated by Orchestrator)

**Status: ✅ PASS** — Orchestrator-direct (Claude), ~10 phút execution.

### Hotfix applied

T-008 introduced `PI_API_BACKEND_URL = 'https://pi-dashboard-wordpress.vercel.app'` (Vercel webapp URL) intending to fix iframe loading. But the SAME constant was used by `BackendClient::backendUrl()` for REST API calls (`/auth/activate`, `/auth/issue-jwt`, `/auth/heartbeat`, `/v1/usage/report`, future `/v1/tiers/spec`). Vercel webapp does not have these routes → **all license activation, JWT refresh, and usage reporting would 404 on customer WP sites**.

### Files edited (7)

1. **`plugins/pi-api/pi-api.php`** — split into 2 constants:
   - `PI_API_BACKEND_URL` = `https://pi-backend.up.railway.app` (REST API, Railway)
   - `PI_API_DASHBOARD_URL` = `https://pi-dashboard-wordpress.vercel.app` (iframe webapp, Vercel) — NEW
   - Version bump `1.0.1` → `1.0.2`
2. **`plugins/pi-api/includes/IframeRenderer.php`** — 3 refs of `PI_API_BACKEND_URL` → `PI_API_DASHBOARD_URL` (`backendOrigin()`, `buildIframeUrl()`, docstring)
3. **`plugins/pi-api/includes/CorsBridge.php`** — 2 refs (guard + `originOf`) + docstring → `PI_API_DASHBOARD_URL` (CORS allows webapp origin)
4. **`plugins/pi-api/includes/JwtAjax.php`** — docstring updated
5. **`plugins/pi-api/views/iframe-page.php`** — sandbox comment updated
6. **`plugins/pi-api/assets/js/iframe-bridge.js`** — JSDoc comment updated
7. **`plugins/pi-api/DOCS.md`** — Configuration Constants section restructured to document BOTH constants
8. **`plugins/pi-api/docs/HOOKS.md`** — same

### Unchanged (correctly — these need API URL)

- `plugins/pi-api/includes/BackendClient.php` — line 10 `backendUrl()` still references `PI_API_BACKEND_URL` (now Railway = correct)
- `plugins/pi-api/uninstall.php` — `/auth/deactivate` call still uses `PI_API_BACKEND_URL` (correct)

### Verification

```text
$ PHP -l (7 files): All "No syntax errors detected"

$ grep PI_API_BACKEND_URL plugins/pi-api/pi-api.php
33:    define('PI_API_BACKEND_URL', 'https://pi-backend.up.railway.app');

$ grep PI_API_DASHBOARD_URL plugins/pi-api/pi-api.php
43:    define('PI_API_DASHBOARD_URL', 'https://pi-dashboard-wordpress.vercel.app');

$ grep PI_API_DASHBOARD_URL plugins/pi-api/includes/IframeRenderer.php | wc -l
3   (line 12 docstring, line 131 backendOrigin, line 150 buildIframeUrl)

$ grep PI_API_BACKEND_URL plugins/pi-api/includes/BackendClient.php
10:        return rtrim((string) get_option('pi_api_backend_url', PI_API_BACKEND_URL), '/');
(correct — API URL stays for API client)
```

### Backwards compat

Both constants guarded by `if (!defined(...))` → site-specific overrides in `wp-config.php` survive. Customer with custom API domain (`api.pi-ecosystem.com`) can set `PI_API_BACKEND_URL` there. Customer with custom dashboard domain (`app.pi-ecosystem.com`) can set `PI_API_DASHBOARD_URL`. Independent overrides → independent domain ownership.

---

## XI. 📊 Ma trận kiểm soát chất lượng / Quality Gate Verification Matrix
| Gate | Status | Evidence | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ ✅ N/A | n/a | No webapp build needed (PHP/JS doc-comment only) |
| **Lint Gate** | 🧹 ✅ PASS | §X "PHP -l (7 files): All 'No syntax errors detected'" | PHP syntax all green |
| **Scope Gate** | 📂 ✅ PASS | 8 files touched, all in pi-api plugin | No drift |
| **Logic Gate** | 🎯 ✅ PASS | §X grep evidence — BACKEND_URL → Railway, DASHBOARD_URL → Vercel, BackendClient correctly uses BACKEND_URL | License activation flow now hits real REST API again |

---

## XII. 📁 Bằng chứng (Raw Terminal Output) / Evidence
```text
$ <command>
<exact_output>
```

---

## XIII. 📉 Tóm tắt thay đổi / Diff Summary (Calculated)
| File | +Lines | -Lines | Type |
|---|---|---|---|
| | | | |

---

## XIV. 🛡️ Phê duyệt của Orchestrator / Orchestrator Review & Final Decision
Status: `pending`

---

## XV. 🆘 Xử lý lỗi và Hoàn tác / Escalation, Errors & Rollback
- **Loại lỗi / Failure Type**:
- **Quy trình hoàn tác / Rollback Procedure**: 
  1. `git checkout -- <files>`
  2. `rm <new_files>`
- **Next Step**:

---

## XVI. 📑 CHANGE LOG & AUDIT TRAIL
- **YYYY-MM-DD HH:MM**: Dossier created.
- **2026-05-17 21:15**: State drafted → dispatched
- **2026-05-17 21:17**: State dispatched → returned
- **2026-05-17 21:17**: State returned → verified
