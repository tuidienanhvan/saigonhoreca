# Rollback Plan — T-20260529-001-qwen-gcup-crawl-pillar

## Quick Rollback
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\themes\saigonhoreca-theme"

# Restore modified files from git
git checkout HEAD -- template-parts/project-pillar/g-cup-coffee-bistro/
git checkout HEAD -- single-project/g-cup-coffee-bistro.php
git checkout HEAD -- single-project/g-cup-coffee-bistro.css
git checkout HEAD -- assets/css/imports/_imports-project.css
git checkout HEAD -- scripts/project-data/g-cup-coffee-bistro.json

# Remove static mirror (untracked)
Remove-Item -Recurse -Force static-mirror/saigonhoreca.vn/du-an/g-cup-coffee-bistro/
Remove-Item -Recurse -Force static-mirror/saigonhoreca.com/project/g-cup-coffee-bistro/

# Rebuild
npm run build:project
```

## What Gets Restored
- All 8 standard section PHP/CSS files revert to pre-task state
- 7 variant rác files restored from git history
- JSON metadata reverts to sections_count: 7
- Static mirror crawl files removed

## Risk Assessment
- **Low risk**: Changes are scoped to G-Cup only, no other projects affected
- **CSS imports**: `_imports-project.css` was not modified (G-Cup block already existed)
- **Single-project template**: Was not modified (already had correct 8 section includes)
