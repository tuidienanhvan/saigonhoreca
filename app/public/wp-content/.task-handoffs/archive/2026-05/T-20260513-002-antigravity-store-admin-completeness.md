---
id: T-20260513-002
owner: antigravity
state: archived
priority: P1
risk: medium
estimated_minutes: 345
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-13 13:30
updated: 2026-05-13 15:06
archived: 2026-05-13 15:06
---

# T-20260513-002 — Pi Store Admin: Complete coverage of pi-backend admin surface

## 0. User Original Intent (Phase 0, verbatim — 2026-05-13)

> "bạn check coi từng tab đã đủ nghiệp vụ đủ chức năng cho admin chưa? phải tương ứng với pi-backend nha
> lên plan dossier bằng task-handoffs trong wp-content r cho gemini làm"

User wants pi-store-webapp (admin UI under `/admin/*`) to fully cover every admin-facing endpoint in pi-backend `/v1/admin/*`. Audit done by Claude on 2026-05-13 found 4 gaps where backend capability exists but admin UI cannot reach it.

---

## 1. Goal — Strategic Objective

Make `pi-store-webapp` admin section a **1:1 superset** of `pi-backend/app/admin/routers/*`. Every backend admin endpoint must be reachable by a real admin action in the UI — no orphaned APIs, no dead admin pages, **no stub/fake UI that pretends to call backend**.

**Outcome:**
- Admin can manage **all** licenses (including manual token credit/debit), **all** users, **all** cron jobs from UI without ever opening curl/postman.
- All existing admin page files are routed (no dead .jsx files in `admin/pages/`).
- Every `api.admin.*` method in `src/lib/api-client.js` is consumed by at least one component.
- **NO stub modals** that show "Form đang được cập nhật..." instead of working form.
- **NO fake actions** that sleep + alert success without hitting backend.

---

## 2. Audit Result — Current Coverage (2026-05-13)

### 2.1 ✅ Endpoints fully covered

| Backend | Page |
|---|---|
| `GET /admin/overview` | `admin/pages/AdminOverviewPage.jsx` |
| `GET /admin/usage` | `admin/pages/ai/usage/AdminUsagePage.jsx` |
| `GET /admin/revenue` | `admin/pages/finance/AdminRevenuePage.jsx` |
| `* /admin/packages*` (incl. routing-mode UI from T-20260513-001) | `admin/pages/finance/AdminPackagesPage.jsx` |
| `* /admin/providers*` (CRUD + test) | `admin/pages/ai/providers/AdminProvidersPage.jsx` |
| `* /admin/keys*` (CRUD + bulk + allocate + revoke + release-all) | `admin/pages/license/AdminKeysPage.jsx` |
| `* /admin/releases*` | `admin/pages/system/AdminReleasesPage.jsx` |
| `GET /admin/audit-log` | `admin/pages/system/AdminAuditLogPage.jsx` |
| `GET/PUT /admin/settings` | `admin/pages/system/AdminSettingsPage.jsx` |

### 2.2 ❌ Gaps to fix in this task

#### Gap A — Adjust license tokens (manual credit/debit)
- Backend: `POST /v1/admin/licenses/{id}/tokens` body `{delta, note}`
- API client: `api.admin.adjustTokens(licenseId, delta, note)` already in `src/lib/api-client.js:379`
- UI: **none** — no button anywhere in admin pages.
- Customer-support use case: refund tokens after bug, gift bonus, manual deduction.

#### Gap B — License package assignment & period reset
- Backend: `GET /v1/admin/licenses/{id}/package`, `POST /v1/admin/licenses/{id}/package`, `POST /v1/admin/licenses/{id}/package/reset-period`
- API client: `api.admin.getLicensePackage`, `assignPackage`, `resetLicensePeriod` exist (api-client.js:437-441)
- UI: **none** — admin cannot change a license's package after creation.
- Onboarding use case: customer upgrades free→pro, admin assigns package + sees auto-allocated keys (T-20260513-001 hook).

#### Gap C — Users management (page exists, no route)
- Backend: `GET /v1/admin/users`, `GET /v1/admin/users/{id}`, `PATCH /v1/admin/users/{id}/profile`
- API client: `api.admin.users`, `getUser`, `updateUserProfile` exist (api-client.js:339-345)
- Pages exist as files: `admin/pages/system/AdminUsersPage.jsx`, `admin/pages/system/AdminUserProfilePage.jsx`
- **NOT wired** in `src/App.jsx` routes — sidebar has no link, no path matches.
- Compliance use case: admin needs to look up registered users, edit profile, lock account.

#### Gap D — Cron jobs (no page at all)
- Backend: `GET /v1/admin/cron` (list jobs + last_run + status), `POST /v1/admin/cron/{slug}/run` (manual trigger)
- API client: `api.admin.cronStatus`, `runCron` exist (api-client.js:453-455)
- UI: **none** — admin cannot see if `token_reset.daily_check` last ran successfully, or trigger `maintenance.cleanup_usage_logs` on demand.
- Ops use case: cron failed silently → admin needs visibility + manual re-run.

### 2.3 🔴 STUB / FAKE pages found (deep audit pass 2026-05-13)

These files exist and are referenced by working pages, but have **vỏ chứ không có nghiệp vụ** — render UI but don't call backend correctly.

#### Stub 1 — `CreateLicenseModal.jsx` (31 lines, only shell)
- Location: `admin/pages/license/CreateLicenseModal.jsx`
- Referenced by: `AdminLicensesPage.jsx:86` — primary "+ Tạo license" button opens this modal
- Current content: header + comment `{/* Placeholder for form fields */}` + body `"Form n?i dung dang du?c c?p nh?t..."` + "Tạo ngay" button calling `onCreated?.()` with **no payload, no API call**.
- Required: full form (email, customer_name, plugin, tier, max_sites, expires_at, send_email checkbox) → submit `api.admin.createLicense(payload)` → close + refetch.
- Backend ready: `POST /v1/admin/licenses` accepts `AdminLicenseCreate` schema in `pi-backend/app/admin/schemas.py`.

#### Stub 2 — `LicenseDetailModal.jsx` (33 lines, only shell)
- Location: `admin/pages/license/LicenseDetailModal.jsx`
- Referenced by: `AdminLicensesPage.jsx` row click (to be wired) — currently NOT used because of stub.
- Current content: header + hardcoded `<Badge tone="success">ACTIVE</Badge>` + body `"Thông tin chi tiết của License đang được tải..."` + "Lưu thay đổi" button calling `onChanged?.()` with **no payload, no API call**.
- Required: fetch license by id (`api.admin.licenses({id})` or new `getLicense` if available), tabs: General / Package / Keys / Usage history, real status badge from `license.status`, editable fields → `api.admin.updateLicense(id, payload)`.

#### Stub 3 — `handleTestKey` in `AdminKeysPage.jsx` (FAKE alert, no backend call)
- Location: `admin/pages/license/AdminKeysPage.jsx:124-136`
- Code:
  ```jsx
  const handleTestKey = async (k) => {
    setTestingKeyId(k.id);
    try {
      await new Promise(r => setTimeout(r, 600));
      alert(`Test giả lập thành công! (Endpoint GET /v1/admin/keys/${k.id}/test đang chưa có).`);
      load();
    } catch (e) { ... }
  };
  ```
- Admin clicks "Test" → sleep 600ms → fake success alert → does NOT hit backend.
- Two options:
  - **(a) Backend adds endpoint** `POST /v1/admin/keys/{key_id}/test` — performs a minimal completion call with that specific key, returns `{ok, latency_ms, error}`. Requires backend change → OUT OF SCOPE for this task.
  - **(b) Frontend removes the Test button** — keep keys-page clean until backend is ready.
- **For this task: pick (b)**. Remove the button + `handleTestKey` + `testingKeyId` state. Log gap "test-key endpoint missing" into `## 12. Out-of-scope Findings` so a future backend task picks it up.

#### Stub 4 — Provider adapter dropdown advertises "TODO" options
- Location: `admin/pages/ai/providers/AdminProvidersPage.jsx:318-319`
- Code:
  ```jsx
  { label: "anthropic (TODO)", value: "anthropic" },
  { label: "gemini (TODO)", value: "gemini" },
  ```
- Admin can select these but backend `app/pi_ai_cloud/services/completion.py` only registers `openai_compat` adapter (`_ADAPTERS = {"openai_compat": OpenAICompatAdapter()}`). Selecting anthropic/gemini = creating provider that will silently fail at runtime.
- Fix: remove the two "(TODO)" options from the dropdown until corresponding adapter classes exist in pi-backend. Log "anthropic + gemini adapters missing" into out-of-scope findings.

---

## 3. Required Reading (Context)

Mandatory:
- `.task-handoffs/SKILL.md` v3.1 — operational protocol, Phase 0+E
- `.task-handoffs/project/PROJECT.md` — workspace context

Source code:
- `pi-backend/app/admin/routers/{licenses,packages,users,cron}.py` — see exact request/response shape
- `pi-backend/app/admin/schemas.py` — `AdminLicenseItem`, `AdminUserItem`, `AdminAuditAction`
- `pi-store-webapp/src/lib/api-client.js` lines 320-460 — existing api wrappers
- `pi-store-webapp/admin/pages/license/AdminLicensesPage.jsx` — pattern to follow for modals
- `pi-store-webapp/admin/pages/license/LicenseDetailModal.jsx` — existing modal pattern
- `pi-store-webapp/admin/pages/system/AdminUsersPage.jsx`, `AdminUserProfilePage.jsx` — existing files to wire
- `pi-store-webapp/admin/layout/AdminLayout.jsx` — sidebar nav definition

---

## 4. Allowed Scope (Strict)

### 4.1 pi-store-webapp

**MODIFY:**
- `src/App.jsx` — add 3 new routes (`/admin/users`, `/admin/users/:id`, `/admin/cron`)
- `admin/layout/AdminLayout.jsx` — sidebar links for Users + Cron (under "System" section)
- `admin/pages/license/AdminLicensesPage.jsx` — wire row actions: "Adjust tokens", "Change package", "View detail" (open detail modal). Wire "+ Tạo license" to open functional CreateLicenseModal.
- `admin/pages/license/AdminKeysPage.jsx` — REMOVE `handleTestKey` stub + the Test button (Stub 3)
- `admin/pages/ai/providers/AdminProvidersPage.jsx` — REMOVE "anthropic (TODO)" + "gemini (TODO)" dropdown options (Stub 4)

**REWRITE (current files are stubs — rewrite full functionality):**
- `admin/pages/license/CreateLicenseModal.jsx` — REPLACE 31-line shell with full form (email, customer_name, plugin, tier, max_sites, expires_at, send_email) → `api.admin.createLicense`. Pattern: follow `AdminPackagesPage.jsx PackageModal` modal style.
- `admin/pages/license/LicenseDetailModal.jsx` — REPLACE 33-line shell with real detail view. Tabs: **General** (read-only id, key, email, plugin, tier, status + Edit button calls `api.admin.updateLicense`), **Package** (current package + change-package button), **Keys** (allocated keys for this license, link to AdminKeysPage filtered), **Usage** (last 30d via `api.admin.usage?license_id={id}`).

**CREATE (new files):**
- `admin/pages/license/AdjustTokensModal.jsx` — form: `delta` (number, +/-), `note` (required string), submit → `api.admin.adjustTokens`
- `admin/pages/license/AssignPackageModal.jsx` — select package + expires_at, submit → `api.admin.assignPackage`. Show "auto-allocate N keys" hint if package.routing_mode in {dedicated, hybrid}.
- `admin/pages/system/AdminCronPage.jsx` — table of cron jobs (slug, last_run_at, last_status, schedule_cron, manual run button)

**VERIFY (already exist, ensure wired & functional):**
- `admin/pages/system/AdminUsersPage.jsx` — table view + filter + open profile detail (already loads via `api.admin.users`, just needs route)
- `admin/pages/system/AdminUserProfilePage.jsx` — edit form for name, role, status (already exists, just needs route)

### 4.2 OUT OF SCOPE (DO NOT TOUCH)

- ❌ pi-backend Python code — already done in T-20260513-001
- ❌ Routing UI in AdminPackagesPage (done in T-20260513-001)
- ❌ pi-dashboard-webapp (separate task domain)
- ❌ Public pages (`pages/public/*`)
- ❌ User-facing pages (`pages/user/*`, `admin/pages/finance/BillingPage.jsx`)

---

## 5. Design Notes

### 5.1 Adjust Tokens Modal (Gap A)
Trigger: row action in `AdminLicensesPage` table.
Form fields:
- `delta` — number input, allow negative. Helper text: "+ to credit, − to deduct"
- `note` — required textarea (audit trail). Max 500 chars.
- Show current `quota_used / quota_limit` of the license at top for context.

On submit: call `api.admin.adjustTokens(id, delta, note)`. On 200: show toast + refetch licenses list. On 4xx: show error inline.

### 5.2 Assign Package Modal (Gap B)
Trigger: row action in `AdminLicensesPage` or button inside `LicenseDetailModal`.
Form fields:
- `package_slug` — `<Select>` populated from `api.admin.packages()` (filter `is_active`)
- `expires_at` — date picker, optional
- Live preview: when user picks package, show its routing config (mode, allowed_tiers, dedicated_key_count) so admin understands consequences.

On submit: `api.admin.assignPackage(id, payload)`. Show success with allocated key count.

### 5.3 Users Pages (Gap C)
- `AdminUsersPage` — paginated table: id, email, name, role, sites_count, last_login, status. Filter: q (email search), role, status.
- `AdminUserProfilePage` route `/admin/users/:id` — load via `api.admin.getUser(id)`, show edit form for `name`, `role`, `status` + read-only license list owned by this user.

Wire in `App.jsx`:
```jsx
<Route path="users" element={<AdminUsersPage />} />
<Route path="users/:id" element={<AdminUserProfilePage />} />
```

Add sidebar link in `AdminLayout.jsx` under "SYSTEM" section, icon `Users` from lucide-react.

### 5.4 Cron Page (Gap D)
- New file `admin/pages/system/AdminCronPage.jsx`.
- Table columns: `slug`, `schedule_cron` (e.g. `0 0 * * *`), `last_run_at`, `last_status` (✅/❌), `last_error` (truncated, hover full), `Action` (▶ Run now button).
- Auto-refresh every 10s while page is open.
- Wire route `/admin/cron`, sidebar link with `Clock` icon.

---

## 6. Verification Commands

```bash
cd pi-store-webapp
npx vite build
# Expect: ✓ built in <2s, no errors

# Manual smoke test (admin logged in):
#   Navigate /admin/licenses → click row → "Adjust tokens" → submit +1000 → AiUsage row appears in audit log
#   /admin/licenses → row → "Change package" → assign 'max' → see 3 dedicated keys allocated in /admin/keys
#   /admin/users → list renders 200 OK, click user → edit → save profile
#   /admin/cron → list renders, click "Run now" on any cron → toast success + last_run_at updates

# Audit no orphaned api.admin.* methods
grep -oE "api\.admin\.[a-zA-Z]+" admin/pages -r | awk -F"api.admin." '{print $2}' | sort -u > /tmp/used.txt
grep -oE "^\s+[a-zA-Z]+:" src/lib/api-client.js | grep -v "^\s*//" | awk -F":" '{print $1}' | sed 's/^\s*//' | sort -u > /tmp/defined.txt
diff /tmp/defined.txt /tmp/used.txt
# Expect: empty diff (every defined method has at least one consumer)
```

---

## 7. Acceptance Criteria

### Build & routing
- [ ] `npx vite build` exits 0, no warnings about missing imports or unused exports for the new pages/modals.
- [ ] Routes added: `/admin/users`, `/admin/users/:id`, `/admin/cron` — all return 200 when navigated to.
- [ ] Sidebar in `AdminLayout.jsx` has links: Users (System group), Cron (System group).

### Gap fixes (new functionality)
- [ ] `AdminLicensesPage` has row action **Adjust tokens** opening `AdjustTokensModal` — submit triggers `POST /v1/admin/licenses/{id}/tokens`.
- [ ] `AdminLicensesPage` (or `LicenseDetailModal`) has **Change package** action opening `AssignPackageModal` — submit triggers `POST /v1/admin/licenses/{id}/package`.
- [ ] `AdminCronPage` lists cron jobs from `GET /v1/admin/cron`, "Run now" button triggers `POST /v1/admin/cron/{slug}/run`, last_run_at refreshes.
- [ ] `AdminUsersPage` lists users from `GET /v1/admin/users` with filter `q`/`role`/`status`.
- [ ] `AdminUserProfilePage` at `/admin/users/:id` loads user via `getUser`, save form triggers `updateUserProfile`.

### Stub elimination (deep audit findings)
- [ ] `CreateLicenseModal` has full form (email, customer_name, plugin, tier, max_sites, expires_at, send_email) — submit calls `api.admin.createLicense(payload)`, NOT just `onCreated?.()`.
- [ ] `LicenseDetailModal` fetches real license data via API (not hardcoded ACTIVE badge), shows tabs (General/Package/Keys/Usage), edit triggers real `updateLicense`.
- [ ] `AdminKeysPage` no longer has fake `handleTestKey` button (or the button is replaced with a real backend call — only if a future task adds the endpoint).
- [ ] `AdminProvidersPage` dropdown does NOT list "anthropic (TODO)" or "gemini (TODO)" until backend supports them.
- [ ] `grep -rnE "TODO|placeholder for|đang chưa có|chua co|cập nhật\.\.\.|t.*p|sim" admin/pages` returns 0 matches related to fake/stub UI (placeholder= attributes on inputs are OK).
- [ ] `grep -rnE "alert\([^)]*(giả lập|simulated|fake|chưa có)[^)]*\)" admin/pages` returns 0 matches.

### Code quality
- [ ] Encoding: Vietnamese diacritics in copy strings are intact (UTF-8, no `Ã`, no `â€`).
- [ ] Component code uses semantic Tailwind tokens (`border-base-border`, `bg-base-200`, `text-base-content`) — NO hardcoded `border-white`, `border-gray-*`, hex colors.
- [ ] No file touched outside §4 Allowed Scope (`git status --short` audit).

### Coverage proof
- [ ] Every `api.admin.*` method in `src/lib/api-client.js` has at least one consumer in `admin/pages/` (validated by grep audit, see §6).

---

## 8. Risk & Rollback

**Risk: medium** — admin-only UI, doesn't touch customer-facing flows. But Adjust Tokens path moves Pi tokens → has financial implication. Use audit log for traceability.

**Mitigation:**
- Every modal submit shows confirm dialog with summary before firing API.
- Adjust-tokens `note` field is **required** to force admin to leave reason in audit log.

**Rollback** (if regression):
```bash
cd pi-store-webapp
git revert <commit-sha>
npx vite build && deploy
# Backend untouched — endpoints continue serving via curl/postman.
```

---

## 9. Out-of-scope Findings (log only, don't fix)

If during work Antigravity notices:
- API method `api.admin.*` defined but no UI uses it — log in dossier `## 12. Out-of-scope Findings`.
- Sidebar link broken (404 on click) — log.
- Mojibake in existing pages (`â€`, `Ã`) — log; T-20260512-005-1 owns that scope.

---

## 10. Worker Self-Check (mandatory before starting)

Antigravity:
- ✅ Capability: React + Tailwind v4 + modal patterns, 1M context, can read entire admin/pages tree
- ✅ Context: ~25 files to read + 7 new files to create — fits comfortably in 1M
- ⚠️ Risk: medium financial path (token adjust) — must include confirm dialog + required note
- Effort: 4h realistic for senior frontend agent

If self-check fails (e.g., capability mismatch), set `state: rejected` with reason in `## Escalation` and STOP. Do NOT half-finish.

---

## 11. Phase Sequence

```
Phase A (audit + dossier read)            → 15min
Phase B (Users + UserProfile wiring)      → 30min (lowest risk, files exist)
Phase C (Cron page)                       → 45min (new file, simple table)
Phase D (Adjust Tokens modal)             → 45min (financial path, careful)
Phase E (Assign Package modal)            → 45min (depends on packages list)
Phase F (Rewrite CreateLicenseModal)      → 45min (replace 31-line shell w/ full form)
Phase G (Rewrite LicenseDetailModal)      → 60min (4 tabs, fetch real data)
Phase H (Remove stub Test button + TODO   → 15min (small surgical edits)
         dropdown options)
Phase I (Sidebar nav + routes)            → 15min
Phase J (Verification + build + grep)     → 30min
───── User accept gate ─────
Phase K (Archive task via archive-task.sh)
```

Total: ~5h45min realistic. Original estimate was 4h before deep stub audit — bumped because of Stubs 1+2 needing full rewrite, not just wiring.

---

## 12. Prompt Block (ready-to-dispatch to Antigravity)

```text
You are executing T-20260513-002 — Pi Store Admin completeness audit.

MUST READ before any edit (in order):
1. .task-handoffs/active/T-20260513-002-antigravity-store-admin-completeness.md (this dossier, FULL)
2. .task-handoffs/SKILL.md (operational protocol v3.1, especially §1.3 Role Self-Identification, §2 anti-hallucination)
3. .task-handoffs/project/PROJECT.md (workspace context)
4. pi-backend/app/admin/routers/licenses.py + packages.py + users.py + cron.py (verify exact request/response shape)
5. pi-store-webapp/src/lib/api-client.js (verify api.admin.* methods exist + signatures)
6. pi-store-webapp/admin/layout/AdminLayout.jsx (sidebar pattern)
7. pi-store-webapp/admin/pages/license/AdminLicensesPage.jsx + LicenseDetailModal.jsx (modal patterns to follow)
8. pi-store-webapp/admin/pages/system/AdminUsersPage.jsx + AdminUserProfilePage.jsx (existing files to wire, NOT recreate)

EXECUTE Phase B → Phase G in order. STOP at each phase boundary and paste:
- File list touched
- `npx vite build` exit code + last 5 lines
- Any out-of-scope finding into ## 12 of this dossier

CRITICAL RULES (violations = fail):
- DO NOT touch files outside §4 Allowed Scope.
- DO NOT modify pi-backend.
- DO NOT use hardcoded colors — only semantic tokens (border-base-border, bg-base-200, ...).
- DO NOT skip the confirm dialog on Adjust Tokens — financial action requires user confirmation in UI.
- Vietnamese strings: type with proper diacritics (UTF-8). Never reuse mojibake from old code.
- PASTE raw `npx vite build` output into ## Evidence at each phase boundary.
- After each phase: update `updated:` frontmatter + STATUS.md heartbeat to 'antigravity-working'.

REPORT format per .task-handoffs/system/REPORTING.md. Final REPORT block at end.

Set state: returned when all Phase B-G acceptance criteria met. Claude will Phase C-verify before archive.
```
