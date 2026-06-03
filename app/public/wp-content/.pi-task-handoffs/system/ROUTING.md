# 🚥 Ma trận điều phối Agent / Agent Specialty & Routing Matrix — v4.3

This document defines how the Orchestrator assigns tasks to specific agent families based on their technical strengths.

---

## I. 🤖 Hồ sơ năng lực Agent / Agent Profiles (The 9-Model Roster, 2-Tier)

### 👑 Điều phối viên (Tầng 1) / Orchestrator (Tier 1)
- **1.1 Claude (The Architect)**: Complex planning, deep reasoning, and high-level orchestration.
- **1.2 Codex / ChatGPT (The Surgeon)**: High-precision implementation, UI/UX synchronization, and logic fixing.
- **1.3 Gemini / Antigravity (The Auditor)**: Codebase scanning, rapid retrieval, and consistency audits.

### 🛠️ Thực thi viên (Tầng 2) / Worker (Tier 2)
- **1.4 Poolside (Laguna M.1)**: CSS/Tailwind specialist and React hook refactoring.
- **1.5 Arcee (Trinity Large)**: Multi-step logical planning and architecture design.
- **1.6 Nemotron (NVIDIA Super)**: Deep technical reasoning, debugging, and performance optimization.
- **1.7 Grok (xAI Fast)**: Speed demon for quick fixes, scripts, and boilerplate.
- **1.8 Ling (inclusionAI)**: Polyglot for SEO, marketing content, and translations.
- **1.9 Stepfun (Flash)**: Ultra-light for quick lookups and small UI tweaks.

> **Note (v3.1)**: Tier 1 agents can also self-implement tasks ≤30 min (Orchestrator-direct mode). Tier 2 agents ONLY execute within Allowed Scope of a dossier.

---

## II. 🗺️ Chiến lược điều phối / Routing Strategy (The Logic)

| Task Category | 🥇 Primary Agent | 🥈 Fallback Agent |
|---|---|---|
| **Architectural Planning** | 👑 Claude | 🛠️ Arcee |
| **Feature Implementation** | 👑 Codex | 👑 Claude |
| **CSS / UI / Animations** | 👑 Codex | 🛠️ Poolside |
| **Deep Debugging / Perf** | 🛠️ Nemotron | 👑 Claude |
| **Linting / Cleanup** | 👑 Gemini | 🛠️ Grok |
| **Security / Auth** | 👑 Claude | 🛠️ Nemotron |
| **SEO / Content** | 🛠️ Ling | 👑 Gemini |
| **Translation / Docs** | 🛠️ Ling | 👑 Claude |
| **Quick Fix / Scripts** | 🛠️ Grok | 🛠️ Stepfun |

---

## III. 🪜 Logic xử lý phân cấp / Escalation Logic

### 3.1 🔄 Chính sách thử lại / Retry Policy
- If an agent fails once, provide corrective feedback (raw error logs) and retry **one** time.

### 3.2 🪜 Phân cấp chiều dọc / Vertical Escalation
- If a Tier 2 worker fails, escalate to a Tier 1 orchestrator (e.g., Grok fails → Claude handles).
- If a Tier 1 agent fails, decompose the task into smaller sub-tasks per WORKFLOW.md §3.8.

---

## IV. 🛡️ Quy tắc an toàn điều phối / Routing Safety Rules
1.  **Scope Verification**: Never assign a task to an agent whose "Context Window" is too small for the scope.
2.  **Specialization Guard**: Avoid using "Generalist" agents for "Specialist" tasks (e.g., avoid using Gemini for high-precision CSS animations).
3.  **Conflict Check**: Ensure the assigned agent is not already handling a task with an overlapping scope.
4.  **Status Check (v3.1)**: Always verify agent status in `AGENT-MODEL.md` dashboard before routing. Skip agents with 🔴 INACTIVE.

---

## V. 📜 Changelog
- **v3.1** (2026-05-10): Fixed tier model — unified to 2-tier (was 3-tier contradicting other docs). Added status check routing rule.
- **v3.0** (2026-05-08): Initial v3.0 with routing matrix.
