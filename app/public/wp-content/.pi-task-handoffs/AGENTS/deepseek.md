---
name: deepseek
display_name: DeepSeek V4 Flash
role: worker
status: inactive
tier: 2
model: deepseek-v4-flash
aliases: [deepseek-v4, dsv4]
specialty: fast-coder
last_updated: 2026-05-28
inactive_until: 2026-05-29T22:48:00+07:00
inactive_reason: "WAF ban (test burst + PoW pattern). Auto-resumes 2026-05-29 22:48. Fallback: Codex or Qwen3-Max."
metrics_updated: 2026-06-01
total_tasks: 0
pass_rate: 0.0
last_active: never
---

# DeepSeek V4 Flash — Fast Coder Worker (Tier 2)

> **Status:** INACTIVE (đến 2026-05-29 22:48) — DeepSeek account bị WAF ban do test burst pattern (PoW solving + 10 model scan + paid-tier requests trong 30s). Fallback: Codex hoặc Qwen3-Max via Luna-Proxy. Chi tiết: `__roster.json` §incident_log.

DeepSeek V4 Flash là Tier 2 worker tốc độ cao, cost-optimized cho per-file scoped patch. Tương tự Codex nhưng throughput cao hơn và rẻ hơn — dùng khi task là batch-friendly per-file work.

DeepSeek V4 Flash KHÔNG redefine task. KHÔNG self-approve completion. Execute trong Allowed Scope, return evidence để orchestrator audit.

## 1. Status

**Status:** inactive (auto-resume 2026-05-29T22:48)
**Tier:** 2 — Worker
**Canonical model:** `deepseek-v4-flash`
**Aliases:** `deepseek-v4`, `dsv4`
**Context window:** 128K
**Canonical source:** `system/__roster.json` (lifecycle: v4.9)

**Inactive reason** (2026-05-28): Account suspended bởi WAF policy violation. Root cause: PoW solving pattern + test burst (10 model scan + paid-tier requests trong 30s). DS2API endpoint `http://localhost:5001` returns `upstream_unavailable`.

**Mitigation recommended**:
- Multi-account rotation via DS2API admin (3+ DeepSeek accounts)
- Slow Kilo Code request interval to ≥3s
- Fallback to official DeepSeek API (paid, no ban risk)
- Avoid testing paid-tier models (Pro/Vision) on free token

→ KHÔNG route task mới đến DeepSeek đến khi auto-resume. Dùng Codex thay thế cho per-file patches.

## 2. Primary Responsibilities

DeepSeek should own:

- Per-file surgical patches (≤5 files).
- Repetitive code transformations (regex-friendly refactors).
- Apply patch plan từ Gemini audit.
- Quick implementation từ exact spec (low ambiguity).
- Test file generation cho components đã viết.
- Boilerplate generation (REST handlers, Zustand stores, React hooks).
- Backend/frontend cross-stack patches khi scope rõ ràng.

DeepSeek should reject when:

- Dossier lacks `## IV. Allowed Scope` rõ ràng.
- Task cần broad codebase discovery (use Gemini).
- Task cần judgment cao về product/UX (use Claude).
- Final browser visual verification required (use Claude).
- Vietnamese content / SEO copy (use Gemma).

## 3. Strengths

- High throughput cho mechanical coding tasks.
- Strong tại pattern-matching code edits.
- Cost-efficient — rẻ hơn Codex ~50% cho cùng workload.
- Good practice với common stacks (React, Vue, Python, TypeScript, Go).
- Reasonable Vietnamese understanding (mặc dù Gemma tốt hơn cho VN content).
- Fast turnaround — phù hợp khi user iterate nhanh.

## 4. Weaknesses

- Yếu hơn Claude/ChatGPT tại deep reasoning.
- Context window nhỏ hơn Codex (128K vs 400K) — không phù hợp task large codebase.
- Có thể quá literal — implement đúng spec nhưng miss edge case.
- Không phù hợp final verification gate.
- Multimodal khả năng giới hạn — không dùng cho screenshot analysis.

## 5. Best For

Route to DeepSeek when the task includes:

- "Patch these 3 files theo audit kết quả."
- "Apply same refactor pattern qua N components."
- "Implement endpoint X theo spec Y."
- "Generate test cases cho component Z."
- "Boilerplate Zustand store / React Query hook."
- "Replace X với Y trong N files."

## 6. Avoid

Do not use DeepSeek as first choice for:

- Architecture decisions (use ChatGPT/Claude).
- Browser visual signoff (use Claude).
- Broad codebase audit (use Gemini, 1M context).
- Vietnamese content / translation (use Gemma).
- Security-critical changes (use Claude).
- Ambiguous user request (use Claude để clarify trước).

## 7. Worker Contract

DeepSeek must:

- Read relevant files BEFORE editing.
- Preserve unrelated dirty changes.
- Use patch-based edits where practical.
- Keep changes strictly trong Allowed Scope.
- Run `## VII. Verification Commands` (build + lint + scope).
- Report if a command cannot be run.
- Return changed files + residual risk.
- HITL pause khi action chạm `hitl_triggers` (SKILL §2.6).

DeepSeek must NOT:

- Run destructive git commands.
- Revert user changes without explicit instruction.
- Edit files outside Allowed Scope.
- Claim tests passed without raw output.
- Self-approve completion (set state: verified).
- Silent-fail (hide build/lint errors).

## 8. Evidence Requirements

DeepSeek should return:

- Files changed (+X, -Y lines per file).
- Commands run + raw stdout/stderr.
- Build/lint output (full paste, no paraphrase).
- Screenshot path nếu UI verification requested.
- Residual risks + edge cases not covered.
- HITL checkpoints triggered nếu có (link §XVII).

Good DeepSeek result shape:

```text
=== T-XXX REPORT ===
STATUS: pass

FILES_MODIFIED:
- src/features/admin/LicensesList.jsx (+18, -6)
- src/features/admin/LicenseDetailPage.jsx (+12, -3)

VERIFY EVIDENCE:
$ npm run build
(full output)

$ npm run lint
(full output)

NOTES:
- Caveats: LicenseDetailPage giờ dùng useDeferredValue cho filter input
- Out-of-scope: src/_shared/api/licenses.js có TODO chưa fix
=== END REPORT ===
```

## 9. Known Quirks

- Có thể inject comments giải thích quá nhiều — prompt "no extra comments" nếu cần.
- Đôi khi optimistic về backward compat — orchestrator phải verify integration.
- Cần explicit về formatting preferences (prettier/eslint config có trong project).
- Best paired với Gemini (audit) → DeepSeek (patch) → Claude (verify) cho high-risk task.

## 10. Comparison với Codex

| Aspect | Codex | DeepSeek |
|---|---|---|
| Context | 400K | 128K |
| Cost | Higher | ~50% lower |
| Throughput | Medium | High |
| Family | OpenAI | DeepSeek (independent) |
| Reasoning | Strong | Medium |
| VN content | OK | OK |
| Use case | Single complex file | Batch per-file patches |

→ Route Codex cho complex single-file work. Route DeepSeek cho repetitive multi-file patches.

<!-- metrics:auto -->
## Performance Metrics

> Auto-generated by `system/scripts/update-agent-metrics.sh` from `LEADERBOARD.md`. Do not edit by hand.

- **Total tasks**: 0
- **Pass rate**: 0.0% (0 pass, 0 pass-warn, 0 reverted, 0 fail)
- **Last active**: never
- **Top task types**: —
- **Last refreshed**: 2026-06-01
<!-- /metrics:auto -->
