---
id: T-20260523-030
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

# 📋 T-20260523-030 | gemini | du-nam-an-an — Hồ Sơ Thiết Kế Độc Bản / Bespoke Task Specification

## I. 📊 Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-20260523-030` | 🆔 Định danh duy nhất của task con. |
| `owner` | `gemini` | 👤 Agent thực thi: Antigravity. |
| `state` | `drafted` | 🔄 Vòng đời: **drafted**. |
| `priority` | `P2` | 🚥 **P2**: Tiêu chuẩn. |
| `risk` | `medium` | ⚠️ Tác động: **medium**. |

---

## II. 🎯 Mục tiêu và Chiến lược / Goal & Strategic Objective

Mục tiêu tối thượng là nâng cấp visual cho dự án **Bếp Ăn Công Nghiệp Nam An An** thoát khỏi sự rập khuôn phẳng lì Elementor cũ, mang lại trải nghiệm thị giác độc bản theo đúng nhóm thiết kế nghệ thuật **Clean-Room Blueprint (Bếp Vô Trùng)**.

### 🌟 Ý tưởng độc bản:
- **Định vị cốt lõi**: Clean-Room Blueprint - Sử dụng màu xanh ngọc lục bảo nhạt phối trắng kháng khuẩn, bố cục ngăn nắp vuông vắn như phòng thí nghiệm sinh học. Các card kính trắng sữa bo viền mịn, bảng specs thể hiện quy trình HACCP đạt chuẩn quốc tế bằng sơ đồ vector tối giản cực kỳ chuyên nghiệp.
- **Intro & Concept**: Layout cực kỳ ngăn nắp và sạch sẽ, các nét vẽ line mảnh xám nhạt phân chia phân khu. Intro sử dụng lưới 2 cột đối xứng chuẩn mực.
- **Parallax GPU 3D**: Tắt overlay đen gradient, nâng cao min-height 80vh giúp ảnh to sáng bừng 100%, ghim phần cứng bằng clip-path inset(0) siêu mượt trên Safari iOS.
- **CTA gộp**: Tích hợp hình ảnh Cinemascope thực tế và thông tin liên hệ thành **Integrated Split CTA Card** (1 hàng 2 cột gọn gàng) có nút trượt sáng Shimmer lấp lánh.

---

## III. 📚 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án, tech stack và quy ước.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu kỹ thuật.

---

## IV. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `template-parts/project-pillar/du-nam-an-an/01-hero.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/02-with_gallery.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/03-split.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/04-split.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/05-with_gallery.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/concept.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/cta.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/gallery.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/hero.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/intro.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/partnership.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/related.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/specs.php`
- 📄 `template-parts/project-pillar/du-nam-an-an/concept.css`
- 📄 `template-parts/project-pillar/du-nam-an-an/cta.css`
- 📄 `template-parts/project-pillar/du-nam-an-an/gallery.css`
- 📄 `template-parts/project-pillar/du-nam-an-an/hero.css`
- 📄 `template-parts/project-pillar/du-nam-an-an/intro.css`
- 📄 `template-parts/project-pillar/du-nam-an-an/partnership.css`
- 📄 `template-parts/project-pillar/du-nam-an-an/related.css`
- 📄 `template-parts/project-pillar/du-nam-an-an/specs.css`

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