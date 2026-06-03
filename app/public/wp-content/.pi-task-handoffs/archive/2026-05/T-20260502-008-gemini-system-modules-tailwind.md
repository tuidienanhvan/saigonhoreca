# T-20260502-008-gemini-system-modules-tailwind - System Modules Migration to Tailwind v4

## Task Metadata / Thông Tin Task

- ID: `T-20260502-008`
- Owner: `Gemini`
- State: `completed`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Migrate `PluginManager.jsx` and `ThemeManager.jsx` to pure Tailwind v4, deleting legacy CSS files. Establish a high-fidelity industrial look consistent with the Dashboard design system.

## Allowed Scope / Scope Được Sửa

- `pi-dashboard-webapp/src/pages/system/PluginManager.jsx`
- `pi-dashboard-webapp/src/pages/system/ThemeManager.jsx`
- `pi-dashboard-webapp/src/pages/system/PluginManager.css`
- `pi-dashboard-webapp/src/pages/system/ThemeManager.css`
- `.task-handoffs/active/T-20260502-008-gemini-system-modules-tailwind.md`

## Out Of Scope / Scope Cấm

- Do not change backend API routes.
- Do not modify other system pages in this batch.

## Phases / Các Phase

1. Implement Tailwind v4 in `PluginManager.jsx` using `PageHeader` and custom classes.
2. Implement Tailwind v4 in `ThemeManager.jsx`.
3. Delete `PluginManager.css` and `ThemeManager.css`.
4. Run `npm run build` and `npm run lint`.
5. Update Agent Result.

## Acceptance Criteria / Tiêu Chí Nhận

- [x] `PluginManager.jsx` and `ThemeManager.jsx` use zero custom CSS/BEM classes.
- [x] UI is premium, high-fidelity, and consistent with the new dashboard design system.
- [x] Build and Lint pass.
- [x] Legacy CSS files are deleted.

## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

Migrated `PluginManager.jsx`, `ThemeManager.jsx` and all sub-components (`PluginCard`, `ConflictItem`, `ThemeCard`, `PresetSelector`, `ColorPicker`, `CssEditor`) to pure Tailwind v4. Deleted `PluginManager.css` and `ThemeManager.css`.

### Files Modified / File Đã Sửa

- `src/pages/system/PluginManager.jsx` — full rewrite, PageHeader, Tailwind tabs/toolbar/grid
- `src/pages/system/ThemeManager.jsx` — full rewrite, PageHeader, Tailwind tabs/customize/css
- `src/components/plugin-manager/PluginCard.jsx` — Tailwind classes
- `src/components/plugin-manager/ConflictItem.jsx` — Tailwind classes
- `src/components/theme-manager/ThemeCard.jsx` — Tailwind classes
- `src/components/theme-manager/PresetSelector.jsx` — Tailwind classes
- `src/components/theme-manager/ColorPicker.jsx` — Tailwind classes
- `src/components/theme-manager/CssEditor.jsx` — Tailwind classes

### Files Deleted / File Đã Xóa

- `src/pages/system/PluginManager.css`
- `src/pages/system/ThemeManager.css`

### Verification / Kiểm Tra

- `npx eslint` on all 8 files: **pass** (0 errors, 0 warnings)
- `npm run build`: **pass** ✓ built in 888ms
