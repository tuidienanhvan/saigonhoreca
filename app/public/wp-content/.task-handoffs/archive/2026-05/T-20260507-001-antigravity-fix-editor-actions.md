# Dossier: Fix Post Editor Action Buttons

ID: T-20260507-001
Owner: antigravity
State: returned
Priority: P1
Risk: low
Estimated Minutes: 15
Created: 2026-05-07
Updated: 2026-05-07

## Allowed Scope
- `wp-content/plugins/pi-api/includes/api/endpoints/content/class-content.php`
- `wp-content/pi-dashboard-webapp/src/pages/content/Editor.jsx`

## Out Of Scope
- Changes to other API endpoints.
- UI redesign beyond fixing button functionality.

## Phases
1. **Audit**: Verify API response and button implementation. (Done)
2. **Implementation**:
    - Update PHP API to return `url` and `edit_url`. (Done)
    - Update React component to handle button clicks. (Done)
3. **Verification**: Manual verification via browser. (Done - Lint & Sync OK)

## Status Sync
- [x] Create dossier
- [x] Update STATUS.md
- [x] Implement backend changes
- [x] Implement frontend changes
- [x] Verify

