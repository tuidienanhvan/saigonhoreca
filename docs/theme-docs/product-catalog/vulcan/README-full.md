# Vulcan — Catalog (`data-full.json`)

Ngày tách thành brand riêng: **2026-07-19** · Brand string: **`Vulcan`**
File dữ liệu: `data-full.json` (1 nhóm · 31 model)

## Vì sao có brand riêng này

Dữ liệu này ban đầu được cào chung vào catalog **Hobart** (theo yêu cầu "bổ sung mảng Cooking Equipment cho Hobart"), vì hobartcorp.com có trang `/convection-ovens` dẫn hướng sang đây. Nhưng **Vulcan là một thương hiệu độc lập**, chỉ cùng thuộc tập đoàn mẹ **ITW Food Equipment Group** (Illinois Tool Works) với Hobart — không phải dòng sản phẩm của Hobart. Máy Vulcan in tên "Vulcan" trên tem/vỏ máy, không ghi "Hobart".

Ngày 2026-07-19, theo yêu cầu khách hàng, đã **tách riêng** thành catalog `vulcan/` để tránh gắn nhầm thương hiệu khi hiển thị lên site Saigon Horeca.

## Nguồn dữ liệu

Toàn bộ dữ liệu lấy thật từ **vulcanequipment.com** (Mỹ). Nhóm **Cooking Equipment** — 31 model: bếp gas/điện công nghiệp (V Series, ranges), lò nướng, lò hấp/combi, chảo nghiêng, fryer... Mỗi model có mô tả thật, thông số kỹ thuật, ảnh sản phẩm (`vulcanequipment.com` CDN), `source_url`.

`seo_title`/`seo_description` là bản gốc tiếng Anh — chưa Việt hóa.

`data.json`/`import.csv` chưa tồn tại cho brand này (chưa từng import vào WP trước đó) — đây là catalog hoàn toàn mới, cần review kỹ trước khi import lần đầu.
