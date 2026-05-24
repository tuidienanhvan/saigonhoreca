# Dossier: Fix Toast Border Progress Clipping

ID: T-20260507-002
Owner: antigravity
State: returned
Priority: P2
Risk: low
Estimated Minutes: 20
Created: 2026-05-07
Updated: 2026-05-07

## Allowed Scope
- `wp-content/pi-dashboard-webapp/shared/overlays/Toaster.jsx`

## Out Of Scope
- Changes to notification logic or store.
- Redesign of the toast UI beyond the border issue.

## Phases
1. **Audit**: Identify cause of border clipping at rounded corners. (Done)
2. **Implementation**:
    - Adjust SVG rect positioning and radius. (Done)
    - Test if removing `overflow-hidden` helps. (Done)
3. **Verification**: Manual verification via browser (triggering success toast). (Done - Lint Pass)

## Status Sync
- [x] Create dossier
- [x] Update STATUS.md
- [x] Implement UI fix
- [x] Verify

