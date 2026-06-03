---
type: pitfall
title: PowerShell ConvertTo-Json silently strips Qwen JWT token from config.json
severity: high
domain: tooling · powershell · llm-proxy
source_tasks: [session-2026-05-30 Luna-Proxy tuning]
hit_count: 2
detection_signature: |
  Symptom: After PowerShell-based edit of data/config.json, Luna-Proxy returns
  401 or "No cookies provided" warning. JWT token (`token: "eyJ..."`) gone from
  providers[0].credentials.token field.
  Quick test: grep -oE '"token":"[^"]*"' config.json → empty value
last_updated: 2026-05-30
---

# PowerShell ConvertTo-Json silently strips Qwen JWT token from config.json

## Symptom

Sau khi sửa `Luna-Proxy/data/config.json` qua PowerShell:
```powershell
$config = Get-Content config.json | ConvertFrom-Json
$config.settings.tokenLimits.maxInputTokens = 240000
$config | ConvertTo-Json -Depth 10 | Out-File config.json
```

→ Restart Luna → 401 trên `/v1/models` HOẶC log `[QwenAI] Warning: No cookies provided`.

Quick check:
```bash
grep -oE '"token":\s*"[^"]*"' data/config.json
# Should show "token": "eyJhbGc..." — but empty after PowerShell edit
```

## Root cause

PowerShell `ConvertFrom-Json` + `ConvertTo-Json` có 2 vấn đề:
1. **Depth default = 2** → JWT trong `providers[0].credentials.token` nằm sâu hơn → bị truncate/drop
2. **UTF-16 BOM encoding** ghi đè JSON → Luna parse lỗi → fallback default → mất token

Ngay cả với `-Depth 10`, encoding BOM vẫn break:
```powershell
$config | ConvertTo-Json -Depth 10 | Out-File config.json -Encoding utf8
# UTF-16 BOM vẫn được prepend → invalid JSON cho Node parser
```

## Fix (lệnh chính xác)

**KHÔNG dùng PowerShell** để edit config.json có JWT/secret. Dùng 1 trong 3 cách:

### Cách 1 (preferred) — Claude Code `Edit` tool

```
Edit file: data/config.json
old_string: "maxInputTokens": 128000,
new_string: "maxInputTokens": 240000,
```
→ String-replace surgical, không re-serialize JSON, giữ nguyên token + encoding.

### Cách 2 — POST qua /api/config endpoint runtime

```bash
curl -s -X POST http://localhost:8088/api/config \
  -H "Authorization: Bearer $KEY" \
  -H "Content-Type: application/json" \
  --data '{"settings":{"tokenLimits":{"maxInputTokens":240000}}}'
```
→ Luna `configStore.updateConfig()` deep-merge → giữ providers untouched.

### Cách 3 — jq nếu cần shell

```bash
jq '.settings.tokenLimits.maxInputTokens = 240000' data/config.json > tmp && mv tmp data/config.json
# jq giữ encoding gốc, không depth-limit
```

## Prevention

1. **Doc rule**: Luna-Proxy `data/config.json` được mark **"DO NOT edit via PowerShell"** trong file comment header.
2. **Detection script** (suggest): `system/scripts/luna-config-guard.sh` chạy sau mỗi config update:
   ```bash
   grep -q '"token":\s*"eyJ' Luna-Proxy/data/config.json || echo "WARN: Qwen token missing!"
   ```
3. **Workflow**: Luôn dùng `Edit` tool surgical hoặc `/api/config` POST cho Luna config.

## First hit

- **Time**: 2026-05-30 ~10:00
- **USER**: setting maxInputTokens via PowerShell ConvertTo-Json
- **Symptom**: Luna 401 → "No cookies provided" → trace token vắng
- **Time wasted**: ~20 phút (tưởng Luna config schema sai)

## Subsequent hits

- Same session, attempt 2 with `-Depth 10` flag → still BOM issue → confirmed root cause

## See also

- `patterns/luna-config-runtime-api.md` (TODO)
- Luna-Proxy `src/configStore.ts::updateConfig()` — deep-merge implementation
