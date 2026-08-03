# Quy Chuẩn Áp Dụng CSS Tokens & Phong Cách Thiết Kế Giao Diện

Tài liệu này hướng dẫn cách áp dụng hệ thống Design System Tokens và Tailwind CSS v4 để xây dựng giao diện dự án con sang trọng, mượt mà và đồng nhất.

---

## 1. Nguyên Tắc "Zero Hardcoded Colors" (Không Viết Cứng Màu Sắc)

Mọi tệp tin CSS của dự án con tuyệt đối không được chứa bất kỳ giá trị mã màu cố định nào (ví dụ: `#f5a623`, `#0a0a0a`, `rgba(0,0,0,0.5)`). Tất cả phải được ánh xạ qua các biến CSS Token toàn cục đã định nghĩa sẵn trong [_tokens.css](file:///C:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/assets/css/_tokens.css).

### Bảng Tra Cứu Các Token Màu Sắc Chủ Đạo:

| Biến CSS Token | Giá Trị Màu Sắc | Ý Nghĩa Sử Dụng |
| :--- | :--- | :--- |
| `var(--gold)` | `#f5a623` | Màu vàng kim chủ đạo của thương hiệu (Brand Accent). |
| `var(--gold-dim)` | `#d18910` | Màu vàng kim tối hơn một tông, dùng cho trạng thái hover/active. |
| `var(--b1)` | `#0a0a0a` | Màu nền tối sâu nhất (Pure Dark Background). |
| `var(--b2)` | `#131313` | Màu nền thẻ, hộp thoại (Card/Elevated Surface). |
| `var(--b3)` | `#1a1a1a` | Màu nền các khu vực phụ trợ, input. |
| `var(--bc)` | `#ffffff` | Màu chữ chính (Primary Text). |
| `var(--bc2)` | `rgba(255,255,255,0.78)`| Màu chữ phụ, mô tả (Secondary Dimmed Text). |
| `var(--bc3)` | `rgba(255,255,255,0.55)`| Màu chữ mờ, ghi chú (Muted/Ghost Text). |
| `var(--bd)` | `rgba(255,255,255,0.08)`| Màu đường viền mảnh tinh tế (Subtle Border). |

---

## 2. Thiết Kế Hiệu Ứng Cao Cấp (Luxury Visual Effects)

### 2.1 Hiệu Ứng Kính Mờ (Glassmorphism Card)
Để làm nổi bật các khối chữ đè lên hình ảnh mà không gây rối mắt, hãy sử dụng nền mờ tích hợp hòa trộn màu `color-mix`:
```css
.pp-concept-glass-card {
  /* Pha trộn 75% màu base --b2 với transparent */
  background: color-mix(in srgb, var(--b2) 75%, transparent) !important;
  backdrop-filter: blur(30px) !important;
  -webkit-backdrop-filter: blur(30px) !important;
  border: 1px solid color-mix(in srgb, var(--gold) 15%, transparent) !important;
  box-shadow: var(--shadow-floating) !important;
}
```

### 2.2 Hiệu Ứng Chuyển Động Đàn Hồi (Spring Animation)
Sử dụng hàm Bezier đàn hồi `cubic-bezier(0.16, 1, 0.3, 1)` cho tất cả các hiệu ứng phóng to ảnh khi hover hoặc di chuyển thẻ chữ:
```css
.pp-image-container-shared img {
  transition: transform 0.6s var(--ease-spring) !important;
}
.pp-image-container-shared:hover img {
  transform: scale(1.04) !important;
}
```

### 2.3 Hiệu Ứng Bo Góc Lệch Phá Cách (Asymmetric Border Radius)
Đối với các layout tạp chí (Editorial), hãy áp dụng bo góc lệch để tạo điểm nhấn nghệ thuật độc đáo:
```css
@media (min-width: 992px) {
  .pp-concept-photo-container .pp-image-container-shared {
    border-radius: var(--radius-xl) 0 0 var(--radius-xl) !important; /* Chỉ bo góc bên trái */
  }
}
```

---

## 3. Tối Ưu Hiệu Năng & Trải Nghiệm Người Dùng (UX)

- **Tránh Cuộn Ngang (Horizontal Scroll)**: Trên hệ điều hành Windows, thanh cuộn chiếm khoảng 15px. Để tránh các phần tử trang trí SVG bị tràn lề ngoài gây lỗi cuộn ngang, hãy dùng `overflow-x: clip` trên thẻ container thay vì `overflow-x: hidden` (vì `hidden` sẽ phá vỡ tính chất `position: sticky` của các phần tử con).
- **Tăng Tốc Cuộn Trang (Content-Visibility)**: Áp dụng class `.sgh-cv-auto` cho các section nằm dưới màn hình đầu tiên (fold) để trình duyệt trì hoãn việc render cho đến khi người dùng cuộn tới gần nó.
- **Custom Scrollbar**: Đã được định nghĩa tự động tại `_tokens.css` với thanh kéo màu vàng kim mờ tinh tế trên nền đen sâu, giữ nguyên tính đồng bộ thẩm mỹ.
