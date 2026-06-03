---
id: T-20260523-010
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

# 📋 T-20260523-010 | gemini | moa-moa — Hồ Sơ Thiết Kế Độc Bản / Bespoke Task Specification

## I. 📊 Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-20260523-010` | 🆔 Định danh duy nhất của task con. |
| `owner` | `gemini` | 👤 Agent thực thi: Antigravity. |
| `state` | `drafted` | 🔄 Vòng đời: **drafted**. |
| `priority` | `P2` | 🚥 **P2**: Tiêu chuẩn. |
| `risk` | `medium` | ⚠️ Tác động: **medium**. |

---

## II. 🎯 Mục tiêu và Chiến lược / Goal & Strategic Objective

Mục tiêu tối thượng là nâng cấp visual cho dự án **Moa Moa Lounge & Bistro** thoát khỏi sự rập khuôn phẳng lì Elementor cũ, mang lại trải nghiệm thị giác độc bản theo đúng nhóm thiết kế nghệ thuật **Organic Wabi-Sabi (Tropical Wild Wabi-Sabi)**.

### 🌟 Ý tưởng độc bản:
- **Định vị cốt lõi**: Raw Bamboo Void - Sử dụng kết cấu tre, nứa mộc mạc và đá cuội hoang dã. Các khối thông tin có đường viền bo bất định dạng (organic fluid contours), nền xám đất sét ấm, khung ảnh so le xen kẽ các khoảng trống Zen ngập tràn lá cọ nhiệt đới.
- **Intro & Concept**: Intro sử dụng layout uốn lượn hữu cơ mềm mại. Concept giới thiệu các góc chill ngoài trời với các khung tre thô mộc.
- **Parallax GPU 3D**: Tắt overlay đen gradient, nâng cao min-height 80vh giúp ảnh to sáng bừng 100%, ghim phần cứng bằng clip-path inset(0) siêu mượt trên Safari iOS.
- **CTA gộp**: Tích hợp hình ảnh Cinemascope thực tế và thông tin liên hệ thành **Integrated Split CTA Card** (1 hàng 2 cột gọn gàng) có nút trượt sáng Shimmer lấp lánh.

---

## III. 📚 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án, tech stack và quy ước.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu kỹ thuật.

---

## IV. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `template-parts/project-pillar/moa-moa/01-hero.php`
- 📄 `template-parts/project-pillar/moa-moa/02-with_gallery.php`
- 📄 `template-parts/project-pillar/moa-moa/03-split.php`
- 📄 `template-parts/project-pillar/moa-moa/04-split.php`
- 📄 `template-parts/project-pillar/moa-moa/05-with_gallery.php`
- 📄 `template-parts/project-pillar/moa-moa/concept.php`
- 📄 `template-parts/project-pillar/moa-moa/cta.php`
- 📄 `template-parts/project-pillar/moa-moa/gallery.php`
- 📄 `template-parts/project-pillar/moa-moa/hero.php`
- 📄 `template-parts/project-pillar/moa-moa/intro.php`
- 📄 `template-parts/project-pillar/moa-moa/partnership.php`
- 📄 `template-parts/project-pillar/moa-moa/related.php`
- 📄 `template-parts/project-pillar/moa-moa/specs.php`
- 📄 `template-parts/project-pillar/moa-moa/concept.css`
- 📄 `template-parts/project-pillar/moa-moa/cta.css`
- 📄 `template-parts/project-pillar/moa-moa/gallery.css`
- 📄 `template-parts/project-pillar/moa-moa/hero.css`
- 📄 `template-parts/project-pillar/moa-moa/intro.css`
- 📄 `template-parts/project-pillar/moa-moa/partnership.css`
- 📄 `template-parts/project-pillar/moa-moa/related.css`
- 📄 `template-parts/project-pillar/moa-moa/specs.css`

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