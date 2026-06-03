---
# Machine-readable frontmatter — orchestrator + lint đọc field này
return_for: T-XXXXXXXX-NNN # task id (PHẢI khớp dossier)
agent: <name> # claude|gemini|chatgpt|codex|deepseek|gemma|qwen
returned_at: YYYY-MM-DD HH:MM # giờ hoàn thành
status: success # success | pass-warn | fail | blocked | rejected
scope_clean: true # true nếu git status forbidden-paths EMPTY
gates_passed: 4 # số gate pass / tổng (vd 4/4)
files_changed: 0 # tổng file modified+created+deleted
recommendation: verify # verify | reopen | reject | archive
---

# RETURN REPORT — <TASK_ID> | <AGENT> | <PROJECT_NAME>

> File này LÀ deliverable khi agent báo "xong". Lưu vào `returns/<TASK_ID>-return.md`.
> Quy tắc vàng (xem `system/REPORTING.md`): Evidence-First · No Paraphrasing · No Sanitization · Absolute Delta · UTF-8.

---

## 1. Summary
<3-5 câu kỹ thuật: làm gì, cách làm, kết quả. KHÔNG chung chung.>

## 2. Scope Compliance
- [ ] Chỉ đụng file trong **Allowed Scope** (dossier §IV)
- [ ] KHÔNG đụng shared partials / `_tokens.css` / `inc/core` / `functions.php`
- [ ] `git status --short -- <forbidden paths>` = **EMPTY**

**Files modified** (absolute delta):
- `<path>` (+X, -Y)

**Files created**:
- `<path>`

**Files deleted**:
- `<path>`

## 3. Quality Gate Matrix
| Gate | Status | Evidence (§5.x) |
|---|---|---|
| Build | pass/fail | §5.x |
| Lint / token-compliance | pass/fail | §5.x |
| Scope discipline | pass/fail | §5.x |
| Logic / quality-bar | pass/fail | §5.x |

## 4. Acceptance Checklist (copy từ dossier §VIII)
- [ ] <criteria 1>
- [ ] <criteria 2>

## 5. Evidence — RAW terminal output (BẮT BUỘC, KHÔNG summarize)

### 5.1 `$ <command>`
```
<PASTE FULL RAW OUTPUT — nếu rỗng ghi "(no output)">
```

### 5.2 `$ <command>`
```
<PASTE>
```

> Lệnh tối thiểu (điều chỉnh theo task type): build · lint/grep-compliance · route/curl · `git status --short` · `git diff --stat` · `git status --short -- <forbidden>` (phải EMPTY).

## 6. Residual Risks / Assumptions / Caveats
- **Technical caveats**: <trade-off đã chọn>
- **Remaining warnings**: <warning chưa fix + lý do>
- **Out-of-scope findings**: <bug ngoài scope, file:line — log KHÔNG fix>

## 7. Self-Implement Forbidden — Confirmation
- [ ] Tôi (agent) KHÔNG vượt scope, KHÔNG tự ý sửa file ngoài Allowed Scope.
- [ ] Mọi claim "pass" đều có raw evidence ở §5.

## 8. Recommendation → Orchestrator
**Đề xuất**: `verify` | `reopen` | `reject` | `archive`
**Lý do / action cần orchestrator**: <vd: cần browser visual smoke test vs rokafella; hoặc Luna lỗi → STOP rejected>

---
<!-- Orchestrator (Claude) điền sau khi verify -->
## 9. Orchestrator Verdict (do orchestrator điền)
- Verified by: <claude> · <YYYY-MM-DD HH:MM>
- Decision: <verified | reopened | rejected>
- Notes: <…>
