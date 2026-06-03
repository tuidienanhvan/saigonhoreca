---
type: playbook
title: Luna-Proxy full tuning for qwen workers (token + cookies + thinking cap + tunnel)
duration_est: ~3h first time · ~30m re-run (cached cookies)
last_validated: 2026-05-30 on session-2026-05-30
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Luna-Proxy full tuning for qwen workers

## Use case

Setup / tune Luna-Proxy để worker `qwen` (Qwen3.7-Max-Preview) chạy ổn định cho agentic coding task: auth bền (token + cookies), không stream cut, không overthink stun, expose context window cho client (Kilo/Cline), và optional tunnel cho team.

## Prerequisites

- Luna-Proxy đã clone + `npm install` + chạy được (`http://localhost:8088`)
- Browser session đăng nhập chat.qwen (để lấy cookies)
- `LUNA_KEY` (Bearer key của Luna admin API)
- Claude Code `Edit` tool (KHÔNG sửa config.json bằng PowerShell)

## Steps (copy-paste)

### Step 1 — Start Luna + smoke test
```bash
cd Luna-Proxy && npm run start &
curl -s http://localhost:8088/v1/models -H "Authorization: Bearer $LUNA_KEY" | jq '.data[].id'
```

### Step 2 — Set JWT token (auth cơ bản)
Sửa `data/config.json` bằng **Edit tool** (surgical string replace), KHÔNG ConvertTo-Json:
```
Edit data/config.json:
  "token": ""  →  "token": "eyJhbGc..."   (providers[0].credentials.token)
```
> Xem `pitfalls/pwsh-converttojson-strips-token.md` — PowerShell strip token.

### Step 3 — POST full cookies (fix stream cut)
```bash
curl -s -X POST http://localhost:8088/api/provider/token \
  -H "Authorization: Bearer $LUNA_KEY" -H "Content-Type: application/json" \
  --data '{"cookies":"cna=...; xlly_s=...; acw_tc=...; token=eyJ...; ssxmod_itna=...; ssxmod_itna2=..."}'
```
> Xem `patterns/qwen-luna-cookies-fix-stream-cut.md`. Đợi ≥1 autosave cycle (~10s) verify không revert (race — `pitfalls/luna-config-autosave-race.md`).

### Step 4 — Expose context window in /v1/models (Kilo/Cline %)
Đảm bảo handler `/v1/models` trả 4 alias field:
```ts
context_length: 262144, context_window: 262144,
max_context_length: 262144, max_model_len: 262144, max_output_tokens: 65536
```
> Xem `pitfalls/kilo-missing-context-window-field.md`.

### Step 5 — Adaptive thinking cap (chống overthink stun)
Verify `qwen-ai.ts` có 3-layer cap: detect agentic (4 signal incl. Cline XML) → smart default budget → force-cap 32K khi agentic + xhigh.
> Xem `patterns/agentic-thinking-cap.md` + `patterns/cline-xml-tool-detection.md` + `pitfalls/qwen-xhigh-overthink-stun.md`.

### Step 6 — XML parser robustness (qwen output quirks)
Verify parser: accept `<ml_tool_call>` standalone (không cần wrapper), filter empty-param tool calls, contract array/object → JSON in CDATA.
> Xem `agent-quirks/qwen.md` Q4.

### Step 7 — Tunnel (optional, cho team)
Quyết định theo nhu cầu:
- Solo / 1 máy → localhost trực tiếp (`http://localhost:8088/v1`), KHÔNG tunnel
- Team office → Tailscale/VPN (bền + free)
- Team distributed → Named Tunnel + domain (URL cố định)
- Demo 1 lần → Quick Tunnel OK (ephemeral)
> Xem `pitfalls/trycloudflare-quick-tunnel-not-persistent.md`.

## Verification
```bash
# Long-response smoke test (kiểm stream cut)
curl -s -N http://localhost:8088/v1/chat/completions \
  -H "Authorization: Bearer $LUNA_KEY" -H "Content-Type: application/json" \
  --data '{"model":"qwen","stream":true,"messages":[{"role":"user","content":"Viết hàm dài ~20K token..."}]}' \
  | tail -5
# Expected: stream tới "finished", KHÔNG dừng ở status:typing
# Expected: agentic task → log "Agentic → cap thinking →32768", 0 stun
```

## Common deviations

| Symptom | Cause | Fix |
|---|---|---|
| 401 / "No cookies provided" | token bị strip (PowerShell) | dùng Edit tool / `/api/config` (pwsh pitfall) |
| Stream cut ở `typing` | thiếu cookies | POST full cookies (Step 3) |
| Update revert sau 10s | autosave race | pause-autosave / verify-after-settle |
| qwen đẻ 40 tool calls, truncate | xhigh + agentic | thinking cap 32K (Step 5) |
| Kilo không hiện % context | thiếu context field | 4 alias field /v1/models (Step 4) |
| Tunnel URL đổi / chết | Quick Tunnel ephemeral | Named Tunnel / localhost (Step 7) |

## Maintenance schedule

- **Cookies refresh**: khi stream cut quay lại (cookies hết hạn ~vài tuần) → lặp Step 3
- **Token refresh**: khi 401 → lặp Step 2
- Health-check provider định kỳ; 403 burst → check provider account (vd `pitfalls/ds2api-account-ban-archived-repo.md`)

## See also

- `patterns/qwen-luna-cookies-fix-stream-cut.md`, `patterns/agentic-thinking-cap.md`, `patterns/cline-xml-tool-detection.md`
- `pitfalls/pwsh-converttojson-strips-token.md`, `pitfalls/luna-config-autosave-race.md`, `pitfalls/qwen-xhigh-overthink-stun.md`, `pitfalls/kilo-missing-context-window-field.md`, `pitfalls/trycloudflare-quick-tunnel-not-persistent.md`
- `agent-quirks/qwen.md`
