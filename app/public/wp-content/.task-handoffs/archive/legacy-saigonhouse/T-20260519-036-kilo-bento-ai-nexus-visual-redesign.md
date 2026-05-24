---
id: T-20260519-036
owner: kilo
state: drafted
priority: P1
risk: high
estimated_minutes: 180
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Gemini]
created: 2026-05-19 15:00
updated: 2026-05-19 15:00
---

# 📋 T-20260519-036 | kilo | bento-ai-nexus-visual-redesign — Premium Holographic Neural Nexus UI Overhaul

## I. 🎯 Goal

Thiết kế lại toàn bộ visual phần **AI Nexus Card** (Kết nối đa dạng) trong Bento Grid trang chủ để đạt độ thẩm mỹ tối thượng (Premium UI/UX standard), loại bỏ hoàn toàn sự trống trải chiều dọc và thay thế các phẳng chip đơn điệu bằng một **Holographic Control Deck / 3D Neural Nexus** đầy sống động.

Các cải tiến cốt lõi yêu cầu:
1. **Premium Holographic Core**: Biến lõi trung tâm thành một khối pha lê phát sáng 3 chiều (3D Crystal Core) với các xung nhịp sóng năng lượng (Ripple Pulse Rings) chạy liên tục.
2. **Glassmorphic Service Cards**: Thay thế các chip text phẳng bằng các tấm Glassmorphic Card nhỏ có chiều sâu, viền sáng Gradient, màu sắc riêng của từng hãng (Gemini Blue, Claude Orange, GPT Green, Groq Coral, Mistral Amber) và hiển thị thêm các chỉ số high-tech giả lập (ví dụ: `99.8% ACC`, `2.4k t/s`, `12ms`).
3. **Sequential Neon Data Flows**: Vẽ đường mạch điện (SVG Circuit lines) kết nối các card hãng với Core. Có vệt sáng Neon chạy dọc mạch (stroke-dashoffset animation) đổ dẫn về trung tâm để thể hiện luồng dữ liệu thông minh.
4. **Cosmic Particle Nebula**: Thêm một lớp nền hạt bụi phát sáng chuyển động xoáy nhẹ trong không gian card, tạo cảm giác vô cực lấp đầy khoảng trống chiều dọc của card.

---

## II. 🚧 Allowed Scope

```
# Core Components
pi-store-webapp/src/features/home/components/bento/BentoAINexusCard.jsx
pi-store-webapp/src/features/home/components/bento/BentoAINexusCard.css

# Layout Sync
pi-store-webapp/src/features/home/components/HomeBento.css
pi-store-webapp/src/styles/index.css

# Dossier
.task-handoffs/active/T-20260519-036-kilo-bento-ai-nexus-visual-redesign.md
```

---

## III. 🚫 Out Of Scope

- ❌ Thay đổi Grid Layout (cấu trúc grid 6 cột vẫn giữ nguyên).
- ❌ Can thiệp logic các card khác (Hạ tầng, Hub VN, Metrics, Router).
- ❌ Add thêm thư viện ngoài (chỉ dùng CSS Animations + React + Inline SVG).

---

## IV. 🎨 Premium Redesign Specification

### IV.1 🧠 3D Crystal Core & Ripple Rings
- Lõi Pi trung tâm sử dụng hiệu ứng bóng kính cường độ cao (`backdrop-filter: blur(10px)`) bọc lớp viền đỏ đặc trưng của Pi.
- Phát ra 3 vòng sóng lan tỏa (Ripples) có kích thước tăng dần và nhạt dần ra xa.
- Kỹ thuật: Sử dụng `animation` có `scale` và `opacity` tuần tự (`animation-delay: 0s, 1s, 2s`).

### IV.2 🎴 Glassmorphic Service Cards
Thay thế `nexus-chip` cũ bằng `nexus-card` cao cấp:
- Có cấu trúc rõ ràng:
  - Header: Logo biểu tượng chấm tròn neon chớp tắt nhịp thở (Pulse) + Tên hãng (Gemini, Claude, GPT, Groq, Mistral).
  - Subtitle: Phiên bản model (`3.5`, `4.5`, `5o`).
  - Stat badge: Một hàng thông số mono nhỏ bên dưới (e.g. `latency: 12ms` hoặc `t/s: 2.1k`).
- Hiệu ứng:
  - Background: `linear-gradient` cực tối bọc glassmorphism.
  - Border: Dùng `color-mix` với màu sắc nhận diện hãng.
  - Khi hover: Card phát sáng rực rỡ và nghiêng nhẹ (`transform: rotateY(15deg) rotateX(10deg)`).

### IV.3 ⚡ Sequential Neon Data Flows
- Vẽ các đường dẫn SVG mảnh kết nối trực tiếp từ rìa mỗi Card hãng đến tâm Core Pi.
- Kỹ thuật:
  - Sử dụng SVG `path` với `stroke-dasharray` để tạo các luồng sáng đứt quãng.
  - Di chuyển luồng sáng bằng `animation: flowEffect 3s linear infinite`.

### IV.4 🌌 Cosmic Nebula Particle Storm
- Tạo các hạt neon li ti (`.nebula-particle`) bay chậm rãi theo quỹ đạo elip xoắn ốc để phủ đầy phần nền trống trải của card.
- Dùng `radial-gradient` lớn phía sau làm nguồn sáng nền huyền ảo (Nebula Glow) hòa trộn giữa sắc xanh đỏ và tím mờ.

---

## V. 🛠️ Execution Phases

### Phase 1: Audit & Prototype (20 min)
- Nghiên cứu CSS variables của `--p`, `--b1`, `--b2` trong `index.css` để đảm bảo màu sắc hòa hợp tối đa.
- Phác thảo layout tọa độ elip cho 5 Card AI hãng trong khung hình elip nằm ngang để tối ưu hóa khoảng trống.

### Phase 2: React Component Overhaul (50 min)
- Cập nhật `BentoAINexusCard.jsx`: Thay đổi thẻ UI biểu diễn 5 hãng sang định dạng card thông số cao cấp.
- Thêm SVG Data Streams kết nối chính xác theo tọa độ trung tâm.
- Thêm danh sách các particle chuyển động ngẫu nhiên.

### Phase 3: CSS Styles & Animations (80 min)
- Viết CSS hoàn chỉnh trong `BentoAINexusCard.css`.
- Triển khai hiệu ứng nghiêng 3D (3D Tilting Effect) cho card khi hover.
- Thiết lập chuyển động mạch dữ liệu neon và hạt vũ trụ bay.
- Tinh chỉnh padding và các khoảng trống dọc để card đầy đặn, sang trọng nhưng không bị ngộp.

### Phase 4: Verification & Polish (30 min)
- Verify hiển thị mượt mượt ở màn hình desktop (100% z-index đúng, không đè lên text).
- Test responsive trên Mobile/Tablet để đảm bảo các card không bị tràn ra ngoài biên bento shell.
- Đảm bảo lệnh `npm run build` của `pi-store-webapp` hoàn thành thành công không có lỗi CSS/JS.

---

## VI. ✅ Acceptance Criteria

- [ ] Lõi trung tâm Pi chuyển thành **3D Holographic Core** phát sóng lan tỏa.
- [ ] 5 AI model chuyển thành các **Glassmorphic Service Cards** có thông số giả lập (`latency`, `acc`).
- [ ] Các đường SVG dẫn luồng neon phát sáng chạy nhấp nháy tuần tự về Core Pi.
- [ ] Nền card có hạt bụi neon bay chậm và quầng sáng mờ **Nebula Glow** lấp đầy khoảng trống dọc.
- [ ] Hiển thị hoàn hảo, không bị cắt xén biên, responsive mượt mà trên mọi thiết bị.
- [ ] `npm run build` trong `pi-store-webapp` chạy thành công.

---

## VII. 📋 Worker Prompt for Kilo Specialist

```
Bạn là Kilo, AI Engineer chuyên về Premium UI/UX Animation.
Nhiệm vụ của bạn là thực thi dossier:
.task-handoffs/active/T-20260519-036-kilo-bento-ai-nexus-visual-redesign.md

Đọc kĩ specification trong file dossier trên. Tiến hành thiết kế lại toàn bộ visual của BentoAINexusCard (cả JSX và CSS) thành một Holographic Control Deck siêu đẳng cấp sử dụng 3D Crystal Core, Glassmorphic Service Cards (có stat), Neon Flows và Nebula Particles.

Hãy đảm bảo card lấp đầy khoảng trống dọc một cách cân đối, nghệ thuật và build thành công!
```
