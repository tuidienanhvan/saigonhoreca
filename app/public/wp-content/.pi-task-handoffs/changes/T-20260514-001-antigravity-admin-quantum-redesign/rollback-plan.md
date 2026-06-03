# Rollback Plan — T-20260514-001

## Trigger conditions
- Admin UI rendering broken on 1+ pages
- Critical feature regression (license creation, package assign, key allocate)
- Performance: pages take >3s to load

## Immediate rollback
```bash
cd pi-store-webapp
# Identify task commits
git log --oneline | grep -E "T-20260514-001|quantum-hud|HUD"
git revert <oldest-commit>..<newest-commit>
npx vite build
deploy
```

## Surgical rollback (one page broken)
- Comment out the route in src/App.jsx
- Deploy
- Fix in isolation, re-enable

## Data impact
- ZERO. Pure UI refactor. No DB schema changes. No API contract changes.
