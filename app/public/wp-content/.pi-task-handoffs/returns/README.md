# 📥 returns/ — Structured Return Reports

Mỗi task khi agent báo **xong** → 1 file `.md` ở đây: `T-<date>-<nnn>-return.md`.

Đây là **deliverable chính thức** (thay cho text REPORT block dán miệng). Orchestrator đọc file này để verify → quyết định `verified | reopened | rejected`.

## Lifecycle
1. Orchestrator dispatch task (dossier `active/`, state=dispatched).
2. Agent làm xong → tạo `returns/<task-id>-return.md` theo `system/templates/RETURN-REPORT.md`.
   - Scaffold nhanh: `bash system/scripts/new-return.sh <task-id>`
3. Orchestrator verify evidence trong return file → set-state `returned` → `verified`/`reopened`/`rejected` + điền §9.
4. Return file Ở LẠI `returns/` kể cả sau khi dossier archive (tránh lệch INDEX count). Link theo task-id.

## Quy tắc
- Frontmatter (`return_for`, `status`, `scope_clean`, `gates_passed`, `recommendation`) PHẢI có — lint + orchestrator đọc.
- Evidence §5 = RAW terminal output, KHÔNG paraphrase (xem `system/REPORTING.md`).
- 1 task = 1 return file. Reopen lần 2 → append section `## RETURN v2` trong cùng file.
