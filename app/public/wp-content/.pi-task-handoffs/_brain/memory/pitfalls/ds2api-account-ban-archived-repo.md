---
type: pitfall
title: ds2api (deepseek) account banned after using archived/abandoned GitHub repo
severity: high
domain: provider-account · deepseek · ds2api
source_tasks: [T-20260529-006]
hit_count: 1
detection_signature: |
  Symptom: deepseek worker via ds2api returns 403 / "account suspended" / empty
  401. Upstream account flagged. The ds2api repo used is GitHub-archived (read-only,
  "This repository has been archived by the owner").
  Quick check: open the ds2api repo URL → banner "archived"; curl /v1/models → 403.
last_updated: 2026-05-29
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# ds2api account ban do dùng archived/abandoned repo

## Symptom (cách nhận ra)

Worker `deepseek` (route qua `ds2api` reverse adapter) đột nhiên fail:
```
HTTP 403 — account suspended
# hoặc
{"error":{"message":"Your account has been restricted","type":"forbidden"}}
```
Trace lên: repo `ds2api` đang dùng đã bị GitHub **archived** ("This repository has been archived by the owner. It is now read-only.") → không còn maintain → endpoint/headers lỗi thời → upstream deepseek phát hiện pattern bất thường → ban account.

## Root cause

1. Repo ds2api archived = ngừng update → request signature / headers / endpoint path lệch so với deepseek upstream hiện tại.
2. Upstream anti-abuse detect adapter cũ + traffic pattern → flag → **ban account** (không chỉ rate-limit).
3. Dùng repo abandoned cho production worker = single point of failure + rủi ro account.

## Fix (lệnh chính xác)

1. **Ngừng ngay** route deepseek qua ds2api archived:
   ```bash
   # disable provider trong Luna/router config (qua /api/config, KHÔNG PowerShell)
   curl -s -X POST http://localhost:8088/api/config \
     -H "Authorization: Bearer $LUNA_KEY" -H "Content-Type: application/json" \
     --data '{"providers":{"ds2api":{"enabled":false}}}'
   ```
2. **Thay** bằng 1 trong:
   - Official deepseek API (api.deepseek.com) với key hợp lệ
   - Adapter ds2api **fork đang active** (không archived) — verify last commit <3 tháng
3. **Account bị ban**: tạo account mới / dùng key khác; account cũ thường không gỡ được.

## Prevention

1. **Rule**: TRƯỚC khi adopt 1 reverse-adapter repo → check repo KHÔNG archived + last commit gần đây (<90d) + issues active.
2. Doc trong `AGENTS/deepseek.md`: "ds2api archived → DEPRECATED, dùng official API".
3. Monitor: health-check provider; 403 burst → alert + auto-disable provider.
4. Đừng dùng 1 account duy nhất cho production worker — có backup route.

## First hit

- **Task**: T-20260529-006
- **Date**: 2026-05-29
- **Time wasted**: ~60m (tưởng key sai → mới phát hiện account banned + repo archived)

## See also

- `AGENTS/deepseek.md` — provider routing
- `playbooks/luna-proxy-full-tuning.md` §provider-health
