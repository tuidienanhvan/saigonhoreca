# Rollback Plan — T-20260517-003

Theme directory is not a git repository (per T-012's archive note). Rollback is manual file restoration. Tested below in order.

## Trigger conditions

Roll back if production deployment shows any of:
- FOUC on first paint > 500 ms (split-mode async should be < 200 ms).
- Layout shifts that didn't exist before (verify with Lighthouse CLS).
- 5xx errors on `theme-rest.css` request (means async filter or build broke).
- Mega menu or hero rendering "naked" (means critical bundle missing styles it shouldn't be).

## Step-by-step revert

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content/themes/saigonhouse-theme"

# 1. Restore single-bundle import
sed -i 's|@import "./_imports-critical.css";|@import "./_imports.css";|' assets/css/style.css

# 2. Remove new entry + chunks
rm -f assets/css/_imports-critical.css assets/css/_imports-rest.css assets/css/style-rest.css

# 3. Restore package.json scripts to single target
# Manual edit (or jq if installed):
node -e '
const fs = require("fs");
const pkg = JSON.parse(fs.readFileSync("package.json", "utf8"));
pkg.scripts = {
  dev:   "tailwindcss -i ./assets/css/style.css -o ./assets/css/dist/theme.css --watch",
  build: "tailwindcss -i ./assets/css/style.css -o ./assets/css/dist/theme.css --minify"
};
fs.writeFileSync("package.json", JSON.stringify(pkg, null, 2) + "\n");
'

# 4. Rebuild (single output)
rm -f assets/css/dist/theme-rest.css
npm run build

# 5. Drop inline cap back to no-op size (30 KB) — bundle will be 465 KB so helper falls through
# Edit inc/core/critical-css.php manually:
#   define('SGH_INLINE_CSS_HARD_CAP', 30 * 1024);
# Remove the entire `if ($handle === 'theme-rest')` block from sgh_async_theme_css().

# 6. Remove theme-rest enqueue
# Edit inc/core/enqueue.php — delete the 4-line block that enqueues 'theme-rest'.

# 7. Restore SVG header skyline (the 64 child elements)
# Restore header.php from this dossier's evidence section §XII, or from any
# pre-T-015 backup. Look for the `<g class="sh-header__arch-skyline">` block.

# 8. Optionally revert .htaccess Brotli block (safe to leave — no-ops without mod_brotli)
```

## Validation after revert

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"

# CSS pipeline still works
curl -s -I http://saigonhouse.local/wp-content/themes/saigonhouse-theme/assets/css/dist/theme.css | grep -E "^(HTTP|Content-Type|Content-Length)"
# Expect: 200 / text/css / 465237 bytes

# Page renders
curl -s http://saigonhouse.local/ | grep -cE "rel='stylesheet'[^>]*theme\.css"
# Expect: 1 (render-blocking single external link, baseline state)

# Lighthouse back to baseline 72
npx --yes lighthouse http://saigonhouse.local/ --only-categories=performance --quiet --chrome-flags="--headless"
```

Expected post-rollback Lighthouse: Perf 72 ± 5 (same as T-014 dispatch baseline).

## Partial rollback

If only the SVG simplification needs reverting (visual feedback):
- Restore `header.php` SVG only (steps 7).
- Leave bundle split + Brotli + everything else.
- Net effect: +64 DOM nodes, lose 1 Perf point at most.

If only the bundle split needs reverting (CSS bug):
- Steps 1–6 above.
- Leave SVG simplification + Brotli in place.
- Net effect: back to baseline 72 with cleaner SVG.
