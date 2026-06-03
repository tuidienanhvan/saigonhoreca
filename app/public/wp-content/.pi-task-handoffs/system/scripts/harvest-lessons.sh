#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# harvest-lessons.sh — Extract lessons from archive/ + returns/ → _brain/memory/
# ──────────────────────────────────────────────────────────────────
# Reads archived dossiers + return files, extracts:
#   - §XV Escalation (raw errors → potential pitfalls)
#   - §6 Residual Risks / Caveats (potential patterns / out-of-scope findings)
#   - §9 Orchestrator Verdict Notes (verified learnings)
#   - Cross-reference task-id ↔ memory entry
# Writes:
#   - _brain/memory/_pending/<TASK_ID>-suggested.md (USER reviews before committing)
#   - Updates _brain/memory/INDEX.md cross-reference table
#
# Usage:
#   bash system/scripts/harvest-lessons.sh                 # all archived tasks not yet harvested
#   bash system/scripts/harvest-lessons.sh T-20260530-001  # specific task
#   bash system/scripts/harvest-lessons.sh --dry-run       # report what would extract
#   bash system/scripts/harvest-lessons.sh --since 30d     # only tasks archived in last 30 days
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ARCHIVE_DIR="$HANDOFFS_DIR/archive"
RETURNS_DIR="$HANDOFFS_DIR/returns"
MEMORY_DIR="$HANDOFFS_DIR/_brain/memory"
PENDING_DIR="$MEMORY_DIR/_pending"
INDEX_FILE="$MEMORY_DIR/INDEX.md"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }
bold()   { printf '\033[1m%s\033[0m\n' "$*"; }

die() { red "$*" >&2; exit 1; }

# ─── Parse args ──────────────────────────────────────────────────
DRY_RUN=0
SINCE_DAYS=0
TARGET_TASK=""

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run) DRY_RUN=1; shift ;;
        --since)
            # Parse like "30d" or "7d"
            SINCE_DAYS=$(echo "$2" | grep -oE '^[0-9]+')
            [[ -n "$SINCE_DAYS" ]] || die "--since needs Nd format (e.g., 30d)"
            shift 2
            ;;
        T-*) TARGET_TASK="$1"; shift ;;
        --help|-h)
            sed -n '2,18p' "${BASH_SOURCE[0]}" | sed 's/^# \?//'
            exit 0
            ;;
        *) die "Unknown arg: $1" ;;
    esac
done

mkdir -p "$PENDING_DIR" "$MEMORY_DIR/patterns" "$MEMORY_DIR/pitfalls" "$MEMORY_DIR/agent-quirks" "$MEMORY_DIR/playbooks"

# ─── Find candidate dossiers ─────────────────────────────────────
bold "═══ harvest-lessons.sh ═══"

CANDIDATES=()
if [[ -n "$TARGET_TASK" ]]; then
    # Specific task (v1.3 dual-mode: folder T-*/dossier.md or legacy T-*.md)
    D="$(resolve_dossier "$TARGET_TASK" 2>/dev/null || true)"
    [[ -n "$D" ]] || die "Task $TARGET_TASK not found in archive/"
    CANDIDATES+=("$D")
else
    # All archived tasks (folder + legacy)
    mapfile -t CANDIDATES < <(list_archive_dossiers)
fi

[[ ${#CANDIDATES[@]} -gt 0 ]] || { yellow "No archived dossiers found."; exit 0; }

blue "Found ${#CANDIDATES[@]} candidate dossier(s) to scan"

# ─── Process each ────────────────────────────────────────────────
HARVESTED=0
SKIPPED=0
SUGGESTIONS=0

for DOSSIER in "${CANDIDATES[@]}"; do
    TASK_ID=$(task_basename "$DOSSIER" | grep -oE '^T-[0-9]{8}-[0-9]+[A-Z]?')
    [[ -n "$TASK_ID" ]] || continue

    PENDING_FILE="$PENDING_DIR/${TASK_ID}-suggested.md"

    # Skip if already harvested (pending file exists or referenced in INDEX)
    if [[ -f "$PENDING_FILE" ]] || grep -q "$TASK_ID" "$INDEX_FILE" 2>/dev/null; then
        ((SKIPPED++))
        continue
    fi

    # Extract sections
    STATE=$(awk '/^---$/{c++; next} c==1 && /^state:/ {print $2; exit}' "$DOSSIER")
    OWNER=$(awk '/^---$/{c++; next} c==1 && /^owner:/ {print $2; exit}' "$DOSSIER")
    RISK=$(awk '/^---$/{c++; next} c==1 && /^risk:/ {print $2; exit}' "$DOSSIER")

    # Extract Escalation section (§XV) — raw errors / blocked reasons
    ESCALATION=$(awk '/^## XV\./,/^## XVI\./' "$DOSSIER" | grep -vE "^## |^---|^$" | head -30)

    # Extract Notes section (residual risks / out-of-scope findings)
    NOTES=$(awk '/Out-of-scope|Residual Risks|Caveats|Technical caveats/,/^## |^---|^$/' "$DOSSIER" | head -20)

    # Look for corresponding return file (v1.3: folder co-located return.md, then legacy returns/)
    RETURN_FILE="$(dirname "$DOSSIER")/return.md"
    [[ -f "$RETURN_FILE" ]] || RETURN_FILE="$RETURNS_DIR/${TASK_ID}-return.md"
    ORCHESTRATOR_NOTES=""
    if [[ -f "$RETURN_FILE" ]]; then
        ORCHESTRATOR_NOTES=$(awk '/^## 9\./,/^## 10\.|^$/' "$RETURN_FILE" | head -15)
    fi

    # Skip if nothing extractable
    if [[ -z "$ESCALATION" && -z "$NOTES" && -z "$ORCHESTRATOR_NOTES" ]]; then
        ((SKIPPED++))
        continue
    fi

    # Generate suggestion file
    if [[ $DRY_RUN -eq 1 ]]; then
        green "  [DRY-RUN] Would harvest: $TASK_ID ($STATE, $OWNER, $RISK)"
        [[ -n "$ESCALATION" ]] && echo "    → has §XV Escalation (potential pitfall)"
        [[ -n "$NOTES" ]] && echo "    → has Out-of-scope / Caveats (potential pattern/findings)"
        [[ -n "$ORCHESTRATOR_NOTES" ]] && echo "    → has Orchestrator Notes (verified learning)"
    else
        cat > "$PENDING_FILE" <<EOF
---
type: pending-suggestion
source_task: $TASK_ID
source_dossier: $(echo "$DOSSIER" | sed "s|$HANDOFFS_DIR/||")
state: $STATE
owner: $OWNER
risk: $RISK
generated_at: harvest-lessons.sh
status: pending-review
---

# Pending Memory Suggestion — $TASK_ID

> USER REVIEW: extract relevant lessons into _brain/memory/ subdirectories below, then DELETE this pending file.

## Raw extracted content

### §XV Escalation (potential pitfall source)

\`\`\`
${ESCALATION:-(none)}
\`\`\`

### Notes / Out-of-scope / Caveats (potential pattern source)

\`\`\`
${NOTES:-(none)}
\`\`\`

### Orchestrator Verdict Notes (verified learning source)

\`\`\`
${ORCHESTRATOR_NOTES:-(none)}
\`\`\`

## USER decisions

For each candidate above, decide:

- [ ] Worth a **pitfall** entry? → Create \`_brain/memory/pitfalls/<slug>.md\` per README §II.2 template
- [ ] Worth a **pattern** entry? → Create \`_brain/memory/patterns/<slug>.md\` per README §II.1 template
- [ ] Worth an **agent-quirk** note? → Append to \`_brain/memory/agent-quirks/${OWNER}.md\`
- [ ] Worth a **playbook**? (rare — only if e2e workflow demonstrated) → Create \`_brain/memory/playbooks/<slug>.md\`

After creating entries, update \`_brain/memory/INDEX.md\` cross-reference table.

Then DELETE this file:
\`\`\`bash
rm "${PENDING_FILE}"
\`\`\`
EOF
        green "  ✓ Harvested: $TASK_ID → $PENDING_FILE"
        ((HARVESTED++))
        ((SUGGESTIONS++))
    fi
done

# ─── Summary ─────────────────────────────────────────────────────
echo
bold "═══ Summary ═══"
echo "  Total candidates : ${#CANDIDATES[@]}"
echo "  Already harvested: $SKIPPED"
if [[ $DRY_RUN -eq 1 ]]; then
    blue "  Would harvest    : $((${#CANDIDATES[@]} - SKIPPED)) (run without --dry-run to write)"
else
    green "  Harvested        : $HARVESTED → $PENDING_DIR"
    echo
    yellow "  → USER review $PENDING_DIR/*.md and decide which to promote to _brain/memory/"
    echo "     After review + memory entries created, DELETE pending files."
fi
echo
echo "  Memory entries currently:"
for sub in patterns pitfalls agent-quirks playbooks; do
    n=$(ls "$MEMORY_DIR/$sub" 2>/dev/null | wc -l)
    printf "    %-15s : %d\n" "$sub" "$n"
done
