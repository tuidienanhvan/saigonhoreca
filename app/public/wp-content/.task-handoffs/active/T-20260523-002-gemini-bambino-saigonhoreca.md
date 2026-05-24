---
id: T-20260523-002
owner: gemini
state: verified
priority: P2
risk: medium
estimated_minutes: 45
parent: T-20260523-001
children: []
depends_on: []
parallelization_ok: true
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-23 09:36
updated: 2026-05-23 09:39
---

# 📋 T-20260523-002 | gemini | bambino-saigonhoreca — Hồ Sơ Thiết Kế Độc Bản / Bespoke Task Specification

## I. 📊 Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-20260523-002` | 🆔 Định danh duy nhất của task con. |
| `owner` | `gemini` | 👤 Agent thực thi: Antigravity. |
| `state` | `drafted` | 🔄 Vòng đời: **drafted**. |
| `priority` | `P2` | 🚥 **P2**: Tiêu chuẩn. |
| `risk` | `medium` | ⚠️ Tác động: **medium**. |

---

## II. 🎯 Mục tiêu và Chiến lược / Goal & Strategic Objective

Mục tiêu tối thượng là nâng cấp visual cho dự án **Bambino Superclub** thoát khỏi sự rập khuôn phẳng lì Elementor cũ, mang lại trải nghiệm thị giác độc bản theo đúng nhóm thiết kế nghệ thuật **Cyberpunk Neon Glow (Club & Lounge)**.

### 🌟 Ý tưởng độc bản:
- **Định vị cốt lõi**: Midnight Neon Grid Panel - Tông tối sâu thẳm, đường viền phát sáng neon tím huyền ảo đan chéo chấn cơ khí cực mạnh, card kính tối (dark glassmorphism) bo góc 24px lồng ảnh quầy bar thực tế với độ phân giải cao. Khung ảnh bọc viền mạ chrome lấp lánh phản chiếu ánh đèn club tạo không gian âm nhạc sống động.
- **Intro & Concept**: Layout Intro bất đối xứng với một cột chữ Serif và một cột ảnh bọc trong khung viền neon tím mịn màng. Concept sử dụng lưới 3 ô so le phát sáng nhẹ khi hover.
- **Parallax GPU 3D**: Tắt overlay đen gradient, nâng cao min-height 80vh giúp ảnh to sáng bừng 100%, ghim phần cứng bằng clip-path inset(0) siêu mượt trên Safari iOS.
- **CTA gộp**: Tích hợp hình ảnh Cinemascope thực tế và thông tin liên hệ thành **Integrated Split CTA Card** (1 hàng 2 cột gọn gàng) có nút trượt sáng Shimmer lấp lánh.

---

## III. 📚 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án, tech stack và quy ước.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu kỹ thuật.

---

## IV. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/concept.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/cta.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/gallery.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/hero.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/intro.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/partnership.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/related.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/specs.php`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/concept.css`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/cta.css`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/gallery.css`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/hero.css`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/intro.css`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/partnership.css`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/related.css`
- 📄 `template-parts/project-pillar/bambino-saigonhoreca/specs.css`

---

## V. 🚫 Ngoài phạm vi xử lý (Nghiêm cấm) / Out Of Scope (Strictly Forbidden)
- ❌ **Refactor không liên quan**: Không dọn dẹp các thư mục của dự án khác.
- ❌ **Cấu hình hệ thống**: Không đụng vào configs hay core theme.

---

## VI. 🛠️ Các giai đoạn thực hiện / Phases of Execution
1. **Giai đoạn 1: Audit** 🔍: Đọc và phân tích các file PHP/CSS hiện tại của dự án.
2. **Giai đoạn 2: Implement** 🛠️: Nâng cấp HTML và CSS theo đúng layout độc bản.
3. **Giai đoạn 3: Verify** 🧪: Chạy Tailwind CLI compile, kiểm tra responsive.
4. **Giai đoạn 4: Report** 📤: Báo cáo bằng chứng thực thi.

---

## VII. 🔍 Lệnh kiểm tra bắt buộc / Verification Commands (Mandatory)
```powershell
cd "c:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\themes\saigonhoreca-theme"
# Biên dịch CSS
npm run build:project
```

---

## VIII. ✅ Tiêu chí nghiệm thu (Checklist) / Acceptance Criteria
- [ ] **Đúng Scope**: 100% thay đổi nằm trong Allowed Scope của dự án.
- [ ] **Độc Bản**: CSS/HTML mang nét thiết kế độc bản theo đúng spec (§II), không copy rập khuôn.
- [ ] **GPU Parallax**: Ảnh Parallax sáng rõ 100%, không bị dải đen che mép trên và mép dưới.
- [ ] **CTA Gộp**: Ảnh và content CTA nằm gọn gàng trên 1 hàng, có hiệu ứng trượt sáng.

---

## IX. 📥 Kết quả thực hiện / Agent Result
Status: `not-started`

---

## X. 📊 Ma trận kiểm soát chất lượng / Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | Production build success. |
| **Lint Gate** | 🧹 `pending` | | Zero new errors. |
| **Scope Gate** | 📂 `pending` | | No drift from Allowed Scope. |
| **Logic Gate** | 🎯 `pending` | | Requirements met 100%. |

---

## XI. 📑 CHANGE LOG & AUDIT TRAIL
- 2026-05-23 09:36: Dossier created by Gemini (Antigravity).