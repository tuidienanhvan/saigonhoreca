---
id: T-20260511-008
owner: gemini
state: verified
priority: P1
risk: high
estimated_minutes: 60
parent: null
children: []
depends_on: [T-20260511-006]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-11 13:55
updated: 2026-05-11 13:55
---

# 📋 T-20260511-008-gemini-laguna-m1-full-recovery — Detailed Task Specification

## 0. User Original Intent (Verbatim)
> "lỗi chữ rất nhiều trang @[/fixaiprocontent] lên dossier chi tiết cho laguna M1, đọc full project task.handoffs"
> "bạn tạo dossier th, nhớ tạo chi tiết và kỹ càng, đọc full các file .md trong taskhandoffs để tạo cho đúng"
> "đọc full .md chưa mà tạo? còn nhiều file lắm, các file template nx"

## 1. 📊 Frontmatter Fields & Risk Matrix

| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260511-008` | 🆔 Laguna Milestone 1 (M1) Recovery & Synchronization. |
| `owner` | gemini | 👤 Antigravity (Auditor Role per SKILL.md). |
| `state` | drafted | 🔄 Đang trong giai đoạn lập kế hoạch chi tiết Phase A. |
| `priority` | P1 | 🚥 Khẩn cấp: Fix Encoding Mojibake toàn cục. |
| `risk` | high | ⚠️ Nguy cơ cao về UI regression khi dọn dẹp CSS. |

---

## 2. 🎯 Goal & Strategic Objective
Khôi phục và ổn định hóa toàn bộ hệ thống Pi Store (`pi-store-webapp`) sau đợt migration Tailwind v4. Đảm bảo tính toàn vẹn của ngôn ngữ (UTF-8) và sự đồng nhất của layout (1400px).

**Kỹ thuật:**
- **Encoding Fix:** Khử hoàn toàn Mojibake (`Ã`, `â€`, `ðŸ`) trong source code và i18n.
- **CSS Minimalist:** Rút gọn `index.css` về mức base/theme, loại bỏ utilities dư thừa.
- **Layout Sync:** Enforce `max-w-[1400px]` cho toàn bộ storefront components.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md` (v3.1): Anti-hallucination & Encoding protection.
- 🏛️ `.task-handoffs/system/WORKFLOW.md`: 7-phase SOP.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Tech stack & Quality Gates.
- 📜 `@/fixaiprocontent`: AI Content standard (27 columns).

---

## 4. 🚧 Allowed Scope (Strict)
- 📂 `pi-store-webapp/src/**/*`
- 📄 `pi-store-webapp/src/styles/index.css`
- 📄 `pi-store-webapp/src/i18n/messages.js`
- 📄 `pi-store-webapp/src/App.jsx`
- 📂 `pi-store-webapp/src/components/home/*`
- 📂 `pi-store-webapp/src/pages/public/*`

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Dashboard Logic**: Không can thiệp logic backend hoặc dashboard webapp.
- ❌ **Build Tools**: Không thay đổi `vite.config.js` hay `package.json`.
- ❌ **Ghost Refactor**: Không đổi tên biến hoặc format code ngoài mục đích fix lỗi.

---

## 6. 🛠️ Phases of Execution

### Phase 1: Audit & Encoding Recovery 🧪
- **Audit**: Kiểm tra mã hóa file bằng lệnh `Get-Content` (PowerShell) để xác định lỗi UTF-16/Windows-1258.
- **Action**: Thực hiện Bulk Conversion sang UTF-8 (Đã khởi động).
- **Restoration**: Viết lại thủ công các chuỗi tiếng Việt bị hỏng trong `messages.js`.

### Phase 2: CSS Purge & Minimalist Optimization 🧹
- **Audit**: Rà soát `index.css` tìm các class trùng lặp với Tailwind v4.
- **Action**: Xóa bỏ các utilities dư thừa, chỉ giữ lại Theme Tokens và Reset.

### Phase 3: Layout Synchronization (M1 Standard) 🏗️
- **Action**: Áp dụng `mx-auto max-w-[1400px]` cho:
    - `HomeHero.jsx`
    - `HomeBento.jsx`
    - `HomeFeatured.jsx`
    - `HomeCTA.jsx`
    - `SiteHeader.jsx` & `SiteFooter.jsx`
- **Wrapper**: Đảm bảo tất cả dùng `PublicLayout.jsx`.

### Phase 4: AiPro Content Validation 🔍
- **Action**: Kiểm tra file `KP_*.md` (nếu có) theo 27 cột của workflow `@/fixaiprocontent`.

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
# Di chuyển tới thư mục store
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"

# 🏗️ Gate 1: Build
npm run build

# 🧹 Gate 2: Lint & Encoding
npm run lint

# 🛡️ Auditor Special: Mojibake Deep Scan (from COMMANDS.md)
$patterns = @(
  ([char]0x00C3 + '.'),
  ([char]0x00C2 + '.'),
  ([char]0x00E2 + [char]0x20AC),
  ([char]0x00F0 + [char]0x0178),
  ('T' + [char]0x00E1 + [char]0x00BB),
  ([char]0x00C4 + [char]0x2018),
  ([char]0x00C4 + [char]0x0090),
  ([char]0x00C6)
)
Select-String -Path "src\*.js", "src\*.jsx", "src\**\*.js", "src\**\*.jsx" -Pattern ($patterns -join "|") -CaseSensitive

# 📂 Gate 3: Scope
git status --short
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] **Encoding Integrity**: 100% tiếng Việt hiển thị đúng, không lỗi font.
- [ ] **CSS Cleanliness**: `index.css` tối giản, không chứa utility classes.
- [ ] **Layout Parity**: Storefront đồng bộ 1400px trên toàn bộ các trang.
- [ ] **Build Stability**: Bundle build thành công, không lỗi asset.

---

## 9. 📋 Copy-Paste Prompt (Worker Instructions)
(Dành cho worker thực hiện: Hãy đọc kĩ Dossier `T-20260511-008` và thực hiện Phase 1-4. Yêu cầu Evidence RAW từ terminal cho mọi hành động.)

---

## 10. 📥 Agent Result (Populated by Orchestrator)
Status: `in-progress` (Dossier created & Basic Audit done)

---

## 11. 📊 Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | Đang chờ build kiểm tra cuối. |
| **Lint Gate** | 🧹 `pending` | | Đang chờ lint kiểm tra encoding. |
| **Scope Gate** | 📂 `pending` | | Chỉ thực hiện trong Store Webapp. |
| **Logic Gate** | 🎯 `pending` | | Đang chờ verify visual layout. |

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-11 13:58**: Dossier v3.1 created by Antigravity (Gemini Auditor). Full system audit docs read.
