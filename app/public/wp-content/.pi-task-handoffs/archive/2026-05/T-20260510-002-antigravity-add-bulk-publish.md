---
id: T-20260510-002
owner: antigravity
state: archived
priority: P2
risk: low
estimated_minutes: 30
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-10 14:21
updated: 2026-05-10 14:23
---

# 📋 T-20260510-002-antigravity-add-bulk-publish — Add Publish Action to Content List

## 0. User Original Intent
"thêm hành động xuất bản ở chọn tất cả" (Add publish action to select all) - 2026-05-10 14:17.

## 1. 📊 Frontmatter Fields & Risk Matrix

| Field | Values | Detailed Operational Description |
|---|---|---|
| `id` | `T-20260510-002` | 🆔 Unique identifier. |
| `owner` | `antigravity` | 👤 Specialist AI agent. |
| `state` | `drafted` | 🔄 Lifecycle status. |
| `priority` | `P2` | 🚥 Standard priority. |
| `risk` | `low` | ⚠️ Impact level. |

---

## 2. 🎯 Goal & Strategic Objective
The goal is to enhance the user experience in the content management dashboard by adding a "Publish" (Xuất bản) action to the bulk action dropdown (Select All) and individual row actions. This allows users to publish one or multiple drafts quickly without entering the full editor.

---

## 3. 📚 Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Core operational guidelines.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Project overview.

---

## 4. 🚧 Allowed Scope (Strict)
- 📄 `pi-dashboard-webapp/src/pages/content/ContentList.jsx`
- 📄 `plugins/pi-api/includes/api/endpoints/content/class-content.php` (if needed)

---

## 5. 🚫 Out Of Scope (Strictly Forbidden)
- ❌ **Unrelated Refactoring**: Do not clean up other parts of ContentList.
- ❌ **Styling Changes**: Maintain existing Tailwind v4 aesthetics.

---

## 6. 🛠️ Phases of Execution
1.  **Phase 1: Audit** 🔍
    - Verify existing bulk action logic in `ContentList.jsx`.
    - Check backend support for `publish` action in PHP.
2.  **Phase 2: Implementation** 🛠️
    - Update `handleRowAction` to support `publish` and `draft`.
    - Add "Xuất bản" button to row actions.
    - Ensure bulk action dropdown is correctly populated.
3.  **Phase 3: Internal Verification** 🧪
    - Manually verify bulk and row actions work as expected.
4.  **Phase 4: Standardized Reporting** 📤

---

## 7. 🔍 Verification Commands (Mandatory)
```powershell
# No automated tests available for this UI change. 
# Manual verification via browser tool is required.
```

---

## 8. ✅ Acceptance Criteria (Checklist)
- [ ] **Bulk Action**: "Xuất bản" works for multiple selected items.
- [ ] **Row Action**: "Xuất bản" button appears and works for individual items.
- [ ] **Technical Excellence**: No new lint errors.
- [ ] **Cleanliness**: No console.logs left behind.

---

## 9. 📋 Copy-Paste Prompt (Worker Instructions)
(To be populated if dispatched to a worker)

---

## 10. 📥 Agent Result (Populated by Orchestrator)
Status: `not-started`

---

## 📑 CHANGE LOG & AUDIT TRAIL
- **2026-05-10 14:21**: Dossier created.
