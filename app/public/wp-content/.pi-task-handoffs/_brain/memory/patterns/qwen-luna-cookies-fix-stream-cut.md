---
type: pattern
title: Fix qwen-via-Luna stream cut by POSTing full cookies (not just JWT)
domain: llm-proxy · qwen · luna-proxy · streaming
source_tasks: [session-2026-05-30 Luna-Proxy tuning, T-20260529-008]
confidence: 3
last_updated: 2026-05-30
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Fix qwen (via Luna-Proxy) stream cut by refreshing full cookies

## Problem

Worker `qwen` chạy qua Luna-Proxy: với setup chỉ có **JWT token**, response dài (>15K tokens) bị cắt giữa chừng — stream dừng ở `status: typing`, không bao giờ tới `finished`. Log Luna:
```
[QwenAI] Warning: No cookies provided. This may cause Bad_Request error.
```
→ tool call cuối hỏng, todos dở (xem `agent-quirks/qwen.md` Q2).

## Approach (steps)

1. Lấy **full cookie set** từ browser session chat.qwen (DevTools → Application → Cookies), không chỉ `token`:
   ```
   cna · xlly_s · acw_tc · token · ssxmod_itna · ssxmod_itna2 · _gcl_au · cnaui · ...
   ```
2. POST nguyên cookie string lên Luna runtime endpoint (KHÔNG sửa config.json bằng PowerShell — xem `pitfalls/pwsh-converttojson-strips-token.md`):
   ```bash
   curl -s -X POST http://localhost:8088/api/provider/token \
     -H "Authorization: Bearer $LUNA_KEY" \
     -H "Content-Type: application/json" \
     --data '{"cookies":"cna=...; xlly_s=...; token=eyJ...; ssxmod_itna=..."}'
   ```
3. Restart KHÔNG cần — `configStore.updateConfig()` deep-merge runtime.
4. Smoke test 1 prompt dài → confirm tới `finished`.

## Why it works

Endpoint upstream của Qwen yêu cầu cookies (đặc biệt `ssxmod_itna*` chống bot + session affinity) để giữ stream sống cho response dài. JWT-only đủ auth ngắn nhưng upstream cắt long-stream khi thiếu session cookies → `Bad_Request`. Đẩy full cookies → upstream giữ kết nối tới hết.

## When to use

- Bất kỳ qwen task qua Luna-Proxy có output dài (refactor nhiều file, crawl, doc generation)
- Khi thấy log "No cookies provided" hoặc stream cut ở `typing`

## When NOT to use

- Task qwen ngắn (chat <15K) → JWT-only đủ
- Provider khác qwen (codex/deepseek/gemma) → không qua cookie mechanism này

## Variations tried

- **JWT token only** → long stream cut ~50% lần
- **token + chỉ `cna`** → vẫn cut (thiếu `ssxmod_itna`)
- **Full cookie set qua /api/provider/token** (this) → 0 cut verified
- **Sửa config.json qua PowerShell** → mất token (BOM/depth) → 401

## Maintenance note

Cookies hết hạn theo session browser (vài ngày–vài tuần). Khi stream cut quay lại → refresh cookies (lặp Step 1-2). Đây là root của `pitfalls/luna-config-autosave-race.md` khi refresh trùng autosave.

## See also

- `agent-quirks/qwen.md` Q2 (cookies needed) — promoted to AGENTS/qwen.md §4
- `pitfalls/pwsh-converttojson-strips-token.md` — đừng sửa config bằng PowerShell
- `pitfalls/luna-config-autosave-race.md` — race khi POST trùng autosave
- `playbooks/luna-proxy-full-tuning.md` — full setup e2e
