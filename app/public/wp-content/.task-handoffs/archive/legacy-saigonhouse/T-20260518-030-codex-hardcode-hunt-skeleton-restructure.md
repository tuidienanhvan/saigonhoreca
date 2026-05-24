---
id: T-20260518-030
owner: codex
state: drafted
priority: P1
risk: medium
estimated_minutes: 90
parent: T-20260518-025
children: []
depends_on: [T-20260518-027]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Claude]
created: 2026-05-18 19:00
updated: 2026-05-18 19:00
---

# 📋 T-20260518-030 | codex | hardcode-hunt-skeleton-restructure — Post-Vercel-Migration Cleanup: Hardcode Hunt + Skeleton Primitive Restructure

## I. 🎯 Goal

Sau khi T-026/T-027 (Gemini) hoàn thành Vercel migration cho `_shared/components/` và admin shell của `pi-store-webapp`, vẫn còn 2 vấn đề kỹ thuật tồn đọng cần Codex 5.3 xử lý:

1. **Hardcode color leakage**: Còn hex/rgba hardcoded trong các file `features/*` (out of T-026/027 scope). Phá vỡ contract "semantic token only" của master dossier T-025 nếu để vậy.
2. **Skeleton primitive misplacement**: `pi-store-webapp/src/_shared/components/ui/Skeleton.jsx` là primitive `<div>` pulse box, không thuộc class "UI component" như Button/Card/Input. pi-dashboard-webapp đã có pattern chuẩn `_shared/skeletons/`. pi-store cần align để consistent.

3. **Verification gate**: Confirm `FullPageLoader.jsx` đã thực sự migrate trong T-026/027 (user nghi ngờ chưa đổi).

---

## II. 📚 Required Reading

- `.task-handoffs/active/T-20260518-025-claude-vercel-design-master.md` (design language spec)
- `.task-handoffs/active/T-20260518-026-gemini-vercel-pi-store-primitives.md` (mục V — primitive design choices đã apply)
- `.task-handoffs/active/T-20260518-027-gemini-vercel-pi-store-layout-shell.md` (mục V — admin component design choices đã apply)

---

## III. 🚧 Allowed Scope (Strict)

### III.1 Phase A — Skeleton restructure (pi-store-webapp)

```
# Create new home (MOVE target)
pi-store-webapp/src/_shared/skeletons/Skeleton.jsx        (CREATE — moved content)
pi-store-webapp/src/_shared/skeletons/index.js            (CREATE — barrel)

# Old home (DELETE)
pi-store-webapp/src/_shared/components/ui/Skeleton.jsx    (DELETE)
pi-store-webapp/src/_shared/components/ui/index.js        (MODIFY — remove Skeleton export)

# Consumer imports (MODIFY)
pi-store-webapp/src/features/user/UsagePage.jsx           (MODIFY — change import)
# + any other file revealed by `grep -rn "Skeleton" pi-store-webapp/src/ pi-store-webapp/admin/` that imports the primitive
```

### III.2 Phase B — Hardcode hunt (BOTH apps)

```
# pi-store-webapp feature CSS hardcode
pi-store-webapp/src/features/home/components/HomeHero.css
pi-store-webapp/src/features/home/components/HomeCTA.css
pi-store-webapp/admin/features/usage/UsageExplorerPage.css
pi-store-webapp/admin/features/tenants/TenantsPage.css
pi-store-webapp/admin/features/tokens/TokenLedgerPage.css

# pi-store-webapp feature JSX hardcode
pi-store-webapp/admin/features/tenants/TenantDetailPage.jsx
# NOTE: BrandingCard.jsx is LEGITIMATE (brand color swatches) — see III.3 exception list

# pi-dashboard-webapp hardcode
pi-dashboard-webapp/src/_shared/overlays/OnboardingTour/OnboardingTour.css
pi-dashboard-webapp/src/features/system/components/email/TemplatePreview.jsx
pi-dashboard-webapp/src/features/system/components/system-health/HealthChart.jsx
pi-dashboard-webapp/src/features/analytics/components/RetentionMatrix.jsx
pi-dashboard-webapp/src/features/performance/components/PwaConfig.jsx
pi-dashboard-webapp/src/features/leads/LeadEnrichment.jsx
pi-dashboard-webapp/src/features/content/components/edit-post/editor/components/Toolbar.jsx
pi-dashboard-webapp/src/features/analytics/components/FunnelResults.jsx
pi-dashboard-webapp/src/features/analytics/components/FunnelEditor.jsx
# NOTE: theme-manager/ColorPicker.jsx, theme-manager/PresetSelector.jsx, seo/og-image/*.jsx
# are LEGITIMATE (UI color picker + OG image canvas) — see III.3 exception list
```

### III.3 LEGITIMATE Exception List (preserve hex, do NOT replace)

Hardcoded hex / rgba may remain in these files because the hex IS the data being displayed/manipulated:

| File | Reason |
|---|---|
| `pi-store-webapp/admin/features/settings/components/BrandingCard.jsx` | Brand color preview swatches |
| `pi-dashboard-webapp/src/features/system/components/theme-manager/ColorPicker.jsx` | Color picker UI (hex IS the input value) |
| `pi-dashboard-webapp/src/features/system/components/theme-manager/PresetSelector.jsx` | Theme preset swatches |
| `pi-dashboard-webapp/src/features/seo/components/og-image/OgPreview.jsx` | OG image canvas rendering user-chosen colors |
| `pi-dashboard-webapp/src/features/seo/components/og-image/OgDesigner.jsx` | OG image designer (user color input) |
| `pi-dashboard-webapp/src/features/seo/components/og-image/OgPreview.test.jsx` | Test fixtures with hex inputs |
| `pi-dashboard-webapp/src/features/seo/components/og-image/ElementPalette.jsx` | Element color presets |
| `pi-dashboard-webapp/src/features/seo/components/og-image/ElementProperties.jsx` | Element editor (user color input) |

**Codex must NOT touch these files in Phase B.**

### III.4 Self-tracking

```
.task-handoffs/active/T-20260518-030-codex-hardcode-hunt-skeleton-restructure.md
```

---

## IV. 🚫 Out Of Scope (Forbidden)

- ❌ `pi-store-webapp/src/styles/index.css` — theme palette (READ-ONLY)
- ❌ `pi-dashboard-webapp/src/index.css` — theme palette (READ-ONLY)
- ❌ `pi-dashboard-webapp/src/animations.css` — animation system (READ-ONLY)
- ❌ Any file in `III.3 LEGITIMATE Exception List`
- ❌ `_shared/components/` (already migrated in T-026/027, do NOT re-touch)
- ❌ Component props or routing (visual treatment only)
- ❌ Adding/removing npm packages
- ❌ Refactoring beyond hardcode-replacement + skeleton-move
- ❌ Creating new component variants
- ❌ Modifying `pi-dashboard-webapp/src/_shared/skeletons/` (already structured correctly)

---

## V. 🎨 Implementation Specification

### V.1 Phase A — Skeleton Primitive Restructure (pi-store)

**Step A1**: Create new file `pi-store-webapp/src/_shared/skeletons/Skeleton.jsx` with verbatim content from current `ui/Skeleton.jsx`. Keep export name `Skeleton` (default + named export).

**Step A2**: Create barrel `pi-store-webapp/src/_shared/skeletons/index.js`:
```js
export { Skeleton, default } from "./Skeleton";
```

**Step A3**: Delete `pi-store-webapp/src/_shared/components/ui/Skeleton.jsx`.

**Step A4**: Remove `Skeleton` line from `pi-store-webapp/src/_shared/components/ui/index.js`. Run `grep -n "Skeleton" pi-store-webapp/src/_shared/components/ui/index.js` to confirm zero matches.

**Step A5**: Update consumers. Run:
```powershell
cd pi-store-webapp
Select-String -Path "src\**\*.jsx","admin\**\*.jsx" -Pattern "Skeleton.*from.*@/_shared/components/ui|Skeleton.*from.*_shared/components/ui"
```
For each match, change the import. Example before:
```js
import { Alert, Badge, Button, Card, EmptyState, Skeleton, Table } from "@/_shared/components/ui";
```
After (split into two imports, or remove Skeleton from existing import):
```js
import { Alert, Badge, Button, Card, EmptyState, Table } from "@/_shared/components/ui";
import { Skeleton } from "@/_shared/skeletons";
```

**Known consumer (audit before edit):** `pi-store-webapp/src/features/user/UsagePage.jsx`. Discover others via grep.

### V.2 Phase B — Hardcode Sanitization

For each file in III.2, replace hex/rgba with semantic tokens per this mapping:

| Hardcode pattern | Semantic replacement |
|---|---|
| `#000`, `#000000` | `var(--b1)` (CSS) or `bg-base-100` (Tailwind) |
| `#fff`, `#ffffff` | `var(--bc)` (CSS) or `text-base-content` (Tailwind) |
| `#dc2626`, `#ef4444`, generic red | `var(--er)` or `bg-error/text-error/border-error` |
| `#10b981`, generic green | `var(--su)` or `bg-success/text-success` |
| `#f59e0b`, generic amber | `var(--wa)` or `bg-warning/text-warning` |
| `#3b82f6`, generic blue | `var(--in)` or `bg-info/text-info` |
| `rgba(0,0,0,0.X)` overlay | `bg-base-content/[X%]` or `bg-base-100/X` |
| `rgba(255,255,255,0.X)` overlay | `bg-base-content/[X%]` |
| Brand-specific red `#ed174c` etc. | `var(--p)` or `bg-primary` |
| Glow `0 0 X #colorglow` | Replace with `shadow-sm` or remove entirely (Vercel = flat) |

**Decision tree per file**:
1. Open file, locate every hardcoded color (`grep -n '#[0-9a-fA-F]\{3,8\}\|rgba\?(' <file>`)
2. Identify intent: is this a state color (success/error), neutral surface, brand callout, or glow effect?
3. Replace with the most semantically correct token from table above
4. If a glow / gradient / dropshadow is the hardcode source — replace with flat hairline border or remove entirely per Vercel master spec II.2

**For CSS files** (HomeHero.css, HomeCTA.css, UsageExplorerPage.css, TenantsPage.css, TokenLedgerPage.css, OnboardingTour.css):
- Use `var(--<token>)` syntax for replacements
- Preserve keyframes structure
- Replace `!important` with proper specificity selectors where possible (HomeCTA.css has 27 `!important` — try to halve count)

**For JSX files** (TenantDetailPage.jsx, HealthChart.jsx, RetentionMatrix.jsx, TemplatePreview.jsx, PwaConfig.jsx, LeadEnrichment.jsx, Toolbar.jsx, FunnelResults.jsx, FunnelEditor.jsx):
- Inline `style={{ color: '#xxx' }}` → use Tailwind class or `style={{ color: 'var(--<token>)' }}`
- Chart fill colors (HealthChart, FunnelResults, FunnelEditor, RetentionMatrix): use `var(--chart-1)` through `var(--chart-6)` (already defined in index.css)

### V.3 Phase C — FullPageLoader Verification (audit only, NO edit)

**Step C1**: Read `pi-store-webapp/src/_shared/components/ui/FullPageLoader.jsx`. Confirm:
- Uses `bg-base-100` for backdrop (semantic token ✓)
- Uses `<Spinner size={32}>` primitive (semantic ✓)
- Caption styled `text-xs uppercase tracking-widest text-base-content/40` (Vercel pattern ✓)
- No `.css` import (FullPageLoader.css was deleted)

**Step C2**: Run `git show c6679fe -- src/_shared/components/ui/FullPageLoader.css 2>&1` to confirm deletion in T-027 commit.

**Step C3**: Document in Section XII (Evidence). Do NOT modify FullPageLoader.jsx unless a real defect is found.

If FullPageLoader.jsx does NOT meet criteria above → FILE A BLOCKING NOTE and stop. Do not silently "fix" — escalate to Claude.

---

## VI. 🛠️ Phases

### Phase 1: Baseline Audit (15 min)
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content"
git -C pi-store-webapp status --short
git -C pi-dashboard-webapp status --short
```
Both should be clean (no uncommitted changes). If not — abort and report.

```powershell
# Audit hardcode count BEFORE
Select-String -Path "pi-store-webapp\src\features\**\*.css","pi-store-webapp\admin\features\**\*.css","pi-store-webapp\admin\features\**\*.jsx" -Pattern '#[0-9a-fA-F]{3,8}' | Measure-Object
Select-String -Path "pi-dashboard-webapp\src\features\**\*.jsx","pi-dashboard-webapp\src\_shared\overlays\**\*.css" -Pattern '#[0-9a-fA-F]{3,8}' | Measure-Object
```
Record the BEFORE count for diff at end.

### Phase 2: Skeleton Restructure (15 min)
Execute Phase A steps A1 → A5 in order. After each step, run `npm run build` in pi-store-webapp to catch breakage early.

### Phase 3: Hardcode Sanitization (50 min)
Process pi-store files first, then pi-dashboard. After every 3 files, run `npm run build` for the affected app to catch breakage.

### Phase 4: Verification (10 min)
```powershell
# pi-store
cd pi-store-webapp
npm run lint
npm run build
git diff --name-only src/styles/      # MUST output NOTHING
git status --short

# pi-dashboard
cd ..\pi-dashboard-webapp
npm run lint
npm run build
git diff --name-only src/index.css src/animations.css   # MUST output NOTHING
git status --short

# Hardcode count AFTER
Select-String -Path "pi-store-webapp\src\features\**\*.css","pi-store-webapp\admin\features\**\*.css","pi-store-webapp\admin\features\**\*.jsx" -Pattern '#[0-9a-fA-F]{3,8}' | Measure-Object
Select-String -Path "pi-dashboard-webapp\src\features\**\*.jsx","pi-dashboard-webapp\src\_shared\overlays\**\*.css" -Pattern '#[0-9a-fA-F]{3,8}' | Measure-Object
```

### Phase 5: Build Artifact + Commit
```powershell
# pi-store-webapp uses Vercel prebuilt strategy — rebuild + commit build/
cd pi-store-webapp
npm run build
git add src/ admin/ build/
git commit -m "chore(pi-store): hardcode hunt + Skeleton primitive restructure (T-030)

- Move Skeleton primitive from _shared/components/ui/ to _shared/skeletons/
  to align with pi-dashboard pattern (semantic separation: primitives vs.
  feature compositions).
- Sanitize hardcoded hex/rgba in feature CSS and JSX, replacing with
  semantic theme tokens (--p, --bc, --su, --wa, --er, --in, --b1...4,
  --chart-1...6) per master dossier T-025 design contract.
- FullPageLoader.jsx verified migrated (no edit needed).
- LEGITIMATE color-data files (ColorPicker, OG image designer, BrandingCard)
  preserved per Exception List in T-030 section III.3."
git push origin main

# pi-dashboard-webapp uses standard Vercel build — no build/ commit
cd ..\pi-dashboard-webapp
git add src/
git commit -m "chore(pi-dashboard): hardcode hunt — replace hex/rgba with semantic tokens (T-030)

Sanitize hardcoded colors in feature JSX (charts, leads, performance,
analytics, system) and OnboardingTour.css. LEGITIMATE color-data files
(theme-manager, og-image) preserved per Exception List in T-030 section III.3."
git push origin main
```

---

## VII. 🔍 Mandatory Verification Commands

```powershell
# pi-store-webapp
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
npm run lint
npm run build
git diff --name-only src/styles/index.css
git status --short

# pi-dashboard-webapp
cd "..\pi-dashboard-webapp"
npm run lint
npm run build
git diff --name-only src/index.css src/animations.css
git status --short

# Cross-app hardcode delta
$beforeStore = 8  # baseline from Phase 1
$beforeDash = 17  # baseline from Phase 1
# After Phase 3, count again — should be <= 6 store, <= 8 dashboard (allowing for legitimate exceptions)
```

---

## VIII. ✅ Acceptance Criteria

- [ ] Skeleton primitive moved to `pi-store-webapp/src/_shared/skeletons/Skeleton.jsx`
- [ ] Old `pi-store-webapp/src/_shared/components/ui/Skeleton.jsx` deleted
- [ ] `pi-store-webapp/src/_shared/components/ui/index.js` no longer exports `Skeleton`
- [ ] All consumers of the primitive (≥1 file) updated to new import path
- [ ] Hardcode count in feature files reduced by ≥70% in pi-store (target: 8 → ≤2 files)
- [ ] Hardcode count in feature files reduced by ≥50% in pi-dashboard (target: 17 → ≤8 files)
- [ ] All exception list files preserved unchanged (verify via `git diff --name-only` does NOT include them)
- [ ] FullPageLoader.jsx verified migrated (Phase C evidence in section XII)
- [ ] `npm run build` exit 0 for BOTH apps
- [ ] `npm run lint` zero new errors for BOTH apps
- [ ] `git diff src/styles/index.css` ZERO changes (pi-store)
- [ ] `git diff src/index.css src/animations.css` ZERO changes (pi-dashboard)
- [ ] pi-store `build/` artifact rebuilt + committed
- [ ] Commits pushed to main on both repos

---

## IX. 📋 Worker Prompt (Copy-Paste for Codex 5.3)

```
You are Codex 5.3 executing T-20260518-030. Read:
1. .task-handoffs/active/T-20260518-025-claude-vercel-design-master.md (design spec)
2. .task-handoffs/active/T-20260518-030-codex-hardcode-hunt-skeleton-restructure.md (THIS file)

Execute Phases 1→5 in order. Do not exit scope. Do not touch any file in the
LEGITIMATE Exception List (section III.3) — even if hex colors are present.
For each color replacement, use the semantic token mapping table (V.2).
Run mandatory verification commands and paste RAW PowerShell output to
section XII. Commit + push direct to main per Phase 5 commands. Report
hardcode count BEFORE and AFTER in section XIII.

Critical reminders:
- pi-store-webapp uses Vercel prebuilt deploy: MUST rebuild + commit build/.
- pi-dashboard-webapp uses standard Vercel build: do NOT commit build/.
- index.css and animations.css are READ-ONLY.
- FullPageLoader.jsx audit only (Phase C) — do not edit unless defect found.
```

---

## X. 📥 Result (filled by worker)
Status: `not-started`

## XI. 📊 Quality Gates
| Gate | Status | Evidence |
|---|---|---|
| Build pi-store | 🏗️ pending | |
| Build pi-dashboard | 🏗️ pending | |
| Lint pi-store | 🧹 pending | |
| Lint pi-dashboard | 🧹 pending | |
| Theme preservation pi-store | 🎨 pending | git diff src/styles/index.css |
| Theme preservation pi-dashboard | 🎨 pending | git diff src/index.css src/animations.css |
| Skeleton restructure | 🦴 pending | new file exists + old deleted + consumers updated |
| Hardcode delta | 🔢 pending | BEFORE/AFTER count in §XIII |
| Exception list preserved | 🛡️ pending | git diff --name-only excludes exception list |
| FullPageLoader verified | 👁️ pending | section XII evidence |
| Prebuilt artifact (pi-store) | 📦 pending | build/ in commit |
| Push to main (both) | 🚀 pending | git log origin/main |

## XII. 📁 Evidence (raw)
```text
$ (pending — paste raw PowerShell output of all verification commands here)
```

## XIII. 📉 Diff Summary
| Metric | Before | After | Delta |
|---|---|---|---|
| pi-store hardcode files | 8 | ? | ? |
| pi-dashboard hardcode files | 17 | ? | ? |
| Skeleton.jsx in ui/ | 1 | 0 | -1 |
| Skeleton.jsx in skeletons/ | 0 | 1 | +1 |
| `!important` in HomeCTA.css | 27 | ? | ? |

## XIV. 🛡️ Orchestrator Decision
Status: `pending`

## XV. 🆘 Rollback
```powershell
# pi-store
cd pi-store-webapp
git revert HEAD --no-edit
git push origin main
npm run build
git add build/
git commit -m "rollback: rebuild after T-030 revert"
git push origin main

# pi-dashboard
cd ..\pi-dashboard-webapp
git revert HEAD --no-edit
git push origin main
```

## XVI. 📑 Change Log
- **2026-05-18 19:00**: Dossier drafted by claude (orchestrator).
