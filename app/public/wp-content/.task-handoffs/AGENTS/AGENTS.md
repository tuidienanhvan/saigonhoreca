# 📌 AGENTS — Model Reference (v4.2)

> Single source of truth: `system/__roster.json`. Run `bash system/scripts/reconcile.sh --fix` to re-sync this table.

| Agent | Tier | Model | Context | Role | Status |
|---|---|---|---:|---|---|
| claude   | 1 — Orchestrator | claude-opus-4.7    |   200K | Architect — planning, MCP, browser, final verify              | 🟢 active  |
| gemini   | 1 — Orchestrator | gemini-3.0-flash   |    1M  | Auditor — broad scan, batch refactor, Antigravity agentic     | 🟢 active  |
| chatgpt  | 1 — Orchestrator | gpt-5.4            |  1.05M | Generalist — frontier coding + reasoning + computer use 75%   | 🟢 active  |
| codex    | 2 — Worker       | gpt-5.3-codex      |   400K | Surgeon — per-file surgical patch via Codex CLI/IDE           | 🟢 active  |
| gemma    | 2 — Worker       | gemma-4-31b        |   256K | Local generalist — quick fix, VN content, multimodal, 0 cost  | 🟢 active  |

<!-- roster:count -->
**Total: 5 agents (3 Orchestrator + 2 Worker)** — see `system/__roster.json` for canonical roster.
<!-- /roster:count -->

## I. 🏛️ Tier Model

**Tier 1 — Orchestrator** (Claude, Gemini, ChatGPT): can plan, dispatch, audit, verify, archive. Can also self-implement when task < 30 minutes. Never delegate to each other — only down to Tier 2.

**Tier 2 — Worker** (Codex, Gemma): execute strictly within `## Allowed Scope` of dispatched dossier. Must self-check capability before starting (return `state: rejected` on mismatch).

## II. 🎯 Specialty Quick-Ref

- **Browser visual / MCP / final verify** → claude
- **Codebase-wide sweep + batch refactor** → gemini (1M context)
- **Multi-domain task (code + analysis + content)** → chatgpt (1.05M context, OSWorld 75%)
- **Surgical 1-file patch / cost-optimized code** → codex
- **Quick 1-liner / VN content / offline / multimodal** → gemma (local, 0 API cost)

## III. 🗑️ Removed in v4.2

Aspirational agents Poolside/Laguna/Arcee/Nemotron/Grok/Ling/StepFun (never operationally available) removed. Historical LEADERBOARD entries preserved for audit. Past CV files deleted. See `__roster.json` `removed_in_v4_2` field for record.
