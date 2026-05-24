---
id: T-20260511-001-antigravity-iframe-opt
owner: antigravity
state: archived
priority: high
risk: medium
estimated_minutes: 45
parent: null
children: []
depends_on: []
parallelization_ok: false
created: 2026-05-11T00:12:00Z
updated: 2026-05-11T00:19:00Z
---

# 🛡️ DOSSIER: Iframe Optimization (WP Plugin)

Tối ưu hóa lớp tích hợp Iframe giữa WordPress và Pi Dashboard Webapp.

## ## 0. User Original Intent
"lên plan tối ưu cho ifame bên wp" - 2026-05-11 00:11 (via chat).

## ## 1. Allowed Scope
- `plugins/pi-api/includes/IframeRenderer.php`
- `plugins/pi-api/views/iframe-page.php`
- `plugins/pi-api/assets/css/iframe.css`
- `plugins/pi-api/assets/js/iframe-bridge.js`
- `pi-dashboard-webapp/src/lib/iframe-bridge.js`

## ## 2. Out Of Scope
- Thay đổi logic Backend API.
- Refactor login page UI.

## ## 3. Phases

### Phase A: Audit & Preparation
- [x] Verify `PI_API_BACKEND_URL` in `IframeRenderer.php`
- [x] Check handshake timing

### Phase B: WP Plugin Implementation
- [x] Add `preconnect` in `IframeRenderer.php`
- [x] Create `pi-api-loader` in `iframe-page.php`
- [x] Style loader and animations in `iframe.css`
- [x] Implement error/timeout logic in `iframe-bridge.js`

### Phase C: Implementation (Webapp)
- [x] Ensure early `postMessage` signal.

### Phase D: Verification
- [x] Test loading transition.
- [x] Test timeout error handling.

## ## 4. Evidence (Raw Logs)
- `IframeRenderer::injectPreconnect` added to `admin_head`.
- `pi-api-loader` and `pi-api-error` implemented in PHP/CSS.
- `loadTimeout` (8s) implemented in `iframe-bridge.js`.
- `notifyIframeReady()` called in `main.jsx` right after `bootstrapTenant()`.

## ## 5. Diff Summary
- `plugins/pi-api/`: +100 lines (UI/UX loading shell).
- `pi-dashboard-webapp/`: +5 lines (Boot optimization).

