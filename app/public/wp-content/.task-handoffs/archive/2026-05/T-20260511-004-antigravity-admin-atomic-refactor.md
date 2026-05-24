---
id: T-20260511-004-antigravity-admin-atomic-refactor
owner: antigravity
state: verified
priority: high
risk: medium
estimated_minutes: 45
parent: null
children: []
depends_on: []
parallelization_ok: false
created: 2026-05-11T11:16:15Z
updated: 2026-05-11T11:16:15Z
---

# 🛡️ DOSSIER: Admin Atomic Refactor (v1.0)

## 0. User Original Intent
"Đừng, phần bên phải thì tách ra. lên plan chi tiết refactor toàn bộ theo atomic design"
- Refactor the Admin Dashboard structure in `pi-store-webapp`.
- Follow **Atomic Design** principles (Atoms, Molecules, Organisms).
- Separate the Sidebar and the Header/Content Area into dedicated files/organisms.
- Ensure a premium, integrated aesthetic (docked sidebar).

## 1. Allowed Scope
- `pi-store-webapp/src/components/admin/**/*` (New directories for atoms/molecules/organisms)
- `pi-store-webapp/src/pages/core/AdminLayout.jsx`
- `pi-store-webapp/src/components/core/DashboardLayout.css` (Already partially modified)

## 2. Out Of Scope
- Modifying `UserLayout` or `DashboardSidebar` (generic version).
- Changing Backend APIs or data structures.
- Modifying `pi-dashboard-webapp` components.

## 3. Implementation Plan (Phases)

### Phase A: Drafting & Infrastructure
- Create folder structure: `src/components/admin/atoms`, `molecules`, `organisms`.
- Extract common logic into Atoms.

### Phase B: Implementation (Atomic Build)
1. **Atoms**: Create `SidebarLink`, `NavGroupLabel`.
2. **Molecules**: Create `NavGroup`, `AdminUserFooter`, `AdminHeaderTools`.
3. **Organisms**: Create `AdminSidebar` (Organism), `AdminHeader` (Organism).
4. **Template**: Update `AdminLayout.jsx` to assemble these organisms.

### Phase C: Review & Verification
- `npm run lint` & `npm run build` validation.
- Visual audit of sidebar docking and header responsiveness.

### Phase D: Archiving
- Standard archive process after user approval and verification.

## 4. Evidence (Implementation Log)

- **Phase A**: Created directories `src/components/admin/atoms`, `molecules`, `organisms`.
- **Phase B**: Implemented 2 Atoms, 3 Molecules, 2 Organisms and updated the `AdminLayout` template.
- **Phase C**: 
    - `npm run lint`: PASSED (Exit code 0)
    - `npm run build`: PASSED (Exit code 0, 1.14s)
    - Architecture verified: Sidebar and Header are fully separated into Atomic components.
    - Aesthetic: Sidebar is now docked (marginless) as requested.

## 5. Escalation
- None. Task completed successfully.

---
**Mantra**: _"Code in English, Think in Logic, Record in Dossier, Speak in Vietnamese."_
