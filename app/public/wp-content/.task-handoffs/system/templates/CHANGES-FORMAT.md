# 📐 Dossier Format Specification - Changes Folder

## I. 📐 decision.md Format

```markdown
# Decision Record - {TASK_ID}

## II. 📌 Basic Info
- Task ID: T-{DATE}-{NUM}
- Date: YYYY-MM-DD
- Owner: {agent}
- Status: verified

## III. 📌 Change Summary
{2-3 sentences describing what changed}

## IV. 📌 Technical Justification
### Before State
{What was broken/not working}

### After State
{How it was fixed/improved}

### Evidence
```bash
Raw terminal output here
```

## V. 📌 Risk Assessment
- Risk Level: {low|medium|high}
- Rollback Complexity: {trivial|moderate|complex}

## VI. 📌 Acceptance Criteria
- [x] Criterion 1
- [x] Criterion 2
```

## VII. 📌 before/ and after/ Snapshots

Each should contain file copies or git show output:
```bash
git show HEAD:{path/to/file} > before/{filename}
```

### Snapshot Scope Rule
**Only snapshot files directly modified in the task.** Do not copy entire modules. 
Example: If task modifies `src/components/shared/PiLogo.jsx`, only snapshot that file.

### Automated Diff Generation
Generate diff using standard tool:
```bash
cd /project/root
diff -u before/ after/ > diff.patch
```

## VIII. 📐 diff.patch Format

Standard unified diff:
```diff
--- a/path/to/file.jsx
+++ b/path/to/file.jsx
@@ -1,5 +1,5 @@
-old line
+new line
```

## IX. 📐 rollback-plan.md Format

```markdown
# ⏪ Rollback Plan - {TASK_ID}

## X. 📌 Files to Revert
- {file1} → git checkout HEAD~1 -- {file1}
- {file2} → git checkout HEAD~1 -- {file2}

## XI. 🔧 Commands to Run
```bash
git checkout HEAD~1 -- src/components/shared/FeatureGate.jsx
npm run build
npm run lint
```

## XII. 🔍 Verification Steps
1. {Step 1}
2. {Step 2}
```