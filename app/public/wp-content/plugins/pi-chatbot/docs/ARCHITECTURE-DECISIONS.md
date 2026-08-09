# Quyết định kiến trúc — pi-chatbot

Tài liệu này ghi lại **những gì cố tình KHÔNG làm, và vì sao**.

Lý do nó tồn tại: tháng 8/2026 một agent viết lại toàn bộ plugin để "áp dụng
sách `datawhalechina/hello-agents`", tạo ra ~8.500 dòng mà phần lớn **không
chạy** — 5 class agent không có caller nào, 3 class memory chết hoàn toàn,
một FAQ engine chỉ ghi mà không ai đọc, và 4 "tool" phục vụ khách bằng dữ
liệu tự bịa. Kèm theo là 79 assertion test luôn xanh vì chúng chỉ kiểm tra
*cái vỏ có tồn tại không*.

Không có tài liệu này, người/agent tiếp theo sẽ nhìn thấy "thiếu pattern X"
rồi thêm lại đúng thứ vừa được gỡ đi.

---

## Tiêu chí quyết định (dùng lại được cho mọi pattern/thư viện mới)

Chỉ áp dụng một pattern khi nêu được **cả ba**:

1. **Bài toán cụ thể** nó giải — không phải "kiến trúc chuẩn hơn".
2. **Số đo** cho thấy bài toán đó có thật *ở dự án này*.
3. **Cách chứng minh** nó thật sự chạy sau khi làm — một test có thể ĐỎ khi
   tắt code đi.

Thiếu bất kỳ điều nào → **không làm**. Ghi lý do vào đây.

Chính `hello-agents` (Extra09) cũng liệt "❌ Over-engineering kiến trúc" là
một anti-pattern. Áp dụng chọn lọc là **tinh thần của sách**, không phải đi
ngược lại nó.

---

## Số đo thật của dự án (đo 2026-08-09, không phải ước lượng)

| Chỉ số | Giá trị |
|---|---|
| Sản phẩm trong index (`wp_pi_chatbot_index`) | **528** |
| Số intent router phân loại | **5** (`price`, `contact`, `service`, `project`, `product`) + `none` |
| Câu hỏi đã log | **4.298** (sau khi xoá 703 dòng do chính test sinh ra, 2026-08-09 — trước đó 5.001) |
| Độ trễ p50 / p90 | **65ms / 184ms** |
| PHP-FPM worker (`pm.max_children`) | **2** |
| Ngân sách LLM/ngày | 600 lệnh gọi |

> ⚠️ **Bẫy đã có người sập**: catalog nằm trong **DATABASE**, không nằm trong
> file. `grep` cả `wp-content` cho một mã sản phẩm sẽ **không thấy gì** dù sản
> phẩm có thật. Một lượt audit đã kết luận nhầm 4 mã Hoshizaki là "bịa" chỉ vì
> grep file. Mọi khẳng định *"dữ liệu X không tồn tại"* **bắt buộc** kiểm bằng
> truy vấn DB.
>
> (Con số 3.493 model đôi khi thấy trong ghi chú dự án là của kho catalog trên
> `D:\Documents`, **không phải** bảng index đang chạy — đừng lẫn hai cái.)

---

## Đối chiếu 10 pattern của hello-agents

### ✅ Hợp — giữ và phát triển

| Pattern | Vì sao hợp | Trạng thái |
|---|---|---|
| **Skill Assetization** | Giá trị đo được thật: một câu hỏi trùng FAQ đã duyệt trả lời với **0 lệnh gọi LLM**, thay vì tối thiểu 2 (router + generate) | Đã nối. `find_faq()` chạy trong `ask()`, có trang duyệt ở wp-admin mục 07 |
| **Toolification** | Đúng hướng — nhưng chỉ khi tool đọc dữ liệu **có nguồn thật** | Registry giữ lại; **0 tool** đăng ký. Xem mục "Vì sao không có tool nào" bên dưới |
| **Atomic Tool Design** | `execute_tool()` cô lập exception, validate schema (enum/minItems/maxItems) — hạ tầng thật, đáng có | Đã enforce đầy đủ |

### ⚪ Vốn đã có sẵn — "áp dụng" chỉ là đổi tên

| Pattern | Đã tồn tại từ trước dưới dạng |
|---|---|
| **Hybrid Generation** | Đường fast/max đã có từ lâu (`resolve_context()` chọn theo `difficulty` của router) |
| **Fallback Pattern** | Xoay vòng nhiều LLM key + degrade khi hết ngân sách (`class-budget.php`) |
| **GSSC — phần Compress** | `class-context-budget.php`. Ba phần còn lại (Gather/Select/Structure) chính là việc retriever đã làm. Hằng `ZONES` trong file đó **không ai đọc** — để lại như tài liệu, không phải code sống |

Ghi nhận điều này để không ai "áp dụng" lại lần nữa rồi tưởng đã thêm giá trị.

### ❌ Không hợp quy mô này — cố tình không làm

| Pattern | Lý do từ chối |
|---|---|
| **ReAct loop** (Thought → Action → Observation) | Xem mục riêng bên dưới — câu trả lời chính xác không phải "không áp dụng", mà là **đã có sẵn dạng cố định, cố tình không mở thành vòng lặp**. |
| **TODO-Driven Research** | Dành cho tác vụ nghiên cứu nhiều bước. Đây là hỏi–đáp 1 lượt về catalog. |
| **3-Tier Skill Loading** | Dành cho agent có nhiều chục skill cần nạp theo tầng. Bot này có 5 intent cố định. |

### N/A

| Pattern | Ghi chú |
|---|---|
| **AST-Safe Execution** | Không dùng `eval()` ở đâu cả — đạt sẵn, không có việc gì để làm. |

---

## ReAct: đã có "thinking + act", cố tình không mở thành vòng lặp

Nói "không áp dụng ReAct" là sai. Đối chiếu đúng thuật ngữ, pipeline hiện tại:

| Bước ReAct | Hiện thực trong code |
|---|---|
| **Thought** | Router (1 lệnh gọi LLM): phân loại intent, rút từ khoá, chấm độ khó, sinh biến thể truy vấn |
| **Action** | Truy xuất catalog theo kết quả đó |
| **Observation** | Reranker (đường `max`): LLM **đọc lại** kết quả, xếp hạng, loại bỏ cái không khớp |
| **Action** | Sinh câu trả lời |
| **Observation** | `Generator::verify()` fact-check |
| **Action** | Sinh lại 1 lần nếu verify trượt |

Thứ **cố tình không làm** là **vòng lặp mở** — để LLM tự chọn tool và tự quyết
lặp bao nhiêu vòng. Lý do: hiện **0 tool** đăng ký (xem mục dưới), 5 intent cố
định, và server chỉ có **2 PHP-FPM worker** nên mỗi vòng lặp thêm là rủi ro
xếp hàng thật, không phải chi phí lý thuyết.

### Ứng viên ReAct-lite đã cân nhắc và BÁC BỎ bằng số đo (2026-08-09)

Đề xuất hợp lý nhất: *quan sát truy xuất ra 0 kết quả → nhờ LLM diễn đạt lại →
thử lại 1 lần*. Bounded, không phải vòng lặp mở. Đã đo trước khi quyết:

| Chỉ số | Giá trị |
|---|---|
| Câu hỏi đã log | 4.890 |
| Không có context | 2.122 (**43,4%**) |
| — trong đó router có chạy | 706 |

43,4% nhìn thì lớn. Bóc 12 câu bị lặp nhiều nhất thì gần như **không câu nào**
là lỗi truy xuất:

| Loại | Ví dụ | Đánh giá |
|---|---|---|
| Rác test (đã dọn) | `xxxxx…` 24x, `câu hỏi faq test`, `hello there` | Không phải khách |
| Chào hỏi | `chào`, `chào bạn`, `chào shop` | **Đúng** — intent=`none`, cố ý không truy xuất |
| Câu tư vấn/quan điểm | *"nên đầu tư cao cấp hay mua rẻ trước"* | **Đúng** — không có SP cụ thể để tra |
| Tấn công | `ignore all previous instructions` | **Đúng** — bị chặn |
| `hoshizaki` 213x | | **Dữ liệu lịch sử.** Kiểm lại: `search("hoshizaki")` trả 5 kết quả, index có **206** SP Hoshizaki, chạy qua `ask()` thật cho `found_count=6`. Không phải lỗi đang sống |

→ Tiêu chí (2) *"số đo cho thấy bài toán có thật"* **không đạt** → không làm.

**Giới hạn của phép đo này, phải nói rõ**: bảng log **không lưu `intent`**, nên
không tách sạch được "rỗng đúng" (intent=`none`) khỏi "rỗng sai" (intent=
`product` mà tra không ra). Con số 706 chỉ là gợi ý. **Nếu sau này muốn mở lại
câu hỏi ReAct, việc đầu tiên là thêm cột `intent` vào log** rồi đo lại — đừng
quyết bằng cảm tính theo cả hai hướng.

---

## Vì sao hiện tại KHÔNG có tool nào được đăng ký

Bốn tool đã bị gỡ (2026-08-08) vì phục vụ khách **dữ liệu tự bịa**. Đối chiếu
với dữ liệu thật:

| Tool | Khẳng định | Thực tế trong catalog (528 SP) |
|---|---|---|
| `warranty_checker` | Hoshizaki 24 tháng, Berjaya 12, + linh kiện + SLA "4-8h" | Chỉ **3** SP nhắc "bảo hành"; 2 trong đó là vòi phun Toprinse "một năm". **Zero** dòng nào khớp |
| `price_calculator` | Chiết khấu 5/8/12%, ship 0/500k/1.5tr, lắp đặt 1tr | `chiết khấu` **0** · `phí giao` **0** · `phí vận chuyển` **0** · `phí lắp đặt` **0** |
| `product_comparer` | 'Nhật Bản', 'Tiêu chuẩn HORECA', '24 Tháng' | Comment trong code tự thú: `// Return synthetic specs` |
| `spec_evaluator` | IM-100CNE "100 kg/ngày", IM-240ANE "240 kg/ngày" | Listing thật: **95** và **205** Kg/24h — luôn **nói quá** |

**Vì sao fact-check không bắt được**: `Generator::verify()` chỉ kiểm *câu trả
lời có khớp context không*, **không** kiểm *context có đúng không*. Tool inject
ở `score => 999`, đè mọi kết quả catalog thật → context bịa làm câu trả lời bịa
trông "faithful".

Ba tool đầu bịa hoàn toàn. `spec_evaluator` tinh vi hơn — sản phẩm **có thật**,
chỉ thông số sai, và sai theo hướng nói quá khiến khách **mua thiếu công suất**.
Nhánh tính điện của nó (`I = P/(√3·U·cosφ)`, biên breaker 1.25) là kỹ thuật
đúng chuẩn và có thể khôi phục riêng.

**Điều kiện để viết lại tool**: dữ liệu phải đến từ (a) DB sản phẩm, hoặc (b)
chính sách chủ site đã xác nhận. **Hằng số gõ tay trong file PHP không phải là
nguồn.** File cũ nằm trong Recycle Bin, khôi phục được.

---

## Tầng multi-agent: có thật, nhưng phạm vi hẹp

Đã nối vào production (2026-08-08). `Pi_Chatbot_Supervisor_Agent::route_intent()`
là **nguồn sự thật duy nhất** cho ánh xạ intent → agent; agent được chọn cung cấp
prompt chuyên biệt, chèn vào system message ở section `4b`, **trước** section
`5. SECURITY` để prompt agent không bao giờ đẩy được phần bảo mật khỏi vị trí
cuối.

6 agent: `supervisor`, `general`, `sales`, `tech_spec`, `lead_capture`,
`warranty`.

**Giá trị thật của nó không phải "kiến trúc đẹp"** mà là chỗ đặt các ràng buộc
chống bịa: prompt của `warranty` cấm tuyệt đối nêu thời hạn bảo hành, `sales`
cấm nêu con số giá/chiết khấu, `tech_spec` cấm tự tính công suất. Trước khi nối,
những ràng buộc đó nằm trong file mà không ai đọc.

**Cố tình CHƯA làm**: handoff xuyên lượt (agent A chuyển tiếp sang agent B và
giữ trạng thái qua nhiều lượt). Cần thêm `session_id` ở REST + widget.
`class-handoff-state.php` đã sẵn sàng nhưng thiếu đầu vào. Với 5 intent và hội
thoại ngắn, đây đúng chỗ dễ over-engineer nhất — đo trước, quyết sau.

---

## Bài học về TEST (quan trọng ngang phần kiến trúc)

Bộ test cũ có 79 assertion "multi-agent" và luôn báo pass:

| Chỉ số đo được | Kết quả |
|---|---|
| Chạm đường request thật (`->ask()`/`->lead()`) | **0 / 79** |
| Không thể ĐỎ dù production đổi | **61 / 79** |
| Vẫn XANH nếu MỌI tool trả `[]` | **49 / 79** |
| Khẳng định số liệu **bịa** là ĐÚNG | **18** |

Nghĩa là chúng **chủ động chống lại** việc sửa cho trung thực, và còn chứa một
câu `DELETE` có thể xoá nhầm dữ liệu production.

**Quy tắc bắt buộc từ nay**: mỗi test mới phải chứng minh ĐỎ được — tắt đúng
dòng code nó bảo vệ, xác nhận đỏ, bật lại, xác nhận xanh.

Hai cái bẫy đã sập thật trong lúc làm việc này, ghi lại để tránh:

1. **Script tắt code không khớp** (lỗi escape) → test vẫn xanh và trông như đã
   "chứng minh". **Luôn kiểm file thật sự đã đổi** trước khi tin kết quả.
2. **Dữ liệu test không thực tế** → `estimate_tokens()` đếm theo *từ*, nên
   `str_repeat('z', 2000)` chỉ là **2 token**; 40 dòng như vậy lọt ngân sách và
   test xanh vì lý do hoàn toàn sai.

---

## Thứ đang có mà cần biết trước khi đụng vào

| Thứ | Trạng thái |
|---|---|
| **33 FAQ chờ duyệt, 0 đã duyệt** (2026-08-09) | wp-admin → Pi Chatbot → mục 07. Duyệt = câu đó được phục vụ **nguyên văn**, bỏ qua cả model lẫn fact-check. Con số "repeats" chỉ đếm số lần **bot lặp lại chính nó**, không phải xác nhận đúng sai |
| ⚠️ Hàng đợi này từng chứa **dữ liệu bịa** | Đỉnh điểm 95 dòng, **63** trong đó là chuỗi test *"Giá tham khảo khoảng 15.000.000đ tùy cấu hình"* — giá chưa ai ở cửa hàng báo bao giờ — cộng 2 dòng *"bảo hành 24 tháng"* sót từ tool đã gỡ. Duyệt nguyên hàng đợi lúc đó sẽ **công bố giá bịa** thành câu trả lời chính thức. Đã dọn; test không còn ghi vào bảng này (teardown ở `tests/lib/harness.php`) và fixture không còn giống báo giá thật. **Vẫn nên đọc từng dòng trước khi duyệt** |
| `find_faq()` chỉ chạy khi **chưa có lượt nào của khách** | Điều kiện cũ là "lịch sử rỗng", và với widget đang chạy thì **không bao giờ đúng** (widget tự chèn lời chào), nên tính năng này chết hoàn toàn trên production. Nó khớp theo text đơn thuần, không biết ngữ cảnh — câu follow-up giống hệt nhau nghĩa khác nhau, nên lượt của khách trước đó vẫn phải chặn |
| `estimate_tokens()` đếm từ, không đếm ký tự | Văn bản không dấu cách bị ước lượng thấp nghiêm trọng. Cố ý chưa sửa — đổi bộ ước lượng là đổi hành vi ngân sách toàn hệ thống, cần đo với tokenizer thật |
| `admin_email` = `dev-email@wpengine.local` | Thông báo lead đã bypass sang email thật của theme, nhưng thông báo WP core khác thì chưa. Đổi khi lên production |
| `wp_mail()` mất ~2s và trả `false` trên máy dev | Thông báo lead đã đẩy sang hook `shutdown`; chỉ thật sự cắt được độ trễ cho khách trên PHP-FPM có `fastcgi_finish_request()` |
