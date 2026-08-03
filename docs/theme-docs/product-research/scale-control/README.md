# Scale Control — Nghiên cứu định danh thương hiệu

Nguồn: SQL dump thực tế của site (`app/sql/local.sql`) + đối chiếu chéo với saigonhoreca.vn (bản public đang chạy) và vinbarista.com (nhà phân phối VN khác bán cùng dòng model number).
Ngày cào dữ liệu: 2026-07-13. Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site liệt kê brand **"Scale Control"** với đúng 1 sản phẩm. Tên brand mơ hồ — có thể là hãng cân điện tử (weighing scale), hãng hóa chất/thiết bị chống đóng cặn (descaler), hoặc thứ khác. Client yêu cầu xác minh trước khi quyết định mở rộng danh mục.

## Kết luận: KHÔNG xác nhận được đây là hãng sản xuất quốc tế độc lập

### Bước 1 — Sản phẩm thật là gì

Tra trực tiếp trong `app/sql/local.sql` (post ID 569, term_id 13 slug `scale-control`), sản phẩm duy nhất dưới brand này là:

> **Bộ Lọc Nước SCNP-1000L – Scale Control**
> https://saigonhoreca.vn/san-pham/bo-loc-nuoc-scnp-1000l-scale-control/

Đây **KHÔNG phải cân điện tử** — đây là **bộ lọc nước/làm mềm nước toàn hệ thống** (whole-house water filtration & softening), mục đích chống cáu cặn (scale) bảo vệ đường ống và thiết bị dùng nước trong bếp (máy rửa chén, máy đá, nồi hơi...). Tên "Scale Control" mô tả **chức năng sản phẩm** (kiểm soát cáu cặn) chứ không hẳn tên một công ty.

Thông số verify từ trang sản phẩm thật:

| Thông số | Giá trị |
|---|---|
| Kích thước | 565 x 170 x 180 mm |
| Kích thước lọc | 0,01 μm |
| Vật liệu màng | PVDF @50,000 Dalton @0,01μm |
| Công suất | 1.000 m³ tổng / 8 lít/phút |
| Bồn chứa | 1.000 gallon |
| Công nghệ | Trao đổi ion (làm mềm nước) + lọc trầm tích |
| Lõi lọc tinh UF | "Scala Control" (tên ghi trong mô tả gốc — lệch 1 ký tự so với "Scale Control", chưa rõ là lỗi đánh máy hay tên lõi lọc riêng) |
| Lõi lọc thô | DFXCB |
| Giá | Không niêm yết công khai |

### Bước 2 — Đã thử tìm hãng gốc, không ra kết quả

Đã search >10 biến thể: "Scale Control commercial kitchen equipment brand", "Scale Control weighing scale manufacturer", "Scale Control descaler foodservice", "SCNP-1000L water filter", "Scala Control filter cartridge", v.v. Không công ty/website chính hãng nào tên "Scale Control" hoạt động trong ngành thiết bị bếp công nghiệp hoặc lọc nước OEM xuất hiện.

### Bước 3 — Bằng chứng cho thấy đây là nhãn OEM/private-label, không phải hãng thật

Tìm được **cùng dải model number "SCNP-xxxxL"** bán bởi **vinbarista.com** (nhà phân phối pha chế/HORECA khác tại VN, không liên quan saigonhoreca) — nhưng gắn brand **KHÁC**:

> **Bộ lọc nước Hoshizaki Nano SCNP-1000LPH** — https://vinbarista.com/san-pham/bo-loc-nuoc-hoshizaki-nano-scnp-1000lph-item1441.html

Hai nhà phân phối độc lập, cùng 1 dải mã sản phẩm (SCNP-1000L vs SCNP-1000LPH), nhưng **2 tên thương hiệu khác nhau** ("Scale Control" vs "Hoshizaki Nano") — dấu hiệu điển hình của **hàng OEM không nhãn gốc**, mỗi nhà phân phối tự đặt tên khi bán lẻ (giống hệt pattern đã gặp ở brand "Toprinse-P.R.C" trước đây).

**⚠️ Cảnh báo quan trọng về nhãn "Hoshizaki"**: Đã kiểm tra catalogue chính thức Hoshizaki (hoshizakiamerica.com) — hãng Nhật thật, chuyên máy làm đá/tủ lạnh công nghiệp. **Không có model "Nano" hay "SCNP" nào** trong dòng sản phẩm chính hãng. Dòng lọc nước thật của Hoshizaki dùng ký hiệu hoàn toàn khác (H9320-51, H9655-11, H9320-54...) và chỉ phục vụ máy làm đá, không phải hệ lọc toàn nhà 1.000-gallon. Nhãn "Hoshizaki" trên Vinbarista nhiều khả năng là **gắn sai/không có ủy quyền** — không phải bằng chứng ngược lại rằng "Scale Control" = Hoshizaki thật.

### Các ứng viên khác đã loại

| Ứng viên | Vì sao loại |
|---|---|
| Scale and Control Inc. (Phoenix, Arizona, Mỹ) | Tên gần giống nhưng chuyên cân xe tải/máy xúc công nghiệp (onboard truck/loader scales) — khác hoàn toàn ngành lọc nước |
| Charger Water Treatment Products — dòng "Scale Control" (Mỹ) | Đúng tên dòng sản phẩm + đúng ngành (chống cáu cặn), nhưng công nghệ khác hẳn: dùng media xúc tác biến khoáng chất thành hạt nano vô hại (không màng lọc), trong khi SCNP-1000L dùng màng UF PVDF + trao đổi ion — không khớp công nghệ, không có bằng chứng liên kết |
| American Home Water & Air — "Scale Control System" (Arizona, Mỹ) | Tên sản phẩm trùng nhưng là công ty dịch vụ lắp đặt địa phương, không phải nhà sản xuất, không có dấu hiệu liên hệ gì tới VN |
| Hoshizaki Corporation (dựa trên nhãn của Vinbarista) | Không tìm thấy model "Nano"/"SCNP" nào trong catalogue chính hãng — xem cảnh báo ở trên |

## Đề xuất

**KHÔNG tạo trang thương hiệu chính hãng "Scale Control"** vì không có hãng gốc quốc tế nào để dẫn nguồn. Đây thực chất là nhãn OEM/private-label/mô tả-chức-năng gắn cho một bộ lọc nước không rõ nhà máy xuất xứ. Nếu muốn giữ hiển thị cho khách hàng, nên cân nhắc đổi thành mô tả trung thực hơn (vd "Bộ lọc nước công nghiệp nhập khẩu") thay vì ngụ ý đây là một thương hiệu quốc tế có catalogue/bảo hành hãng. **Không nên mở rộng thêm sản phẩm** dưới tên "Scale Control" cho tới khi xác nhận được nhà cung cấp/nhà máy thật.

## Re-check sâu hơn (2026-07-13, theo yêu cầu user)

User yêu cầu kiểm tra kỹ hơn trước khi chốt "không có hãng thật". Đã làm thêm:

1. **Alibaba/1688/Made-in-China** theo đúng mã "SCNP-1000L"/"SCNP-1000LPH" — không có kết quả khớp. Chỉ ra các hệ RO 1000L/H chung chung từ nhiều xưởng TQ khác nhau, không xưởng nào dùng ký hiệu "SCNP".
2. **Dữ liệu hải quan/nhập khẩu VN** theo mã này — không có nguồn công khai tra được.
3. **"SCNP" như tiền tố SKU dùng chung cho cả dòng sản phẩm** — không tìm thấy hãng nào khác dùng tiền tố này ngoài 2 sản phẩm đã biết (saigonhoreca + vinbarista).
4. **Tổ hợp tiếng Việt/Anh khác** ("máy lọc nước công nghiệp SCNP xuất xứ", "SCNP-1000L nhập khẩu xuất xứ") — không ra thêm nguồn mới, chỉ lặp lại 2 trang đã biết.
5. **"Scale Control" như tên dòng sản phẩm thật của hãng lọc nước** — tìm lại rộng, xác nhận nhiều hãng thật (Charger Water Treatment, Water Control Corporation SC-Series, 3M/Aqua-Pure AP430SS, iSpring) đều dùng cụm "scale control/anti-scale" cho dòng sản phẩm của họ, nhưng đây là **thuật ngữ chung ngành**, không phải brand riêng, và không hãng nào khớp công nghệ UF PVDF 0.01μm + trao đổi ion + mã "SCNP".

### Phát hiện mới quan trọng

Cả 2 trang sản phẩm (saigonhoreca.vn và vinbarista.com) đều mô tả linh kiện bên trong là **"lõi lọc tinh UF: Scala Control"** + "lõi lọc thô: DFXCB". Điều này gợi ý mạnh: **"Scale Control"/"Scala Control" nhiều khả năng là tên của LÕI LỌC (cartridge) bên trong hệ thống, không phải tên hãng sản xuất cả bộ thiết bị.** Rất có thể người nhập sản phẩm ở saigonhoreca đã lấy nhầm tên linh kiện gắn vào trường "Thương Hiệu" hiển thị ngoài trang — giải thích hợp lý tại sao không tra ra được công ty/website chính hãng nào tên này.

**Kết luận sau re-check: (b) — vẫn KHÔNG xác nhận được hãng thật, kết luận cũ được củng cố thêm bằng bằng chứng mới, không đảo ngược.**

## Việc còn thiếu / không thể xác minh qua web

- Tên nhà máy thực sự sản xuất bộ lọc SCNP-1000L (không có catalogue công khai vì không có hãng gốc)
- Có phải cùng nguồn hàng với model SCNP-1000LPH đang bị Vinbarista gắn nhãn "Hoshizaki" hay không
- Lõi lọc UF ghi "Scala Control" (lệch 1 ký tự so với "Scale Control") trong mô tả gốc — lỗi đánh máy hay tên gọi riêng, cần hỏi người nhập liệu sản phẩm ban đầu
- Giá bán và ảnh sản phẩm chính hãng
- Việc có nên tiếp tục dùng tên "Scale Control" làm brand hiển thị, hay đổi thành mô tả trung thực hơn — quyết định thuộc về client
