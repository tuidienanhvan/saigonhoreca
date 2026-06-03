# ──────────────────────────────────────────────────────────────────
# dossier-lib.sh — shared dossier resolution helpers (v1.3, dual-mode)
# (reconstructed 2026-06-03 after data-loss incident — verify against usage)
# ──────────────────────────────────────────────────────────────────
# Sourced by sibling scripts (NOT executed directly — no shebang, no set -e).
#
# Supports TWO on-disk layouts transparently:
#   • folder model : active/T-<id>-<owner>-<slug>/dossier.md
#   • legacy flat  : active/T-<id>-<owner>-<slug>.md
#
# Public functions:
#   resolve_dossier <TASK_ID> [SEARCH_DIR]
#       Echo absolute path to the dossier .md for TASK_ID.
#       1-arg: search active/ first, then archive/<YYYY-MM>/.
#       2-arg: search only the given directory (recursively).
#       Returns 1 (no output) if not found.
#   task_basename <dossier_path>
#       Echo the logical task basename WITHOUT the .md extension.
#       folder model → the container folder name (T-...-slug)
#       legacy file  → the filename minus .md       (T-...-slug)
#   task_container <dossier_path>
#       Echo the directory that "owns" the task.
#       folder model → the T-... folder; legacy → the parent dir.
#   is_folder_model <dossier_path>
#       Return 0 if path is .../T-.../dossier.md (folder model), else 1.
#   list_dossiers [SEARCH_DIR]
#       Echo (one per line) every dossier under SEARCH_DIR (default active/),
#       both folder and legacy models.
#   list_archive_dossiers
#       list_dossiers across all archive/<YYYY-MM>/ subdirs.
# ──────────────────────────────────────────────────────────────────

# Resolve HANDOFFS_DIR even if caller didn't set it.
if [[ -z "${HANDOFFS_DIR:-}" ]]; then
  _DOSSIER_LIB_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
  HANDOFFS_DIR="$(cd "$_DOSSIER_LIB_DIR/../../.." && pwd)"
fi

# ─── is_folder_model <dossier_path> ───────────────────────────────
is_folder_model() {
  local p="$1"
  [[ "$(basename "$p")" == "dossier.md" ]]
}

# ─── task_container <dossier_path> ────────────────────────────────
task_container() {
  local p="$1"
  if is_folder_model "$p"; then
    dirname "$p"
  else
    dirname "$p"
  fi
}

# ─── task_basename <dossier_path> (no .md) ────────────────────────
task_basename() {
  local p="$1"
  if is_folder_model "$p"; then
    basename "$(dirname "$p")"
  else
    local b
    b="$(basename "$p")"
    echo "${b%.md}"
  fi
}

# ─── _find_in_dir <dir> <TASK_ID> ─────────────────────────────────
# Echo first matching dossier path in <dir> (folder OR legacy). Empty if none.
_find_in_dir() {
  local dir="$1" id="$2" hit
  [[ -d "$dir" ]] || return 1

  # 1) folder model: dir/T-<id>-*/dossier.md
  shopt -s nullglob
  for d in "$dir"/${id}-*/ "$dir"/${id}/; do
    if [[ -f "${d}dossier.md" ]]; then
      shopt -u nullglob
      echo "${d}dossier.md"
      return 0
    fi
  done
  # 2) legacy flat: dir/T-<id>-*.md  (exclude *-return.md siblings)
  for f in "$dir"/${id}-*.md "$dir"/${id}.md; do
    case "$f" in *-return.md) continue ;; esac
    if [[ -f "$f" ]]; then
      shopt -u nullglob
      echo "$f"
      return 0
    fi
  done
  shopt -u nullglob
  return 1
}

# ─── resolve_dossier <TASK_ID> [SEARCH_DIR] ───────────────────────
resolve_dossier() {
  local id="$1" search_dir="${2:-}" hit

  if [[ -n "$search_dir" ]]; then
    hit="$(_find_in_dir "$search_dir" "$id")" && { echo "$hit"; return 0; }
    return 1
  fi

  # Default: active/ first
  hit="$(_find_in_dir "$HANDOFFS_DIR/active" "$id")" && { echo "$hit"; return 0; }

  # Then each archive/<YYYY-MM>/ (newest first)
  if [[ -d "$HANDOFFS_DIR/archive" ]]; then
    local month
    for month in $(ls -1r "$HANDOFFS_DIR/archive" 2>/dev/null); do
      [[ -d "$HANDOFFS_DIR/archive/$month" ]] || continue
      hit="$(_find_in_dir "$HANDOFFS_DIR/archive/$month" "$id")" && { echo "$hit"; return 0; }
    done
  fi
  return 1
}

# ─── list_dossiers [SEARCH_DIR] ───────────────────────────────────
list_dossiers() {
  local dir="${1:-$HANDOFFS_DIR/active}"
  [[ -d "$dir" ]] || return 0
  shopt -s nullglob
  local d f
  # folder model
  for d in "$dir"/T-*/; do
    [[ -f "${d}dossier.md" ]] && echo "${d}dossier.md"
  done
  # legacy flat
  for f in "$dir"/T-*.md; do
    case "$f" in *-return.md) continue ;; esac
    [[ -f "$f" ]] && echo "$f"
  done
  shopt -u nullglob
}

# ─── list_archive_dossiers ────────────────────────────────────────
list_archive_dossiers() {
  local month
  [[ -d "$HANDOFFS_DIR/archive" ]] || return 0
  for month in "$HANDOFFS_DIR/archive"/*/; do
    [[ -d "$month" ]] || continue
    list_dossiers "$month"
  done
}
