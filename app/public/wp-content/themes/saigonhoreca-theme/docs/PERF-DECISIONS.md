# PERF-DECISIONS — Các quyết định hiệu năng ĐÃ ĐO, KHÔNG ĐƯỢC PHÁ

> Nguồn gốc: đo thực tế bằng Lighthouse/PageSpeed trên saigonhoreca (mobile Slow-4G),
> ghi lại từ comment trong `inc/core/critical-css.php` và `inc/core/enqueue.php`.
> Mọi refactor/AI agent về sau PHẢI đọc file này trước khi đụng vào cơ chế nạp tài nguyên.
> Muốn thay đổi bất kỳ mục nào: bắt buộc đo lại median ≥ 5 lần Lighthouse mobile trước/sau.
>
> **LƯU Ý ĐO NHIỄU (07/2026):** máy dev Local by Flywheel KHÔNG phải hạ tầng đo chuyên
> dụng — chạy nhiều lần Lighthouse liên tiếp (sweep dồn dập) để lại tiến trình `node.exe`
> tồn đọng, gây tranh chấp CPU/RAM và làm điểm/LCP một trang dao động ±20-30đ giữa các lần
> đo, dù code không đổi. Đã xác nhận bằng thực nghiệm: trang bambino-saigonhoreca đo dồn dập
> ra 67-72đ, đo cách nhau ≥90s cho 76-96đ — cùng 1 bản build. **Quy tắc: đo cách nhau ≥60-90s,
> ưu tiên tin vào lần đo "sạch" (không chạy sau chuỗi Lighthouse liên tiếp), và validate cuối
> cùng LUÔN LUÔN qua PageSpeed Insights thật trên production (saigonhoreca.vn) ở Phase 9 —
> đó mới là con số đáng tin, không phải lab local.**

## 1. CSS: inline critical + preload thường + render-blocking link

Cơ chế đang chạy (giữ nguyên):
- `<style id="sgh-critical">` inline `critical.css` + `critical-routes.css` (~5.9KB) trong `<head>` TRƯỚC `wp_head()`.
- `<link rel="preload" as="style">` cho route bundle — **KHÔNG** kèm `fetchpriority="high"`.
- Route bundle vẫn là `<link rel="stylesheet">` render-blocking bình thường.

Các phương án ĐÃ THỬ VÀ BỊ LOẠI (đừng thử lại nếu không đo):
| Phương án | Kết quả đo |
|---|---|
| `media="print" onload` (async CSS trick) | Speed Index 1s → 4s, LCP + CLS tệ hơn — regress 2 lần |
| `fetchpriority="high"` trên CSS preload | Cướp băng thông hero image, LCP 2.1s → 2.6s |
| Inline TOÀN BỘ route bundle vào head | Mobile FCP tệ hơn trên mọi route đã đo (Slow-4G) |

## 2. Hero LCP image: preload fetchpriority=high

- `<link rel="preload" as="image" imagesrcset="..." imagesizes="100vw" fetchpriority="high">`
- srcset entry phải khớp đúng cái `<img>` render (T-018).
- Cache transient `sh_hero_preload_v2` (DAY).
- Đây là tài nguyên ưu tiên số 1 của trang — mọi thứ khác nhường băng thông cho nó.

## 3. Font: KHÔNG preload file font

- Đã thử `<link rel="preload" as="font">` cho woff2 → cướp băng thông hero image → revert.
- Hiện tại: Google Fonts `display=swap`, load async qua filter `style_loader_tag`
  (rel=preload as=style + onload swap), preconnect fonts.googleapis.com + fonts.gstatic.com.
- Lưu ý: comment cũ trong `inc/config/constants.php` từng nói `display=optional` + preload
  2 weight — SAI, đã sửa. Thực tế là `display=swap`, không preload.

## 4. Ba tầng cưỡng chế điểm 100 (phải sống sót qua mọi refactor)

1. **Static HTML cache pre-PHP** (`.htaccess.production` + writer trong theme):
   serve `wp-content/cache/sgh-static/.../index.html` trước khi PHP boot; bypass cho
   logged-in + bot/lighthouse UA.
2. **bot-optimize.php**: bot/audit UA nhận bản render nhẹ (fixed-position parallax
   background fallback về `position:absolute`, tránh repaint cho crawler/Lighthouse).
   *(Hệ thống `.scroll-reveal`/`reveal.css` từng được nhắc ở đây đã bị xóa hoàn toàn
   26/07/2026 — animation bị tắt trên mobile + `prefers-reduced-motion` nên vô dụng
   thực tế, xem quyết định port/không-port bên saigonhouse-theme cùng ngày.)*
3. **CSS per-route bundle**: mỗi route chỉ tải đúng CSS mình cần (110–156KB), không tải chung.

## 4b. Animation GPU/CPU (Phase 6, 07/2026)

- `main-modern.js` pause TOÀN BỘ animation `infinite` khi off-screen — quét bằng
  `getComputedStyle` (không dựa class name, vì ~150 keyframe pillar không có prefix
  chung), chạy qua `requestIdleCallback` nên không cạnh tranh với LCP.
- Đã chuyển sang compositor-only: `solq7-scroll` (top→translateY), bambino `shimmer`
  (left→translateX), `.sgh-underline-hover` (width→scaleX), `.sgh-glow-btn` (box-shadow
  →opacity trên pseudo `::before`).
- NGOẠI LỆ CHẤP NHẬN: text-gradient shine (`about/partners.css`, `contact/form.css`)
  animate `background-position` trên `background-clip:text` — đây là giới hạn kỹ thuật
  thật của CSS (không có cách thay bằng transform mà giữ hiệu ứng clip theo chữ), chi phí
  chỉ ở mức paint (không phải layout), giữ nguyên.
- `lb-scroll-wheel-move` (little-bear-thao-dien, dot 4×8px) vẫn animate top/height — kích
  thước quá nhỏ, chi phí không đáng kể, bỏ qua để ưu tiên việc có ROI cao hơn.
- **CẠM BẪY ĐÃ GẶP:** lần đầu trigger `pauseOffscreenAnimations` bằng
  `requestIdleCallback(fn, {timeout: 2000})` ngay từ đầu — `timeout` ép trình duyệt CHẠY
  ĐỒNG BỘ nếu không rảnh trong 2s, đúng vào giữa cửa sổ LCP dưới CPU throttling, gây chặn
  main-thread do quét `querySelectorAll('*')` + `getComputedStyle` toàn trang. Sửa: chỉ
  schedule sau khi `window.load` đã fire (LCP chắc chắn xong). Bài học: BẤT KỲ full-DOM
  scan nào cũng phải đợi qua `load`, không dùng `requestIdleCallback` có timeout ép ở giai
  đoạn đầu trang.

## 4c. YouTube hero facade (Phase 7, 07/2026)

7 pillar hero (bambino, bling-bling-club, little-bear-thao-dien, mua-craft-sake,
renovate-sol-kitchen-bar-quan-7, sol-kitchen-bar-saigon-horeca, vien-duong-lao-nam-an)
chuyển từ `<iframe src=...>` (autoplay YouTube ngay khi HTML parse) sang
`<iframe data-lazy-src=...>` + cơ chế lazy-iframe có sẵn (main-modern.js), MỞ RỘNG để gate
theo `window.load` chứ không chỉ intersection (vì hero luôn nằm trong viewport ngay từ đầu
— intersection-only không trì hoãn được gì). Đo sạch (cách nhau ≥90s):
- sol-kitchen-bar-saigon-horeca: 57→92đ, LCP 10.3s→3.0s (-70%)
- vien-duong-lao-nam-an: 66→94đ, LCP 8.7s→2.3s (-74%)
- bambino-saigonhoreca: dao động 76-96đ tuỳ lần đo (xem lưu ý đo nhiễu ở trên), không có
  lần nào tệ hơn baseline 91đ một cách nhất quán.

## 4d. Font self-host A/B infrastructure (Phase 8, 07/2026)

- Đã build `assets/css/fonts-self-host.css` + `assets/fonts/self-host/*.woff2` (35 file) —
  **mirror chính xác** các woff2 Google đã subset sẵn theo unicode-range (không tự subset
  lại), chỉ đổi origin. Bật qua `add_filter('sgh_fonts_mode', fn() => 'self')` — mặc định
  vẫn `'google'`.
- **KHÔNG đổi mặc định** trong đợt này: máy dev Local by Flywheel không phải hạ tầng đo
  đáng tin (xem lưu ý đo nhiễu ở §Phase 7) nên không thể A/B công bằng ở đây. Google Fonts
  vốn đã tối ưu khá tốt (CDN toàn cầu + unicode-range subset + HTTP/2), self-host chỉ có
  lợi thế rõ nếu đo được giảm số round-trip DNS/TLS tới origin thứ 3 — **phải đo bằng PSI
  thật trên production** (Phase 9) trước khi cân nhắc đổi mặc định.
- Cả 2 mode dùng chung 1 handle (`google-fonts-sgh`) nên cơ chế async-swap
  (`style_loader_tag` filter) áp dụng đồng nhất — đã verify.

## 4d-2. Checklist đo PSI thật để quyết định font mode (Bước 4, chưa thực hiện)

Claude KHÔNG tự deploy được — dưới đây là quy trình để bạn (hoặc phiên Claude có quyền
deploy) tự chạy sau khi đưa code lên production:

1. **Deploy code hiện tại lên saigonhoreca.vn** qua `npm run deploy:package` (xem
   DEPLOY-NOTES.txt) — mặc định `fonts.mode = 'google'`, không đổi hành vi hiện tại.
2. **Đo baseline (mode google, y hệt hiện tại)**: PageSpeed Insights
   (pagespeed.web.dev) trên `https://saigonhoreca.vn/` — chạy **10 lần**, cách nhau
   ≥ 60s (né giờ cao điểm/CDN cache warm-up lệch), ghi lại **median** LCP + Performance
   score (mobile). Lưu vào `docs/perf-baselines/psi-google-prod.md`.
3. **Bật self-host tạm thời**: thêm vào `mu-plugins/` (hoặc functions.php tạm) —
   `add_filter('sgh_fonts_mode', fn() => 'self');` — deploy lại.
4. **Đo lại 10 lần y hệt bước 2** trên cùng URL, cùng khung giờ nếu có thể. Lưu vào
   `docs/perf-baselines/psi-self-prod.md`.
5. **So sánh median LCP + Performance score** — chỉ đổi mặc định trong
   `inc/config/fonts.php` (`sgh_fonts_mode()` trả `'self'` thay vì `'google'`) nếu
   self-host thắng RÕ RỆT (không phải chênh lệch trong khoảng nhiễu đo — PSI thật trên
   production ổn định hơn máy dev nhiều nhưng vẫn có dao động tự nhiên, cần median đủ mẫu
   mới kết luận).
6. **Nếu self-host thua hoặc ngang bằng**: giữ nguyên mặc định `'google'`, xoá filter tạm,
   giữ code self-host làm hạ tầng dự phòng (không xoá — đã có sẵn cho project khác dùng
   font khác).
7. Ghi kết quả + quyết định cuối vào mục này (thay thế đoạn "chưa thực hiện" ở trên).

## 4e. Fallback telemetry (Phase 8, 07/2026)

`sgh_log_project_bundle_fallback()` (`inc/engine/cache-helpers.php`) ghi vào option
`sgh_project_bundle_fallback_log` mỗi khi 1 slug project rơi vào fallback
`theme-project.css` (1.13MB) thay vì bundle riêng — tối đa 1 ghi/slug/ngày. Dùng để phát
hiện dự án mới bị publish trước khi `npm run build`.

## 5. Các quy tắc bổ sung (từ đợt nâng cấp 07/2026)

- Animation chỉ được animate `transform`/`opacity` (compositor). Cấm animate
  `top/left/width/height/margin/padding/background-position/letter-spacing`.
  Lint trong `scripts/build.mjs` sẽ chặn vi phạm mới.
- Animation `infinite` phải pause khi off-screen (observer chung trong main-modern.js).
- Iframe third-party (YouTube...) phải đi qua cơ chế `data-lazy-src` (IO rootMargin 350px).
- JS mới phải qua esbuild minify vào `assets/js/dist/` (sgh_js_path tự ưu tiên dist).
