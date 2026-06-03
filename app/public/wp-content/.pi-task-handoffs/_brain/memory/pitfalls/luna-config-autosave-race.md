---
type: pitfall
title: Luna-Proxy config autosave race overwrites runtime cookie/token update
severity: medium
domain: luna-proxy · concurrency · config
source_tasks: [session-2026-05-30 Luna-Proxy tuning]
hit_count: 1
detection_signature: |
  Symptom: POST /api/provider/token succeeds (200) but seconds later token/cookies
  revert to old value. data/config.json mtime shows a write right AFTER your POST.
  Quick check: tail Luna log for "config autosaved" timestamp > your update timestamp.
last_updated: 2026-05-30
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Luna-Proxy config autosave race ghi đè runtime update

## Symptom (cách nhận ra)

POST cookies/token mới qua `/api/provider/token` → 200 OK → test thấy work 1-2 request → rồi đột nhiên revert về giá trị cũ. Kiểm tra:
```bash
stat -c '%y' Luna-Proxy/data/config.json     # mtime mới HƠN lúc bạn POST
grep "config autosaved\|writeConfig" luna.log | tail -3
# → thấy autosave timestamp > update timestamp của bạn
```

## Root cause

Luna-Proxy có **2 đường ghi config.json đồng thời**:
1. Runtime API (`/api/provider/token`, `/api/config`) → cập nhật in-memory `configStore` + persist.
2. **Autosave timer** (định kỳ snapshot in-memory → disk) HOẶC một adapter khác load config từ disk vào memory.

Race: bạn POST update → in-memory đổi → nhưng autosave timer dùng **snapshot in-memory CŨ** (đã đọc trước POST) ghi đè disk → lần load sau đọc disk cũ → revert. Không có lock/version giữa write-paths.

## Fix (lệnh chính xác)

### Cách 1 (preferred) — quiesce rồi update atomic
```bash
# 1) dừng autosave / Luna trước khi sửa
curl -s -X POST http://localhost:8088/api/admin/pause-autosave \
  -H "Authorization: Bearer $LUNA_KEY"
# 2) update qua API
curl -s -X POST http://localhost:8088/api/provider/token \
  -H "Authorization: Bearer $LUNA_KEY" -H "Content-Type: application/json" \
  --data '{"cookies":"...; token=eyJ..."}'
# 3) resume
curl -s -X POST http://localhost:8088/api/admin/resume-autosave \
  -H "Authorization: Bearer $LUNA_KEY"
```

### Cách 2 — verify-after-settle (nếu không có pause endpoint)
POST update → đợi 1 autosave cycle (~10s) → re-check; nếu revert → re-POST. Lặp tới khi mtime ổn định ≥1 cycle.

### Cách 3 — stop Luna, Edit file surgical, start lại
```bash
# stop process → dùng Edit tool sửa data/config.json (KHÔNG PowerShell) → start
```

## Prevention

1. **Single writer**: chỉ update config qua API runtime KHI Luna đang chạy; KHÔNG sửa file disk song song lúc Luna live.
2. Feature request Luna: version/etag trên config (optimistic lock) để autosave bỏ qua nếu in-memory đã đổi.
3. Luôn verify-after-settle (đợi ≥1 autosave cycle) trước khi tin update đã bền.

## First hit

- **Date**: 2026-05-30
- **Symptom**: cookies POST OK rồi revert sau ~10s → stream cut quay lại
- **Time wasted**: ~25m (tưởng cookies sai)

## See also

- `patterns/qwen-luna-cookies-fix-stream-cut.md` — cookie update workflow bị race này ảnh hưởng
- `pitfalls/pwsh-converttojson-strips-token.md` — đừng sửa config qua PowerShell song song
- `playbooks/luna-proxy-full-tuning.md`
