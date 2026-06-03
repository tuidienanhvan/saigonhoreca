# Memory Index — Pi Task Handoffs `_brain`

> Cập nhật: 2026-06-03 (reconstructed). Bảng tra **1 nguồn** cho patterns / pitfalls / agent-quirks / playbooks. Trước khi dispatch task → lọc theo domain/owner, add entry liên quan vào dossier §III.M (skill `reference-memory`).
>
> <!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

---

## I. Patterns (approach ĐÃ ăn)

| Entry | Domain | Source tasks | Confidence |
|---|---|---|---|
| [agentic-thinking-cap](patterns/agentic-thinking-cap.md) | llm-proxy · agentic-coding | session-2026-05-30 | 3 |
| [archive-checkpoint-step0](patterns/archive-checkpoint-step0.md) | tooling · atomicity | Pi-rename-2026-05-30 | 1 |
| [cline-xml-tool-detection](patterns/cline-xml-tool-detection.md) | llm-proxy · cline | session-2026-05-30, T-...-008 | 3 |
| [qwen-luna-cookies-fix-stream-cut](patterns/qwen-luna-cookies-fix-stream-cut.md) | llm-proxy · qwen · streaming | session-2026-05-30, T-...-008 | 3 |

---

## II. Pitfalls (lỗi + fix + dấu hiệu nhận sớm)

| Entry | Severity | Source tasks | Hit count | Dấu hiệu sớm |
|---|---|---|---|---|
| [ds2api-account-ban-archived-repo](pitfalls/ds2api-account-ban-archived-repo.md) | high | T-20260529-006 | 1 | deepseek 403 "suspended" · repo archived banner |
| [kilo-missing-context-window-field](pitfalls/kilo-missing-context-window-field.md) | low | session-2026-05-30 | 1 | Kilo không hiện % context · field thiếu trong UI |
| [luna-config-autosave-race](pitfalls/luna-config-autosave-race.md) | medium | session-2026-05-30 | 1 | update 200 rồi revert sau ~10s · config.json mtime sau POST |
| [pwsh-converttojson-strips-token](pitfalls/pwsh-converttojson-strips-token.md) | high | session-2026-05-30 | 2 | Luna 401 "No cookies" · token rỗng sau PowerShell edit |
| [qwen-xhigh-overthink-stun](pitfalls/qwen-xhigh-overthink-stun.md) | high | session-2026-05-30, T-...-009 | 3 | 30-40 tool calls/turn · output >32K truncate · qwen "đứng hình" |
| [static-cache-stale-on-redesign](pitfalls/static-cache-stale-on-redesign.md) | high | T-Casa-Maria, session-2026-05-30 | 2 | redesign xong vẫn thấy giao diện cũ · asset URL/ver không đổi |
| [trycloudflare-quick-tunnel-not-persistent](pitfalls/trycloudflare-quick-tunnel-not-persistent.md) | medium | session-2026-05-30 | 2 | trycloudflare URL chết HTTP 530 · restart đổi URL random |

---

## III. Agent-quirks (runtime behavior beyond AGENTS/*)

| Entry | Agent | Quirks | Promote status |
|---|---|---|---|
| [qwen](agent-quirks/qwen.md) | qwen | 4 (Q1 overthink · Q2 cookies · Q3 forced-thinking · Q4 XML parser) | Q2,Q3 promoted · Q1,Q4 pending |
| [claude-opus](agent-quirks/claude-opus.md) | claude | runtime notes | — |

---

## IV. Playbooks (e2e workflow đã validate)

| Entry | Khâu | Last validated |
|---|---|---|
| [luna-proxy-full-tuning](playbooks/luna-proxy-full-tuning.md) | qwen worker tuning: token→cookies→cap→tunnel | 2026-05-30 |
| [pi-task-handoffs-rename-v1.0](playbooks/pi-task-handoffs-rename-v1.0.md) | rename/restructure hệ thống → v1.0 | 2026-05-30 |

---

## V. Promotion candidates (lesson chín → nâng lên SKILL.md / AGENTS docs)

| Entry | Maturity | Target |
|---|---|---|
| qwen-xhigh-overthink-stun | hit_count 3 | → AGENTS/qwen.md §9 (invoke `promote-to-canonical`) |
| static-cache-stale-on-redesign | hit_count 2 | → SKILL.md §IV gate (post-redesign cache purge) — chờ hit 3 |
| agent-quirks/qwen.md Q1, Q4 | pending confirm | → AGENTS/qwen.md §9 |

---

## VI. Pending review (chưa finalize — invoke `harvest-and-review`)

| Stub | Source task | Status |
|---|---|---|
| [_pending/T-20260529-006](_pending/T-20260529-006-suggested.md) | T-...-006 (deepseek/ds2api) | pending-review (pitfall created) |
| [_pending/T-20260529-007](_pending/T-20260529-007-suggested.md) | T-...-007 (qwen crawl CSS) | pending-review (all §none → skip candidate) |
| [_pending/T-20260529-008](_pending/T-20260529-008-suggested.md) | T-...-008 (qwen stream cut) | pending-review (pattern created) |
| [_pending/T-20260529-009](_pending/T-20260529-009-suggested.md) | T-...-009 (qwen overthink) | pending-review (pitfall created) |

> Sau khi finalize entry từ stub → DELETE stub + cập nhật bảng này.
