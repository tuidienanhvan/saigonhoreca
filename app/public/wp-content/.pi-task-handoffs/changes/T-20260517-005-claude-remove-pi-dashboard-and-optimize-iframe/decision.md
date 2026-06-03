# Decision Record — T-20260517-005

**Date**: 2026-05-17
**Owner**: claude (orchestrator-direct)
**Risk**: high
**Final verdict**: PASS (4/4 gates green)

---

## 1. Part A — Remove `plugins/pi-dashboard/` plugin shell

### Context

`plugins/pi-dashboard/` was a folder-only artifact: no `pi-dashboard.php` plugin
header, no PHP entry point, just `assets/app/` populated by
`pi-dashboard-webapp` build. WordPress never recognized it as an active plugin.
The real dashboard lives at `app.pi-ecosystem.com` and is embedded into WP
admin via `plugins/pi-api/`'s iframe — confirmed in
`plugins/pi-api/includes/IframeRenderer.php::buildIframeUrl()` which points at
`PI_API_BACKEND_URL`, not at any local asset.

### Decision

Delete the folder. Reconfigure `pi-dashboard-webapp` build pipeline to output
to its own local `dist/` instead of the now-deleted plugin folder. Drop
`output/pi-dashboard.zip` from the distribution set.

### Alternatives considered

1. **Keep the folder for self-hosted mode** — rejected. `pi-api`'s iframe code
   has no branch that loads local plugin assets; turning the shell back on
   would require also adding a self-hosted mode in `IframeRenderer.php`. No
   active customer or roadmap item requested that.
2. **Add a `pi-dashboard.php` entry to make it a real plugin** — rejected for
   the same reason: nothing would call it.
3. **Keep the zip in `output/` "just in case"** — rejected. The zip would be
   stale on every webapp build and would confuse future packagers.

### Files changed (Part A)

| File | Change |
|---|---|
| `plugins/pi-dashboard/` | **DELETED** (entire tree) |
| `output/pi-dashboard.zip` | **DELETED** |
| `pi-dashboard-webapp/vite.config.js` | `outDir`: `'../plugins/pi-dashboard/assets/app'` → `'dist'` (also fixed fallback in `enforceBundleSize` and the leading docstring) |
| `pi-dashboard-webapp/package.json` | `build`: removed `&& mkdir -p dist && cp -r ../plugins/pi-dashboard/assets/app/* dist/` — vite writes to `dist/` directly now |
| `pi-dashboard-webapp/eslint.config.js` | Removed `'../plugins/pi-dashboard/assets/app/**'` from `globalIgnores` |
| `pi-backend/app/pi_dashboard/__init__.py` | Updated docstring (1 minor scope expansion — line 6 referenced the deleted plugin path; corrected to reference the webapp source + cloud runtime path) |

### Validation

- `cd pi-dashboard-webapp && rm -rf dist && npm run build` → `✓ built in 20.11s`, 50+ assets in `dist/`.
- `[ ! -d plugins/pi-dashboard ]` → OK.
- `[ ! -f output/pi-dashboard.zip ]` → OK.
- `grep -rln 'plugins/pi-dashboard' plugins/ pi-dashboard-webapp/src/ pi-store-webapp/src/ themes/saigonhouse-theme/` → no matches.

---

## 2. Part B — Harden `pi-api` iframe stack

Pre-task audit (Section §II of dossier) identified 15 issues B1-B15. All
addressed; specific decisions:

### B1 — Mock JWT bypass in production

**Found**: `IframeRenderer::renderPage()` (old) generated a self-signed mock
JWT with `tier=max, is_admin=true, features=['*']` whenever
`BackendClient::issueJwt()` returned empty — with no `WP_DEBUG` /
`PI_API_DEV_MODE` guard. A misconfigured production site would silently grant
admin-tier API access via this fallback.

**Decision**: Extract the dev-only mock JWT generator into a private method
`devMockJwt()`, only callable when `isDevMode()` returns true
(`PI_API_DEV_MODE || WP_DEBUG`). Production JWT failure now renders a new
`views/iframe-error-page.php` instead of falling back to mock.

Did NOT touch `BackendClient::buildMockJwt()` — that method is already gated
by `PI_API_MOCK_MODE` (opt-in constant), so the risk vector was only the
inline fallback inside `IframeRenderer`.

### B2 — JWT issuance logic duplicated

**Found**: `IframeRenderer::renderPage()` had 13 lines of inline JWT
issuance/expiry handling that couldn't be reused by the refresh ajax handler
(B3).

**Decision**: Add `Settings::ensureValidJwt(): array{jwt, expires_in, error}`
as the single source of truth. Both `renderPage()` and `JwtAjax::handle()`
call it. Returns explicit error codes (`no_license`, `jwt_empty`,
`jwt_issue_failed`) so callers can render appropriate UI without re-running
the issuance logic.

### B3 — `pi_api_refresh_jwt` AJAX handler missing

**Found**: `iframe-bridge.js` fetched
`admin-ajax.php?action=pi_api_refresh_jwt` on session-expired postMessage,
but no PHP handler was registered. Result: silent fail every time the iframe
tried to refresh its session.

**Decision**: New class `PiApi\JwtAjax` registers
`wp_ajax_pi_api_refresh_jwt`. Capability check (`manage_options`), nonce
check (`pi_api_iframe`), calls `Settings::ensureValidJwt()`, returns
`wp_send_json_success({jwt, expires_in})` or `wp_send_json_error`.

Wired into `pi-api.php`'s `plugins_loaded` action right after
`Settings::init()`. The action name + nonce are passed to the bridge script
via `wp_localize_script` so JS doesn't hardcode them — uses constants
`JwtAjax::ACTION` and `JwtAjax::NONCE`.

### B4 — `$expires_in` undefined in `iframe-page.php`

**Found**: `views/iframe-page.php` line 6 read `$expires_in` but
`renderPage()` did not pass it into the include scope. Triggered E_NOTICE
when `WP_DEBUG=true`.

**Decision**: `renderPage()` now sets `$expires_in = (int)
$jwt_result['expires_in']` before the include. View uses `?? 0` as a defensive
default.

### B5 — `self::backendOrigin()` called from view

**Found**: View file `iframe-page.php` called `self::backendOrigin()` (private
method). Worked only because PHP resolved `self::` against `IframeRenderer`
since the include happened inside a static method — fragile pattern.

**Decision**: Made `backendOrigin()` public (legitimate use from views/other
plugins). View now uses the `$backend_origin` local variable passed via
include scope instead of `self::`.

### B6 — License-inactive sites render iframe anyway

**Found**: No `Settings::isActive()` check before rendering iframe. An
unactivated site would still load the iframe and (combined with B1) get a
mock JWT, exposing the dashboard UI without a real license.

**Decision**: `renderPage()` now early-returns to `views/license-page.php`
(which already had the activation form) when `!Settings::isActive() &&
!isDevMode()`.

### B7 — Loader timeout hardcoded at 8s

**Decision**: Exposed via `pi_api_iframe_loader_timeout` filter (default
8000ms). Passed to JS via `wp_localize_script`'s `loaderTimeout` field.

### B8 — JWT leaks via Referer header

**Found**: `referrerpolicy="strict-origin-when-cross-origin"` would still
send `Origin` (without path/query) but third-party scripts loaded by the
webapp could leak the full URL.

**Decision**: Hardened to `referrerpolicy="no-referrer"` — backend doesn't
need any referrer info, and this prevents any leak of the JWT-bearing URL.
Full migration to a POST-based handshake (avoiding the URL JWT entirely) is
deferred — needs backend cooperation, logged as candidate T-006.

### B9 — Hardcoded CORS allowed origins

**Decision**: Wrapped `$allowed_origins` in
`apply_filters('pi_api_cors_allowed_origins', $allowed_origins,
$request_origin)` so customers can add staging/dev domains without forking.

### B10 — CSS sidebar offsets hardcoded

**Decision**: Introduced CSS custom properties
(`--pi-sidebar-offset`, `--pi-sidebar-offset-folded`,
`--pi-sidebar-offset-mobile`, `--pi-admin-bar-height`,
`--pi-admin-bar-height-mobile`) at `:root`. Pixel values become defaults a
child theme can override inline. Layout selectors all use `var()` now.

### B11 — `allow-scripts + allow-same-origin` sandbox combo

**Decision**: Documented the rationale with a PHP comment block above the
`<iframe>` tag — the combo is normally a sandbox-escape, but the iframe is
cross-origin so SOP enforces the boundary anyway. Same-origin is needed for
the webapp's own localStorage/cookies. No code change beyond the explanatory
comment.

### B12 — No CSP frame-src

**Decision**: `injectHeadMeta()` (renamed from `injectPreconnect()`) now
emits both `<link rel="preconnect">` and a CSP
`<meta http-equiv="Content-Security-Policy" content="frame-src
<backend>;">` to whitelist exactly the backend origin.

### B13 — `siteUrl` duplicated via query + postMessage

**Decision**: Dropped `siteUrl` from `buildIframeUrl()`'s query args. Bridge
forwards it via postMessage on `pi-api/ready` (existing code path), keeping
the URL minimal and avoiding mismatch risk.

### B14 — `indexOf` origin check on `pi-api/navigate`

**Decision**: Replaced with `URL` parser — `new URL(data.url,
window.location.origin).origin === window.location.origin`. Rejects
attacker-crafted strings like `https://evil.example/?fake=https://wp.local/`.

### B15 — Unvalidated JWT refresh response

**Decision**: Bridge now validates `body.jwt` is a non-empty string AND
`body.expires_in > 0` before posting `pi-api/jwt-refreshed`. On failure
posts `pi-api/jwt-refresh-failed` with an error string so the webapp can
react instead of silent-fail.

---

## 3. Net effect

- 1 plugin removed from customer sites (`pi-dashboard`)
- 1 new PHP class (`PiApi\JwtAjax`, 50 lines, single responsibility)
- 1 new view (`views/iframe-error-page.php`, ~40 lines, production JWT failure)
- ~330 LOC modified across 11 existing files
- 1 critical security bypass closed (production mock JWT)
- 1 dead code path fixed (`pi_api_refresh_jwt` handler now exists)
- 1 latent bug fixed (`$expires_in` undefined)
- 6 polish improvements (filters, configurable timeout, URL parser, CSS vars, CSP, no-referrer)
- 0 new npm/composer dependencies
- 0 customer-facing UI changes (same layout, same flow)

## 4. Deferred work

- **B8 follow-up**: Full migration of JWT from URL query → POST handshake.
  Requires backend webapp to accept POST with `Authorization: Bearer <jwt>`
  on initial load instead of reading `?t=`. Tracking as future T-006.
- **Mock mode review**: `BackendClient::buildMockJwt()` still active when
  `PI_API_MOCK_MODE=true` regardless of environment. Acceptable today
  (opt-in constant); revisit if mock mode ever becomes default-on in any
  shipped config.
