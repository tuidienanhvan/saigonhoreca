# Rollback Plan — T-20260517-004

The theme dir is not a git repo (per T-012 archive note). Rollback is manual
file restoration from this dossier's `before/` snapshot.

## Trigger conditions

Roll back if production deployment shows any of:

- Visual regression on saigonhouse.local/ (compare to T-016 baseline screenshots).
- `npm run build` fails after the conversion runs (it should not — verified at write time).
- `<style id="sgh-theme-inline">` returns content that doesn't render `.sh-*` classes correctly.

## Full rollback (back to T-016 final state)

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"

# Restore CSS files
cp -r .task-handoffs/changes/T-20260517-004-claude-sgh-tailwind-utilities-refactor/before/themes/saigonhouse-theme/assets/css/* \
   themes/saigonhouse-theme/assets/css/

# Restore template-parts CSS
cp -r .task-handoffs/changes/T-20260517-004-claude-sgh-tailwind-utilities-refactor/before/themes/saigonhouse-theme/template-parts/* \
   themes/saigonhouse-theme/template-parts/

# Restore page-templates CSS (only the .css files — leave .php alone, they weren't touched)
find .task-handoffs/changes/T-20260517-004-claude-sgh-tailwind-utilities-refactor/before/themes/saigonhouse-theme/page-templates \
   -name "*.css" -exec cp -t themes/saigonhouse-theme/page-templates/ {} +

# Rebuild
cd themes/saigonhouse-theme && npm run build

# Verify back to baseline
wc -c assets/css/dist/theme.css                 # expect 461,719 (±1k)
gzip -c assets/css/dist/theme.css | wc -c        # expect 56,362 (±1k)
```

## Validation after rollback

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"

# Page still renders
curl -s -I http://saigonhouse.local/ | grep -E "^HTTP|^Content-Type"
# Expect: 200 OK, text/html

# Critical classes still present
curl -s http://saigonhouse.local/ | grep -cE "(sh-hero|sh-svc-card|sh-fp__card|sh-footer)"
# Expect: > 100

# Lighthouse back to baseline
npx --yes lighthouse http://saigonhouse.local/ --only-categories=performance --quiet --chrome-flags="--headless"
# Expect: Perf ~0.80 ± 0.05
```

## Partial rollback (revert only one file)

If a specific file shows visual regression:

```bash
# Replace <FILE> with the relative path, e.g. template-parts/home/hero-carousel.css
cp .task-handoffs/changes/T-20260517-004-claude-sgh-tailwind-utilities-refactor/before/themes/saigonhouse-theme/<FILE> \
   themes/saigonhouse-theme/<FILE>
cd themes/saigonhouse-theme && npm run build
```

The conversion is per-file, so partial rollback is safe.

## Re-applying the conversion later

The conversion script is preserved at `scripts/deapply.py` inside the theme.
After any visual fix to source CSS files, re-run:

```bash
cd themes/saigonhouse-theme
npm run build                              # produce fresh dist
python scripts/deapply.py --dry-run        # preview changes
python scripts/deapply.py                  # apply for real
npm run build                              # rebuild after
```

The script is idempotent — running it on already-converted files is a no-op.
