# T-20260502-001 - Gemini: pi-api Carrier + Dashboard License UX

## Task Metadata / Thông Tin Task

- ID: `T-20260502-001`
- Owner: `Gemini`
- State: `archived`
- Created: `2026-05-02`
- Archived: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Correct the architecture so `plugins/pi-api/` is the WordPress-side API carrier and iframe host, while license activation UX lives inside `pi-dashboard-webapp`.

Sửa đúng mô hình: `pi-api` sở hữu REST bridge và iframe host, còn user nhập license trong `Pi Dashboard`, không nhập ở một màn `Pi API` riêng.

## Allowed Scope / Scope Được Sửa

Gemini may inspect:

- `plugins/pi-api/`
- `pi-dashboard-webapp/`
- `themes/saigonhouse-theme/functions.php`

Gemini may edit:

- `plugins/pi-api/`
- `pi-dashboard-webapp/`

## Out Of Scope / Scope Cấm

- Do not edit plugins outside `plugins/pi-api/`.
- Do not edit `pi-store-webapp/`.
- Do not edit `pi-backend/`.
- Do not redesign dashboard UI.
- Do not touch build artifacts.

## Required Phases / Các Phase Bắt Buộc

1. Audit API ownership.
2. Remove primary Pi API license menu.
3. Add or normalize license REST endpoints.
4. Move license UX into dashboard iframe mode.
5. Keep theme compatibility only.
6. WP Admin smoke checklist.
7. Verification.

## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

Gemini implemented the first pass:

- Wrapped the old top-level `Pi API` menu behind `PI_API_DEV_MODE`.
- Added `GET /wp-json/pi/v1/license/status`.
- Added `POST /wp-json/pi/v1/auth/issue-jwt`.
- Added `LicenseGate.jsx` and wrapped dashboard layout in `App.jsx`.
- Reported dashboard lint/test/build as passing.

### Files Modified / File Đã Sửa

- `plugins/pi-api/includes/Settings.php`
- `plugins/pi-api/includes/api/endpoints/class-license.php`
- `pi-dashboard-webapp/src/App.jsx`

### Files Created / File Đã Tạo

- `pi-dashboard-webapp/src/components/license/LicenseGate.jsx`

### Agent Verification / Kiểm Tra Của Agent

- Dashboard lint: reported pass.
- Dashboard tests: reported pass, 122/122.
- Dashboard build: reported pass.
- PHP syntax: `not run`, `php` unavailable.
- WP Admin smoke: `not run`.

## Codex Review / Codex Kiểm Tra

Status: `pass with warnings`

### Scope Review / Kiểm Scope

Pass with context:

- Touched implementation files stayed within allowed edit scope: `plugins/pi-api/` and `pi-dashboard-webapp/`.
- `pi-store-webapp` remains dirty from earlier unrelated source-tree work, but this task did not require editing it.
- `plugins/pi-api` is not a git repo in this workspace, so plugin diff could not be checked through git status.

### Fixes Applied By Codex / Codex Đã Sửa Thêm

Codex found and fixed issues after Gemini:

- `class-license.php` still contained mojibake strings and comments.
- `LicenseGate.jsx` still contained mojibake UI text.
- `LicenseGate.jsx` had large inline style blocks and no same-name CSS file.
- `POST /license/activate` only accepted `key`; Codex updated it to accept `license_key` and legacy `key`.
- `Settings.php` legacy form redirects still pointed to `pi-api-license`; Codex redirected them to `pi-api-dashboard`.
- `App.jsx` had mojibake comment text from prior edits; Codex cleaned touched comments.

Additional file created by Codex:

- `pi-dashboard-webapp/src/components/license/LicenseGate.css`

### Endpoint Review / Kiểm Endpoint

Pass:

- `GET /wp-json/pi/v1/license/status` registered.
- `POST /wp-json/pi/v1/license/activate` registered and stores activation through `PiApi\Settings::storeActivation()`.
- `POST /wp-json/pi/v1/license/deactivate` registered and clears activation through `PiApi\Settings::clearActivation()`.
- `POST /wp-json/pi/v1/auth/issue-jwt` registered and refreshes JWT through `PiApi\BackendClient::issueJwt()`.
- Legacy `GET /license` is kept for compatibility and points to normalized status output.

### Menu Review / Kiểm Menu

Pass with note:

- `Settings::init()` now registers the old `Pi API` menu only if `PI_API_DEV_MODE` is defined and truthy.
- `Pi Dashboard` remains the primary WP Admin menu through `IframeRenderer`.
- `pi-api-license` still exists as a dev/legacy slug inside `Settings::registerMenu()`, but it is not registered in normal mode.

### Encoding Review / Kiểm Encoding

Pass:

- Mojibake check on touched files returned no matches:
  - `plugins/pi-api/includes/Settings.php`
  - `plugins/pi-api/includes/api/endpoints/class-license.php`
  - `pi-dashboard-webapp/src/App.jsx`
  - `pi-dashboard-webapp/src/components/license/LicenseGate.jsx`
  - `pi-dashboard-webapp/src/components/license/LicenseGate.css`

### Gate Review / Kiểm Gates

Dashboard gates run by Codex:

```text
npm run lint      -> pass
npm run test:run  -> pass, 22 files, 122 tests
npm run build     -> pass with warning
```

Build warning:

```text
Some chunks are larger than 500 kB after minification.
```

PHP syntax:

```text
php -l plugins/pi-api/includes/Settings.php
php -l plugins/pi-api/includes/api/endpoints/class-license.php
```

Result: `not run`; `php` command is not available in this PowerShell environment.

WP Admin browser smoke:

Result: `not run`; not executed in this review pass.

### Decision / Quyết Định

Accepted as `pass with warnings`.

Remaining warnings are explicit and bounded:

- PHP syntax still needs to be checked on an environment with `php`.
- WP Admin smoke still needs browser verification.
- Dashboard production build passes but emits a chunk-size warning unrelated to this specific license gate change.

## Escalation / Xử Lý Fail

- Failure / Lỗi: none blocking after Codex fixes.
- Evidence / Bằng chứng: dashboard lint/test/build pass; mojibake check pass on touched files.
- Recovery / Cách xử lý: run PHP syntax and WP Admin smoke when environment is available.
- Owner / Người xử lý: Codex or user with local PHP/WP browser session.
