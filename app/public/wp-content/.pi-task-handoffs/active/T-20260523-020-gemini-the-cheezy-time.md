---
id: T-20260523-020
owner: gemini
state: drafted
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
updated: 2026-05-23 09:36
---

# 📋 T-20260523-020 | gemini | the-cheezy-time — Hồ Sơ Thiết Kế Độc Bản / Bespoke Task Specification

## I. 📊 Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-20260523-020` | 🆔 Định danh duy nhất của task con. |
| `owner` | `gemini` | 👤 Agent thực thi: Antigravity. |
| `state` | `drafted` | 🔄 Vòng đời: **drafted**. |
| `priority` | `P2` | 🚥 **P2**: Tiêu chuẩn. |
| `risk` | `medium` | ⚠️ Tác động: **medium**. |

---

## II. 🎯 Mục tiêu và Chiến lược / Goal & Strategic Objective

Mục tiêu tối thượng là nâng cấp visual cho dự án **The Cheezy Time** thoát khỏi sự rập khuôn phẳng lì Elementor cũ, mang lại trải nghiệm thị giác độc bản theo đúng nhóm thiết kế nghệ thuật **European Elegance (Warm Cheese Bistro)**.

### 🌟 Ý tưởng độc bản:
- **Định vị cốt lõi**: Gouda Warmth Grid - Tông vàng mật ong ấm áp và trắng sữa kem ngọt ngào, card thủy tinh trong vắt bo góc siêu mềm mại 32px giống như khối phô mai tan chảy quyến rũ. Khung ảnh kiểu tạp chí ẩm thực phô mai tươi đan cài những lát cắt tròn tinh nghịch.
- **Intro & Concept**: Intro sử dụng layout tròn trịa, ấm cúng và đầy năng lượng ngọt ngào. Concept dùng các khối phô mai màu vàng ấm bao lấy text.
- **Parallax GPU 3D**: Tắt overlay đen gradient, nâng cao min-height 80vh giúp ảnh to sáng bừng 100%, ghim phần cứng bằng clip-path inset(0) siêu mượt trên Safari iOS.
- **CTA gộp**: Tích hợp hình ảnh Cinemascope thực tế và thông tin liên hệ thành **Integrated Split CTA Card** (1 hàng 2 cột gọn gàng) có nút trượt sáng Shimmer lấp lánh.

---

## III. 📚 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án, tech stack và quy ước.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu kỹ thuật.

---

## IV. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `template-parts/project-pillar/the-cheezy-time/01-hero.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/02-with_gallery.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/03-bg_section.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/04-with_gallery.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/05-split.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/06-split.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/07-text.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/concept.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/cta.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/gallery.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/hero.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/intro.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/partnership.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/related.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/specs.php`
- 📄 `template-parts/project-pillar/the-cheezy-time/concept.css`
- 📄 `template-parts/project-pillar/the-cheezy-time/cta.css`
- 📄 `template-parts/project-pillar/the-cheezy-time/gallery.css`
- 📄 `template-parts/project-pillar/the-cheezy-time/hero.css`
- 📄 `template-parts/project-pillar/the-cheezy-time/intro.css`
- 📄 `template-parts/project-pillar/the-cheezy-time/partnership.css`
- 📄 `template-parts/project-pillar/the-cheezy-time/related.css`
- 📄 `template-parts/project-pillar/the-cheezy-time/specs.css`

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