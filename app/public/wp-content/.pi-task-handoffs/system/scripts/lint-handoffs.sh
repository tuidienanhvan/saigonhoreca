#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# lint-handoffs.sh — Drift detector for .task-handoffs (v4.0)
# ──────────────────────────────────────────────────────────────────
# Usage:
#   bash system/scripts/lint-handoffs.sh           # default (warns only)
#   bash system/scripts/lint-handoffs.sh --strict  # hard-fail all drift
#   bash system/scripts/lint-handoffs.sh --fix     # run reconcile --fix, re-lint
#
# Exit code: 0 = clean, 1 = drift detected
#
# Checks (A–L):
#   A. Active vs STATUS.md alignment
#   B. Archive vs LEADERBOARD.md coverage
#   C. Frontmatter validity (state/owner present)
#   D. STATUS.md date freshness (≤7 days)
#   E. STATUS.md table integrity
#   F. High-risk archived tasks have changes/ entries (advisory)
#   G. Stale dispatched tasks (>1d soft, >7d hard with --strict)
#   H. Version consistency across docs (--strict promotes to fail)
#   I. STATUS §4 entry count ≤15 (--strict promotes to fail)
#   J. (v4.0) Roster consistency: AGENTS.md count vs __roster.json
#   K. (v4.0) Archive INDEX.md accuracy: claimed vs actual file count
#   L. (v4.0) Misplaced archive files at archive/ root (not in YYYY-MM/)
# ──────────────────────────────────────────────────────────────────

set -u

SCRIPT_DIR_EARLY="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

STRICT=0
case "${1:-}" in
    --strict) STRICT=1 ;;
    --fix)
        # Run reconcile --fix then re-exec self with --strict for verification
        printf '\033[34m%s\033[0m\n' "🔧 lint --fix: running reconcile.sh --fix first..."
        bash "$SCRIPT_DIR_EARLY/reconcile.sh" --fix
        printf '\033[34m%s\033[0m\n' "🔍 Re-linting with --strict..."
        exec bash "$SCRIPT_DIR_EARLY/lint-handoffs.sh" --strict
        ;;
    "")       STRICT=0 ;;
    *) echo "Usage: $0 [--strict | --fix]" >&2; exit 2 ;;
esac

SCRIPT_DIR="$SCRIPT_DIR_EARLY"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
ARCHIVE_DIR="$HANDOFFS_DIR/archive"
STATUS_FILE="$HANDOFFS_DIR/STATUS.md"
LEADERBOARD_FILE="$HANDOFFS_DIR/LEADERBOARD.md"
CHANGES_DIR="$HANDOFFS_DIR/changes"
ROSTER_FILE="$HANDOFFS_DIR/system/__roster.json"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }

DRIFT_COUNT=0
flag_drift() { DRIFT_COUNT=$((DRIFT_COUNT + 1)); red "  ❌ $*"; }
soft_warn()  {
    if [[ $STRICT -eq 1 ]]; then
        flag_drift "$*"
    else
        yellow "  ⚠ $*"
    fi
}

blue "🔍 Linting .task-handoffs/ system$([[ $STRICT -eq 1 ]] && echo ' (--strict)')..."
echo

# ─── A. Active vs STATUS.md ──────────────────────────────────────
blue "A. Active folder vs STATUS.md alignment"
shopt -s nullglob
ACTIVE_FILES=("$ACTIVE_DIR"/T-*.md)
shopt -u nullglob
DRIFT_A_START=$DRIFT_COUNT

for f in "${ACTIVE_FILES[@]}"; do
    tid=$(basename "$f" | grep -oE '^T-[0-9]{8}-[0-9]+[A-Z]?(-[0-9]+)?')
    if ! grep -q "$tid" "$STATUS_FILE"; then
        flag_drift "$tid in active/ but NOT in STATUS.md"
    fi
done

while IFS= read -r line; do
    tid=$(echo "$line" | grep -oE 'T-[0-9]{8}-[0-9]+[A-Z]?(-[0-9]+)?' | head -1)
    [[ -z "$tid" ]] && continue
    [[ "$line" == *"archive/"* ]] && continue
    [[ "$line" == *"None"* ]] && continue
    if ! ls "$ACTIVE_DIR"/${tid}-*.md >/dev/null 2>&1; then
        flag_drift "STATUS.md mentions $tid as active but no file in active/"
    fi
done < <(awk '/^## 1\./,/^## 2\./' "$STATUS_FILE" | grep -E '^\| `?T-' || true)

[[ $DRIFT_COUNT -eq $DRIFT_A_START ]] && green "  ✓ Active sync OK"
echo

# ─── B. Archive vs LEADERBOARD ───────────────────────────────────
blue "B. Archive coverage in LEADERBOARD.md"
DRIFT_B_START=$DRIFT_COUNT
if [[ -d "$ARCHIVE_DIR" ]]; then
    while IFS= read -r f; do
        tid=$(basename "$f" | grep -oE '^T-[0-9]{8}-[0-9]+[A-Z]?(-[0-9]+)?')
        d=$(echo "$tid" | sed 's/T-\([0-9]\{4\}\)\([0-9]\{2\}\)\([0-9]\{2\}\).*/\1\2\3/')
        [[ "$d" -lt 20260504 ]] && continue
        if ! grep -q "$tid" "$LEADERBOARD_FILE"; then
            flag_drift "$tid archived but NOT in LEADERBOARD.md"
        fi
    done < <(find "$ARCHIVE_DIR" -name 'T-*.md' -type f 2>/dev/null)
fi
[[ $DRIFT_COUNT -eq $DRIFT_B_START ]] && green "  ✓ LEADERBOARD coverage OK"
echo

# ─── C. Frontmatter validity ─────────────────────────────────────
blue "C. Frontmatter validity"
DRIFT_C_START=$DRIFT_COUNT
for f in "${ACTIVE_FILES[@]}"; do
    head -20 "$f" | grep -qE '^state:' || flag_drift "$(basename "$f"): missing 'state:'"
    head -20 "$f" | grep -qE '^owner:' || flag_drift "$(basename "$f"): missing 'owner:'"
done
[[ $DRIFT_COUNT -eq $DRIFT_C_START ]] && green "  ✓ Frontmatter OK"
echo

# ─── D. STATUS.md date freshness ─────────────────────────────────
blue "D. STATUS.md date freshness"
STATUS_DATE=$(grep -oE 'Current Date.*[0-9]{4}-[0-9]{2}-[0-9]{2}' "$STATUS_FILE" | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | head -1)
if [[ -z "$STATUS_DATE" ]]; then
    flag_drift "STATUS.md missing 'Current Date'"
else
    TODAY=$(date +%Y-%m-%d)
    DD=$(( ($(date -d "$TODAY" +%s) - $(date -d "$STATUS_DATE" +%s)) / 86400 ))
    if [[ $DD -gt 7 ]]; then
        flag_drift "STATUS.md Current Date $DD days stale ($STATUS_DATE vs $TODAY)"
    else
        green "  ✓ Date fresh: $STATUS_DATE (${DD}d)"
    fi
fi
echo

# ─── E. STATUS.md table integrity ────────────────────────────────
blue "E. STATUS.md table integrity"
DRIFT_E_START=$DRIFT_COUNT
DISTINCT=$(awk -F'|' '/^\|/ {print NF}' "$STATUS_FILE" | sort -u | wc -l)
[[ $DISTINCT -gt 3 ]] && flag_drift "STATUS.md has $DISTINCT distinct pipe counts — malformed"
[[ $DRIFT_COUNT -eq $DRIFT_E_START ]] && green "  ✓ Table integrity OK ($DISTINCT schemas)"
echo

# ─── F. High-risk → changes/ entry (advisory) ────────────────────
blue "F. High-risk archived tasks → changes/ entries (advisory)"
while IFS= read -r f; do
    risk=$(awk '/^---$/{c++; next} c==1 && /^risk:/ {print $2; exit}' "$f")
    if [[ "$risk" == "high" || "$risk" == "critical" ]]; then
        tid=$(basename "$f" | grep -oE '^T-[0-9]{8}-[0-9]+[A-Z]?(-[0-9]+)?')
        ls -d "$CHANGES_DIR"/${tid}-*/ >/dev/null 2>&1 || yellow "  ⚠ $tid (risk=$risk) lacks changes/ entry (advisory)"
    fi
done < <(find "$ARCHIVE_DIR" -name 'T-*.md' -type f 2>/dev/null)
green "  ✓ Risk-coverage scan done"
echo

# ─── G. Stale dispatched tasks ───────────────────────────────────
blue "G. Stale dispatched tasks"
DRIFT_G_START=$DRIFT_COUNT
for f in "${ACTIVE_FILES[@]}"; do
    state=$(awk '/^---$/{c++; next} c==1 && /^state:/ {print $2; exit}' "$f")
    [[ "$state" != "dispatched" ]] && continue
    tid=$(basename "$f" | grep -oE '^T-[0-9]{8}-[0-9]+[A-Z]?(-[0-9]+)?')
    upd=$(awk '/^---$/{c++; next} c==1 && /^updated:/ {$1=""; print; exit}' "$f" | xargs)
    ud=$(echo "$upd" | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}' | head -1)
    [[ -z "$ud" ]] && continue
    TODAY=$(date +%Y-%m-%d)
    DAYS=$(( ($(date -d "$TODAY" +%s 2>/dev/null || echo 0) - $(date -d "$ud" +%s 2>/dev/null || echo 0)) / 86400 ))
    if [[ $DAYS -gt 7 ]]; then
        flag_drift "$tid dispatched ${DAYS}d ago (>7d zombie HARD-FAIL)"
    elif [[ $DAYS -gt 1 ]]; then
        soft_warn "$tid dispatched ${DAYS}d ago (zombie risk)"
    fi
done
[[ $DRIFT_COUNT -eq $DRIFT_G_START ]] && green "  ✓ No stale dispatched tasks"
echo

# ─── H. Version consistency ──────────────────────────────────────
blue "H. Version consistency across docs"
DRIFT_H_START=$DRIFT_COUNT
GOLDEN_VER=$(head -10 "$STATUS_FILE" | grep -oE 'v[0-9]+\.[0-9]+' | head -1)
if [[ -n "$GOLDEN_VER" ]]; then
    for doc in "$HANDOFFS_DIR"/README.md "$HANDOFFS_DIR"/SKILL.md "$HANDOFFS_DIR"/GUIDE.md \
               "$HANDOFFS_DIR"/system/WORKFLOW.md "$HANDOFFS_DIR"/system/AGENT-MODEL.md \
               "$HANDOFFS_DIR"/system/AI-COLLAB.md "$HANDOFFS_DIR"/system/HOW-TO-USE.md \
               "$HANDOFFS_DIR"/system/ROUTING.md "$HANDOFFS_DIR"/system/QUALITY-GATES.md \
               "$HANDOFFS_DIR"/system/REPORTING.md; do
        [[ -f "$doc" ]] || continue
        bn=$(basename "$doc")
        first_ver=$(head -10 "$doc" | grep -oE 'v[0-9]+\.[0-9]+' | head -1)
        if [[ -n "$first_ver" && "$first_ver" != "$GOLDEN_VER" ]]; then
            soft_warn "$bn version $first_ver vs golden $GOLDEN_VER"
        fi
    done
fi
[[ $DRIFT_COUNT -eq $DRIFT_H_START ]] && green "  ✓ Version consistency OK"
echo

# ─── I. STATUS §4 entry count ────────────────────────────────────
blue "I. STATUS §4 entry count (≤15)"
SEC4=$(awk '/^## 4\./,/^## 5\./' "$STATUS_FILE" | grep -cE '^\| `?T-' || true)
[[ $SEC4 -gt 15 ]] && soft_warn "Section 4 has $SEC4 entries — rotate (keep ≤15)"
[[ $SEC4 -le 15 ]] && green "  ✓ Section 4 size OK ($SEC4 entries)"
echo

# ─── J. (v4.0) Roster consistency ────────────────────────────────
blue "J. (v4.0) Roster consistency vs __roster.json"
DRIFT_J_START=$DRIFT_COUNT
ROSTER_WIN="$ROSTER_FILE"
command -v cygpath >/dev/null 2>&1 && ROSTER_WIN="$(cygpath -m "$ROSTER_FILE")"
if [[ -f "$ROSTER_FILE" ]]; then
    OC=$(python -c "import json; d=json.load(open(r'$ROSTER_WIN')); print(len(d['tiers']['orchestrator']['members']))")
    WC=$(python -c "import json; d=json.load(open(r'$ROSTER_WIN')); print(len(d['tiers']['worker']['members']))")
    TOTAL=$((OC + WC))
    AGENTS_MD="$HANDOFFS_DIR/AGENTS/AGENTS.md"
    if [[ -f "$AGENTS_MD" ]]; then
        if ! grep -qE "Total: $TOTAL agents \($OC Orchestrator \+ $WC Worker\)" "$AGENTS_MD"; then
            flag_drift "AGENTS.md count mismatch (expected $TOTAL = $OC orch + $WC worker)"
        fi
    fi
    # README check
    README="$HANDOFFS_DIR/README.md"
    if [[ -f "$README" ]] && grep -qE 'Tier 1 — Orchestrator' "$README"; then
        readme_orch=$(grep -A20 'Tier 1' "$README" | grep -cE '^- \*\*' || true)
        if [[ "$readme_orch" -ne "$OC" && "$readme_orch" -gt 0 ]]; then
            soft_warn "README §Tier 1 lists $readme_orch agents (expected $OC)"
        fi
    fi
else
    flag_drift "system/__roster.json MISSING — v4.0 requires it"
fi
[[ $DRIFT_COUNT -eq $DRIFT_J_START ]] && green "  ✓ Roster consistency OK"
echo

# ─── K. (v4.0) Archive INDEX.md accuracy ─────────────────────────
blue "K. (v4.0) Archive INDEX.md accuracy"
DRIFT_K_START=$DRIFT_COUNT
for mo in "$ARCHIVE_DIR"/[0-9][0-9][0-9][0-9]-[0-9][0-9]/; do
    [[ -d "$mo" ]] || continue
    idx="$mo/INDEX.md"
    shopt -s nullglob
    files=("$mo"T-*.md)
    shopt -u nullglob
    actual=${#files[@]}
    if [[ -f "$idx" ]]; then
        claimed=$(grep -cE '^\| `?T-[0-9]+' "$idx" 2>/dev/null || echo 0)
        if [[ "$actual" -ne "$claimed" ]]; then
            flag_drift "$(basename "$mo")/INDEX.md claims $claimed but actual is $actual"
        fi
    elif [[ $actual -gt 0 ]]; then
        flag_drift "$(basename "$mo")/INDEX.md missing ($actual files exist)"
    fi
done
[[ $DRIFT_COUNT -eq $DRIFT_K_START ]] && green "  ✓ INDEX accuracy OK"
echo

# ─── L. (v4.0) Misplaced archive files ───────────────────────────
blue "L. (v4.0) Misplaced archive files (root of archive/)"
DRIFT_L_START=$DRIFT_COUNT
shopt -s nullglob
MISPLACED=("$ARCHIVE_DIR"/T-*.md)
shopt -u nullglob
for f in "${MISPLACED[@]}"; do
    flag_drift "$(basename "$f") in archive/ root — should be in archive/YYYY-MM/"
done
[[ $DRIFT_COUNT -eq $DRIFT_L_START ]] && green "  ✓ No misplaced archive files"
echo

# ─── M. (v4.1) Agent metrics freshness (Overstory CV pattern) ────
blue "M. (v4.1) Agent metrics freshness (≤7 days)"
DRIFT_M_START=$DRIFT_COUNT
TODAY_EPOCH=$(date +%s)
WEEK=$((7*86400))
shopt -s nullglob
for f in "$HANDOFFS_DIR"/AGENTS/*.md; do
    base=$(basename "$f")
    [[ "$base" == "AGENTS.md" || "$base" == "laguna.md" ]] && continue
    mu=$(awk '/^---$/{c++; next} c==1 && /^metrics_updated:/ {print $2; exit}' "$f")
    if [[ -z "$mu" ]]; then
        flag_drift "$base: no metrics_updated field (run update-agent-metrics.sh)"
        continue
    fi
    mu_epoch=$(date -d "$mu" +%s 2>/dev/null || echo 0)
    if [[ $mu_epoch -eq 0 ]]; then
        flag_drift "$base: invalid metrics_updated=$mu"
        continue
    fi
    age=$((TODAY_EPOCH - mu_epoch))
    if [[ $age -gt $WEEK ]]; then
        flag_drift "$base: metrics stale ($((age/86400))d old, threshold 7d)"
    fi
done
shopt -u nullglob
[[ $DRIFT_COUNT -eq $DRIFT_M_START ]] && green "  ✓ All agent metrics fresh"
echo

# ─── N. (v4.1) Roster ↔ AGENTS/*.md status & tier sync ───────────
blue "N. (v4.1) Roster ↔ AGENTS/<name>.md status+tier sync"
DRIFT_N_START=$DRIFT_COUNT
ROSTER_FILE="$HANDOFFS_DIR/system/__roster.json"
if [[ -f "$ROSTER_FILE" ]] && command -v python >/dev/null 2>&1; then
    ROSTER_WIN=$(command -v cygpath >/dev/null 2>&1 && cygpath -m "$ROSTER_FILE" || echo "$ROSTER_FILE")
    export ROSTER_WIN
    while IFS=$'\t' read -r name r_tier r_status; do
        name="${name//$'\r'/}"; r_tier="${r_tier//$'\r'/}"; r_status="${r_status//$'\r'/}"
        [[ -z "$name" ]] && continue
        f="$HANDOFFS_DIR/AGENTS/$name.md"
        [[ ! -f "$f" ]] && { flag_drift "AGENTS/$name.md missing (in roster but no CV)"; continue; }
        a_tier=$(awk '/^---$/{c++; next} c==1 && /^tier:/ {print $2; exit}' "$f" | tr -d '\r')
        a_status=$(awk '/^---$/{c++; next} c==1 && /^status:/ {print $2; exit}' "$f" | tr -d '\r')
        [[ "$a_tier" != "$r_tier" ]] && flag_drift "AGENTS/$name.md tier=$a_tier ≠ roster tier=$r_tier"
        [[ "$a_status" != "$r_status" ]] && flag_drift "AGENTS/$name.md status=$a_status ≠ roster status=$r_status"
    done < <(python -c "import json,os; r=json.load(open(os.environ['ROSTER_WIN'],encoding='utf-8'))
for n,m in r['agents'].items(): print('{}\t{}\t{}'.format(n,m['tier'],m['status']))" | tr -d '\r')
else
    yellow "  ⚠ skip — roster.json missing or python unavailable"
fi
[[ $DRIFT_COUNT -eq $DRIFT_N_START ]] && green "  ✓ Roster ↔ AGENTS sync OK"
echo

# ─── Summary ─────────────────────────────────────────────────────
echo "─────────────────────────────────────"
if [[ $DRIFT_COUNT -eq 0 ]]; then
    green "✅ CLEAN — no drift detected"
    exit 0
else
    red "❌ FOUND $DRIFT_COUNT drift issue(s) — run 'reconcile.sh --fix' or address manually"
    exit 1
fi
