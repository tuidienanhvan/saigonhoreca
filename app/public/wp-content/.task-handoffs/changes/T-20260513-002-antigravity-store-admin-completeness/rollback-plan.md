# Rollback Plan — T-20260513-002

## Trigger conditions
- Adjust Tokens modal incorrectly debits a license
- Assign Package modal allocates wrong dedicated keys
- Cron Run-now triggers wrong job

## Immediate rollback (frontend only — no DB impact)
```bash
cd pi-store-webapp
git revert <commit-sha>
npx vite build
deploy
```
Backend endpoints untouched — admin can still curl/postman if needed.

## Surgical rollback (disable single modal)
- Comment out AdjustTokensModal import + button trigger in AdminLicensesPage.jsx
- Rebuild + deploy

## Data rollback (if Adjust Tokens caused incorrect balance)
- Look up audit_log table: `WHERE action='token_adjust' AND created_at >= '<incident_time>'`
- Apply inverse delta via same endpoint with note "rollback of admin#X action Y"

## Escalation
If multiple licenses affected → freeze admin token-adjust UI immediately, alert on-call.
