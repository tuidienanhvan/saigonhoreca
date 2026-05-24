# 🛡️ DOSSIER: T-20260506-011-antigravity-editor-visual-fidelity

- **ID**: T-20260506-011
- **Agent**: Antigravity (Infinity Edition)
- **Scope**: `src/components/editor/TipTapEditor.jsx`, `src/index.css`, `src/components/content/EditorMain.jsx`
- **Status**: GENESIS (Planning)

## 🎯 OBJECTIVE
Nâng cấp toàn diện trải nghiệm thị giác (Visual Fidelity) bên trong lõi Editor. Đảm bảo các thành phần văn bản (H2, H3, Blockquote, Table) hiển thị đẹp, chuyên nghiệp và đúng tinh thần "Infinity Pro" như ảnh mẫu.

## 🏗️ PROPOSED CHANGES

### [2026-05-06 01:58] Hoàn tất Nâng cấp Typography
- **CSS Upgraded**: Đã định nghĩa lớp `.prose` tùy chỉnh trong `index.css` cho H2, H3, Blockquote và Table.
- **Editor Injected**: Đã đưa `prose prose-invert` vào trực tiếp thẻ contenteditable của TipTap.
- **Cleanup**: Gỡ bỏ các class layout dư thừa, tối ưu padding.

## 🧪 QUALITY GATES
- [x] Build Gate: `npm run build` thành công (Exit code 0).
- [x] Visual Gate: H2, H3 đã có font Lexend và viền brand sang trọng.
- [x] Table Gate: Cấu trúc bảng hiển thị hiện đại với glass-header.
- [x] Quote Gate: Blockquote có dải màu brand mượt mà.

## 📅 TIMELINE
1. Research & CSS Setup: 5 mins.
2. TipTap Refactor: 10 mins.
3. Verification: 5 mins.
