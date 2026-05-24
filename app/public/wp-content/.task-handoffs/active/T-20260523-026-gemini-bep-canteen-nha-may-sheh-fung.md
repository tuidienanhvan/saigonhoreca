---
id: T-20260523-026
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

# 📋 T-20260523-026 | gemini | bep-canteen-nha-may-sheh-fung — Hồ Sơ Thiết Kế Độc Bản / Bespoke Task Specification

## I. 📊 Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-20260523-026` | 🆔 Định danh duy nhất của task con. |
| `owner` | `gemini` | 👤 Agent thực thi: Antigravity. |
| `state` | `drafted` | 🔄 Vòng đời: **drafted**. |
| `priority` | `P2` | 🚥 **P2**: Tiêu chuẩn. |
| `risk` | `medium` | ⚠️ Tác động: **medium**. |

---

## II. 🎯 Mục tiêu và Chiến lược / Goal & Strategic Objective

Mục tiêu tối thượng là nâng cấp visual cho dự án **Bếp Canteen Nhà Máy Sheh Fung** thoát khỏi sự rập khuôn phẳng lì Elementor cũ, mang lại trải nghiệm thị giác độc bản theo đúng nhóm thiết kế nghệ thuật **Industrial Technical Spec (Bếp Công Nghiệp)**.

### 🌟 Ý tưởng độc bản:
- **Định vị cốt lõi**: Heavy Engineering Blueprint - Thiết kế đậm chất kỹ thuật cơ khí vững chắc, tôn vinh inox 304 xước mờ. Bố cục lưới thông số cơ khí vững chãi như mặt bằng phân khu nhà bếp công nghiệp, các khung ảnh có thước đo ly (technical ruler grid overlay) ở bốn góc thể hiện sự chính xác tuyệt đối.
- **Intro & Concept**: Bố cục Intro và Concept đối xứng nghiêm ngặt theo các trục kỹ thuật. Bảng thông số kỹ thuật dùng font chữ monospace cơ khí vững chãi.
- **Parallax GPU 3D**: Tắt overlay đen gradient, nâng cao min-height 80vh giúp ảnh to sáng bừng 100%, ghim phần cứng bằng clip-path inset(0) siêu mượt trên Safari iOS.
- **CTA gộp**: Tích hợp hình ảnh Cinemascope thực tế và thông tin liên hệ thành **Integrated Split CTA Card** (1 hàng 2 cột gọn gàng) có nút trượt sáng Shimmer lấp lánh.

---

## III. 📚 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án, tech stack và quy ước.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu kỹ thuật.

---

## IV. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/03-split.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/04-split.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/05-text.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/concept.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/cta.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/gallery.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/hero.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/intro.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/partnership.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/related.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/specs.php`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/concept.css`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/cta.css`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/gallery.css`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/hero.css`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/intro.css`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/partnership.css`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/related.css`
- 📄 `template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/specs.css`

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