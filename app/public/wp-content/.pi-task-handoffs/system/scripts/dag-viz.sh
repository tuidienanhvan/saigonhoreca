#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# dag-viz.sh — visualize the task dependency DAG (text / mermaid) (v1.2)
# (reconstructed 2026-06-03 after data-loss incident — verify against usage)
# ──────────────────────────────────────────────────────────────────
# Scans active/ dossiers (dual-mode) for `depends_on:` frontmatter and
# renders the dependency graph. Default output = Mermaid flowchart
# (paste-ready into STATUS.md / GitHub). `--text` prints an ASCII tree.
# `--inject` rewrites STATUS.md §VI with the fresh Mermaid block.
#
# Usage:
#   bash system/scripts/dag-viz.sh                 # mermaid to stdout
#   bash system/scripts/dag-viz.sh --text          # ascii adjacency
#   bash system/scripts/dag-viz.sh --inject        # write into STATUS.md §VI
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
STATUS_FILE="$HANDOFFS_DIR/STATUS.md"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }

MODE="mermaid"
INJECT_STATUS="false"
for arg in "$@"; do
  case "$arg" in
    --text)    MODE="text" ;;
    --mermaid) MODE="mermaid" ;;
    --inject)  INJECT_STATUS="true" ;;
    --help|-h) sed -n '2,18p' "${BASH_SOURCE[0]}" | sed 's/^# \?//'; exit 0 ;;
  esac
done

field() {
  awk '/^---$/{c++; next} c==1' "$2" 2>/dev/null \
    | grep -m1 "^$1:" \
    | sed "s/^$1:[[:space:]]*//; s/[[:space:]]*#.*$//; s/[[:space:]]*$//" || true
}

# ─── Step 1: Collect tasks ────────────────────────────────────────
declare -A TASK_STATE
declare -A TASK_SLUG
declare -A TASK_DEPS

while IFS= read -r dossier; do
  [[ -n "$dossier" ]] || continue
  base="$(task_basename "$dossier")"
  id="$(echo "$base" | grep -oE '^T-[0-9]{8}-[0-9]+[A-Z]?' || true)"
  [[ -n "$id" ]] || continue
  st="$(field state "$dossier")";   [[ -n "$st" ]] || st="unknown"
  # slug = basename minus "T-<id>-<owner>-"
  slug="$(echo "$base" | sed -E 's/^T-[0-9]{8}-[0-9]+[A-Z]?-[a-z0-9]+-//')"
  [[ -n "$slug" ]] || slug="$id"
  deps="$(field depends_on "$dossier")"
  # normalize "[a, b]" / "[]" → "a,b"
  deps="${deps#[}"; deps="${deps%]}"
  TASK_STATE["$id"]="$st"
  TASK_SLUG["$id"]="$slug"
  TASK_DEPS["$id"]="$deps"
done < <(list_dossiers "$ACTIVE_DIR")

if [[ ${#TASK_STATE[@]} -eq 0 ]]; then
  yellow "No active tasks found — nothing to graph."
  exit 0
fi

# ─── Step 2a: Text renderer ───────────────────────────────────────
generate_text() {
  echo "Task Dependency DAG (text)"
  echo "──────────────────────────"
  for id in $(printf '%s\n' "${!TASK_STATE[@]}" | sort); do
    echo "• $id [${TASK_STATE[$id]}] ${TASK_SLUG[$id]}"
    local deps="${TASK_DEPS[$id]}"
    if [[ -z "$deps" || "$deps" == "null" ]]; then
      echo "    └─ (no dependencies)"
    else
      IFS=',' read -ra DEP_ARR <<< "$deps"
      for dep in "${DEP_ARR[@]}"; do
        dep=$(echo "$dep" | tr -d ' "')
        [[ -z "$dep" ]] && continue
        echo "    └─ depends_on → $dep"
      done
    fi
  done
}

# ─── Step 2b: Mermaid renderer ────────────────────────────────────
generate_mermaid() {
  echo "\`\`\`mermaid"
  echo "flowchart LR"
  echo "  classDef drafted     fill:#eee,stroke:#999;"
  echo "  classDef dispatched  fill:#cce5ff,stroke:#3399ff;"
  echo "  classDef returned    fill:#fff3cd,stroke:#ffc107;"
  echo "  classDef verified    fill:#d4edda,stroke:#28a745;"
  echo "  classDef blocked     fill:#f8d7da,stroke:#dc3545;"
  echo "  classDef decomposed  fill:#e2d9f3,stroke:#6f42c1;"
  echo "  classDef unknown     fill:#fff,stroke:#bbb;"
  echo

  # Nodes
  for id in $(printf '%s\n' "${!TASK_STATE[@]}" | sort); do
    local state="${TASK_STATE[$id]}"
    local short_slug="${TASK_SLUG[$id]}"
    # mermaid classDef must exist; fall back to unknown
    case "$state" in
      drafted|dispatched|returned|verified|blocked|decomposed) : ;;
      *) state="unknown" ;;
    esac
    echo "  $id[\"$id<br/>$short_slug\"]:::$state"
  done
  echo

  # Edges (depends_on)
  local has_edges=false
  for id in "${!TASK_DEPS[@]}"; do
    local deps="${TASK_DEPS[$id]}"
    [[ -z "$deps" || "$deps" == "null" || "$deps" == "[]" ]] && continue
    # Split CSV (may have spaces, T-XXX, T-YYY)
    IFS=',' read -ra DEP_ARR <<< "$deps"
    for dep in "${DEP_ARR[@]}"; do
      dep=$(echo "$dep" | tr -d ' "')
      [[ -z "$dep" ]] && continue
      echo "  $dep --> $id"
      has_edges=true
    done
  done

  if [[ "$has_edges" == "false" ]]; then
    echo "  %% No dependencies declared. All tasks parallelizable."
  fi

  echo "\`\`\`"
}

# ─── Step 3: Output ───────────────────────────────────────────────
if [[ "$MODE" == "text" ]]; then
  generate_text
  exit 0
fi

MERMAID_OUTPUT=$(generate_mermaid)

if [[ "$INJECT_STATUS" == "true" ]]; then
  blue "Injecting DAG into STATUS.md §VI..."
  # Remove old §VI if exists, then append new
  TMP="$STATUS_FILE.tmp.$$"
  awk '/^## VI\. Task DAG/{skip=1; next} skip && /^## /{skip=0} !skip {print}' "$STATUS_FILE" > "$TMP"
  {
    cat "$TMP"
    echo ""
    echo "---"
    echo ""
    echo "## VI. Task DAG (auto-generated by \`dag-viz.sh\`)"
    echo ""
    echo "> Updated: $(date +%Y-%m-%dT%H:%M:%S)"
    echo ""
    echo "$MERMAID_OUTPUT"
  } > "$STATUS_FILE"
  rm -f "$TMP"
  green "  ✓ DAG injected into STATUS.md §VI"
else
  echo "$MERMAID_OUTPUT"
fi
