---
id: T-20260518-031
owner: claude
state: completed
priority: P1
risk: high
estimated_minutes: 120
parent: T-20260518-025
children: []
depends_on: [T-20260518-030]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, User]
created: 2026-05-18 20:00
updated: 2026-05-18 20:00
---

# 📋 T-20260518-031 | claude | pi-dashboard-skeleton-distribution — Distribute pi-dashboard `_shared/skeletons/` into Feature Folders

## I. 🎯 Goal

Per user architectural principle: **`_shared/` cannot know what each feature needs for skeletons** — each feature must own its skeleton folder. The current `pi-dashboard-webapp/src/_shared/skeletons/` (33 files) violates this principle.

Mirror the fix already applied to `pi-store-webapp` (commit `29f18c0` — removed `_shared/skeletons/` entirely).

For pi-dashboard, the structure is larger (15 component-skeletons + 16 page-skeletons + 1 test + 2 indexes), so distribution requires careful mapping.

## II. 📚 Required Reading

- `.task-handoffs/active/T-20260518-025-claude-vercel-design-master.md`
- pi-store commit `29f18c0` for reference fix pattern

## III. 📊 Pre-Audit (Consumer Map)

Completed by grep across `pi-dashboard-webapp/src/features/`:

### III.1 Dead code (0 consumers — DELETE)
| File | Status |
|---|---|
| `SkeletonKanban.jsx` | DEAD |
| `SkeletonTable.jsx` | DEAD |
| `SkeletonCoreOverviewPage.jsx` | DEAD |
| `SkeletonDataTablePage.jsx` | DEAD |
| `SkeletonMasterDetail.jsx` | DEAD |
| `SkeletonWizard.jsx` | DEAD |

**6 files to delete.**

### III.2 Page-skeletons → feature owners (11 files)
| File | New home |
|---|---|
| `SkeletonAdminAuditLogPage.jsx` | `features/system/skeleton/SkeletonAdminAuditLogPage.jsx` |
| `SkeletonAiWorkspacePage.jsx` | `features/ai/skeleton/SkeletonAiWorkspacePage.jsx` |
| `SkeletonContentWorkspacePage.jsx` | `features/content/skeleton/SkeletonContentWorkspacePage.jsx` |
| `SkeletonCoreAnalyticsPage.jsx` | `features/analytics/skeleton/SkeletonCoreAnalyticsPage.jsx` |
| `SkeletonLeadsWorkspacePage.jsx` | `features/leads/skeleton/SkeletonLeadsWorkspacePage.jsx` |
| `SkeletonLicenseManagerPage.jsx` | `features/system/skeleton/SkeletonLicenseManagerPage.jsx` |
| `SkeletonSeoWorkspacePage.jsx` | `features/seo/skeleton/SkeletonSeoWorkspacePage.jsx` |
| `SkeletonSitesManagementPage.jsx` | `features/system/skeleton/SkeletonSitesManagementPage.jsx` |
| `SkeletonSystemHealthPage.jsx` | `features/system/skeleton/SkeletonSystemHealthPage.jsx` |
| `SkeletonSystemToolsPage.jsx` | `features/system/skeleton/SkeletonSystemToolsPage.jsx` |
| `SkeletonUsersRolesPage.jsx` | `features/system/skeleton/SkeletonUsersRolesPage.jsx` |

### III.3 Cross-feature page-skeletons (2 files)
| File | Decision |
|---|---|
| `SkeletonCorePerformancePage.jsx` | DUPLICATE into `features/performance/skeleton/` AND `features/billing/skeleton/` (used by both, distinct visual ownership) |
| `SkeletonSettingsLayout.jsx` | DUPLICATE into `features/seo/skeleton/` AND `features/system/skeleton/` (settings layout used in both feature contexts) |

Rationale: cross-feature imports create coupling; duplication for small files (<150 LOC) is acceptable maintenance cost.

### III.4 Single-feature component-skeletons (5 files)
| File | New home |
|---|---|
| `SkeletonChartPanel.jsx` | `features/ai/skeleton/components/SkeletonChartPanel.jsx` |
| `SkeletonList.jsx` | `features/system/skeleton/components/SkeletonList.jsx` |
| `SkeletonRect.jsx` | `features/auth/skeleton/components/SkeletonRect.jsx` |
| `SkeletonText.jsx` | `features/auth/skeleton/components/SkeletonText.jsx` |
| `SkeletonTree.jsx` | `features/system/skeleton/components/SkeletonTree.jsx` |

Test file `SkeletonText.test.jsx` follows to `features/auth/skeleton/components/SkeletonText.test.jsx`.

### III.5 Cross-feature component-skeletons (7 files)
These are UI primitives used by 2-4 features. Per user principle (shared cannot have skeleton folder) BUT these ARE genuinely shared atomic UI building blocks (like Button, Card, Spinner). Best home: `_shared/components/ui/` alongside other UI primitives.

| File | Consumers | New home |
|---|---|---|
| `SkeletonCard.jsx` | overview, system (2 features) | `_shared/components/ui/SkeletonCard.jsx` |
| `SkeletonCardGrid.jsx` | overview, system | `_shared/components/ui/SkeletonCardGrid.jsx` |
| `SkeletonCrudTable.jsx` | ai, content, seo, system (4) | `_shared/components/ui/SkeletonCrudTable.jsx` |
| `SkeletonEditor.jsx` | content, seo, system (3) | `_shared/components/ui/SkeletonEditor.jsx` |
| `SkeletonFeed.jsx` | seo, system (2) | `_shared/components/ui/SkeletonFeed.jsx` |
| `SkeletonForm.jsx` | ai, seo, system (3) | `_shared/components/ui/SkeletonForm.jsx` |
| `SkeletonStatStrip.jsx` | ai, overview, system (3) | `_shared/components/ui/SkeletonStatStrip.jsx` |

**Justification for `_shared/components/ui/`**: These are atomic UI primitives, not feature-page skeletons. They follow the same pattern as `Spinner`, `Loader`, etc. The user's principle targets `_shared/skeletons/` (a feature-pretending shared folder), not legitimate UI primitives.

## IV. 🚧 Allowed Scope

```
# CREATE (new feature skeleton files)
pi-dashboard-webapp/src/features/ai/skeleton/SkeletonAiWorkspacePage.jsx
pi-dashboard-webapp/src/features/ai/skeleton/components/SkeletonChartPanel.jsx
pi-dashboard-webapp/src/features/analytics/skeleton/SkeletonCoreAnalyticsPage.jsx
pi-dashboard-webapp/src/features/auth/skeleton/components/SkeletonRect.jsx
pi-dashboard-webapp/src/features/auth/skeleton/components/SkeletonText.jsx
pi-dashboard-webapp/src/features/auth/skeleton/components/SkeletonText.test.jsx
pi-dashboard-webapp/src/features/billing/skeleton/SkeletonCorePerformancePage.jsx
pi-dashboard-webapp/src/features/content/skeleton/SkeletonContentWorkspacePage.jsx
pi-dashboard-webapp/src/features/leads/skeleton/SkeletonLeadsWorkspacePage.jsx
pi-dashboard-webapp/src/features/performance/skeleton/SkeletonCorePerformancePage.jsx
pi-dashboard-webapp/src/features/seo/skeleton/SkeletonSeoWorkspacePage.jsx
pi-dashboard-webapp/src/features/seo/skeleton/SkeletonSettingsLayout.jsx
pi-dashboard-webapp/src/features/system/skeleton/SkeletonAdminAuditLogPage.jsx
pi-dashboard-webapp/src/features/system/skeleton/SkeletonLicenseManagerPage.jsx
pi-dashboard-webapp/src/features/system/skeleton/SkeletonSettingsLayout.jsx
pi-dashboard-webapp/src/features/system/skeleton/SkeletonSitesManagementPage.jsx
pi-dashboard-webapp/src/features/system/skeleton/SkeletonSystemHealthPage.jsx
pi-dashboard-webapp/src/features/system/skeleton/SkeletonSystemToolsPage.jsx
pi-dashboard-webapp/src/features/system/skeleton/SkeletonUsersRolesPage.jsx
pi-dashboard-webapp/src/features/system/skeleton/components/SkeletonList.jsx
pi-dashboard-webapp/src/features/system/skeleton/components/SkeletonTree.jsx
pi-dashboard-webapp/src/_shared/components/ui/SkeletonCard.jsx
pi-dashboard-webapp/src/_shared/components/ui/SkeletonCardGrid.jsx
pi-dashboard-webapp/src/_shared/components/ui/SkeletonCrudTable.jsx
pi-dashboard-webapp/src/_shared/components/ui/SkeletonEditor.jsx
pi-dashboard-webapp/src/_shared/components/ui/SkeletonFeed.jsx
pi-dashboard-webapp/src/_shared/components/ui/SkeletonForm.jsx
pi-dashboard-webapp/src/_shared/components/ui/SkeletonStatStrip.jsx

# DELETE (entire shared skeletons folder)
pi-dashboard-webapp/src/_shared/skeletons/  (whole directory recursive)

# MODIFY (consumer imports — ~50 files)
pi-dashboard-webapp/src/features/**/*.jsx  (only import line changes for skeletons)

# Self-tracking
.task-handoffs/active/T-20260518-031-claude-pi-dashboard-skeleton-distribution.md
```

## V. 🚫 Out Of Scope

- ❌ `pi-dashboard-webapp/src/index.css` — theme palette
- ❌ `pi-dashboard-webapp/src/animations.css` — animation system
- ❌ Component logic / props (only IMPORT path changes + file moves)
- ❌ `pi-store-webapp/**` — already cleaned in T-030
- ❌ Refactoring skeleton internals (just MOVE, don't redesign)
- ❌ Updating existing feature `skeleton/index.js` barrel files (will add to existing exports)

## VI. 🛠️ Phases

### Phase 1: Audit baseline + create folder structure
- `git status --short` should be clean
- Create needed `features/<name>/skeleton/components/` subfolders for: ai, auth, system

### Phase 2: Move 11 single-feature page-skeletons + 2 cross-feature page-skeletons
- Copy each file content to new location
- Update barrel `features/<name>/skeleton/index.js` to re-export new files

### Phase 3: Move 5 single-feature component-skeletons + test file
- Copy to feature-specific `components/` subfolder
- Add component-level barrel `features/<name>/skeleton/components/index.js`
- Update feature skeleton barrel to re-export components

### Phase 4: Move 7 cross-feature component-skeletons to ui/
- Copy to `_shared/components/ui/`
- Update `_shared/components/ui/index.js` to export new primitives

### Phase 5: Update consumer imports (~50 files)
- For each old import `from '@pi-ui/skeletons/page-skeleton/SkeletonX'` → new path
- For each old import `from '@pi-ui/skeletons/component-skeleton/SkeletonX'` → new path
- Single-shot grep-replace per file, verify with build after each major group

### Phase 6: Delete `_shared/skeletons/` entirely

### Phase 7: Build + lint + commit + push
```powershell
cd pi-dashboard-webapp
npm run lint
npm run build
git add src/
git commit -m "feat(pi-dashboard): distribute _shared/skeletons/ into feature folders (T-031)"
git push origin main
```

## VII. ✅ Acceptance Criteria

- [ ] `src/_shared/skeletons/` folder no longer exists
- [ ] 11 page-skeletons relocated to single-owner features
- [ ] 2 cross-feature page-skeletons duplicated to both consumers
- [ ] 5 single-feature component-skeletons relocated with new `components/` subfolders
- [ ] 7 cross-feature component-skeletons moved to `_shared/components/ui/`
- [ ] 6 dead-code files deleted
- [ ] `_shared/components/ui/index.js` updated to export 7 new primitives
- [ ] All ~50 consumer imports updated
- [ ] `npm run build` exit 0
- [ ] `npm run lint` zero new errors
- [ ] `git diff src/index.css src/animations.css` ZERO changes
- [ ] Commit pushed to main

## VIII. 📑 Change Log
- **2026-05-18 20:00**: Dossier drafted by claude (orchestrator + executor).
