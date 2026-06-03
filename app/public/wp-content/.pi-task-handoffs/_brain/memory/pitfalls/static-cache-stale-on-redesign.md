---
type: pitfall
title: Static cache serves stale CSS/JS after a theme redesign
severity: high
domain: wordpress · cache · theme · frontend
source_tasks: [T-Casa-Maria-redesign, session-2026-05-30]
hit_count: 2
detection_signature: |
  Symptom: after deploying a redesign, browser still shows old layout/colors.
  Hard-refresh fixes it locally but other users/CDN still see old. Asset URL has
  no version hash or same ?ver= as before.
  Quick check: curl -sI <site>/wp-content/.../style.css | grep -i 'age\|etag';
  view-source → asset URL unchanged after redesign.
last_updated: 2026-05-30
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Static cache serve asset CŨ sau redesign

## Symptom (cách nhận ra)

Sau khi deploy redesign theme (đổi CSS/JS/layout), user vẫn thấy giao diện CŨ:
- Hard-refresh (Ctrl+Shift+R) máy mình thì đúng → tưởng xong
- Nhưng user khác / mobile / qua CDN vẫn thấy bản cũ
- Asset URL không đổi version: `style.css?ver=1.0` y như trước

```bash
curl -sI https://site/wp-content/themes/x/style.css | grep -iE 'age|cache-control|etag'
# Age: 86400  → đang serve từ cache 1 ngày, chưa purge
```

## Root cause

Nhiều lớp cache cùng giữ asset cũ:
1. **Static page cache plugin** (WP Super Cache / W3TC / LiteSpeed) chưa purge sau deploy.
2. **CDN edge cache** (Cloudflare) giữ asset theo TTL.
3. **Browser cache** vì asset URL/`?ver=` KHÔNG đổi → trình duyệt coi là file cũ → không tải lại.
4. `enqueue` style với `$ver` hardcode/không bump → cache-busting fail.

## Fix (lệnh chính xác)

```bash
# 1) Bump version asset để cache-bust (cách bền nhất) — trong functions.php
#    wp_enqueue_style('theme', '.../style.css', [], filemtime($path));
#    → ?ver= đổi theo mtime mỗi lần sửa file

# 2) Purge static page cache plugin
wp cache flush
wp litespeed-purge all            # nếu LiteSpeed
# hoặc plugin UI → Purge All

# 3) Purge CDN edge
curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$ZONE/purge_cache" \
  -H "Authorization: Bearer $CF_TOKEN" -H "Content-Type: application/json" \
  --data '{"purge_everything":true}'

# 4) Verify hash/ver đổi
curl -sI https://site/.../style.css | grep -i 'age\|etag'   # Age nhỏ lại
```

## Prevention

1. **Cache-bust tự động**: enqueue dùng `filemtime()` làm `$ver` → asset URL đổi mỗi lần sửa file → browser tự tải lại.
2. **Deploy checklist** (thêm vào SKILL.md §IV): sau redesign theme → PHẢI (a) purge page cache, (b) purge CDN, (c) verify `Age` reset / hash đổi TRƯỚC khi set-state `verified`.
3. Verify ở **incognito + 1 thiết bị khác**, không chỉ hard-refresh máy mình.

## First hit

- **Task**: T-Casa-Maria-redesign
- **Date**: ~2026-05
- **Time wasted**: ~45m (tưởng CSS sai → thực ra cache stale)

## Subsequent hits

- session-2026-05-30 redesign khác → lặp lại → confirm cần checklist

## See also

- `playbooks/pi-task-handoffs-rename-v1.0.md` (cache-bust trong deploy flow)
- Candidate promote → SKILL.md §IV Quality Gate "post-redesign cache purge"
