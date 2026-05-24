---
id: T-20260510-003
owner: antigravity
state: archived
priority: P1
risk: medium
estimated_minutes: 60
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Gemini]
created: 2026-05-10 15:20
updated: 2026-05-10 17:15
---

# 📋 T-20260510-003-antigravity-fix-store-css — Detailed Task Specification

## 0. User Original Intent
"lên plan fix full @[/pi-task-handoffs]" - bối cảnh là dọn dẹp hàng loạt class Tailwind "tào lao" (không tồn tại trong config hoặc sai cú pháp v4) như `text-text-*`, `bg-surface-base`, `glass`, v.v. trong `pi-store-webapp`.

## 1. 📊 Frontmatter Fields & Risk Matrix
(See frontmatter above)

---

## 2. 🎯 Goal & Strategic Objective
Standardize the Tailwind v4 theme tokens in `pi-store-webapp` and eliminate all "nonsense" utility classes. Ensure the codebase uses clean, predictable tokens like `text-1`, `text-2`, `bg-surface`, etc., instead of redundant ones like `text-text-1`.

---

## 3. ⚖️ Current State vs Expected State
- **Current**: Many components use `text-text-1`, `bg-glass-bg`, `text-text-secondary`, which are either redundant or invalid in Tailwind v4.
- **Expected**: All colors/backgrounds follow the standardized tokens defined in `index.css`. `index.css` is the single source of truth for the theme.

---

## 4. 📝 Proposed Changes & Allowed Scope

### [MODIFY] [index.css](file:///c:/Users/Administrator/Local%20Sites/saigonhouse/app/public/wp-content/pi-store-webapp/src/styles/index.css)
- Rename `--color-text-1` to `--color-1`, `--color-text-2` to `--color-2`, etc.
- Add `--color-primary` as an alias for `--color-brand`.
- Ensure `.glass` utility is accessible for `@apply` if needed, or stick to utility classes in JSX.

### [REFACTOR] All Components & CSS Files
- Global find and replace across `pi-store-webapp/src`:
  - `text-text-1` -> `text-1`
  - `text-text-2` -> `text-2`
  - `text-text-3` -> `text-3`
  - `text-text-primary` -> `text-1`
  - `text-text-secondary` -> `text-2`
  - `bg-glass-bg` -> `bg-surface/80 backdrop-blur-md` (or similar)
  - `bg-surface-base` -> `bg-surface`
  - `border-glass-border` -> `border-border`
  - `var(--color-text-primary)` -> `var(--text-1)`
  - `var(--color-text-secondary)` -> `var(--text-2)`

---

## 6. 🛠️ Phases of Execution
1.  **Phase 1: Token Standardization** 🛠️
    - Update `src/styles/index.css` with cleaner theme variables.
2.  **Phase 2: Global Cleanup** 🧹
    - Use `grep`/`ripgrep` to identify all occurrences of invalid classes.
    - Systematic replacement using `sed` or IDE tools.
3.  **Phase 3: Verification** 🧪
    - Run `npm run lint` to catch broken styles or invalid Tailwind syntax.
    - Run `npm run dev` and check build logs for "unknown utility" warnings.
    - Visual check of the UI to ensure no regressions in colors/layout.

---

## 8. ✅ Acceptance Criteria (Checklist)
- [x] **Zero Redundancy**: No more `text-text-*` classes in the codebase.
- [x] **Zero Warnings**: `npm run dev` starts without "Cannot apply unknown utility class" errors.
- [x] **Lint Clean**: `npm run lint` passes 100%.
- [x] **Theme Consistency**: Colors look exactly the same as before, but with better code quality.

---

## 9. 📥 Agent Result (Populated by Orchestrator)
Status: `verified`
Summary: 44 files were successfully refactored. `npm run lint` and `npm run build` completed successfully without any Tailwind utility errors.

## 10. Evidence
```text
> pi-store-webapp@2.0.0 lint
> eslint .
Exit code: 0

> pi-store-webapp@2.0.0 build
> vite build
vite v8.0.8 building client environment for production...
✓ built in 1.05s
Exit code: 0
```
