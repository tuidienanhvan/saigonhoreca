# 🤝 Hồ Sơ Thiết Kế Độc Bản (Bespoke Visual Dossier) — Bếp Ăn Trường Mầm Non Trinh Vương

**Mã hồ sơ / Dossier ID**: `D-20260523-bep-an-truong-mam-non-tu-thuc-trinh-vuong`  
**Dự án**: `bep-an-truong-mam-non-tu-thuc-trinh-vuong`  
**Ngôn ngữ Visual**: `Safe-Kitchen Industrial (Bếp Học Đường)`  
**Trạng thái**: 🟡 Chờ duyệt (Review Pending)  

---

## I. 🎨 Định Vị Mỹ Thuật & Ý Tưởng Sáng Tạo Độc Bản

Bếp ăn trường mầm non đòi hỏi tiêu chuẩn vệ sinh, an toàn thực phẩm cực kỳ khắt khe, đồng thời phải mang lại cảm giác thân thiện, ấm áp và đáng tin cậy. Vì vậy, layout của dự án này phải kết hợp hài hòa giữa tính chính xác của inox công nghiệp và sự tươi sáng, dịu nhẹ của môi trường giáo dục trẻ thơ.

### 🌟 Ý tưởng độc bản: "Safe-Kitchen Spec Timeline"
Chúng ta sẽ mang tinh thần tươi sáng, sạch sẽ và an lành vào trang dự án:

1. **Intro Section (Bảo An Sức Khỏe)**:
   - Sử dụng bố cục **Friendly Split Layout** – một bên giới thiệu quy trình chế biến 1 chiều an toàn bằng chữ Sans-serif bo viền tròn trịa thân thiện, một bên là hình ảnh thực phẩm tươi ngon cho bé được bọc trong **Soft-Edged Frame** (khung ảnh bo tròn lớn 32px mịn màng với viền màu sữa pastel).
   - Dropcap chữ **B** được cách điệu nhẹ nhàng và mang tông màu xanh mint mát mắt.

2. **Concept Section (Quy Trình Một Chiều)**:
   - Thay vì dùng grid phức tạp, Concept của Trinh Vương sử dụng **Timeline Spec Panel** – sơ đồ quy trình bếp một chiều được trình bày theo dạng các bước liên hoàn, sử dụng các icon vector tối giản màu cam pastel và xanh ngọc nhạt.
   - Nền card sử dụng chất liệu kính trắng sữa trong suốt nhẹ (`backdrop-filter: blur(10px)`), mang lại cảm giác cực kỳ sạch sẽ và vô trùng.

3. **Specs Section (Thông Số An Toàn Thực Phẩm)**:
   - Thiết kế bảng thông số thiết bị inox 304 tiêu chuẩn quốc tế an toàn cho trẻ theo phong cách **Clean Dashboard Spec** – sử dụng các card inox bo viền mịn màng, bảng đo lường khoa học dễ đọc màu pastel thanh lịch.
   - Parallax Section bọc ảnh khu sơ chế thực tế có `min-height: 80vh`, loại bỏ hoàn toàn overlay đen, giúp không gian bếp sáng bừng, sạch bóng 100%.

4. **CTA Section (Integrated Split Card & Soft Frame)**:
   - Gộp gọn gàng ảnh thực tế khu chia soạn thức ăn của bé và nội dung thông tin liên hệ vào **Integrated Split CTA Card** (tỷ lệ 50% text - 50% ảnh trên cùng một hàng).
   - Nút bấm hành động có hiệu ứng trượt sáng Shimmer màu xanh mint tươi tắn, tạo cảm giác thân thiện, đáng tin cậy.

---

## II. 🛠️ Quy Chuẩn Kỹ Thuật Nâng Cấp

### 1. Parallax GPU 3D
- Sử dụng công nghệ ghim phần cứng `clip-path` siêu nhẹ trên `specs.php` và `partnership.php`.
- Xoá bỏ hoàn toàn dải overlay xám đen ở mép trên và mép dưới để bức ảnh nguyên bản tươi sáng hiện rõ 100%.

### 2. Integrated Split CTA Card
- Gộp nội dung và ảnh vào một khối thống nhất sang trọng trên `cta.php` và `cta.css`, loại bỏ hoàn toàn sự đứt gãy thị giác cũ.

---

## III. 📂 Danh Sách Các File Nâng Cấp (Proposed Changes)

#### [MODIFY] [hero.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/hero.php)
#### [MODIFY] [hero.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/hero.css)
#### [MODIFY] [intro.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/intro.php)
#### [MODIFY] [intro.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/intro.css)
#### [MODIFY] [concept.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/concept.php)
#### [MODIFY] [concept.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/concept.css)
#### [MODIFY] [partnership.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/partnership.php)
#### [MODIFY] [partnership.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/partnership.css)
#### [MODIFY] [specs.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/specs.php)
#### [MODIFY] [specs.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/specs.css)
#### [MODIFY] [gallery.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/gallery.php)
#### [MODIFY] [gallery.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/gallery.css)
#### [MODIFY] [related.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/related.php)
#### [MODIFY] [related.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/related.css)
#### [MODIFY] [cta.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/cta.php)
#### [MODIFY] [cta.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/cta.css)
