# 🔧 `.task-handoffs/` Automation Scripts — v4.3

8 bash scripts cover full Phase A→D + drift gates + housekeeping. Chạy qua **Git-Bash** trên Windows.

## I. 📜 Scripts Overview

| Script | Phase | Purpose |
|---|---|---|
| `new-task.sh`         | A | Create dossier + lock + STATUS §I row (atomic) |
| `set-state.sh`        | B/C | State transition: YAML + timestamp + STATUS row movement |
| `archive-task.sh`     | D | Atomic archive: verified → archived (6+2 steps) |
| `reconcile.sh`        | All | Drift auto-repair: orphan, INDEX, version, lock, metrics |
| `lint-handoffs.sh`    | All | 14 drift checks (A→N), `--strict` hard-fail, `--fix` alias |
| `update-agent-metrics.sh` | D | Refresh per-agent metrics from LEADERBOARD (auto-called on archive) |
| `prune-changes.sh`    | Maintenance | Drop `changes/T-*/` folders >30d archived |
| `install-hooks.sh`    | Setup | Install `.git/hooks/pre-commit` running lint --strict |
| `format-md.py`        | Maintenance | Normalize H1 (emoji) + H2 (Roman+emoji) headings |

---

## II. 🆕 v4.3 Scripts (Phase A/B automation)

### `new-task.sh` — Atomic Phase A

```bash
bash system/scripts/new-task.sh \
  --owner codex --priority P2 --risk medium \
  --slug refactor-store-admin \
  --description "Refactor store admin to feature-slice" \
  --intent "<verbatim user message>"
```

**Operations**: auto-ID gen (NNN+1 of today) → copy template → fill frontmatter → inject Phase 0 → create lock → append STATUS §I row.

**Output**: `active/T-YYYYMMDD-NNN-<owner>-<slug>.md` + `system/locks/T-{ID}.lock` + STATUS row.

### `set-state.sh` — State transitions

```bash
bash system/scripts/set-state.sh T-20260516-001 dispatched
bash system/scripts/set-state.sh T-20260516-001 returned
bash system/scripts/set-state.sh T-20260516-001 verified         # → moves §I to §II
bash system/scripts/set-state.sh T-20260516-001 blocked --reason "build x3"
```

**State machine**: drafted → dispatched → returned → verified → (archive-task.sh) → archived. Any state can go to `blocked` → `reopened`.

**STATUS movement** (auto):
- `dispatched|returned` → stay §I, update state cell
- `verified` → §I → §II (Waiting for Review)
- `blocked` → §I/§II → §III (Blocked)

### `prune-changes.sh` — Housekeeping

```bash
bash system/scripts/prune-changes.sh --dry-run                 # list candidates
bash system/scripts/prune-changes.sh --fix                     # prompt y/N per folder
bash system/scripts/prune-changes.sh --fix --force             # no prompts
CHANGES_RETENTION_DAYS=60 bash system/scripts/prune-changes.sh --dry-run
```

**Policy**: delete `changes/T-*/` folder when parent task archived >30 days. Configurable via `CHANGES_RETENTION_DAYS` env.

### `install-hooks.sh` — Git pre-commit gate

```bash
bash system/scripts/install-hooks.sh             # install (backs up existing)
bash system/scripts/install-hooks.sh --status    # check if installed
bash system/scripts/install-hooks.sh --uninstall # restore .bak
```

**Effect**: every `git commit` runs `lint-handoffs.sh --strict` first. Drift → commit blocked. Bypass: `git commit --no-verify` (not recommended).

### `lint-handoffs.sh --fix` — Auto-repair alias

```bash
bash system/scripts/lint-handoffs.sh --fix
```

Runs `reconcile.sh --fix` (8 ops) then re-execs lint with `--strict`. Exit 0 if clean after fix.

---

## III. 📦 Pre-existing Scripts

### `archive-task.sh` — Atomic Phase D

```bash
bash system/scripts/archive-task.sh T-20260508-001
```

8 atomic steps: validate verified → check changes/ if high-risk → set archived → move file → update STATUS → append LEADERBOARD → reconcile post-archive → refresh agent metrics.

### `lint-handoffs.sh` — 14 drift checks (A–N)

```bash
bash system/scripts/lint-handoffs.sh           # warn-only default
bash system/scripts/lint-handoffs.sh --strict  # hard-fail all
bash system/scripts/lint-handoffs.sh --fix     # auto-repair + re-lint
```

| ID | Check |
|----|------|
| A | Active ↔ STATUS.md sync |
| B | Archive ↔ LEADERBOARD coverage |
| C | Frontmatter validity (state/owner) |
| D | STATUS.md date freshness ≤7d |
| E | Table integrity |
| F | High-risk archived → changes/ entry |
| G | Stale dispatched (>7d strict) |
| H | Version uniformity across docs |
| I | STATUS §IV entry count ≤15 |
| J | Roster ↔ AGENTS.md count |
| K | Archive INDEX.md accuracy |
| L | Misplaced archive files (root level) |
| M | Agent metrics freshness ≤7d |
| N | Roster ↔ AGENTS/<name>.md status+tier sync |

### `reconcile.sh` — Drift auto-repair

```bash
bash system/scripts/reconcile.sh --dry-run     # preview ops
bash system/scripts/reconcile.sh --fix         # apply all 8 ops
bash system/scripts/reconcile.sh --reap-stale  # only stale-lock op
```

8 ops: STATUS↔active sync, INDEX regen, roster cascade, §IV rotation, version sync, AGENTS.md count, stale lock reap, agent metrics refresh.

### `format-md.py` — Heading normalizer

```bash
python system/scripts/format-md.py .          # processes 24 system .md files
```

Rules: H1 = 1 emoji + title; H2 = Roman numeral + emoji + title. Code fences skipped. 55-entry semantic emoji map (status→🟢, lock→🔒, etc.).

---

## IV. 🪝 Recommended Setup

```bash
# 1. Install pre-commit gate
bash system/scripts/install-hooks.sh

# 2. Verify clean baseline
bash system/scripts/lint-handoffs.sh --strict

# 3. From now on, every commit auto-runs lint
```

---

## V. 🛠️ Requirements

- **Bash 4+** (Git-Bash on Windows OK)
- **Python 3** (only for `format-md.py`)
- **GNU coreutils** (`grep`, `awk`, `sed`, `find`)
- **jq** optional (for roster JSON parsing — fallback grep works)

## VI. 🔍 Troubleshooting

| Error | Fix |
|-------|-----|
| `Invalid task ID format` | Use `T-YYYYMMDD-NNN` exactly (3 digits) |
| `No file matching` | Check task is in `active/`, not archived |
| `Task state is 'X', expected 'verified'` | Use `set-state.sh <ID> verified` first |
| `Multiple files match` | Task ID collision — rename with suffix `T-XXX-A.md` |
| `Owner 'X' not found in __roster.json` | Check agent registered in `system/__roster.json` |
| `Invalid transition: X → Y` | See state machine in `set-state.sh --help` |
| `No .git/ found` (install-hooks) | Run inside git workspace |

## VII. 📅 Versioning

- **v4.3** (2026-05-16) — Phase A/B automation: new-task, set-state, prune-changes, install-hooks, lint --fix alias
- **v4.2** (2026-05-15) — Slim roster trim 9→5 agents
- **v4.1** (2026-05-14) — Per-agent metrics auto-derived (Overstory pattern)
- **v4.0** (2026-05-13) — `__roster.json` SoT, `reconcile.sh`, 14 hard-fail checks
- **v3.0** (2026-05-08) — Initial archive + lint
