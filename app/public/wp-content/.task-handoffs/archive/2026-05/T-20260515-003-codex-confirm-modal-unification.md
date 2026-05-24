---
id: T-20260515-003
title: ConfirmModal — Unify across pi-dashboard + pi-store, migrate all window.confirm
owner: codex
state: archived
verified: 2026-05-15
verifier: claude
risk: medium
priority: P1
scope: pi-dashboard-webapp + pi-store-webapp — polish ConfirmModal primitive, sync 2 apps, migrate 26+ window.confirm call sites
created: 2026-05-15
---

# T-20260515-003 — ConfirmModal Unification

## 🎯 Goal
Hợp nhất confirm dialog logic giữa 2 app. Mục tiêu cuối:
- **1 base `<ConfirmModal>` component đồng nhất** ở cả pi-dashboard và pi-store (UI khung rõ ràng, contrast, header/body/footer tách)
- **0 `window.confirm` calls** còn lại — tất cả migrate sang `<ConfirmModal>`
- **Behavior nhất quán**: Esc / backdrop click / loading state / disable double-submit
- **API chuẩn hoá**: `title, message, confirmLabel, cancelLabel, variant, severity, onConfirm async-safe`

## 📊 Hiện trạng

### pi-dashboard-webapp
- ✅ Có `src/_shared/overlays/ConfirmModal/` (index.jsx + ConfirmModal.css)
- ⚠️ UI hiện tại "khá xấu, chưa có khung rõ ràng" (per user feedback)
- ⚠️ **26 chỗ vẫn dùng `window.confirm`** (audit đã có sẵn từ phiên trước)

### pi-store-webapp
- ✅ Có `admin/_shared/components/AdminConfirmDialog.jsx` (đã dùng cho admin delete)
- ✅ 0 `window.confirm` còn lại
- ⚠️ AdminConfirmDialog API khác ConfirmModal — không cross-compatible

## 🎨 Spec component thống nhất

File chuẩn (sync byte-identical hoặc near-identical 2 apps):

```jsx
<ConfirmModal
  open={boolean}
  onClose={() => void}
  onConfirm={async () => void}    // async-safe — modal hiển thị loading
  title="Xoá API key?"
  message="Key này sẽ bị thu hồi ngay lập tức. Không thể hoàn tác."
  confirmLabel="Xoá"               // default theo variant
  cancelLabel="Huỷ"                // default "Huỷ"
  variant="danger"                 // "danger" | "warning" | "info" | "success"
  icon={IconComponent}             // optional, default theo variant
  disabled={boolean}               // optional, disable confirm button
/>
```

### UI requirements (khung rõ ràng):

```
┌──────────────────────────────────────────┐
│  [icon]  Title                       [×] │  ← Header: icon + title, close X
├──────────────────────────────────────────┤
│                                          │
│  Message body — readable text-sm         │  ← Body: padding 20px
│  có thể multi-line.                      │
│                                          │
├──────────────────────────────────────────┤
│              [Huỷ]      [Xác nhận]       │  ← Footer: Hủy LEFT, Confirm RIGHT
└──────────────────────────────────────────┘
```

### CSS spec (tạo `ConfirmModal.css` mới hoặc rewrite):
```css
.confirm-modal {
  background: var(--b2);
  border: 1px solid var(--bd);
  border-radius: 16px;
  box-shadow: 0 30px 80px -20px rgba(0,0,0,0.5);
  width: 100%;
  max-width: 440px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.confirm-modal__header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 18px 20px;
  border-bottom: 1px solid var(--bd);
  background: color-mix(in srgb, var(--b1) 40%, transparent);
}
.confirm-modal__icon { /* tinted by variant */ }
.confirm-modal__title { font-size: 1rem; font-weight: 600; }
.confirm-modal__body  { padding: 20px; font-size: 0.875rem; color: var(--bc2); }
.confirm-modal__footer {
  display: flex;
  gap: 8px;
  padding: 12px 20px;
  border-top: 1px solid var(--bd);
  background: color-mix(in srgb, var(--b1) 30%, transparent);
}
.confirm-modal__footer .cancel { margin-right: auto; }  /* push confirm to right */
```

### Variant colors:
- `danger`: red icon + red confirm button (Trash icon default)
- `warning`: amber icon + amber confirm (AlertTriangle)
- `info`: blue icon + primary confirm (Info)
- `success`: green icon + primary confirm (CheckCircle)

### Behaviors:
1. **Esc** → call `onClose` (nếu không đang loading)
2. **Backdrop click** → call `onClose` (nếu không đang loading)
3. **Confirm click async**:
   - Set internal `loading = true`
   - Disable both buttons + show spinner trên Confirm button
   - Call `await onConfirm()`
   - Catch error → display inline alert trong body, không close
   - Success → `onClose()` automatic
4. **Disable double-click**: nếu loading, click tiếp không trigger
5. **Focus trap**: Tab cycle giữa Cancel + Confirm
6. **Auto-focus Cancel** trên mount (an toàn cho danger)

## 📋 Audit — 26 window.confirm chỗ cần migrate (pi-dashboard)

### High priority (destructive — top 6):
1. `src/features/system/ResetDB.jsx`
2. `src/features/system/components/api-keys/KeyRow.jsx`
3. `src/features/system/BackupRestore.jsx`
4. `src/features/system/PluginManager.jsx`
5. `src/features/system/AuditLog.jsx`
6. `src/features/system/LogViewer.jsx`

### Medium priority (5):
7. `src/features/system/CronScheduler.jsx`
8. `src/features/system/SitesManagement.jsx`
9. `src/features/system/ThemeManager.jsx`
10. `src/features/system/components/db-explorer/SavedQueryList.jsx`
11. `src/_shared/components/forms/FormCanvas.jsx`

### Domain (≥15):
12+. `features/ai/AiProviders.jsx`, `RagConfig.jsx`
    `features/analytics/*`
    `features/content/FindReplacePanel.jsx`
    `features/leads/LeadTimeline.jsx`
    `features/performance/CdnConfig.jsx`
    `features/seo/RobotsTxtEditor.jsx`
    + 8+ chỗ khác (codex grep find khi vào)

## 🚧 Constraints

1. **KHÔNG đụng `index.css`** (synced)
2. **KHÔNG đụng generic `Modal.jsx`** (base) — chỉ rewrite `ConfirmModal`
3. **API breaking change OK** — tất cả call sites đều migrate cùng, không cần backward compat
4. **PHẢI keep behavior khi đang loading**: Esc + backdrop click bị disable
5. **PHẢI async-safe onConfirm**: support `async () => await api.delete(...)` pattern
6. **PHẢI sync giữa 2 app**: 
   - pi-dashboard: `src/_shared/overlays/ConfirmModal/index.jsx`
   - pi-store: rewrite `admin/_shared/components/AdminConfirmDialog.jsx` → `ConfirmDialog.jsx` (rename hoặc symlink) + cùng signature
7. **Build PASS + ESLint clean** sau mỗi batch

## 📊 Phases

### Phase A — Polish ConfirmModal primitive (30min)
1. Rewrite `pi-dashboard-webapp/src/_shared/overlays/ConfirmModal/index.jsx` + `ConfirmModal.css` theo spec trên
2. Test mount với 4 variants (danger/warning/info/success)
3. Test async onConfirm với artificial 1s delay → loading state hiển thị
4. Sync sang pi-store: rewrite `admin/_shared/components/AdminConfirmDialog.jsx` cùng signature
5. Build PASS cả 2 app

### Phase B — Migrate high-priority destructive (45min)
Top 6 file pi-dashboard:
- ResetDB.jsx, KeyRow.jsx (api-keys), BackupRestore.jsx, PluginManager.jsx, AuditLog.jsx, LogViewer.jsx

Per file:
- Add `useState` cho `[confirmState, setConfirmState] = useState(null)`
- Replace `if (window.confirm("..."))` block bằng `setConfirmState({ action, message, ... })`
- Mount `<ConfirmModal open={!!confirmState} onConfirm={confirmState?.action} ... />` ở cuối component
- Verify build sau từng file

Commit: `feat(dashboard/system): migrate destructive confirms to ConfirmModal`

### Phase C — Migrate medium + domain (60min)
- 5 medium-priority files
- 15+ domain files (ai, content, seo, leads, performance, analytics)

Per batch (4-5 files):
- Migrate
- Build PASS
- Commit `feat(dashboard/<domain>): migrate confirms to ConfirmModal`

### Phase D — Verify (15min)
1. `grep -rn "window.confirm\|^\s*confirm(" pi-dashboard-webapp/src` → **must be 0**
2. Final build + ESLint cả 2 app
3. Manual smoke test:
   - Click 5 random destructive buttons → ConfirmModal mở đẹp, Esc đóng, backdrop đóng, async confirm hiển thị loading
   - Visual check: header/body/footer rõ ràng, Cancel trái + Confirm phải, variant color đúng
4. Update dossier với verified state

## ✅ Acceptance Criteria
- [ ] `ConfirmModal` rewrite trong pi-dashboard với header/body/footer khung rõ, 4 variants
- [ ] `AdminConfirmDialog`/`ConfirmDialog` trong pi-store sync cùng signature
- [ ] Async-safe: loading state + disable double-click verified
- [ ] Esc + backdrop disabled khi loading
- [ ] 0 `window.confirm` còn trong pi-dashboard-webapp/src (grep verify)
- [ ] 0 `window.confirm` còn trong pi-store-webapp (đã = 0, giữ nguyên)
- [ ] Build PASS cả 2 app
- [ ] ESLint clean cả 2 app

## 🔒 Out of Scope
- `index.css` changes
- Modal primitive (`Modal.jsx`) changes
- Toast/notification migration
- Drawer migration
- Form validation pattern
- Backend changes

## 📎 References
- Hiện tại: `pi-dashboard-webapp/src/_shared/overlays/ConfirmModal/index.jsx`
- Hiện tại: `pi-store-webapp/admin/_shared/components/AdminConfirmDialog.jsx`
- Modal primitive (đừng đụng): `pi-store-webapp/src/_shared/components/ui/Modal.jsx`
- User feedback gốc: "Confirm modal này còn khá xấu và chưa có khung rõ ràng" — đó là vấn đề chính phải fix

## ✅ Phase D Verification (Claude, 2026-05-15)
- `window.confirm` remaining: **0** in both pi-dashboard + pi-store ✅
- ConfirmModal files created/updated:
  - `pi-dashboard-webapp/src/_shared/overlays/ConfirmModal/index.jsx`
  - `pi-dashboard-webapp/src/_shared/overlays/ConfirmModal/ConfirmModal.css`
  - `pi-dashboard-webapp/src/_shared/overlays/ConfirmModal/useConfirmDialog.jsx` (new shared hook)
  - `pi-store-webapp/admin/_shared/components/AdminConfirmDialog.jsx` (synced signature)
- Build pi-dashboard: ✅ PASS 1.1s
- Build pi-store: ✅ PASS 739ms (after fixing 3 pre-existing bugs unrelated to Codex)
- ESLint cả 2 app: ✅ Clean
- 37 files migrated across pi-dashboard system/ai/analytics/content/leads/performance/seo domains

### 🔧 Claude corrective fixes (3 pre-existing bugs, không phải Codex)
- `admin/features/settings/components/CronCard.jsx:83` — `j.last_status?` → `j.last_status` (stray `?`)
- `admin/features/packages/PackageEditPage.jsx:347` — duplicate `</form></div>` removed
- `admin/features/releases/ReleaseUploadPage.jsx:167` — duplicate `</form></div>` removed

**Status**: CLOSED | Owner: Codex (all phases) + Claude (Phase D verify + 3 pre-existing fixes)
