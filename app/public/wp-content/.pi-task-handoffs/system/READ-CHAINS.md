# Read-Chains — đọc gì, theo thứ tự nào (per role/phase) — v1.5

> **1 nhà** cho "đọc gì khi nào" của hệ. Gom từ: `../SKILL.md` §0 (bootstrap) · dossier §III Required Reading + §III.M Memory · `prime-context.sh`. Mọi session bắt đầu bằng prime-context.
> Quy ước: **[full]** đọc kỹ · **[ref]** lướt/khi cần · **(out)** tạo ra.

---

## 0. MỌI session (bắt buộc TRƯỚC)
1. `bash system/scripts/prime-context.sh` — active tasks + memory (pitfalls/patterns) + skills + pending. (`../SKILL.md` §0)
2. [`../SKILL.md`](../SKILL.md) [full] — operating protocol (lần đầu session).
3. Role self-ID (SKILL §1.3): **Orchestrator** / **Worker** / **Orchestrator-direct**.

## A. ORCHESTRATOR — tạo + dispatch task
1. [`../STATUS.md`](../STATUS.md) [ref] — task đang chạy.
2. [`LIFECYCLE.md`](LIFECYCLE.md) [ref] (state) · [`QUALITY-GATES.md`](QUALITY-GATES.md) [ref] (cổng) · [`ROUTING.md`](ROUTING.md) [full] (chọn agent) · [`__roster.json`](__roster.json) [full] (agent `active`?).
3. [`../_brain/memory/INDEX.md`](../_brain/memory/INDEX.md) [full] → skill `reference-memory` cho task tương tự → fill dossier **§III.M**.
4. (out) `scripts/new-task.sh` → `active/T-…/dossier.md`; điền §II–IX → `state: dispatched`.

## B. WORKER — thực thi task
1. Dossier **bootstrap block** "ĐỌC TRƯỚC KHI LÀM" [full] → §III Required Reading + **§III.M Memory** [full] (pitfall/pattern liên quan).
2. [`../SKILL.md`](../SKILL.md) [ref] (luật) · tech-stack doc của dossier (§III) [ref].
3. [`QUALITY-GATES.md`](QUALITY-GATES.md) [full] (4 gate + HITL) · [`REPORTING.md`](REPORTING.md) [full] (evidence format).
4. Gặp trigger → [`HITL.md`](HITL.md) → `state: awaiting_approval`.
5. (out) làm trong Allowed Scope → REPORT block / `returns/<ID>-return.md` → `state: returned`.

## C. VERIFY — orchestrator
1. [`QUALITY-GATES.md`](QUALITY-GATES.md) [full] (gate matrix 4+HITL) · [`HITL.md`](HITL.md) (HITL resolved?).
2. Dossier §XI gate matrix + §XII evidence [full] — verify **raw output**, KHÔNG tin summary của worker.
3. (out) all pass + HITL `n/a|resolved` → `state: verified`; fail → `blocked`.

## D. ARCHIVE + học
1. `scripts/archive-task.sh` → `archive/YYYY-MM/` + LEADERBOARD.
2. Auto-harvest → `../_brain/memory/_pending/` → skill `harvest-and-review` distill thành pitfall/pattern.

## See also
[`../SKILL.md`](../SKILL.md) §0 (bootstrap) · [`LIFECYCLE.md`](LIFECYCLE.md) (state) · [`WORKFLOW.md`](WORKFLOW.md) (phases) · [`HITL.md`](HITL.md).
