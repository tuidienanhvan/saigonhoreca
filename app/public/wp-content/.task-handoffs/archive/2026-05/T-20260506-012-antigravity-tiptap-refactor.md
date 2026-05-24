# 🛡️ DOSSIER: T-20260506-012-antigravity-tiptap-refactor

- **ID**: T-20260506-012
- **Agent**: Antigravity (Infinity Edition)
- **Scope**: `src/components/editor/TipTapEditor.jsx`, `src/components/editor/components/` [NEW]
- **Status**: GENESIS (Planning)

## 🎯 OBJECTIVE
Refactor `TipTapEditor.jsx` để tăng tính module hóa, dễ bảo trì và hoàn thiện logic "Active State" cho toàn bộ các nút bấm trên Toolbar.

## 🏗️ PROPOSED CHANGES

### 1. [NEW] `src/components/editor/components/EditorButton.jsx`
- Tách component `B` cũ ra thành file riêng.
- Hoàn thiện logic styling `active` cho toàn bộ các trạng thái: Bold, Italic, Align, Lists...

### 2. [NEW] `src/components/editor/components/MenuDropdowns.jsx`
- Chứa logic của `MenuDropdowns`.
- Quản lý các menu thả xuống: Format, Font Family, Font Size.

### 3. [NEW] `src/components/editor/components/Toolbar.jsx`
- Chứa toàn bộ giao diện thanh công cụ chính.
- Import và sử dụng `EditorButton`.

### 4. [MODIFY] `src/components/editor/TipTapEditor.jsx`
- Rút gọn file, chỉ giữ lại cấu hình `useEditor`, `extensions` và logic chính.
- Import các sub-components mới.

## 🧪 QUALITY GATES
- [ ] Build Gate: `npm run build` thành công.
- [ ] Logic Gate: Kiểm tra toàn bộ Toolbar buttons có hiển thị Active State khi quét text.
- [ ] Integrity Gate: Không làm mất tính năng Autosave, Zen Mode hay AI Integration.

## 📅 TIMELINE
1. Component Extraction: 10 mins.
2. Toolbar Refactor: 5 mins.
3. Integration & Testing: 5 mins.
