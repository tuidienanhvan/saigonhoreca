# Task Templates (chuyên biệt) — v1.5

Dossier skeleton pre-filled (scope + verification + acceptance) cho **task type cụ thể**. Khác `../DOSSIER.md` (template gốc, dùng cho mọi task).

## Available

| File | Task type | Default agent | Risk | Est. time |
|---|---|---|---|---|
| `project-pillar-upgrade.md` | Crawl static-mirror → refactor CSS gold-standard cho 1 trang project-pillar | qwen / gemini | medium | ~90 min |

> **Lịch sử**: 5 template generic (`tailwind-migration`, `lint-fix`, `ui-skeleton-coverage`, `security-audit`, `api-refactor`) **đã gỡ ở v1.5** — chúng chưa bao giờ được wire vào `new-task.sh` (flag `--template` không tồn tại), chỉ là scaffold chết. Tạo các loại task đó: dùng `new-task.sh` bình thường rồi tự điền scope.

## Usage

`new-task.sh` **không** có flag `--template` (chưa build). Cách dùng: tạo dossier bình thường, rồi **đọc spec** trong template chuyên biệt và copy/fill §VI Phases + §VIII Acceptance theo nó.

```bash
# 1. Tạo dossier (folder model v1.3)
bash system/scripts/new-task.sh \
  --owner qwen --priority P2 --risk medium \
  --slug <project-slug>-pillar-upgrade \
  --description "Crawl + CSS upgrade project-pillar/<slug>" \
  --intent "<verbatim user message>"

# 2. Mở active/T-.../dossier.md → fill §VI/§VIII theo spec:
cat system/templates/tasks/project-pillar-upgrade.md   # §A quality-bar · §B CSS techniques · §G crawl spec
```

## Convention (template chuyên biệt)

- KHÔNG có frontmatter (dossier đã có sẵn từ `DOSSIER.md`).
- Là **spec/quality-bar** để orchestrator fill scope, không phải file tự load.
- Pre-fill placeholder `<TARGET>` / `<slug>` — customize với path thật khi điền dossier.
