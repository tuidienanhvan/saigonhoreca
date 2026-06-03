# V1-Controls and Custom CSS Migration Cluster

- ID: `T-20260502-014`
- Owner: `Gemini`
- State: `active`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.pi-task-handoffs/project/PROJECT.md`

## Context / Bối Cảnh

The dashboard still contains ~15 legacy CSS files and hundreds of custom classes (`v1-*`, `pi-*`, `sgh-*`, etc.). This task targets the elimination of `v1-controls.css` and other custom CSS clusters to move towards a Pure Tailwind architecture.

## Acceptance Criteria / Tiêu Chí Nhận

- [ ] Identify all components still using `v1-*` classes from `v1-controls.css`.
- [ ] Migrate `v1-controls` utilities to standard Tailwind v4 tokens.
- [ ] Migrate custom CSS in `Leads` module (`FormBuilder.css`, `LeadEnrichment.css`).
- [ ] Migrate shared styles in `src/styles/components.css` and `src/styles/app.css`.
- [ ] Delete successfully migrated CSS files.
- [ ] Ensure visual fidelity is maintained or improved (Glassmorphism + Premium feel).
- [ ] Build and Lint pass.

## Agent Result / Kết Quả Agent

Status: `in-progress`

### Summary / Tóm Tắt
(To be filled by the agent)
