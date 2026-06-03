# 🤝 Hồ Sơ Thiết Kế Độc Bản (Bespoke Visual Dossier) — Yuzu Omakase Sushi

**Mã hồ sơ / Dossier ID**: `D-20260523-yuzu-omakase`  
**Dự án**: `yuzu-omakase`  
**Ngôn ngữ Visual**: `Organic Wabi-Sabi (Omakase & Zen Void)`  
**Trạng thái**: 🟡 Chờ duyệt (Review Pending)  

---

## I. 🎨 Định Vị Mỹ Thuật & Ý Tưởng Sáng Tạo Độc Bản

Yuzu Omakase là đỉnh cao của nghệ thuật ẩm thực tối giản Nhật Bản tại Sài Gòn, nơi từng lát cá tươi ngon được phục vụ trực tiếp trên quầy gỗ tuyết tùng dưới ánh sáng dịu nhẹ đầy nghệ thuật. Trang dự án của Yuzu Omakase phải tôn vinh triết lý **Zen Void (Khoảng trống thiền định)** và các đường nét bo tròn hữu cơ tự nhiên, hoàn toàn không sao chép layout góc cạnh của Sol hay kết cấu hoài cổ của Michelin.

### 🌟 Ý tưởng độc bản: "Asymmetric Citrus Void"
Chúng ta sẽ mang tinh thần tĩnh lặng, cao quý và thanh khiết của một phòng trà Omakase Nhật Bản vào trang dự án:

1. **Intro Section (Khoảng Lặng Thanh Khiết)**:
   - Sử dụng bố cục **Zen Void Layout** – thiết lập các khoảng trắng cực kỳ rộng rãi và thoáng đãng để tạo nhịp thở cho mắt.
   - Text mô tả nép mình trang nhã ở một góc nhỏ với font chữ Serif thanh mảnh cực kỳ sang trọng. Ảnh đại diện quầy bar được bọc trong một **Organic Fluid Frame** (khung ảnh có đường viền bo góc tự nhiên bất đối xứng giống như lát cắt chéo của quả chanh Yuzu tươi mát).

2. **Concept Section (Hơi Thở Gỗ Tuyết Tùng)**:
   - Bố cục sử dụng **Zen Offset Double Frame** – hình ảnh các phòng ăn VIP và góc vườn thiền được bao quanh bởi các khung chỉ gỗ tuyết tùng sáng màu mộc mạc đặt lệch tầng 3D nhẹ nhàng đè lên nhau.
   - Nền trang sử dụng tông xám ấm của đá cuội tự nhiên phối hợp kem sồi sáng, mang lại cảm giác mộc mạc nhưng cực kỳ cao cấp.

3. **Specs Section (Tinh Hoa Cơ Khí Thầm Lặng)**:
   - Các card thông tin thông số kỹ thuật được tối giản hóa tối đa, sử dụng các đường line xám sương mảnh mai phân tách các hạng mục.
   - Parallax Section bọc ảnh quầy sushi thực tế nâng cao `min-height: 80vh`, loại bỏ hoàn toàn dải phủ tối, ảnh bừng sáng rực rỡ như đón nắng ban mai.

4. **CTA Section (Cinematic Cinemascope & Ambient Glow)**:
   - Bố cục **Cinemascope 21:9 & Ambient Glow** – card CTA được thu gọn tối đa dạng thanh ngang siêu mỏng như một bức tranh ngang tối giản.
   - Nền card trong vắt như sương mai ban sớm, xung quanh hắt ra ánh sáng màu vàng Yuzu nhạt ấm áp (`box-shadow: 0 0 50px rgba(245, 197, 24, 0.08)`). Nút bấm trượt sáng Shimmer màu vàng hoàng kim quý phái.

---

## II. 🛠️ Quy Chuẩn Kỹ Thuật Nâng Cấp

### 1. Parallax GPU 3D
- Sử dụng công nghệ ghim phần cứng `clip-path` siêu nhẹ trên `specs.php` và `partnership.php`.
- Xoá bỏ hoàn toàn dải overlay xám đen ở mép trên và mép dưới để bức ảnh nguyên bản tươi sáng hiện rõ 100%.

### 2. Integrated Split CTA Card
- Gộp nội dung và ảnh vào một khối thống nhất sang trọng trên `cta.php` và `cta.css`, loại bỏ hoàn toàn sự đứt gãy thị giác cũ.

---

## III. 📂 Danh Sách Các File Nâng Cấp (Proposed Changes)

#### [MODIFY] [hero.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/hero.php)
#### [MODIFY] [hero.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/hero.css)
#### [MODIFY] [intro.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/intro.php)
#### [MODIFY] [intro.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/intro.css)
#### [MODIFY] [concept.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/concept.php)
#### [MODIFY] [concept.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/concept.css)
#### [MODIFY] [partnership.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/partnership.php)
#### [MODIFY] [partnership.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/partnership.css)
#### [MODIFY] [specs.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/specs.php)
#### [MODIFY] [specs.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/specs.css)
#### [MODIFY] [gallery.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/gallery.php)
#### [MODIFY] [gallery.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/gallery.css)
#### [MODIFY] [related.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/related.php)
#### [MODIFY] [related.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/related.css)
#### [MODIFY] [cta.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/cta.php)
#### [MODIFY] [cta.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/yuzu-omakase/cta.css)
