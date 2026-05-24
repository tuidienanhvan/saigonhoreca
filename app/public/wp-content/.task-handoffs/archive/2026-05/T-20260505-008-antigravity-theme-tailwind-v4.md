# 🛡️ DOSSIER: T-20260505-008-antigravity-theme-tailwind-v4

- **ID**: T-20260505-008
- **Agent**: Antigravity (Infinity Edition)
- **Scope**: `themes/saigonhouse-theme/`
- **Status**: IN_PROGRESS

## 🎯 OBJECTIVE
Chuyển đổi hoàn toàn SaigonHouse WordPress Theme sang Tailwind CSS v4 và thống nhất Design System:
1. **Loại bỏ tiền tố `--pi-`**: Chuyển sang bộ biến chuẩn `--brand`, `--bg`, `--text-1`,... đồng bộ với Dashboard/Store.
2. **Kích hoạt Tailwind v4**: Chuyển đổi `assets/css/style.css` sang chuẩn v4 (`@import "tailwindcss"`).
3. **Refactor Base & Typography**: Đưa các styles đặc thù của WordPress vào `@layer base` và `@layer components`.
4. **Build Optimization**: Đảm bảo quy trình biên dịch qua `@tailwindcss/cli` hoạt động mượt mà.

## 🏗️ EXECUTION LOG

### [2026-05-05 23:55] Hoàn tất Chuyển đổi & Refactor
- Cấu hình thành công `style.css` theo chuẩn Tailwind v4 với `@source` quét toàn bộ theme.
- Loại bỏ 100% tiền tố `--pi-` trong các file PHP, CSS, JS.
- Thống nhất bộ Token Infinity Pro (Light/Dark mode) đồng bộ với Webapp.
- Vô hiệu hóa `utilities.css` cũ để tối ưu hóa performance và tránh xung đột.
- Chạy `npm run build`: Thành công (Exit code 0).

## 🧪 QUALITY GATES
- [x] Build Gate: `npm run build` (PASSED)
- [x] Integrity Gate: Không còn biến `--pi-`, kế thừa hoàn hảo Gutenberg list styles.
- [x] Alignment Gate: Theme Tokens khớp 100% với Webapp Tokens.
