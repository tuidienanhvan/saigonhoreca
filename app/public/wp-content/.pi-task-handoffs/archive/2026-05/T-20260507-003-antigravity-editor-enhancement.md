# Dossier: Enhance Editor Toolbar & Features

ID: T-20260507-003
Owner: antigravity
State: returned
Priority: P1
Risk: low
Estimated Minutes: 60
Created: 2026-05-07
Updated: 2026-05-07

## Allowed Scope
- `wp-content/pi-dashboard-webapp/src/components/editor/components/Toolbar.jsx`
- `wp-content/pi-dashboard-webapp/src/components/editor/TipTapEditor.jsx`
- `wp-content/pi-dashboard-webapp/src/index.css`
- `wp-content/pi-dashboard-webapp/src/components/content/PublishWidget.jsx`
- `wp-content/pi-dashboard-webapp/src/components/content/EditorMain.jsx`
- `wp-content/pi-dashboard-webapp/src/main.jsx`
- `wp-content/pi-dashboard-webapp/shared/base/PageTitle.jsx`

## Out Of Scope
- Backend changes.

## Phases
1. **Audit**: Review current toolbar and Word ribbon reference. (Done)
2. **Implementation**:
    - Redesign Toolbar into a grouped Ribbon layout. (Done)
    - Add group labels (Font, Paragraph, Tools, Actions). (Done)
    - Implement Word-style Color & Highlight buttons. (Done)
    - Clean up unwanted UI elements (Cloud Sync, Fullscreen). (Done)
    - Make Link button more prominent in a dedicated group. (Done)
    - Add explicit CSS for links in editor to ensure visibility. (Done)
    - Update 'Lần cuối' label to 'Lần cập nhật cuối' in PublishWidget. (Done)
    - Fix Font Selector menu clipping by setting overflow-visible. (Done)
    - Increase article title font size to 5xl for better impact. (Done)
    - Install and configure react-helmet-async for dynamic page titles. (Done)
    - Create PageTitle component and update core pages. (Done)
3. **Verification**: Manual verification via editor. (Done - Lint Pass)

## Status Sync
- [x] Create dossier
- [x] Update STATUS.md
- [x] Implement Ribbon UI redesign
- [x] Verify










