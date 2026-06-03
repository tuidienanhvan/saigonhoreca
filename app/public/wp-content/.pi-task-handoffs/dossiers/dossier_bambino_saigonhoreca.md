# 🤝 Hồ Sơ Thiết Kế Độc Bản (Bespoke Visual Dossier) — Bambino Superclub

**Mã hồ sơ / Dossier ID**: `D-20260523-bambino-saigonhoreca`  
**Dự án**: `bambino-saigonhoreca`  
**Ngôn ngữ Visual**: `Cyberpunk Neon Glow (Club & Lounge)`  
**Trạng thái**: 🟡 Chờ duyệt (Review Pending)  

---

## I. 🎨 Định Vị Mỹ Thuật & Ý Tưởng Sáng Tạo Độc Bản

Bambino không chỉ đơn thuần là một nhà hàng ăn uống, đó là một **Superclub đích thực** ẩn mình giữa lòng Sài Gòn - nơi giao hòa giữa ẩm thực tinh hoa và nhịp đập âm nhạc điện tử sôi động. Vì vậy, layout của Bambino phải toát lên sự bí ẩn, quyến rũ và đầy tính cơ khí đương đại, tuyệt đối không được copy sự mộc mạc của Heiwa hay sự phóng khoáng Latin của Sol Kitchen.

### 🌟 Ý tưởng độc bản: "Midnight Neon Grid Panel"
Chúng ta sẽ biến trang dự án thành một hành trình trải nghiệm thị giác huyền bí trong bóng tối của màn đêm đô thị:

1. **Intro Section (Vũ Điệu Bóng Tối)**:
   - Thay vì dùng grid phẳng thông thường, Intro của Bambino sử dụng **Bố cục Asymmetric Neon Column** – một cột văn bản giới thiệu sâu lắng nép bên trái, và một cột ảnh thực tế quầy DJ bọc trong **3D Mechanical Frame** (khung cơ khí 3D góc cạnh với viền nhôm xước mạ chrome phản chiếu ánh sáng cực chất).
   - Tên dự án có dropcap chữ **B** lớn màu neon tím huyền ảo, phát ra ánh sáng mờ dịu (soft glow overlay) lan tỏa bên dưới văn bản.

2. **Concept Section (Chấn Cơ Khí & Ánh Sáng Neon)**:
   - Sử dụng **Lưới Panel Không Đối Xứng** mô phỏng hệ thống bàn mixer DJ.
   - Các card thông tin có nền kính đen khói siêu mờ (`background: rgba(10, 10, 10, 0.75)`) phối hợp với các đường chỉ neon tím violet mỏng mảnh 1px chạy dọc theo góc chấn cơ khí. Khi hover mạnh mẽ, viền neon này sẽ chuyển dần sang xanh laser và phát sáng rực rỡ, tạo micro-animation đầy phấn khích.

3. **Specs Section (Hạ Tầng Cơ Khí Bếp & Bar Siêu Tải)**:
   - Thiết kế bảng thông số thiết bị inox 304 theo cấu trúc **Monospace Technical Spec Card** – sử dụng font chữ lập trình kỹ thuật số màu xanh lục neon nhạt trên nền lưới tọa độ cơ khí chìm mờ ảo.
   - Parallax Section bọc ảnh quầy bar thực tế có `min-height: 85vh` cực rộng, loại bỏ hoàn toàn dải đen che phủ, ảnh bừng sáng rực rỡ.

4. **CTA Section (Integrated Split Card & Parallel Neon Framer)**:
   - Gộp gọn gàng ảnh quầy bar thực tế của Bambino và nội dung thông tin vào **Integrated Split CTA Card** (tỷ lệ 52% text - 48% ảnh trên cùng một hàng).
   - Card được bao bọc bởi hai góc khung viền vàng kim chéo 3D co giãn khi hover, mang cảm giác vững chãi của kết cấu thép club. Nút bấm hành động có hiệu ứng trượt sáng Shimmer cực mạnh.

---

## II. 🛠️ Quy Chuẩn Kỹ Thuật Nâng Cấp

### 1. Parallax GPU 3D
- Áp dụng kỹ thuật `clip-path: inset(0 0 0 0)` và `position: fixed` chạy bằng phần cứng cho `partnership.php`.
- Tắt hoàn toàn overlay đen gradient che khuất ảnh quầy bar ở mép trên và dưới trang để ảnh sáng rõ 100%.

### 2. Integrated Split CTA Card
- Gộp file `cta.php` và `cta.css` thành cấu trúc lưới 2 cột sang trọng, ôm khít ảnh thực tế dự án, loại bỏ khoảng hở đứt gãy.

---

## III. 📂 Danh Sách Các File Nâng Cấp (Proposed Changes)

#### [MODIFY] [hero.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/hero.php)
#### [MODIFY] [hero.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/hero.css)
#### [MODIFY] [intro.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/intro.php)
#### [MODIFY] [intro.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/intro.css)
#### [MODIFY] [concept.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/concept.php)
#### [MODIFY] [concept.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/concept.css)
#### [MODIFY] [partnership.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/partnership.php)
#### [MODIFY] [partnership.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/partnership.css)
#### [MODIFY] [specs.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/specs.php)
#### [MODIFY] [specs.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/specs.css)
#### [MODIFY] [gallery.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/gallery.php)
#### [MODIFY] [gallery.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/gallery.css)
#### [MODIFY] [related.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/related.php)
#### [MODIFY] [related.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/related.css)
#### [MODIFY] [cta.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/cta.php)
#### [MODIFY] [cta.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/template-parts/project-pillar/bambino-saigonhoreca/cta.css)
