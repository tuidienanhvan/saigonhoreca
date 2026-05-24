---
id: T-20260506-013
owner: gemini
state: drafted
priority: P1
risk: medium
estimated_minutes: 60
parent: null
children: [T-20260506-013-1, T-20260506-013-2, T-20260506-013-3]
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [codex, claude]
created: 2026-05-06 15:30
updated: 2026-05-06 15:30
---

# T-20260506-013-gemini-audit-antigravity-work — Audit Antigravity's T-002 + T-003 Work

## Goal

Antigravity claims 2 task done (T-002 backend restructuring, T-003 auto-auth workflow) but DID NOT create dossier files — protocol violation. Gemini must:

1. Audit actual code changes vs Antigravity's chat claims
2. Verify nothing is broken (load, auth flow, DB schema)
3. Generate retroactive dossiers T-20260506-002 + T-20260506-003 with raw git evidence
4. Report any drift, missing wiring, or fake claims

This is a **broad cross-file audit** — Gemini's specialty.

## Scope (Read-only audit + 2 dossier markdown files)

**Read-only audit paths:**
- `plugins/pi-api/includes/api/endpoints/` (8 subfolders + class-api-controller.php)
- `plugins/pi-api/pi-api.php`
- `plugins/pi-api/includes/AuthManager.php`
- `plugins/pi-api/includes/BackendClient.php`
- `pi-backend/app/shared/auth/` (models.py, schemas.py, service.py, router.py)
- `pi-backend/scripts/seed_pi_users.py`
- `pi-dashboard-webapp/src/api/client.js`
- `pi-dashboard-webapp/src/store/authStore.js`
- `pi-dashboard-webapp/src/pages/auth/Login.jsx`

**Write paths (only these):**
- `.task-handoffs/active/T-20260506-002-antigravity-backend-restructuring.md` (CREATE)
- `.task-handoffs/active/T-20260506-003-antigravity-auto-auth-workflow.md` (CREATE)

## Sub-tasks

### Sub-task 1: Backend Restructuring Audit (T-013-1)

Verify all 8 subfolders are properly loaded by `class-api-controller.php`:

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\plugins\pi-api"
ls includes/api/endpoints/
grep -rn "require\|include\|autoload" includes/api/endpoints/class-api-controller.php
grep -rn "register_rest_route" includes/api/endpoints/ | wc -l
```

Expected: every endpoint file has at least one `register_rest_route` call AND is included by controller.

**Red flags to report:**
- File trong subfolder không được load
- 2 endpoint cùng route path (collision)
- File cũ ở root chưa cleanup

### Sub-task 2: Auto-Auth Flow Trace (T-013-2)

Trace login flow end-to-end:

```
Login.jsx (FE) → authStore.js → client.js → BackendClient.php → /auth/login → service.py → DB
                                              ↓
                                         AuthManager.php auto-mint AppPass
                                              ↓
                                         backend stores app_pass column
```

For each step, verify:

```powershell
# Backend
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-backend"
grep -n "app_pass" app/shared/auth/models.py
grep -n "app_pass" app/shared/auth/schemas.py
grep -n "app_pass" app/shared/auth/service.py
grep -n "app_pass" app/shared/auth/router.py
cat scripts/seed_pi_users.py

# Plugin
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\plugins\pi-api"
grep -rn "app_pass\|generate_application_password\|wp_create_application_password" includes/

# Frontend
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-dashboard-webapp"
grep -rn "app_pass\|appPassword" src/api/ src/store/ src/pages/auth/
```

**Red flags:**
- Race condition (2 lần mint AppPass, không idempotent)
- Token leak (app_pass vào URL, console.log, localStorage không encrypt)
- Backend trả app_pass plaintext (phải hash hoặc just-in-time)
- Frontend gửi password thật về backend (phải dùng app_pass sau lần đầu)

### Sub-task 3: Generate Dossiers (T-013-3)

Tạo 2 file dossier theo format `system/templates/TASK.md`:

**File 1:** `.task-handoffs/active/T-20260506-002-antigravity-backend-restructuring.md`
- frontmatter: state=`returned` (vì Antigravity claim done), owner=`antigravity`
- Goal: tóm tắt Antigravity claim
- Allowed Scope: 8 subfolder + class-api-controller.php
- Agent Result: paste git diff stats (`git diff --stat HEAD~10 HEAD -- plugins/pi-api/includes/api/`)
- Evidence: paste output Sub-task 1 commands
- Codex Review: state=`pending` (Claude review sau)

**File 2:** `.task-handoffs/active/T-20260506-003-antigravity-auto-auth-workflow.md`
- frontmatter: state=`returned`, owner=`antigravity`
- Goal: tóm tắt auto-auth flow
- Allowed Scope: 11 file (4 Python + 3 PHP + 3 JS + 1 JSX)
- Agent Result: list từng file với +/- lines từ git
- Evidence: paste output Sub-task 2 commands
- Codex Review: state=`pending`

## Out of Scope (CẤM)

- Không sửa code production (audit-only, chỉ tạo 2 file markdown)
- Không chạy npm install / pip install
- Không touch DB
- Không tạo migration mới
- Không touch file ngoài 11 paths trên

## Verification Commands

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content"
git status --short
git log --oneline -20
git diff --stat HEAD~10 HEAD -- plugins/pi-api/ pi-backend/ pi-dashboard-webapp/
ls .task-handoffs/active/T-20260506-002-*.md .task-handoffs/active/T-20260506-003-*.md
```

## Acceptance Criteria

- [ ] Sub-task 1 audit complete: tất cả 8 subfolder load OK, không collision
- [ ] Sub-task 2 audit complete: auth flow trace đủ 6 step, không red flag
- [ ] Sub-task 3: 2 file dossier tạo đúng template, có raw git evidence
- [ ] Không sửa file ngoài 2 dossier markdown
- [ ] Báo cáo theo REPORT BLOCK format trong `system/REPORTING.md`

## Copy-Paste Prompt for Gemini

```
Bạn là Gemini Antigravity Auditor đang vận hành trong Pi Ecosystem multi-agent system.

Đọc dossier:
C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\.task-handoffs\active\T-20260506-013-gemini-audit-antigravity-work.md

NHIỆM VỤ: Audit work của Antigravity (T-002 backend restructure + T-003 auto-auth) — Antigravity đã code nhưng SKIP dossier. Bạn cần verify thật code đã làm gì và tạo 2 dossier hồi tố.

LUẬT BẮT BUỘC:
1. AUDIT-ONLY trên 11 file production paths (xem ## Scope) — KHÔNG sửa code production.
2. CHỈ ĐƯỢC tạo 2 file markdown:
   - .task-handoffs/active/T-20260506-002-antigravity-backend-restructuring.md
   - .task-handoffs/active/T-20260506-003-antigravity-auto-auth-workflow.md
3. Mọi claim phải có RAW COMMAND OUTPUT (paste vào Evidence section).
4. Reply theo REPORT BLOCK format:
   === T-20260506-013 REPORT ===
   STATUS: pass | pass-warn | fail
   SUMMARY: ...
   FILES_CREATED: 2 dossiers
   AUDIT FINDINGS:
   - Backend Restructure: [findings + red flags]
   - Auto-Auth Flow: [findings + red flags]
   VERIFY EVIDENCE: [raw output]
   NOTES: [any drift / missing wiring / fake claims]
   === END REPORT ===

Workspace: C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content
Estimated time: 60 phút
```

## Agent Result

Status: `not-started`

## Evidence

Pending.

## Diff Summary

| File | Type |
|---|---|
| `T-20260506-002-...md` | created |
| `T-20260506-003-...md` | created |

## Codex Review

Status: `pending`

## Escalation

- Failure: 
- Evidence: 
- Recovery:
