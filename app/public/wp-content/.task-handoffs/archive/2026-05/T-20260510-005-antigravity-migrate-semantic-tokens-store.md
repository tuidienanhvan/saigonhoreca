---
id: T-20260510-005
owner: antigravity
state: verified
priority: high
risk: medium
created: 2026-05-10T18:48:00+07:00
updated: 2026-05-10T18:48:00+07:00
---

# Migrate pi-store-webapp to Semantic Tokens (DaisyUI)

## 0. User Original Intent
> check và làm cho pi-store luôn
> lên dossier r làm, chưa lên dossier

## 1. Context & Scope
- **Problem**: `pi-store-webapp` đang dùng hệ thống token màu sắc cũ (legacy) như `text-text-1`, `bg-brand`,... Cần được đồng bộ hóa sang chuẩn Semantic Tokens của DaisyUI (giống như đã làm cho `pi-dashboard-webapp`) để đảm bảo tính nhất quán trên toàn bộ PI Ecosystem.
- **Goal**: Cập nhật `index.css` và migrate hàng loạt toàn bộ source code React component. Giữ lại legacy variables trong `:root` để chống lỗi hồi quy.
- **Allowed Scope**:
  - `c:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content/pi-store-webapp/src/**/*.jsx`
  - `c:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content/pi-store-webapp/src/**/*.css`

## 2. Evidence
Mass migration completed using Python script.
- **Files Migrated**: 44 files in Phase 1, 17 files in Phase 2 (total 61 files).
- **Core CSS updated**: `src/styles/index.css` refactored to Tailwind v4 semantic standards.
- **Build Verification**: `npm run build` success in 1.01s.

## 3. Review & Verification
- [x] Audit legacy colors (`white/`, `black/`)
- [x] Replace hardcoded colors with Semantic Tokens
- [x] Build pass (`npm run build`)
- [x] Scope check
- [x] Logic verify (Giao diện hiển thị bình thường, không vỡ layout/mất màu)

## 4. Notes & Out-of-scope
- Alias legacy được giữ trong `:root` nhưng mọi component phải dùng semantic classes mới.
