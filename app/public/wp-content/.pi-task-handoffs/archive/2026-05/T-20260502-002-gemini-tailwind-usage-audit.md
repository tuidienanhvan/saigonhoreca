# T-20260502-002 - Gemini: Tailwind Usage Audit + Migration Plan

## Task Metadata / Thông Tin Task

- ID: `T-20260502-002`
- Owner: `Gemini`
- State: `archived`
- Created: `2026-05-02`
- Archived: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Audit the real Tailwind CSS usage in `pi-dashboard-webapp` and `pi-store-webapp`, then produce a practical migration plan.

Kiểm tra thật hai webapp đã dùng Tailwind tới đâu, còn phụ thuộc custom CSS/BEM bao nhiêu, và đề xuất lộ trình migrate an toàn.

## Scope / Phạm Vi

Allowed inspection:

- `pi-dashboard-webapp/`
- `pi-store-webapp/`

Allowed edit:

- This task dossier only.

Out of scope:

- No app source edits.
- No CSS migration.
- No UI redesign.
- No plugin edits.
- No build artifact changes.

## Agent Result / Kết Quả Agent

Status: `completed`

Gemini reported:

- `pi-dashboard-webapp` has Tailwind installed and wired, but real JSX usage is minimal compared with custom CSS/BEM classes.
- `pi-store-webapp` is hybrid: admin areas use Tailwind more, while auth/public areas still contain custom BEM CSS.
- Full migration is not recommended.
- Recommended direction is Option B/C:
  - new components use Tailwind,
  - migrate shared primitives first,
  - migrate Store before Dashboard,
  - migrate Dashboard shell/nav last,
  - delete old CSS only after visual regression checks.

## Gemini Report Highlights / Điểm Chính Từ Report Gemini

### Dashboard

- Tailwind version: `4.0.0`.
- Vite plugin: `@tailwindcss/vite` active.
- CSS entry: `src/index.css`.
- `@import "tailwindcss"` exists.
- CSS-first Tailwind v4 config; no `tailwind.config.*`.
- Reported custom-heavy class prefixes: `pi-`, `sgh-`, `og-`, `lic-`, `api-`, `login-`.
- Largest CSS files reported: `help.css`, `DbExplorer.css`, `stat-cards.css`, `AiCloud.css`, `components.css`, `v1-controls.css`.

### Store

- Tailwind version: `4.2.4`.
- Vite plugin: `@tailwindcss/vite` active.
- CSS entry: `src/styles/index.css`.
- `@import "tailwindcss"` exists.
- CSS-first Tailwind v4 config; no `tailwind.config.*`.
- Hybrid usage: admin pages are closer to Tailwind, while auth/public pages still use custom CSS.
- Reported custom-heavy prefixes: `auth-`, `tier-`, `feature-`, `marketing-`, `product-`.

## Codex Review / Codex Kiểm Tra

Status: `pass with warnings`

### Scope Review / Kiểm Scope

Pass with correction:

- No app source edit was needed for this audit.
- `pi-store-webapp/build` had build artifact churn during review; Codex restored tracked build files and removed untracked generated build files.
- Active task file from Gemini was mojibake when read in PowerShell, so Codex rewrote this archived dossier cleanly in UTF-8.

### Evidence Review / Kiểm Bằng Chứng

Codex independently verified the setup and broad usage trend with PowerShell native commands because `rg.exe` returned `Access is denied` in this environment.

Dashboard independent check:

```text
JSX files: 254
CSS files: 48
Custom class lines matching sgh-/og-/lic-/api-/login-: 345
Tailwind-like class lines by broad heuristic: 521
Tailwind setup matches: 6
```

Tailwind setup evidence:

```text
package.json: @tailwindcss/vite ^4.0.0
package.json: tailwindcss ^4.0.0
vite.config.js: imports and enables @tailwindcss/vite
src/index.css: @import "tailwindcss"
src/index.css: @theme block
```

Store independent check:

```text
JSX files: 82
CSS files: 49
Custom class lines matching auth-/marketing-/tier-/feature-/product-: 103
Tailwind-like class lines by broad heuristic: 346
Tailwind setup matches: 6
```

Tailwind setup evidence:

```text
package.json: @tailwindcss/vite ^4.2.4
package.json: tailwindcss ^4.2.4
vite.config.js: imports and enables @tailwindcss/vite
src/styles/index.css: @import "tailwindcss"
src/styles/index.css: @theme block
```

### Important Caveat / Lưu Ý Quan Trọng

The exact numeric counts should be treated as an audit baseline, not a final truth table.

Các con số count nên xem là baseline audit, không phải số tuyệt đối, vì:

- broad Tailwind regex can overcount utility-like fragments,
- BEM/custom prefix count depends on selected prefixes,
- earlier source-tree refactors left the repos dirty before this task started.

The conclusion is still valid:

- Dashboard is not Tailwind-first yet.
- Store is hybrid.
- Full migration is high risk.
- Gradual migration by primitives and low-risk surfaces is the correct direction.

### Recommended Strategy / Chiến Lược Được Chấp Nhận

Accepted:

1. New components use Tailwind utilities by default.
2. Keep existing custom CSS unless a component is intentionally refactored.
3. Migrate shared primitives first.
4. Migrate Store admin/public components before Dashboard shell.
5. Keep Dashboard shell/nav, iframe mode, virtualized tables, editor, charts, and drag/drop surfaces until late stages.
6. Delete CSS only when no imports/classes remain and visual regression checks pass.

### Gate Review / Kiểm Gates

No app build/test/lint required, because this task was audit-only and source files should not change.

Docs/encoding:

```text
Archived dossier rewritten by Codex in UTF-8 with Vietnamese diacritics.
```

Build artifacts:

```text
pi-store-webapp/build restored after review.
git status --short build -> no output
```

### Decision / Quyết Định

Accepted as `pass with warnings`.

Warnings:

- Gemini task file output was mojibake.
- Gemini's exact counts should not be treated as authoritative without the specific scripts used.
- `rg.exe` was unavailable for Codex verification due `Access is denied`; Codex used PowerShell fallback commands.

## Follow-Up / Việc Tiếp Theo

Create a separate implementation task only if the user wants migration work. The first implementation task should be small and safe:

- Tailwind convention doc + token bridge review, or
- migrate one shared primitive component, with screenshot before/after.
