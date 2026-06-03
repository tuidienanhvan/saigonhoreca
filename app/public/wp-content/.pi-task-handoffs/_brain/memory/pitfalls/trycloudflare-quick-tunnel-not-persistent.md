---
type: pitfall
title: Cloudflare Quick Tunnel URL changes on restart + dies randomly
severity: medium
domain: networking · tunneling
source_tasks: [session-2026-05-30 tunnel setup]
hit_count: 2
detection_signature: |
  Symptom: trycloudflare.com URL works initially → fails HTTP 530 after hours.
  Restart cloudflared → new random URL → team setup broken.
last_updated: 2026-05-30
---

# Cloudflare Quick Tunnel — URL changes on restart + dies randomly

## Symptom
- Setup `cloudflared tunnel --url http://localhost:8088` → URL `https://leaf-xxx.trycloudflare.com`
- Initial test works (200)
- Hours later: HTTP 530 (Cloudflare error)
- Restart cloudflared → NEW random URL `https://moon-yyy.trycloudflare.com`
- Team's saved config broken

## Root cause
Quick Tunnel **by design**:
- Anonymous (no account binding) → ephemeral URL each run
- No SLA → Cloudflare can rotate/cut anytime
- Best for **demo/testing only**, không phải production

## Fix
**3 options** tùy dùng:

### Option A — Localhost only (best nếu mọi việc trên 1 máy)
Dùng `http://localhost:8088/v1` trực tiếp. KHÔNG cần tunnel.
- Pros: never dies, no URL rotation
- Cons: chỉ máy này

### Option B — Named Tunnel (cần domain trên Cloudflare)
```bash
cloudflared tunnel create luna-proxy
cloudflared tunnel route dns luna-proxy qwen.your-domain.com
cloudflared tunnel run luna-proxy
```
- URL CỐ ĐỊNH: `qwen.your-domain.com`
- Auto-restart via Windows service / systemd
- Cần Cloudflare account + domain ($10/year nếu chưa có)

### Option C — Quick Tunnel + scripted URL fetch (workaround)
```bash
# Script tự lấy URL mỗi lần restart, post lên shared note
TUN_URL=$(grep -oE "https://[a-z-]+\.trycloudflare\.com" cloudflared.log | head -1)
echo "$TUN_URL" > shared-note.txt
```
→ Vẫn die ngẫu nhiên, không recommend cho production.

## Prevention
1. **Decision tree**:
   - Solo developer → Localhost only
   - Team trong văn phòng → Localhost + VPN/Tailscale (siêu bền + free)
   - Team distributed → Named Tunnel + domain
   - Demo 1 lần → Quick Tunnel OK
2. **Doc rule**: KHÔNG share Quick Tunnel URL với team như "production endpoint".

## See also
- `playbooks/luna-proxy-full-tuning.md` §X "Tunnel options"
- Cloudflare docs: Quick Tunnels are explicitly "ephemeral, no SLA"
