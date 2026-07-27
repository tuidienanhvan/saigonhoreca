# Prompt mẫu: Audit toàn diện 1 hãng trong Product Catalog

Dùng prompt này (thay `{BRAND}` bằng tên hãng, VD: Vulcan, Sammic, Winterhalter...) khi bắt đầu 1 session mới để audit/hoàn thiện dữ liệu 1 hãng. Mỗi session chỉ làm 1 hãng, không chạy song song nhiều agent để tránh xung đột ghi đè file dùng chung.

---

## PROMPT

Check full lại toàn bộ hãng {BRAND} trong `docs/product-catalog/{brand-slug}/data-full.json`. Thực hiện tuần tự, KHÔNG được bịa/đoán bất kỳ dữ liệu hay ảnh nào — thiếu thì để trống, không đoán bừa.

### 1. Kiểm tra sản phẩm bị bỏ sót (so với sitemap sống, KHÔNG chỉ trang category)
- Category/listing page trên site hãng thường lazy-load hoặc phân trang, dẫn tới liệt kê thiếu link sản phẩm thật.
- Lấy toàn bộ URL sản phẩm từ `sitemap.xml` của hãng, lọc theo path category liên quan, so với URL đã crawl.
- Nếu số lượng URL từ sitemap NHIỀU HƠN số đã crawl → đây là dấu hiệu chắc chắn có sản phẩm bị bỏ sót do lazy-load, phải crawl bổ sung toàn bộ chênh lệch.
- Sau khi có danh sách URL đầy đủ, fetch từng trang, trích mã model, đối chiếu với JSON hiện có → liệt kê mã nào có trên site nhưng thiếu trong JSON.

### 2. Ảnh sản phẩm — lấy ảnh riêng từng model, không dùng ảnh banner chung
- Vào từng trang sản phẩm riêng lẻ (không phải trang danh mục/so sánh) để lấy ảnh `data-src` gắn với đúng model đó.
- Với CDN dạng Drupal/webdam (`webdam_asset/...?itok=xxx`): BẮT BUỘC giữ nguyên toàn bộ query string `?itok=...`, cắt cụt sẽ ra URL chết (404 nhưng vẫn tải về file có kích thước hợp lệ — không phát hiện được nếu chỉ check byte size).
- Sau khi tải mỗi ảnh: validate bằng PIL (`Image.open(path).load()` trong try/except) để bắt cả file lỗi/corrupt lẫn trang lỗi 404 giả dạng ảnh.
- Bọc mọi lệnh `curl`/network trong try/except — 1 timeout không được làm crash toàn bộ batch.
- Regex trích mã model phải xử lý được model có khoảng trắng (VD: `"Frymate VX15"`) — dùng `Model:\s*#([^<\n]+)` rồi `.strip()`, KHÔNG dùng `[A-Za-z0-9-]+` (sẽ cắt cụt tại khoảng trắng).

### 3. Kiểm tra ảnh trùng — PHẢI check cả path lẫn nội dung byte
- Check `image_local_path` trùng nhau giữa các model (dấu hiệu gán nhầm ảnh).
- **QUAN TRỌNG**: path không trùng KHÔNG có nghĩa ảnh không trùng. Phải hash MD5 nội dung từng file ảnh và so sánh — nhiều trang hãng tự phục vụ cùng 1 file ảnh cho các model khác nhau dù URL trang là riêng biệt.
- Với mỗi nhóm ảnh trùng nội dung: kiểm tra `source_url` của từng model liên quan — nếu source_url khác nhau, thực sự riêng biệt, và ảnh nhúng trong từng trang đó đúng là cùng 1 asset ID → đây là giới hạn thật từ phía hãng (không phải lỗi crawl), báo cáo minh bạch chứ không tự tạo ảnh khác.
- Nếu source_url giống hệt nhau hoặc là trang category/so sánh chung → không nhất thiết là lỗi crawl có thể sửa thêm. Trước khi kết luận, kiểm tra lại trang gốc: nếu trang liệt kê nhiều biến thể nhưng chính carousel/Model List trên trang CHỈ có ảnh cho một số mã (không phải toàn bộ) → đây là **giới hạn thật từ phía hãng**, không phải lỗi crawl bỏ sót — không cố "sửa" bằng cách gán bừa ảnh khác.
- **QUAN TRỌNG — lỗi minh bạch tên file (phát hiện ở Fujimak 21/07/2026, xảy ra ở HÀNG CHỤC cụm cùng lúc, không phải cá biệt)**: khi nhiều model dùng chung 1 ảnh thật (hợp lệ), file ảnh thường được đặt tên theo mã của MỘT model đại diện (VD: `FICPXS.jpg` dùng chung cho cả FICP61G, FICP62G...). Tên file này gây hiểu lầm là "ảnh riêng của FICP61G" dù thực chất không phải. Phải quét TOÀN BỘ các cụm trùng-ảnh-cùng-nhóm (không chỉ 1-2 cụm đầu tìm thấy), với mỗi cụm: xác định model nào là "chủ sở hữu" thật của ảnh (mã có trong tên file/Model List), đổi tên file thành dạng trung lập `{dòng_sản_phẩm}_Family_Representative_Image_no_individual_photo.{ext}`, và ghi rõ vào `note` của các model KHÔNG phải chủ sở hữu: "không có ảnh riêng trên site, đang dùng ảnh đại diện chung của dòng sản phẩm".
- **Xác minh chéo qua đại lý khi nghi ngờ thiếu ảnh riêng**: trước khi chốt "hãng không có ảnh riêng cho model X", thử `WebSearch` mã model xem có bị đại lý (Yahoo Shopping, Rakuten, Tenpos, MTC Kitchen...) bán lại không. Nếu trang đại lý chặn bot (Cloudflare/Akamai, response rỗng hoặc lỗi "Reference #..."), dùng `curl_cffi` (`from curl_cffi import requests; requests.get(url, impersonate="chrome")`) hoặc `cloudscraper` để vượt qua — cả 2 lib đã cài sẵn trên máy. So sánh `og:image` của đại lý với ảnh đã có trong catalog: nếu **trùng file/trùng nội dung** → xác nhận chắc chắn 100% không có ảnh riêng ở bất kỳ đâu (bằng chứng mạnh nhất, không cần nghi ngờ thêm). Nếu đại lý có ảnh KHÁC → đây là ảnh thật bị bỏ sót, cân nhắc bổ sung nhưng phải ghi rõ nguồn là đại lý (không phải chính hãng) trong `note`.
- Visual spot-check: dùng tool Read mở trực tiếp 8-10 ảnh ngẫu nhiên, đối chiếu với specs/tên model để bắt lỗi gán nhầm loại thiết bị (không chỉ dựa vào số liệu). Nếu user tự soi thấy nghi vấn trùng ảnh khi xem sample — luôn coi là tín hiệu đáng tin, đào sâu lại chứ không gạt đi.

### 4. SEO fields
- Check số dòng thiếu `seo_title`, `seo_description`, `description_en`.
- Nếu thiếu: fetch meta description/title thật từ chính trang sản phẩm hãng (thẻ `<meta name="description">`, `<title>`), hoặc dịch từ bản ngôn ngữ khác của hãng sang tiếng Anh nếu bản tiếng Anh không có — không tự viết mới nếu không có nguồn.

### 5. Chuẩn đặt tên file ảnh (bắt buộc)
- Tên file ảnh phải mang tính mô tả, KHÔNG dùng mã model trần trụi (VD: `VMCS-201.webp` → khó hiểu, không thân thiện SEO).
- Công thức: `{title_sản_phẩm_đã_slugify}_{mã_model}.{ext}`
  - Nếu `title` đã chứa sẵn mã model thì không lặp lại mã.
  - Slugify: bỏ ký tự đặc biệt, khoảng trắng → `_`.
  - Mã model luôn nằm ở cuối để đảm bảo tên file KHÔNG trùng nhau giữa các biến thể cùng dòng.
- Đã xác minh chuẩn này tốt cho SEO: search Google nguyên tên file (bỏ đuôi extension) ra đúng 1 kết quả khớp chính xác sản phẩm — VD: `12in_Wide_Grooved_Steel_Platen_Clamshell_Griddle_Accessory_2-Sided_Grill_Marks_208V_VMCS-201`.
- Áp dụng đổi tên cho MỌI file ảnh (kể cả ảnh cũ tên bằng mã model) ở cả 2 nơi: `wp-content/uploads/saigonhoreca/product-catalog/{brand}/` và `D:\Administrator\Downloads\Saigonhoreca_Product_Catalog\images\{brand}\`, đồng thời cập nhật `image_local_path` trong JSON (cả bản gốc trong `docs/product-catalog` và bản sync trong Downloads).

### 6. Dọn file rác
- Sau khi crawl lại ảnh thật, kiểm tra file ảnh cũ còn sót lại trên đĩa nhưng KHÔNG còn được `image_local_path` nào trong JSON tham chiếu tới (banner cũ, ảnh sai đã gỡ, v.v.) → liệt kê và xóa ở cả `wp-content/uploads/.../{brand}/` lẫn bản Downloads.

### 7. Link nguồn (source_url) — BẮT BUỘC phải hiển thị được ra ngoài HTML, không chỉ nằm trong data
- `source_url` (trang sản phẩm gốc trên site hãng) phải có mặt trong `data-full.json` (field `source_url`) VÀ trong `import.csv` (cột `meta:_source_url`) — bước này thường đã tự động đúng qua `generate_import_csv.py`.
- **Điểm hay bị bỏ sót**: có `_source_url` trong postmeta khi import KHÔNG có nghĩa nó hiện ra trên trang sản phẩm thật. Phải kiểm tra theme có thực sự render link này ra HTML không — check `single.php` (hoặc template single-product tương ứng) xem có đoạn code `get_post_meta($post_id, '_source_url', true)` được echo ra dưới dạng `<a href>` hay chưa.
- Theme `saigonhoreca-theme` đã được fix cố định 1 lần (21/07/2026) tại `single.php` — meta box sản phẩm giờ luôn hiện dòng "Nguồn: Trang sản phẩm chính hãng" link ra `_source_url` (rel="nofollow noopener noreferrer", mở tab mới). Chỉ cần đảm bảo mỗi sản phẩm của hãng đang audit có `source_url` không rỗng trong JSON/CSV — không cần sửa lại theme mỗi lần audit hãng mới.
- Nếu sau này đổi theme hoặc thêm CPT sản phẩm khác không dùng chung `single.php`, phải re-check bước hiển thị này.

### 8. Rebuild & sync — cẩn thận session song song
- Trước khi chạy rebuild pipeline dùng chung (manifest tổng, Excel master `Saigonhoreca_Product_Catalog.xlsx`): kiểm tra `mtime` của `data-full.json` các hãng KHÁC đang có thể được session khác chỉnh sửa song song.
- Nếu phát hiện file hãng khác vừa được sửa trong vài phút gần đây → KHÔNG chạy rebuild/sync toàn bộ (tránh ghi đè). Chỉ đồng bộ riêng file của hãng đang làm: `data-full.json`, `import.csv`, và thư mục ảnh sang cả `wp-content/uploads/.../{brand}/` và `D:\Administrator\Downloads\Saigonhoreca_Product_Catalog\` (data-json, images, import-csv).
- **QUAN TRỌNG — không có lưới an toàn git (phát hiện 21/07/2026)**: toàn bộ `docs/product-catalog/` hiện KHÔNG được git track (`git status` báo "Untracked files"), dù `wp-content` nói chung LÀ 1 git repo. Nghĩa là nếu 2 session cùng đọc-sửa-ghi 1 file `data-full.json` gần như đồng thời (kiểu read-modify-write không khóa file), **không có cách nào dùng `git diff` để xác minh có mất dữ liệu của session kia hay không** — chỉ có thể kiểm tra gián tiếp qua tổng số model/nhóm có bất thường không (giảm đột ngột, nhóm rỗng...).
- Trước khi sửa bất kỳ field nào của 1 hãng, LUÔN check mtime của `data-full.json` hãng đó trước — nếu vừa đổi trong vài phút gần đây, coi như đang có session khác hoạt động, cân nhắc bỏ qua hãng đó cho đến khi mtime ổn định.
- Đề xuất về lâu dài: `git add` toàn bộ `docs/product-catalog/` (sau khi xác nhận không có secret/credential trong đó) để có lưới an toàn diff được cho các lần sửa sau — nên hỏi ý kiến người dùng trước khi làm vì đây là quyết định ở tầm dự án, không tự ý thực hiện.

### 9. Ngôn ngữ — toàn bộ field tiếng Anh phải THẬT SỰ tiếng Anh (không lẫn tiếng Việt)
- Check 4 field `title`, `description_en`, `seo_title`, `seo_description` (và `group_name`) của MỌI model — không được lẫn tiếng Việt. Đây là lỗi hay bị bỏ sót vì các field này thường được đặt tên có chữ "_en" khiến ta lầm tưởng đã luôn là tiếng Anh.
- Cách phát hiện chính xác (tránh false positive với từ mượn tiếng Pháp/Ý/Tây Ban Nha như "café", "sauté" vốn cũng có dấu): chỉ match các ký tự/dấu CHỈ tồn tại trong tiếng Việt: `đ, ơ, ư, ă` và các tổ hợp dấu đôi kiểu `ấ ầ ẩ ẫ ậ ắ ằ ẳ ẵ ặ ế ề ể ễ ệ ố ồ ổ ỗ ộ ớ ờ ở ỡ ợ ứ ừ ử ữ ự ỹ ỷ`. KHÔNG dùng danh sách nguyên âm có dấu chung chung (à, á, é...) vì sẽ báo sai hàng loạt.
- Nguyên nhân phổ biến: các field này được nhiều session/nguồn khác nhau nhập liệu qua thời gian dài, một số lần nhập bằng tiếng Việt do quen tay hoặc do dịch tự động dở dang.
- Cách sửa AN TOÀN (không bịa): đây là dịch thuật (chuyển ngữ nội dung ĐÃ CÓ, không phải tạo nội dung mới) nên không vi phạm quy tắc "không bịa" — miễn giữ đúng nghĩa gốc. Với hãng có nhiều field theo template lặp lại (VD: "Lò Nướng Than Josper {model} — xuất xứ Tây Ban Nha. kích thước ..."), trích các cụm từ khác nhau (thường 20-140 cụm cho cả hãng), dịch từng cụm 1 lần, rồi áp dụng find-replace theo thứ tự cụm DÀI/CỤ THỂ nhất trước để tránh cụm ngắn thay thế nhầm bên trong cụm dài hơn.
- Sau mỗi lượt find-replace, luôn re-scan để bắt các cụm hiếm/không theo template (thường còn sót 5-15%) rồi dịch thủ công từng dòng còn lại.
- Best-effort đã áp dụng 21/07/2026 cho toàn bộ 17 hãng — nếu phát hiện thêm hãng mới hoặc field mới được nhập thêm sau này, chạy lại quy trình quét này.

### 10. Báo cáo cuối
Báo cáo bằng tiếng Việt, đầy đủ số liệu cụ thể:
- Tổng sản phẩm, tổng ảnh, sản phẩm/mã/ảnh trùng (path VÀ nội dung byte)
- Sản phẩm bị bỏ sót so với sitemap live (nếu có, đã bổ sung chưa)
- Số dòng bị gắn nhãn minh bạch "dùng ảnh đại diện chung, không phải ảnh riêng" (sau khi đổi tên file + xác minh chéo qua đại lý nếu cần)
- SEO completeness (số dòng thiếu từng trường)
- Số dòng thiếu `source_url` (nếu có), và xác nhận link nguồn có thực sự hiện trên trang HTML live không
- Kết quả spot-check ảnh trực quan
- Trạng thái sync (đã đồng bộ đâu, bỏ qua đâu và lý do)
