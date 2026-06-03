# Rollback Plan — T-20260517-005

Use this if the task introduces a regression on a live site. Workspace is
NOT a git repository (per T-004 dossier), so rollback uses the snapshots
captured in `before/`.

## Pre-rollback verification

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"
CHANGES=".task-handoffs/changes/T-20260517-005-claude-remove-pi-dashboard-and-optimize-iframe"

# Confirm snapshots exist
ls "$CHANGES/before/"     # expect 13 files + pi-dashboard-plugin.tar.gz
ls "$CHANGES/after/"      # expect same files as their post-edit state
ls "$CHANGES/diff.patch"  # expect populated (~645 lines)
```

## Full rollback (all changes reverted)

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"
SNAP=".task-handoffs/changes/T-20260517-005-claude-remove-pi-dashboard-and-optimize-iframe/before"

# 1. Restore pi-dashboard plugin folder
tar -xzf "$SNAP/pi-dashboard-plugin.tar.gz"
# Verify
ls plugins/pi-dashboard/assets/app/ | head -3

# 2. Restore modified pi-api PHP files
cp "$SNAP/IframeRenderer.php"  plugins/pi-api/includes/
cp "$SNAP/BackendClient.php"   plugins/pi-api/includes/
cp "$SNAP/Settings.php"        plugins/pi-api/includes/
cp "$SNAP/CorsBridge.php"      plugins/pi-api/includes/
cp "$SNAP/Heartbeat.php"       plugins/pi-api/includes/
cp "$SNAP/iframe-page.php"     plugins/pi-api/views/
cp "$SNAP/iframe.css"          plugins/pi-api/assets/css/
cp "$SNAP/iframe-bridge.js"    plugins/pi-api/assets/js/
cp "$SNAP/pi-api.php"          plugins/pi-api/

# 3. Restore pi-dashboard-webapp config
cp "$SNAP/vite.config.js"      pi-dashboard-webapp/
cp "$SNAP/package.json"        pi-dashboard-webapp/
cp "$SNAP/eslint.config.js"    pi-dashboard-webapp/

# 4. Remove newly-created files (no snapshot, didn't exist before)
rm -f plugins/pi-api/includes/JwtAjax.php
rm -f plugins/pi-api/views/iframe-error-page.php

# 5. Revert pi-backend docstring (minor)
# (re-apply original docstring manually if needed — see decision.md §1)

# 6. Restore output zip
# pi-dashboard.zip is generated from the webapp build output. Rebuild:
cd pi-dashboard-webapp && rm -rf dist && npm run build
cd ..
# Then manually repackage:
# zip -r output/pi-dashboard.zip plugins/pi-dashboard/

# 7. Verify rollback
[ -d plugins/pi-dashboard ] && echo "OK: plugin restored"
[ ! -f plugins/pi-api/includes/JwtAjax.php ] && echo "OK: JwtAjax removed"

PHP="/c/Users/Administrator/AppData/Roaming/Local/lightning-services/php-8.2.30+1/bin/win64/php.exe"
for f in plugins/pi-api/includes/*.php plugins/pi-api/views/*.php plugins/pi-api/pi-api.php; do
    "$PHP" -l "$f"
done

cd pi-dashboard-webapp && npm run build && echo "OK: webapp build still works"
```

## Partial rollback options

### A. Revert ONLY mock JWT guard (keep other improvements)

If the new license-active check is causing a production block for a customer
mid-rollout, you can temporarily re-enable the mock JWT path while keeping
the rest:

1. Open `plugins/pi-api/includes/IframeRenderer.php`
2. Find `renderPage()` and change:
   ```php
   if (!Settings::isActive() && !self::isDevMode()) {
   ```
   to:
   ```php
   if (false && !Settings::isActive() && !self::isDevMode()) {
   ```
3. Also change:
   ```php
   if ($jwt === '' && self::isDevMode()) {
   ```
   to:
   ```php
   if ($jwt === '') {
   ```

This is a HOTFIX, not a rollback — schedule a proper investigation within
24h. The original B1 security bypass returns when you do this.

### B. Restore pi-dashboard plugin folder only

Customer asks for the local plugin back without reverting iframe fixes:

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"
tar -xzf ".task-handoffs/changes/T-20260517-005-claude-remove-pi-dashboard-and-optimize-iframe/before/pi-dashboard-plugin.tar.gz"

# Re-enable webapp output to plugin folder
# Open pi-dashboard-webapp/vite.config.js line 80, change:
#   outDir: 'dist',
# to:
#   outDir: '../plugins/pi-dashboard/assets/app',
#
# Open pi-dashboard-webapp/package.json line 8, change:
#   "build": "vite build",
# to:
#   "build": "vite build && mkdir -p dist && cp -r ../plugins/pi-dashboard/assets/app/* dist/",

cd pi-dashboard-webapp && npm run build
```

Note this is not enough to actually use the plugin shell — `pi-api`'s iframe
still loads from cloud (`PI_API_BACKEND_URL`). Real self-hosted mode would
also require adding an iframe-src toggle in `IframeRenderer.php`. Tracked as
future scope if/when needed.

## Diff inspection without applying

```bash
cat ".task-handoffs/changes/T-20260517-005-claude-remove-pi-dashboard-and-optimize-iframe/diff.patch" | less
# Or per-file:
diff -u before/IframeRenderer.php after/IframeRenderer.php
```

## Confidence level

**Medium-high.** All 4 quality gates green on local; PHP syntax verified;
webapp build verified; structural greps confirm wiring. The remaining risk
vectors:
- WP admin smoke (manual, browser-based) not run in this session — needs a
  human to open `Pi Dashboard` menu in WP admin on the test site.
- JWT refresh ajax handler tested only at the code level; live exercise
  requires the dashboard webapp to actually trigger
  `pi-api/refresh-jwt` postMessage.

Mid-task rollback gates (per dossier §XV):
- PHP fatal at WP admin load → restore IframeRenderer + JwtAjax + pi-api.php
  immediately.
- Iframe blank screen → revert iframe-page.php + iframe-bridge.js, leave
  rest in place.
- License-active customer reports "I see the license form instead of the
  dashboard" → review `Settings::isActive()` logic, do NOT roll back the
  whole task.
