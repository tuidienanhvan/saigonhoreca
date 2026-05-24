# ♾️ Hệ thống điều phối Pi Handoff / Pi Handoff System — v4.3 (Phase A/B Automation)

`.task-handoffs/` là hệ điều phối multi-agent cho Pi Ecosystem workspace. Mô hình **5-agent (3 Orchestrator + 2 Worker)** trimmed từ 9 agents ảo cũ — chỉ giữ model thực sự đang available. Atomic Phase A→D automation, zero-drift enforcement, commit-time gate.

**v4.3 highlights** (2026-05-16):
- 🆕 `new-task.sh` + `set-state.sh` — atomic Phase A/B (dossier + lock + STATUS row + transitions)
- 🧹 `prune-changes.sh` — auto-prune `changes/T-*/` folders >30d old
- 🪝 `install-hooks.sh` — git pre-commit gate (block commit on drift)
- 🩹 `lint-handoffs.sh --fix` — alias: reconcile --fix + re-lint
- 🩺 14 drift checks (A→N) hard-fail in --strict mode
- 📚 Single source of truth: `system/__roster.json`

> **TL;DR**: `new-task.sh` → fill scope → `set-state.sh ... dispatched` → worker returns → `set-state.sh ... verified` → `archive-task.sh` atomic close.

---

## I. 🚀 Bắt đầu tại đây / Start Here

1. 📖 Read `SKILL.md` — operating protocol bắt buộc cho mọi agent
2. 📂 Read `project/PROJECT.md` — workspace context + tech stack
3. 📊 Check `STATUS.md` — current active tasks
4. 🔄 Read `system/WORKFLOW.md` — full lifecycle Phase 0→E (v3.0)
5. ✏️  Create task: copy `system/templates/TASK.md`
6. 📁 Archive done: `bash system/scripts/archive-task.sh T-...`

---

## II. 👥 Mô hình Agent (2 tầng, 5 Agent) / Agent Model (2-Tier, 5 agents)

### 2.1 👑 Điều phối viên (Tầng 1) / Orchestrator (Tier 1)
- **Claude Opus 4.7** — Architect. Planning, multi-tool MCP, browser, final verify. 200K context. Always-on.
- **Gemini 3.0 Flash (Antigravity)** — Auditor. 1M context, SWE-bench 78%, batch refactor, agentic platform.
- **ChatGPT (GPT-5.4)** — Generalist. 1.05M context, SWE-bench ~80%, OSWorld 75% (beats human), frontier coding + reasoning.

### 2.2 👷 Thực thi viên (Tầng 2) / Worker (Tier 2)
- **Codex (GPT-5.3)** — Surgeon. Per-file surgical patch, legacy specialist, cost-optimized. SWE-bench ~80%.
- **Gemma 4 31B** — Local generalist. 256K context, multimodal, 0 API cost, quick fix + VN content. #3 open model on Arena leaderboard.

Per-agent capability + telemetry: `AGENTS/<name>.md`. Routing: `system/ROUTING.md`. Tier detail: `system/AGENT-MODEL.md`. Canonical: `system/__roster.json`.

---

## III. 🆔 Quy tắc đặt tên Task / Task Naming

```text
T-YYYYMMDD-XXX-<owner>-<slug>.md
```

- `YYYYMMDD`: date created
- `XXX`: 3-digit sequence per day
- `<owner>`: lowercase agent (claude, codex, gemini, poolside, ...)
- `<slug>`: kebab-case description

**Sub-task** (decomposition): `T-YYYYMMDD-XXX-{n}-<owner>-<slug>.md`
**Collision**: `T-007A`, `T-007B` (avoid same ID, use suffix)

---

## IV. 📂 Sơ đồ thư mục / Folder Map

```text
.task-handoffs/
├── README.md                ← this file
├── GUIDE.md                 ← quick workflow guide
├── SKILL.md                 ← agent operating protocol (v3.0)
├── STATUS.md                ← live dashboard (active + recent archive)
├── LEADERBOARD.md           ← append-only perf log
├── active/                  ← in-progress dossiers (state: drafted|dispatched|returned|verified)
├── archive/YYYY-MM/         ← closed dossiers (state: archived)
├── changes/                 ← high-risk decision records (risk: high|critical only — v3.0)
│   ├── README.md
│   ├── DECISION_TEMPLATE.md
│   ├── FORMAT.md
│   └── T-{id}-{owner}-{slug}/
│       ├── decision.md
│       ├── diff.patch
│       └── rollback-plan.md
├── AGENTS/                  ← per-agent capability + context window
│   ├── AGENTS.md            ← roster index
│   └── <name>.md            ← claude/gemini/chatgpt/codex/gemma (5 agents)
├── project/
│   └── PROJECT.md           ← workspace rules + tech stack
└── system/
    ├── WORKFLOW.md          ← lifecycle Phase 0→E (v3.0)
    ├── AGENT-MODEL.md       ← 2-tier hierarchy
    ├── ROUTING.md           ← task type → agent
    ├── QUALITY-GATES.md     ← 4 gates (Build/Lint/Scope/Logic)
    ├── REPORTING.md         ← REPORT format
    ├── locks/               ← heartbeat locks (atomic acquire — v3.0)
    ├── templates/
    │   ├── TASK.md              ← dossier template
    │   ├── PROMPT.md            ← worker prompt
    │   ├── COMMANDS.md          ← common commands
    │   ├── STATUS-ROW.md        ← STATUS.md row format
    │   ├── DECISION_TEMPLATE.md ← high-risk decision record template
    │   └── CHANGES-FORMAT.md    ← changes/ folder format spec
    └── scripts/             ← AUTOMATION (v3.0 NEW)
        ├── archive-task.sh  ← atomic Phase D
        ├── lint-handoffs.sh ← drift detector
        └── README.md        ← usage docs
```

---

## V. 🛡️ Các quy tắc cứng / Hard Rules

1. ❌ Không sửa plugin ngoài `plugins/pi-api/` nếu user không gọi tên rõ
2. ❌ Không claim `production-stable` nếu chưa chạy gates thật
3. ❌ Không nói `clean` nếu còn warnings/dirty artifacts/unverified assumption
4. ❌ Không khôi phục workflow/roadmap/status cũ đã lỗi thời
5. ✅ Mọi dossier mới PHẢI có YAML frontmatter (id, owner, state, priority, risk, created, updated)
6. ✅ Mọi `## Agent Result` PHẢI có raw command output trong `## Evidence`
7. ✅ Mọi state change → update `STATUS.md` + (nếu archive) append `LEADERBOARD.md`
8. ✅ Phase 0 (v3.0): paste user message verbatim trước khi draft (audit trail)
9. ✅ Phase D atomic: dùng `archive-task.sh`, không manual multi-step
10. ✅ High-risk task (risk: high|critical) PHẢI có `changes/T-{id}-*/` folder

---

## VI. ⚖️ Ba đạo luật cốt lõi / Three Laws

1. **Worker không tự duyệt mình** — orchestrator audit độc lập
2. **Orchestrator chỉ tin evidence** — không tin báo cáo paraphrase
3. **Dossier là sự thật duy nhất** — chat/voice/screenshot không có giá trị pháp lý

**Mantra**: _"No evidence, no archive. No dossier, no truth. No script, no atomicity."_

**Mantra**: _"No evidence, no archive. No dossier, no truth. No script, no atomicity."_

---

## VII. 📜 Version

- **v4.3** (2026-05-16) — Phase A/B automation: `new-task.sh` + `set-state.sh` + `prune-changes.sh` + `install-hooks.sh` + `lint --fix` alias. Pre-commit gate.
- **v4.2** (2026-05-15) — Slim roster trim 9→5 agents, merged HOW-TO-USE + AI-COLLAB, heading normalization
- **v4.1** (2026-05-14) — Per-agent metrics auto-derived from LEADERBOARD (Overstory pattern), lint checks M+N
- **v4.0** (2026-05-13) — Drift-kill: `__roster.json` SoT, `reconcile.sh`, 14 hard-fail lint checks
- **v3.1** (2026-05-10) — Fixed tier model contradiction, added Role Self-ID (§1.3)
- **v3.0** (2026-05-08) — Infinity Edition: Phase 0 + E, automation scripts, atomic locks
- **v2.1** (2026-05) — Multi-Agent Safe: lock system + cross-family audit
- **v2.0** (2026-05) — English translation
- **v1.0** (2026-04) — Initial 2-agent

_Pi Ecosystem — Infinity Edition v4.3_
