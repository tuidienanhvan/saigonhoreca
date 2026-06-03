# 📋 Task Handoffs | Claude Opus 4.7 — Project Template Migration & Fix

Tài liệu bàn giao công việc chi tiết dành cho **Claude Opus 4.7** thực hiện việc khắc phục lỗi vỡ giao diện dự án (`project` Custom Post Type) và hoàn thành việc di chuyển (migration) + crawl dữ liệu tĩnh cho dự án **Casa Maria**.

---

## I. 🔍 Chẩn đoán lỗi vỡ giao diện (Root Cause Diagnosis)

### 1. Hiện trạng lỗi
* Khi người dùng truy cập các liên kết dự án thật trong database (như `/du-an/casa-maria/` hoặc `/du-an/the-cheezy-time/`):
  * Giao diện hiển thị bị vỡ nát, mang cấu trúc của một bài viết blog thông thường (hiển thị ngày đăng, số phút đọc, sidebar, font chữ và căn lề bị lệch).
  * Ảnh chụp màn hình lỗi ở local của người dùng hiển thị tiêu đề và ngày đăng `April 1, 2026` hoặc `February 1, 2026`.

### 2. Nguyên nhân gốc rễ (Root Cause)
* **Quy trình định tuyến cũ**: Trước đây, các trang dự án được phục vụ qua cơ chế định tuyến ảo của `inc/core/pillar-routes.php`. Nếu không có post thật trong database, nó giả lập trang và require trực tiếp file `page-templates/page-project-<slug>.php`.
* **Cơ chế bypass khi có post thật**: Khi chúng ta tạo Post thật trong Database thuộc Custom Post Type `project` để hiển thị ở trang danh sách dự án `/du-an/`, file `pillar-routes.php` sẽ **bypass** định tuyến ảo và nhường quyền quyết định cho WordPress Core.
* **Sự thiếu hụt template single-project**:
  * WordPress Core sẽ tìm kiếm template theo thứ tự ưu tiên:
    1. `single-project-{slug}.php`
    2. `single-project.php`
    3. `single.php` (Fallback mặc định)
  * Do trong theme hiện tại **không có file `single-project.php`** và đối với các dự án cũ (như `the-cheezy-time`), file template vẫn đang nằm ở `page-templates/page-project-the-cheezy-time.php`.
  * Do đó, WordPress Core fallback về load file `single.php` (mô hình bài viết blog thường), dẫn đến vỡ giao diện BEM của dự án.
* **Lỗi với Casa Maria**: Giao diện Casa Maria mới đã được tạo file [single-project/casa-maria.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/single-project/casa-maria.php), nhưng hook `single_template` ban đầu chưa xử lý chính xác đường dẫn hoặc bị bypass cache, dẫn đến việc không load đúng file template.

---

## II. 🛠️ Kế hoạch sửa lỗi tương thích ngược (Backward Compatibility Fix)

Cần cập nhật filter `single_template` và `body_class` trong [inc/core/pillar-routes.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/inc/core/pillar-routes.php) để tự động ánh xạ template cho cả dự án mới và cũ:

### 1. Cập nhật `single_template` filter
Sửa đổi filter để tìm kiếm template theo thứ tự:
1. Kiểm tra dự án mới trong thư mục `single-project/<slug>.php`.
2. Kiểm tra dự án cũ (tương thích ngược) trong thư mục `page-templates/page-project-<slug>.php`.
3. Nếu không tìm thấy, fallback về `$template` mặc định.

```php
add_filter('single_template', static function($template) {
    $post = get_queried_object();

    if ($post instanceof WP_Post && $post->post_type === 'project') {
        $slug = $post->post_name;
        
        // 1. Kiểm tra dự án mới ở single-project/
        $custom_template = get_template_directory() . '/single-project/' . $slug . '.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
        
        // 2. Tương thích ngược: Kiểm tra dự án cũ ở page-templates/
        $legacy_template = get_template_directory() . '/page-templates/page-project-' . $slug . '.php';
        if (file_exists($legacy_template)) {
            return $legacy_template;
        }
    }
    return $template;
});
```

### 2. Cấu hình `body_class` tương thích ngược
Đảm bảo rằng khi render Single Project CPT, body luôn có class `page-template-page-project-<slug>` và `page-template-page-project` để toàn bộ CSS cũ (được viết dựa trên các class này) hoạt động bình thường:
```php
add_filter('body_class', static function($classes) {
    global $post;
    if (is_singular('project')) {
        $slug = $post->post_name;
        $classes[] = 'page-template-page-project-' . $slug;
        $classes[] = 'page-template-page-project';
    }
    return $classes;
});
```

---

## III. 📥 Quy trình Static Mirror Crawl & Phân tách Template cho Casa Maria

Claude Opus 4.7 cần thực hiện các bước sau để đồng bộ hóa và nâng cấp trang dự án **Casa Maria**:

### Giai đoạn 1: Crawl dữ liệu tĩnh (Static Mirror)
1. Thực hiện crawl toàn bộ hình ảnh thực tế và tài nguyên của dự án Casa Maria tại 2 nguồn:
   * **Nguồn Tiếng Việt (.vn)**: `https://saigonhoreca.vn/du-an/casa-maria/`
   * **Nguồn Tiếng Anh (.com)**: `https://saigonhoreca.com/project/casa-maria/`
2. Tải toàn bộ các ảnh thực tế định dạng `.jpg` / `.png` / `.webp` và lưu trữ cục bộ vào các thư mục tương ứng trong theme:
   * `static-mirror/saigonhoreca.vn/wp-content/uploads/2026/05/`
   * `static-mirror/saigonhoreca.com/wp-content/uploads/2026/05/`
3. Tuyệt đối không sử dụng các hình ảnh placeholder của dự án khác (như `sheh-fung-*.jpg` hay `bi-quyet-tao-thuc-don-nha-hang.jpg`) hiện tại.

### Giai đoạn 2: Phân tách cấu trúc mã nguồn (Template Parts Breakup)
1. Đọc dữ liệu từ file `scripts/project-data/casa-maria.json` (nếu có) để có thông tin chuẩn.
2. Chia nhỏ file [single-project/casa-maria.php](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/single-project/casa-maria.php) thành các template part độc lập đặt trong thư mục `template-parts/project-pillar/casa-maria/`:
   * `hero.php` & `hero.css` — Banner chính của dự án.
   * `intro.php` & `intro.css` — Giới thiệu tổng quan và địa chỉ.
   * `concept.php` & `concept.css` — Ý tưởng thiết kế và bố trí công năng.
   * `partnership.php` & `partnership.css` — Các giải pháp bếp & bảo quản nguyên liệu.
   * `specs.php` & `specs.css` — Bản vẽ kỹ thuật chi tiết phối cảnh.
   * `gallery.php` & `gallery.css` — Thư viện ảnh thực tế thi công.
   * `related.php` & `related.css` — Liên kết các dự án liên quan.
   * `cta.php` & `cta.css` — Nút kêu gọi hành động (Call To Action).
3. Đăng ký import CSS của Casa Maria vào [assets/css/imports/_imports-project.css](file:///c:/Users/Administrator/Local%20Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/assets/css/imports/_imports-project.css).

### Giai đoạn 3: Thiết kế Đẳng cấp & Hoạt ảnh SVG
1. Áp dụng container `.pp-container` hoặc `.pp-container-shared` thống nhất.
2. Sử dụng khung ảnh luxury bo góc lượn sóng Địa Trung Hải `.pp-cma-luxury-frame` kèm 4 tai góc mạ đồng cổ điển.
3. Thiết kế các họa tiết trang trí SVG có hoạt ảnh CSS keyframe chuyển động mượt mà (cành lá ô liu đung đưa, ly rượu vang sóng sánh phản chiếu, đĩa tapas đặc sản).
4. Đảm bảo toàn bộ thẻ `<img>` đều có `loading="lazy"` và `decoding="async"` phục vụ tốt nhất cho SEO.

---

## IV. 🔍 Lệnh xác minh & Build (Verification Commands)

Sau khi hoàn thành sửa đổi, Claude Opus 4.7 cần chạy các lệnh sau để kiểm tra:

```bash
# 1. Compile CSS dự án
npm run build:project

# 2. Xóa cache transient & opcache thông qua URL clear-cache
curl -k "https://saigonhoreca.local/clear-cache.php"

# 3. Kiểm tra HTTP Status của các dự án
curl -k -I "https://saigonhoreca.local/du-an/casa-maria/"
curl -k -I "https://saigonhoreca.local/du-an/the-cheezy-time/"

# 4. Cập nhật đồ thị tri thức graphify
graphify update .
```

---

## V. 📝 Tiêu chí nghiệm thu (Acceptance Criteria)

* [ ] Truy cập `/du-an/casa-maria/` hiển thị chính xác giao diện đặc thù của Casa Maria, không còn thông tin bài viết blog thường (ngày đăng, số phút đọc mặc định, author) và không vỡ font.
* [ ] Truy cập các dự án cũ khác như `/du-an/the-cheezy-time/` hiển thị chính xác giao diện cũ của dự án đó mà không bị vỡ giao diện.
* [ ] Ảnh thực tế Casa Maria đã được tải về local và hiển thị đúng thay cho các ảnh placeholder.
* [ ] Lệnh `npm run build:project` hoạt động thành công và không tạo ra lỗi cú pháp.
