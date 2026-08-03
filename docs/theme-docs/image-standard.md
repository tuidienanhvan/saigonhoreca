# Quy Chuẩn Quản Lý & Tối Ưu Hóa Hình Ảnh

> **[BẮT BUỘC - KHÔNG THƯƠNG LƯỢNG]**  
> Toàn bộ hình ảnh trong thư mục `uploads/` phải tồn tại dưới dạng **WebP duy nhất**.  
> Nghiêm cấm lưu trữ song song file `.jpg` / `.jpeg` / `.png` gốc bên cạnh file `.webp` tương ứng.  
> Mọi ảnh upload mới phải được **chuyển đổi sang `.webp` trước khi đưa vào uploads/**, không có ngoại lệ.

Tài liệu này hướng dẫn cách tổ chức, đặt tên và tối ưu hóa hình ảnh trong thư mục `uploads/` nhằm đạt điểm SEO tối đa và tốc độ tải trang (LCP) nhanh nhất dưới 1.5 giây.

---

## 1. Tổ Chức Thư Mục Uploads Cục Bộ

Tất cả các tệp tin hình ảnh của một dự án con tuyệt đối không được tải trực tiếp lên các thư mục mặc định theo năm tháng của WordPress (ví dụ `/uploads/2026/03/`). Thay vào đó, toàn bộ hình ảnh phải được gom nhóm cục bộ trong một thư mục chuyên biệt mang tên slug của dự án đó:

```text
wp-content/uploads/
└── [slug-du-an]/
    ├── [slug-du-an]-banner-chinh.webp
    ├── [slug-du-an]-khong-gian-dung-bua.webp
    ├── [slug-du-an]-noi-that-co-dien.webp
    ├── [slug-du-an]-bep-gas-cong-nghiep.webp
    ├── [slug-du-an]-lo-nuong-pizza.webp
    └── [slug-du-an]-tum-hut-mui.webp
```

> **Lưu ý bắt buộc:** Chỉ được phép có file `.webp` trong thư mục dự án. Tuyệt đối **không lưu song song** file `.jpg` / `.jpeg` / `.png` gốc bên cạnh.

*Lợi ích*: Giúp quản lý assets cực kỳ gọn gàng, tránh thất lạc hoặc bị trùng tên tệp khi đồng bộ dữ liệu giữa các môi trường (Development, Staging, Production).

---

## 2. Quy Tắc Đặt Tên File Ảnh Chuẩn SEO

- **Định dạng bắt buộc**: Chỉ `.webp` — không có ngoại lệ.
- **Ngôn ngữ**: 100% Tiếng Việt không dấu, kết nối bằng ký tự gạch ngang (`-`).
- **Cấu trúc**: `[slug-du-an]-[mo-ta-ngan-gon-bang-tieng-viet-khong-dau].webp`
- **Ví dụ chuẩn**:
  - `bling-bling-club-khong-gian-quay-bar-soi-dong.webp` ✓ Chuẩn
  - `the-royal-all-day-dining-vach-kinh-phan-chieu-logo.webp` ✓ Chuẩn
  - `du-an-the-cheezy-time-13.webp` ✗ Chưa tốt — thiếu tính miêu tả SEO
  - `SGH-product-bling2.jpg` ✗ Tệ kép — sai định dạng (jpg) + tên vô nghĩa
- **Từ khóa cần tránh**: Không chèn các mã số nội bộ khó hiểu hoặc từ khóa vô nghĩa (ví dụ: `image001`, `photo-new`, `untitled-2`).

---

## 3. Quy Chuẩn Viết Chú Thích (Caption) Nghệ Thuật Tiếng Việt

Mỗi hình ảnh hiển thị trên trang chi tiết dự án phải được đi kèm một dòng chú thích nghệ thuật tiếng Việt F&B sâu sắc để thu hút sự chú ý của khách hàng, thay thế hoàn toàn các từ tiếng Anh kỹ thuật khô khan.

### Công Thức Viết Caption:
> **[Tính chất thiết kế/Vật liệu nổi bật]** + **[Công năng chi tiết]** + **[Giá trị mang lại cho việc vận hành thực tế]**

### Ví dụ so sánh:
- **Tệ (Chưa tối ưu)**: `Lò pizza gốm truyền thống tại The Cheezy Time`
- **Tốt (Nghệ thuật F&B)**: `Lò nướng Pizza bằng đất sét nung đỏ bóng đặt tại quầy bếp mở, nơi thực khách có thể trực tiếp chiêm ngưỡng nghệ thuật làm pizza thủ công.`

- **Tệ (Chưa tối ưu)**: `Chụp hút mùi bếp công nghiệp`
- **Tốt (Nghệ thuật F&B)**: `Hệ thống tum hút mùi inox dạng đục lỗ tiêu âm chuyên dụng, đảm bảo tối ưu việc lưu thông không khí trong gian bếp mở.`

---

## 4. Tối Ưu Hóa Kỹ Thuật Bằng Hàm Trợ Giúp `sgh_img`

Khi phát triển giao diện trong file PHP template, không được viết cứng đường dẫn ảnh dạng tĩnh. Bắt buộc sử dụng hàm `sgh_img()` để tận dụng các cơ chế tối ưu hóa tích hợp:

```php
<img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-pizza-oven.jpg'); ?>" 
     alt="<?php echo esc_attr__('Lò nướng Pizza đất nung tại quầy bếp mở', 'saigonhoreca'); ?>" 
     loading="lazy" 
     decoding="async">
```

### Các Tính Năng Tự Động Của Hàm `sgh_img()`:
1. **Local Assets Priority**: Tự động phục vụ file ảnh cục bộ nằm trong thư mục `wp-content/uploads/` của site WordPress hiện hành.
2. **Auto WebP Conversion Swap**: Nếu trình duyệt của khách hàng hỗ trợ WebP (hầu hết các trình duyệt hiện đại) và hệ thống tồn tại tệp ảnh định dạng `.webp` tương ứng (ví dụ `the-cheezy-time-pizza-oven.webp` bên cạnh tệp `.jpg`), hàm sẽ tự động tráo đổi định dạng để tối ưu hóa dung lượng truyền tải nhẹ hơn 30%.
3. **Smart Fallback**: Nếu chạy ở môi trường development bị thiếu ảnh cục bộ, hàm sẽ tự động ánh xạ tải ảnh từ máy chủ production để tránh tình trạng vỡ layout giao diện.

---

## 5. Ví Dụ Đặc Tả Ảnh: The Royal All Day Dining

Dưới đây là bảng ánh xạ phân tích chi tiết và quy chuẩn đặt tên ảnh đã được chuẩn hóa cho dự án The Royal All Day Dining:

| Ảnh Gốc (Mirror) | Tên File Mới Chuẩn SEO | Phân Tích Vật Thể & Bố Cục | Caption Nghệ Thuật (F&B) |
| :--- | :--- | :--- | :--- |
| `du-an-the-royal-sgh.jpg` | `the-royal-all-day-dining-mat-tien-nha-hang.jpg` | Mặt tiền nhà hàng The Royal tại 41-47 Đông Du, Quận 1 với hệ khung gỗ và cửa kính sang trọng đón khách. | Mặt tiền sang trọng phong cách hoàng gia của The Royal All Day Dining tại trung tâm Quận 1, nổi bật với hệ khung gỗ và cửa kính lớn. |
| `the-royal-sgh-1.jpg` | `the-royal-all-day-dining-vach-kinh-phan-chieu-logo.jpg` | Vách gương ghép ô quả trám phản chiếu logo nổi chữ vàng **THE ROYAL** và đèn chùm ấm cúng. | Hệ gương ghép trám màu đồng sang trọng phản chiếu ánh đèn chùm rực rỡ và logo nổi tinh xảo tại sảnh đón khách. |
| `the-royal-sgh-2.jpg` | `the-royal-all-day-dining-ghe-sofa-cong-da-bo.jpg` | Dãy ghế sofa bọc da bò nâu dáng cong cổ điển tựa lưng múi quả trám, sàn gỗ xương cá, bàn gỗ chỉ vàng. | Dãy ghế sofa da dáng cong cổ điển kết hợp bàn ăn gỗ tối màu chỉ vàng thanh lịch dưới ánh sáng ấm cúng của đèn chùm cổ điển. |
| `the-royal-sgh-3.jpg` | `the-royal-all-day-dining-can-canh-setup-ban-an.jpg` | Góc cận cảnh bàn ăn mặt gỗ tối màu bày đĩa sứ trắng, dao muỗng nĩa bạc trên placemat xanh rêu nhạt. | Cận cảnh setup bàn ăn chuẩn Fine Dining với bộ dao muỗng nĩa bạc tinh tế và ly thủy tinh lấp lánh trên nền gỗ sẫm màu. |
| `the-royal-sgh-4.jpg` | `the-royal-all-day-dining-ghe-don-nhung-xanh-reu.jpg` | Dãy ghế đơn bọc nhung xanh rêu bên ngoài, lót da trắng kem bên trong, bàn gỗ chân sắt. | Sự hòa quyện giữa chất liệu vải nhung xanh rêu và da kem của ghế ăn tạo điểm nhấn êm ái sang trọng cho không gian ẩm thực. |
| `the-royal-sgh-5.jpg` | `the-royal-all-day-dining-khu-bep-nong-fujimak.jpg` | Lò hấp nướng đa năng Fujimak Combi Pro đặt trên bàn inox, phía trên là lò nướng salamander/vi sóng, đối diện là các bếp chiên nhúng, bếp phẳng và vỉ nướng chuyên dụng. | Khu vực bếp nóng inox sáng bóng được trang bị lò hấp nướng đa năng Fujimak Combi Pro hiện đại phục vụ các món Âu cao cấp. |
| `the-royal-sgh-6.jpg` | `the-royal-all-day-dining-ban-mat-ngan-keo-undercounter.jpg` | Hệ thống bàn đông, bàn mát kết hợp bàn sơ chế/soạn chia thức ăn dưới quầy. | Hệ thống bàn mát dưới quầy bar (under-counter refrigerator) tích hợp ngăn kéo tiện dụng giúp bảo quản nguyên liệu tươi ngon ngay tầm tay đầu bếp. |
| `the-royal-sgh-7.jpg` | `the-royal-all-day-dining-khu-so-che-chau-rua.jpg` | Bàn sơ chế inox tích hợp chậu rửa vòi nước, máy lọc nước Trim Ion, tủ lạnh đứng inox lớn bên trái. | Khu sơ chế sạch sẽ với bàn inox tích hợp bồn rửa công nghiệp cùng hệ thống lọc nước tinh khiết chuyên dụng. |
| `the-royal-sgh-8.jpg` | `the-royal-all-day-dining-collage-chi-tiet-thiet-bi.jpg` | Collage 3 phần: lò vi sóng Sammic và lò nướng Salamander; hệ tủ kệ inox; bếp gas công nghiệp Fujimak. | Collage đặc tả thiết bị bếp chuyên dụng từ lò nướng Salamander điện, hệ tủ kệ inox gọn gàng đến bếp gas công nghiệp Fujimak. |

### 5.2. Ví Dụ Đặc Tả Ảnh: Amdang Typhoon

Dưới đây là ánh xạ đổi tên chuẩn SEO cho tệp tin hình ảnh tiêu biểu của dự án Amdang Typhoon:

| Ảnh Gốc (Mirror) | Tên File Mới Chuẩn SEO | Phân Tích Vật Thể & Bố Cục | Caption Nghệ Thuật (F&B) |
| :--- | :--- | :--- | :--- |
| `amdang-typhoon-thumbnail-project-cover.jpg` | `amdang-typhoon-phong-vip-ban-tron-gieng-troi.jpg` | Không gian phòng VIP sang trọng với bàn tròn xoay lớn ở trung tâm, dãy sofa cong màu cam đỏ bao quanh, phía trên trần có giếng trời tròn phản chiếu cây xanh. | Không gian phòng VIP sang trọng với bàn tiệc tròn xoay kết hợp dãy sofa cong cam đỏ ấm cúng, nổi bật bên dưới giếng trời tròn lấy sáng tự nhiên thanh lịch. |

### 5.3. Ví Dụ Đặc Tả Ảnh: G-Cup Coffee Bistro

Dưới đây là bảng ánh xạ phân tích chi tiết và quy chuẩn đặt tên ảnh đã được chuẩn hóa cho dự án G-Cup Coffee Bistro:

| Ảnh Gốc (Mirror) | Tên File Mới Chuẩn SEO | Phân Tích Vật Thể & Bố Cục | Caption Nghệ Thuật (F&B) |
| :--- | :--- | :--- | :--- |
| `g-cup-coffee-bistro-hero.webp` | `g-cup-coffee-bistro-quay-bar-da-trang-van-may.webp` | Toàn cảnh không gian quầy bar với máy pha La Marzocco bóng bẩy phía trước, bình Drip thủ công, lọ hoa ly trắng, máy xay Anfim và ánh đèn vàng ấm áp. | Không gian Bistro ngập tràn ánh sáng với điểm nhấn là hệ quầy bar gỗ kết hợp đá trắng vân mây sang trọng tại trung tâm. |
| `g-cup-coffee-bistro-coffee-station.webp` | `g-cup-coffee-bistro-quay-pha-che-chinh.webp` | Góc cận cảnh station pha chế chính với máy pha La Marzocco và máy xay Mahlkönig lớn màu trắng bên phải. | Hệ thống quầy pha chế chuyên nghiệp tích hợp khu vực rửa và sơ chế nhanh tiện lợi tối ưu diện tích. |
| `g-cup-coffee-bistro-lamarzocco-reflection.webp` | `g-cup-coffee-bistro-can-canh-may-pha-ca-phe-la-marzocco.webp` | Cận cảnh máy pha cà phê cao cấp La Marzocco bằng thép không gỉ bóng loáng phản chiếu nghệ thuật kiến trúc của quán. | Cận cảnh máy pha cà phê La Marzocco mạ chrome bóng bẩy phản chiếu nghệ thuật kiến trúc độc đáo của quán. |
| `g-cup-coffee-bistro-coffee-grinders.webp` | `g-cup-coffee-bistro-day-may-xay-ca-phe-anfim.webp` | Cặp máy xay Anfim đen công suất lớn chứa hạt cà phê, bên trái có máy xay Blendtec chuyên dụng. | Cặp máy xay cà phê Anfim công suất lớn đặt song song, sẵn sàng phục vụ những tách espresso hảo hạng tốc độ cao. |
| `g-cup-coffee-bistro-bar-details.webp` | `g-cup-coffee-bistro-chi-tiet-voi-rua-ly-am-ban.webp` | Cận cảnh vòi nước và thiết bị rửa ly xoay âm bàn inox 304 sáng bóng ngay cạnh hộc đựng bã cà phê knock-box gõ âm bàn. | Hệ thống vòi rửa ly xoay âm bàn bằng inox 304 siêu bền giúp tối ưu hóa thao tác vệ sinh tại quầy. |
| `g-cup-coffee-bistro-drip-filter.webp` | `g-cup-coffee-bistro-phin-nhom-den-pha-ca-phe.webp` | Cà phê phin Việt Nam nhôm đen nhám in logo chữ G của G-Cup đang nhỏ giọt cà phê xuống ly thuỷ tinh đặt trên khay gỗ. | Nghệ thuật pha chế Drip Filter chậm rãi tinh tế với bộ dụng cụ thủy tinh chịu nhiệt cao cấp. |
| `g-cup-coffee-bistro-beverage-counter.webp` | `g-cup-coffee-bistro-he-quay-beverage-counter-chuyen-nghiep.webp` | Hệ quầy pha chế inox dưới mặt đá tích hợp khay đá và các hộc để nguyên liệu uống, máy xay Blendtec và Anfim. | Hệ quầy beverage counter tích hợp khay đá bảo ôn âm bàn giữ nhiệt chuyên dụng cho barista thao tác nhanh chóng. |
| `g-cup-coffee-bistro-blendtec-blenders.webp` | `g-cup-coffee-bistro-cap-may-xay-blendtec-chong-on.webp` | Cặp máy xay Blendtec công nghiệp có cối nhựa trong suốt và nắp chống ồn mở hờ, đặt trên mặt quầy bar inox. | Cặp máy xay Blendtec công nghiệp tích hợp hộp cách âm chống ồn chuyên dụng cho môi trường yên tĩnh. |
| `g-cup-coffee-bistro-hoshizaki-freezer.webp` | `g-cup-coffee-bistro-ban-dong-cong-nghiep-hoshizaki.webp` | Cận cảnh mặt trước bàn đông inox Hoshizaki, bảng điều khiển nhiệt độ hiển thị số đỏ rực -16 độ C. | Bàn đông công nghiệp Hoshizaki bằng inox cao cấp tích hợp màn hình led báo nhiệt độ âm chuẩn xác. |
| `g-cup-coffee-bistro-underbar-fridges.webp` | `g-cup-coffee-bistro-ban-mat-inox-duoi-quay-bar.webp` | Hệ thống các cánh cửa của bàn mát inox dưới quầy bar (under-counter refrigerators) với dán logo chim cánh cụt Hoshizaki. | Hệ thống bàn mát dưới quầy bar (under-counter fridge) with thiết kế gọn gàng, giúp bảo quản lạnh sữa và nguyên liệu. |
| `g-cup-coffee-bistro-chef-wok.webp` | `g-cup-coffee-bistro-dau-bep-xao-bep-a-cong-nghiep.webp` | Đầu bếp đang thao tác xào chảo gang bùng lửa lớn trên bếp Á công nghiệp họng lò đúc inox rất hoành tráng. | Gian bếp nóng với bếp gas Á họng lò công suất lớn, nơi tạo nên những món ăn nóng hổi bùng nổ hương vị. |
| `g-cup-coffee-bistro-location.webp` | `g-cup-coffee-bistro-vi-tri-dia-ly-metropole.webp` | Station pha chế có logo G-Cup đè chéo ở giữa và một dải băng trắng thể hiện thông tin địa chỉ G-Cup. | Bản đồ đặc tả định vị địa lý thương hiệu G-Cup Coffee & Bistro tại trung tâm shophouse Metropole Thủ Thiêm. |

---

## 6. Quy Định Nghiêm Ngặt Về Quy Trình Xử Lý Ảnh (Bắt Buộc Cho AI Engineer)

Để đảm bảo chất lượng hình ảnh hiển thị không bị lỗi, tuyệt đối tuân thủ các quy tắc xử lý thủ công dưới đây:

1. **Cấm Sử Dụng Lệnh Tự Động (PowerShell/Bash)**:
   - Nghiêm cấm hoàn toàn việc viết script, sử dụng các câu lệnh sao chép hàng loạt (`Copy-Item`, `cp`), hoặc đổi tên tự động bằng CLI để di chuyển ảnh. Việc này dễ dẫn đến ánh xạ sai nội dung ảnh sang tên file không khớp, gây vỡ giao diện.
2. **Quy Trình Phân Tích Hình Ảnh Thủ Công (Mắt Thấy - Tay Làm)**:
   - **Bước 1**: AI phải trực tiếp mở và đọc nội dung từng hình ảnh bằng công cụ xem file (`view_file`).
   - **Bước 2**: Soi chi tiết thiết kế, vật liệu (inox 304, gỗ sồi, vải nhung...), và thiết bị F&B xuất hiện trong ảnh (lò hấp nướng Fujimak, máy rửa ly Winterhalter, bàn đông mát under-counter...).
   - **Bước 3**: Đối chiếu nội dung thực tế trên ảnh với vị trí hiển thị trên giao diện trang dự án để đặt tên tệp tiếng Việt không dấu mô tả chính xác nhất.
3. **Thực Thi Tuần Tự**:
   - Phải xử lý chỉnh sửa từng ảnh một (tuần tự), không làm gộp hay song song nhiều tệp cùng lúc. Sửa xong ảnh nào trong thư mục thì cập nhật ngay ảnh đó vào mã nguồn (`.php` hoặc `.json`) tương ứng trước khi chuyển sang ảnh kế tiếp.

---

## 7. Quy Trình Bắt Buộc: Chuyển Đổi WebP Trước Khi Upload

> **Nguyên tắc vàng:** Không có file `.jpg` / `.jpeg` / `.png` nào được phép tồn tại trong thư mục `uploads/` sau khi đã được xử lý. Mọi ảnh mới đưa vào dự án **bắt buộc phải qua bước convert WebP** trước.

### 7.1. Quy Trình Khi Nhận Ảnh Mới Từ Client

```
[Nhận file JPG/PNG từ client]
         ↓
[Đặt tên đúng chuẩn SEO tiếng Việt không dấu + .webp]
         ↓
[Convert sang WebP bằng script jpg_to_webp_full.py hoặc tool thủ công]
   - Quality: 82 (ảnh nội thất / F&B)
   - Method: 6 (nén tối ưu)
   - Giữ đúng tỉ lệ gốc, không scale tùy tiện
         ↓
[Verify: file .webp tồn tại + size > 0 + mở được bằng PIL]
         ↓
[Copy .webp vào uploads/[slug-du-an]/]
         ↓
[Xóa file JPG/PNG gốc — KHÔNG lưu lại song song]
         ↓
[Cập nhật reference trong file PHP/JSON tương ứng]
```

### 7.2. Script Tiện Ích Chuẩn

Script `scratch/jpg_to_webp_full.py` thực hiện đầy đủ quy trình trên:

```bash
# Chạy thử (xem trước, chưa thực sự xóa gì)
python scratch/jpg_to_webp_full.py

# Chạy thật (convert + xóa JPG gốc)
python scratch/jpg_to_webp_full.py --run
```

**Tính năng:**
- Dry-run an toàn (mặc định) — xem toàn bộ danh sách trước khi thực thi
- Tự động bỏ qua nếu WebP đã tồn tại (tránh overwrite)
- Verify file WebP sau convert trước khi xóa JPG gốc
- Log chi tiết từng file: tên, kích thước trước/sau, % tiết kiệm

### 7.3. Kiểm Tra Định Kỳ (Không Để Tích Tụ JPG)

Sau mỗi lần thêm ảnh mới vào dự án, chạy lệnh kiểm tra nhanh:

```powershell
# Đếm số JPG còn sót lại trong uploads/ (kết quả phải = 0)
Get-ChildItem -Recurse -Path "app\public\wp-content\uploads" -Include "*.jpg","*.jpeg" | Measure-Object | Select-Object Count
```

Nếu kết quả `Count > 0` → chạy lại `jpg_to_webp_full.py --run` để dọn sạch.

### 7.4. Tham Chiếu WebP Trong PHP Template

Sau khi upload WebP, **không viết cứng đường dẫn** — luôn dùng `sgh_img()`:

```php
// CHUẨN — sgh_img() tự resolve đường dẫn uploads/
<img src="<?php echo sgh_img('slug-du-an/slug-du-an-ten-anh.webp'); ?>"
     alt="<?php echo esc_attr__('Mô tả ảnh tiếng Việt', 'saigonhoreca'); ?>"
     loading="lazy"
     decoding="async"
     width="800" height="533">

// CẤM — hardcode path + sai định dạng
<img src="/wp-content/uploads/slug-du-an/photo.jpg">
```
