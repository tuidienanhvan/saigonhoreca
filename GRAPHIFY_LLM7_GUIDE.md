# Hướng Dẫn Trích Xuất Đồ Thị Ngữ Nghĩa Bằng LLM7 Proxy (Graphify) cho SaigonHoreca

Tài liệu này hướng dẫn chi tiết cách cấu hình và chạy trích xuất đồ thị tri thức ngữ nghĩa (Semantic Graph) cho dự án **SaigonHoreca** bằng API Key `llm7.io`.

---

## 💡 Quy Trình Làm Việc Lai (Hybrid Workflow)

Để tối ưu hóa chi phí token và thời gian phát triển:
1. **Hàng ngày (Phát triển nhanh)**: Chỉ chạy lệnh **Dùng Thường (Offline AST)** tại thư mục gốc dự án để đồng bộ cấu trúc code nhanh trong 1 giây mà không tốn token:
   ```powershell
   graphify update .
   ```
2. **Khi Review / Bàn giao (Milestones)**: Chạy **Dùng LLM7 (Semantic LLM)** để bổ sung mô tả nghiệp vụ và phân tích ngữ nghĩa sâu sắc.

---

## 🚀 Cách Chạy Trích Xuất Bằng LLM7 Proxy

API Key miễn phí của `llm7.io` (anonymous token) có các **giới hạn nghiêm ngặt**:
* **Tối đa 8,000 ký tự** cho mỗi request gửi lên (Lỗi 400 nếu vượt quá).
* **Tối đa 60 requests/giờ** và **10 requests/phút** (Lỗi 429 Rate Limit).

Chúng ta bắt buộc phải sử dụng bộ cờ tối ưu dưới đây để **lách hoàn toàn các giới hạn này**:

### 🛠️ Các Bước Thực Hiện:

**Bước 1**: Mở terminal tại thư mục gốc của dự án `saigonhoreca`.

**Bước 2**: Khởi chạy lệnh trích xuất với bộ cấu hình tối ưu:
```powershell
# 1. Thiết lập cấu hình kết nối proxy llm7.io
$env:OPENAI_API_KEY="cXThNrL9TBAUvI35qvKkysxF+BSldnZOW5Fqt3H9rSGzkISEPgcjUyNxdIbfM5v1vy+l52fkucw3X4NeByET5VihP/JegcY7oyj6VjHYrd6EMApW3CkGAiu7Ktd/qha4fusm"
$env:OPENAI_BASE_URL="https://api.llm7.io/v1"
$env:OPENAI_API_BASE="https://api.llm7.io/v1"

# 2. Chạy trích xuất semantic với chunk nhỏ (budget 1000) và tuần tự (concurrency 1)
graphify extract . --backend openai --model default --token-budget 1000 --max-concurrency 1
```

### 🧐 Giải thích các cờ tối ưu:
* `--backend openai`: Ép Graphify sử dụng OpenAI backend (đã được cấu hình đè base URL sang proxy `llm7.io` trong thư viện hệ thống).
* `--token-budget 1000`: Gom các file code thành các chunk nhỏ dưới 1000 tokens (~4000 ký tự), đảm bảo tổng dung lượng request luôn **dưới mức trần 8000 ký tự** của LLM7.
* `--max-concurrency 1`: Chạy tuần tự từng request một để không bao giờ bị khóa bởi giới hạn Rate Limit của proxy.

---

## 🎨 Cập Nhật Nhãn Cộng Đồng Tiếng Việt

Sau khi trích xuất hoặc cập nhật xong đồ thị, hãy chạy lại script phân tích để gán nhãn thông minh bằng tiếng Việt cho các cụm chức năng cốt lõi (Walker, Elementor, carousel...) và tự động sinh file tương tác 3D:
```powershell
python graphify-out/label_communities.py
```

### 📊 Kết quả đầu ra:
* **Báo cáo đồ thị tri thức chi tiết**: `graphify-out/GRAPH_REPORT.md`
* **Đồ thị trực quan 3D tương tác**: `graphify-out/graph.html` (Mở trực tiếp trên trình duyệt để xoay, thu phóng và khám phá cấu trúc code).
