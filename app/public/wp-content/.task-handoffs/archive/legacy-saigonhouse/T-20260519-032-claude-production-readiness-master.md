---
id: T-20260519-032
owner: claude
state: completed
priority: P0
risk: high
estimated_minutes: 30
parent: null
children: [T-20260519-033, T-20260519-034, T-20260519-035]
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-19 09:00
updated: 2026-05-19 18:30
---

# 📋 T-20260519-032 | claude | production-readiness-master — Production Readiness Audit & Child Task Routing

## I. 🎯 Goal

User audit (2026-05-19) đã verify 3 repo clean, `window.alert + alert(` đều = 0, OAuth + email infrastructure đã code thật. **NHƯNG** vẫn còn 9 production gaps khiến hệ thống **CHƯA THỂ GỌI LÀ FULL CHỨC NĂNG**. Master dossier này phân loại + route các gap còn lại cho 3 sub-task của Codex 5.3.

## II. 📊 Verified state (sau commit 63f9c29 / 4a5bfc4 / 0598a52 / 08f55ca)

### II.1 ĐÃ ĐÓNG (verified)
- ✅ `pi-dashboard` 3 repo clean tree
- ✅ `window.alert + bare alert(` = 0 matches trong `pi-dashboard/src/features/`
- ✅ OAuth Google/GitHub endpoints code thật (`pi-backend/app/shared/auth/oauth.py`)
- ✅ Forgot/reset password endpoints code thật (stateless JWT 15min TTL)
- ✅ Email service Resend + SMTP fallback + dev no-op (`pi-backend/app/shared/email/service.py`)
- ✅ OAuth `redirect_base` config trỏ đúng API domain (commit 0598a52)
- ✅ SeoSchema endpoint mismatch fix: PUT → POST upsert (commit 63f9c29)
- ✅ PostToolbar 3 functional CTAs (Broken Links, CSV, ZIP) — wired real (commit 63f9c29)

### II.2 GAPS CÒN LẠI (9)

| # | Gap | Severity | Sub-task |
|---|---|---|---|
| 1 | `pi-api` SEO AI Bot stub Phase 3 — mock deterministic, không gọi pi-backend `/v1/seo/bot/generate` thật | **P0** | T-033 |
| 2 | `pi-store-admin` AI provider routing/quota/cost-per-customer UX chưa khép kín — provider/key/package/license pages tồn tại nhưng business flow rời rạc | **P0** | T-034 |
| 3 | `pi-backend` RAG/KB chatbot endpoints return 501 (`/rag/query`, `/kb/upload`, `/kb/list`) — cần pgvector/Qdrant infra | P1 | (defer — needs infra) |
| 4 | `pi-backend` performance endpoints 501 (`/cdn/purge`, `/critical-css`) — cần Cloudflare API + Puppeteer worker | P1 | (defer — needs infra) |
| 5 | `pi-backend` Google Indexing chỉ queue Redis, không submit thật Google Indexing API | P1 | T-035 |
| 6 | `pi-dashboard` FormBuilder designer + analytics tab vẫn "Visual Form Designer — đang phát triển" | P2 | T-035 |
| 7 | `pi-dashboard` LicenseGenerate.jsx sinh fake license key client-side | P1 | T-035 |
| 8 | `pi-store` AuthContext có `MOCK_MODE` accept any email when `VITE_MOCK_AUTH=1`; `pi-api` BackendClient có `PI_API_MOCK_MODE`; `pi-backend` billing có `/subscribe/simulate-success` — cần production toggle / removal | P1 | T-035 |
| 9 | Mojibake tiếng Việt còn rộng trong `pi-dashboard` chatbot/SeoSchema description, `pi-store` không nhiều, `pi-api` PHP files | P2 | T-035 |

### II.3 SELF-IDENTIFIED GAPS (audit không list nhưng nên check)

| Gap | Note |
|---|---|
| AI provider business architecture: chưa có routing engine wiring per package | Default flag `PI_AI_NEW_ROUTING_ENABLED=false` — feature chưa active in prod |
| Provider adapter coverage: chỉ `openai_compat` được test trong `/admin/routers/providers.py` | Other adapters return "not implemented" |
| Password reset không có audit log: stateless token revoke không có | Defer — minor security improvement |
| `pi-api` SQL injection: 9 sites đã fix `SHOW TABLES` nhưng dynamic table names trong FROM clause vẫn raw | Note in T-035 if covered |
| `pi-api` REST mutations missing nonce verification | Defer to security audit task |

---

## III. 🎯 Sub-task routing

### III.1 T-20260519-033 — pi-api SEO AI Bot → real pi-backend (P0)

User explicit priority: *"Phần cần làm thật nhất theo mục tiêu của bạn là **pi-api SEO/POST AI gọi pi-backend thật**"*

**Scope**:
- Replace `class-seo-ai-bot.php` mock_suggestions_for() with real call to pi-backend
- Use existing pi-backend route: `POST /v1/seo-bot/generate` (single) hoặc `/bulk` (batch)
- Charge tokens via existing pi-backend wallet system (auto via license bearer)
- Persist real results in pi-api option `pi_seo_ai_bot_tasks`
- Error handling: tier check (pro+ for bulk), quota exhausted handling, network retry

**Detail**: T-20260519-033-codex-pi-api-seo-ai-bot-real-backend.md

### III.2 T-20260519-034 — pi-store-admin AI provider routing/quota/cost flow (P0)

User explicit priority #2: *"pi-store-admin quản trị AI providers/quota/routing/cost theo khách hàng"*

**Scope**:
- New admin page `admin/features/provider-policy/` để wire provider pool + routing mode per package
- Extend existing PackageEditPage với routing_mode field (shared/dedicated/hybrid) + dedicated_key_count
- New CostMarginPage hiển thị upstream_cost_cents vs pi_tokens_charged aggregate per customer
- Wire `PI_AI_NEW_ROUTING_ENABLED=true` enable instructions

**Detail**: T-20260519-034-codex-pi-store-admin-ai-provider-management.md

### III.3 T-20260519-035 — Cleanup batch (P1 + P2)

Catch-all cho remaining gaps không thuộc 2 P0 trên:
- Mojibake batch sweep VN strings (dashboard + store + pi-api PHP)
- LicenseGenerate.jsx → call pi-backend `/v1/admin/licenses` real
- Google Indexing API real submission (`/seo/indexing/submit`)
- FormBuilder designer/analytics tabs → minimal real UI hoặc honest "Coming soon"
- Production toggle: doc cách disable `VITE_MOCK_AUTH`, `PI_API_MOCK_MODE`, `/subscribe/simulate-success`

**Detail**: T-20260519-035-codex-cleanup-mojibake-stubs.md

---

## IV. 🚫 Out Of Scope (Master)

- ❌ RAG/Chatbot KB implementation (gap #3) — cần pgvector/Qdrant infrastructure, separate epic
- ❌ Performance CDN/Critical CSS (gap #4) — cần Cloudflare API + Puppeteer worker infra
- ❌ Stripe billing full lifecycle (invoice sync, dunning, retry) — separate billing epic
- ❌ 2FA implementation (cần TOTP lib)
- ❌ Real-time logs SSE stream

## V. ✅ Master Acceptance Criteria

- [x] T-033 completed: pi-api SEO AI Bot returns real AI suggestions from pi-backend
- [x] T-034 completed: admin can configure provider routing policy per package + view cost/margin per customer
- [x] T-035 completed: mojibake reduced ≥80% across 3 repos + LicenseGenerate wired real + Google Indexing real submission
- [x] All 3 sub-tasks verified by orchestrator (claude) before archive
- [x] No new regressions: build pass on all 3 webapps, lint clean
- [x] Production toggle documentation added to `DEPLOYMENT.md`

## VI. 📑 Change Log

- **2026-05-19 09:00**: Master dossier drafted by claude after user audit.
- **2026-05-19 18:30**: All 3 sub-tasks (T-033, T-034, T-035) completed. All acceptance criteria pass. Executed directly by Claude per user directive.
