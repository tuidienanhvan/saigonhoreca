---
id: T-20260511-009
owner: poolside
state: verified
priority: P0
risk: high
estimated_minutes: 90
parent: T-20260511-008
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-11 14:05
updated: 2026-05-11 14:05
---

# 📋 T-20260511-009-laguna-dashboard-utf8-restore — Detailed Task Specification

## 0. User Original Intent (Verbatim)
> "hiện tại rất nhiều lỗi utf8 do powershell, lỗi chữ có ?"
> "cho laguna M.1 làm luôn, đọc pi-dashboard, check r lên plan chi tiết cho laguna m1"

---

## 1. 📊 Frontmatter Fields & Risk Matrix

| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260511-009` | 🆔 Dashboard UTF-8 Encoding Restoration. |
| `owner` | poolside | 👤 Laguna M.1 — CSS/Tailwind + React deep refactor specialist. |
| `state` | drafted | 🔄 Awaiting dispatch to Laguna M.1. |
| `priority` | P0 | 🚥 CRITICAL — Tiếng Việt hiển thị lỗi trên toàn bộ Dashboard. |
| `risk` | high | ⚠️ 110 file bị ảnh hưởng, nguy cơ regression nếu sửa sai. |

---

## 2. 🎯 Goal & Strategic Objective

**Vấn đề gốc rễ (Root Cause):**
Lệnh PowerShell `Get-Content` / `Set-Content` trên Windows mặc định sử dụng encoding **UTF-16 LE BOM** thay vì **UTF-8 without BOM**. Khi các file `.jsx` / `.js` / `.css` chứa tiếng Việt (diacritics) bị đọc/ghi qua PowerShell, ký tự bị double-encode thành Mojibake:
- `Ã` thay cho `à/á/ả/ã/ạ`
- `á»` thay cho `ổ/ộ/ở/ỡ/ợ`
- `áº` thay cho `ắ/ằ/ẳ/ẵ/ặ`
- `Ä` thay cho `đ/Đ`

**Mục tiêu:**
Khôi phục 100% encoding UTF-8 without BOM cho toàn bộ **110 file** trong `pi-dashboard-webapp/src/` mà không thay đổi bất kỳ logic code nào.

**Yêu cầu kỹ thuật:**
1. Mỗi file phải được đọc dưới dạng byte stream (binary), KHÔNG dùng `Get-Content`.
2. Xác định và xử lý từng chuỗi bị corrupt bằng cách đối chiếu với bản gốc UTF-8.
3. Sau khi sửa, file phải giữ nguyên line endings (LF, không CRLF).
4. Encoding output phải là **UTF-8 without BOM** (không có byte `EF BB BF` ở đầu file).

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Anti-hallucination & Encoding Guard (§2.3).
- 🏗️ `.task-handoffs/project/PROJECT.md`: Tech stack (Vite + React 19 + Tailwind v4).
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: 4 gates — đặc biệt Gate 2 (Lint + Encoding).
- 📤 `.task-handoffs/system/REPORTING.md`: REPORT block format.
- 👤 `.task-handoffs/AGENTS/poolside.md`: Laguna M.1 capability profile.
- 📋 `.task-handoffs/system/templates/COMMANDS.md`: Mojibake detection patterns.

---

## 4. 🚧 Allowed Scope (Strict)

### 4.1 Components (53 files)
- 📂 `pi-dashboard-webapp/src/components/ai-cloud/` (3 files: Settings, TopUp, UsageBreakdown)
- 📂 `pi-dashboard-webapp/src/components/analytics/` (4 files: FunnelEditor, FunnelResults, RetentionMatrix, TopPostsTab)
- 📂 `pi-dashboard-webapp/src/components/api-keys/` (1 file: KeyRow)
- 📂 `pi-dashboard-webapp/src/components/audit-log/` (1 file: LogDetailDrawer)
- 📂 `pi-dashboard-webapp/src/components/backup/` (4 files: BackupCard, BackupTargetConfig, CreateBackupModal, RestoreModal)
- 📂 `pi-dashboard-webapp/src/components/chatbot/` (2 files: ChatbotSettings, RagConfig)
- 📂 `pi-dashboard-webapp/src/components/db-explorer/` (3 files: ExportModal, SchemaViewer, TableRows)
- 📂 `pi-dashboard-webapp/src/components/edit-post/legacy/` (2 files: ContentEditor, PublishBox)
- 📂 `pi-dashboard-webapp/src/components/editor/` (1 file: TipTapEditor)
- 📂 `pi-dashboard-webapp/src/components/email/` (4 files: CampaignModal, CampaignStats, TemplateEditor, VariablePicker)
- 📂 `pi-dashboard-webapp/src/components/forms/` (1 file: SubmissionDetail)
- 📂 `pi-dashboard-webapp/src/components/funnels/` (1 file: FunnelChart)
- 📂 `pi-dashboard-webapp/src/components/instant-indexing/` (1 file: AutoConfig)
- 📂 `pi-dashboard-webapp/src/components/layout/` (3 files: Header, Layout, Navbar)
- 📂 `pi-dashboard-webapp/src/components/leads/` (2 files: LeadTimeline, LeadTimeline.test)
- 📂 `pi-dashboard-webapp/src/components/og-image/` (1 file: OgTemplateCard)
- 📂 `pi-dashboard-webapp/src/components/performance/` (5 files: CdnConfig, CriticalCssConfig, PwaConfig, WebpQueue, WpCleanupConfig)
- 📂 `pi-dashboard-webapp/src/components/posts/` (2 files: BulkActions, PostToolbar)
- 📂 `pi-dashboard-webapp/src/components/seo-bulk-edit/` (1 file: EditableCell)
- 📂 `pi-dashboard-webapp/src/components/seo-llm-txt/` (1 file: LlmTxtEditor)
- 📂 `pi-dashboard-webapp/src/components/seo-tools/` (6 files: BreadcrumbConfig, HtmlSitemapConfig, ImageSeoConfig, PrimaryCategoryConfig, RobotsTxtEditor, TocConfig)
- 📂 `pi-dashboard-webapp/src/components/system-health/` (3 files: AlertConfigForm, HealthChart, RecentErrorsTable)
- 📂 `pi-dashboard-webapp/src/components/users-roles/` (1 file: EditUserModal)

### 4.2 Libs (1 file)
- 📄 `pi-dashboard-webapp/src/lib/notify.js`

### 4.3 Pages (46 files)
- 📂 `pi-dashboard-webapp/src/pages/ai/` (5 files)
- 📂 `pi-dashboard-webapp/src/pages/auth/` (1 file: Login)
- 📂 `pi-dashboard-webapp/src/pages/content/` (6 files)
- 📂 `pi-dashboard-webapp/src/pages/core/` (3 files: Analytics, Overview, Performance)
- 📂 `pi-dashboard-webapp/src/pages/leads/` (8 files)
- 📂 `pi-dashboard-webapp/src/pages/seo/` (11 files)
- 📂 `pi-dashboard-webapp/src/pages/system/` (17 files)

### 4.4 Total: 110 files

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Code Logic**: Không thay đổi bất kỳ logic JS/React nào. Chỉ sửa encoding.
- ❌ **Build Tools**: Không thay đổi `vite.config.js`, `package.json`, `tailwind.config.js`.
- ❌ **CSS Architecture**: Không thay đổi class names hay Tailwind utilities.
- ❌ **Ghost Refactor**: Không đổi tên biến, import order, hay formatting.
- ❌ **Store Webapp**: Không đụng vào `pi-store-webapp/`.

---

## 6. 🛠️ Phases of Execution

### Phase 1: Audit & Baseline 🔍 (10 min)
1. Đọc toàn bộ file trong scope bằng binary reader (`[System.IO.File]::ReadAllBytes`).
2. Xác nhận pattern Mojibake tồn tại.
3. Tạo backup snapshot: `git stash` hoặc note commit hash hiện tại.

### Phase 2: Bulk UTF-8 Restoration 🛠️ (60 min)
**Chiến lược:** Dùng script để:
1. Đọc file dưới dạng **raw bytes**.
2. Decode bytes dùng `System.Text.Encoding.UTF8`.
3. Ghi lại file dùng `[System.IO.File]::WriteAllText($path, $content, [System.Text.UTF8Encoding]::new($false))` — UTF-8 WITHOUT BOM.

**Script mẫu:**
```powershell
$utf8NoBom = [System.Text.UTF8Encoding]::new($false)
Get-ChildItem "pi-dashboard-webapp\src" -Recurse -Include "*.jsx","*.js","*.css" | ForEach-Object {
    $bytes = [System.IO.File]::ReadAllBytes($_.FullName)
    $content = [System.Text.Encoding]::UTF8.GetString($bytes)
    # Kiểm tra BOM và loại bỏ
    if ($content.Length -gt 0 -and $content[0] -eq [char]0xFEFF) {
        $content = $content.Substring(1)
    }
    [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
}
```

**Lưu ý quan trọng:**
- Script này KHÔNG sửa nội dung, chỉ đảm bảo encoding output là UTF-8 no BOM.
- Nếu nội dung đã bị double-encode (UTF-8 bytes đọc dưới dạng Latin-1 rồi encode lại UTF-8), cần decode 2 lần.

### Phase 3: Verification 🧪 (15 min)
1. Chạy `npm run build` — phải exit 0.
2. Chạy `npm run lint` — 0 errors.
3. Chạy Mojibake scan — 0 matches.
4. Spot check 5 file random để verify tiếng Việt hiển thị đúng.

### Phase 4: Reporting 📤 (5 min)
1. Tạo REPORT block theo `REPORTING.md`.
2. Paste raw terminal output cho mọi verification command.

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-dashboard-webapp"

# 🏗️ Gate 1: Build
npm run build

# 🧹 Gate 2: Lint + Encoding
npm run lint

# 🛡️ Mojibake Deep Scan (from COMMANDS.md)
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
Select-String -Path "src\**\*.jsx","src\**\*.js","src\**\*.css" -Pattern ($patterns -join "|") -CaseSensitive

# 📂 Gate 3: Scope
git status --short

# 🎯 Gate 4: Spot check
Get-Content "src\components\layout\Navbar.jsx" -TotalCount 30
Get-Content "src\pages\core\Overview.jsx" -TotalCount 30
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] **Encoding**: 0 file chứa Mojibake pattern (`Ã`, `á»`, `áº`, `Ä`, `Æ°`).
- [ ] **BOM**: 0 file có UTF-8 BOM header (`EF BB BF`).
- [ ] **Build**: `npm run build` exit 0.
- [ ] **Lint**: `npm run lint` — 0 new errors.
- [ ] **Scope**: `git status --short` — chỉ file trong allowed scope.
- [ ] **Content**: Tiếng Việt (aria-label, placeholder, comment) hiển thị đúng dấu.

---

## 9. 📋 Copy-Paste Prompt (Worker Instructions)

```
Bạn là Laguna M.1, Senior Level 7 Software Engineer trong Pi Ecosystem.

📋 Task: T-20260511-009 — Dashboard UTF-8 Encoding Restoration
📂 Dossier: .task-handoffs/active/T-20260511-009-laguna-dashboard-utf8-restore.md

🎯 Mục tiêu: Khôi phục encoding UTF-8 without BOM cho 110 file trong pi-dashboard-webapp/src/.
Nguyên nhân: PowerShell Set-Content đã corrupt tiếng Việt thành Mojibake.

🛡️ Bắt buộc đọc trước khi làm:
1. .task-handoffs/SKILL.md
2. .task-handoffs/project/PROJECT.md
3. Dossier T-20260511-009

⚠️ KHÔNG sửa logic code. CHỈ sửa encoding.
⚠️ KHÔNG dùng Get-Content / Set-Content. Dùng [System.IO.File]::ReadAllText/WriteAllText.
⚠️ Output phải là UTF-8 WITHOUT BOM.

📤 Reply bằng REPORT block chuẩn (xem system/REPORTING.md).
```

---

## 10. 📥 Agent Result (Populated by Orchestrator)
Status: `not-started`

### 10.1 Summary
(Chờ Laguna M.1 thực thi.)

### 10.2 Artifact Changes
- 📝 **Modified**: (chờ)
- ✨ **Created**: (chờ)
- 🗑️ **Deleted**: (chờ)

---

## 11. 📊 Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | `npm run build` exit 0. |
| **Lint Gate** | 🧹 `pending` | | `npm run lint` 0 errors + 0 Mojibake. |
| **Scope Gate** | 📂 `pending` | | Only 110 files in allowed scope. |
| **Logic Gate** | 🎯 `pending` | | Tiếng Việt hiển thị đúng. |

---

## 12. 📁 Evidence (Raw Terminal Output)
```text
(Chờ Laguna M.1 paste raw output.)
```

---

## 13. 📉 Diff Summary (Calculated)
| File | +Lines | -Lines | Type |
|---|---|---|---|
| (110 files — encoding only) | 0 | 0 | encoding-fix |

---

## 14. 🛡️ Orchestrator Review & Final Decision
Status: `pending`

### 14.1 Technical Review
### 14.2 Final Verdict (Approve / Reject)

---

## 15. 🆘 Escalation, Errors & Rollback
- **Failure Type**: Nếu encoding fix gây build fail
- **Rollback Procedure**:
  1. `git checkout -- pi-dashboard-webapp/src/`
  2. Verify: `npm run build`
- **Next Step**: Escalate to Codex cho per-file surgical fix

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-11 14:05**: Dossier created by Antigravity (Gemini Auditor). 110 files identified via deep scan.
