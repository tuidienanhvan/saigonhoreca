---
type: playbook
title: Pi Task Handoffs — rename/restructure to v1.0 (folder + scripts + docs)
duration_est: ~2h first time · n/a (one-off migration)
last_validated: 2026-05-30 on Pi-task-handoffs-v1.0-rename-2026-05-30
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Pi Task Handoffs — rename/restructure v1.0

## Use case

Migration playbook: đổi tên + tái cấu trúc hệ thống task-handoffs cũ sang chuẩn **Pi Task Handoffs v1.0** (đổi folder name, cập nhật path trong scripts, docs, hooks) một cách atomic + có rollback. Dùng lại khi cần rename/restructure tương tự lần sau.

## Prerequisites

- Git repo clean (`git status --short` rỗng) — để rollback dễ
- `checkpoint.sh` available (pre-mutation snapshot — xem `patterns/archive-checkpoint-step0.md`)
- Danh sách path cũ → mới (mapping table)
- Backup toàn bộ `.pi-task-handoffs/` (zip ngoài git)

## Steps (copy-paste)

### Step 1 — Snapshot + branch an toàn
```bash
git checkout -b rename/pi-task-handoffs-v1.0
bash system/scripts/checkpoint.sh --all     # snapshot toàn bộ dossier active/
cp -r .pi-task-handoffs ../pi-task-handoffs.bak.$(date +%Y%m%d)
```

### Step 2 — Inventory path references
Liệt kê mọi nơi nhắc tên/path cũ (scripts, docs, hooks, dossier templates):
```bash
grep -rln "<OLD_NAME>" . --include="*.sh" --include="*.md" > /tmp/rename-refs.txt
wc -l /tmp/rename-refs.txt
```

### Step 3 — Rename folder(s)
```bash
git mv <old-folder> .pi-task-handoffs     # giữ history
```

### Step 4 — Update path trong scripts
Sửa từng file trong `system/scripts/` (new-task.sh, archive-task.sh, reconcile.sh, ...): đổi `OLD_ROOT` → `.pi-task-handoffs`. Dùng Edit tool surgical, KHÔNG mass-sed mù (tránh đổi nhầm string trong evidence).

### Step 5 — Update docs + templates
SKILL.md, WORKFLOW.md, DOSSIER.md template, REPORTING.md, AGENTS/*.md → đổi tên hệ thống + path references.

### Step 6 — Add Step 0 checkpoint vào archive-task.sh
Thêm pre-archive auto-snapshot (xem `patterns/archive-checkpoint-step0.md`) — best-effort, không block archive.

### Step 7 — Wire memory layer hooks
- `new-task.sh` → auto-invoke `reference-memory` (fill §III.M)
- `archive-task.sh` Step 8.5 → `harvest-lessons.sh` (sinh `_pending/` stubs)

## Verification
```bash
# 1) Không còn path cũ
grep -rln "<OLD_NAME>" . --include="*.sh" --include="*.md" | grep -v "CHANGELOG\|archive/" 
# Expected: empty (trừ changelog ghi lịch sử)

# 2) Scripts chạy được
bash system/scripts/new-task.sh --dry-run
bash system/scripts/reconcile.sh --check
# Expected: exit 0, không lỗi path-not-found

# 3) Git history giữ (git mv, không delete+add)
git log --follow --oneline .pi-task-handoffs | head -3
```

## Common deviations

| Symptom | Cause | Fix |
|---|---|---|
| Script "path not found" | sót 1 ref path cũ | grep lại Step 2, sửa file còn sót |
| Mất git history file | dùng `mv` thay `git mv` | `git mv` để giữ --follow |
| Evidence string bị đổi nhầm | mass-sed mù | dùng Edit surgical, review từng diff |
| Hook không fire | quên wire Step 7 | check new-task.sh/archive-task.sh gọi đúng skill |

## Maintenance schedule

- One-off migration; sau khi merge branch → xóa backup zip sau ~30d
- Lần rename sau → tái dùng playbook này (Step 1-7)

## See also

- `patterns/archive-checkpoint-step0.md` — pre-mutation snapshot dùng trong Step 1/6
- `../README.md` §I 3-layer (memory layer additive lên v1.0)
- SKILL.md (canonical rules sau rename)
