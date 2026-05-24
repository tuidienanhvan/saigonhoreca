#!/usr/bin/env bash
# Crawl all upload assets for a given pillar slug into static-mirror.
# Usage: bash scripts/crawl-missing.sh <slug>
set -e
slug="$1"
[ -z "$slug" ] && { echo "Usage: $0 <slug>"; exit 1; }
cd "$(dirname "$0")/../static-mirror"

html="du-an/$slug/index.html"
[ ! -f "$html" ] && { echo "Missing $html — fetch first"; exit 1; }

# Collect post-XXX.css ids
postids=$(grep -oE 'post-[0-9]+\.css' "$html" | sort -u)

# Collect all upload URLs from HTML + each post css
{
  grep -hoE 'https://saigonhoreca\.vn/wp-content/uploads/[^"'\''\) ]+' "$html"
  for pid in $postids; do
    [ -f "wp-content/uploads/elementor/css/$pid" ] && \
      grep -hoE 'https://saigonhoreca\.vn/wp-content/uploads/[^"'\''\) ]+' "wp-content/uploads/elementor/css/$pid"
  done
} | sort -u | while read url; do
  rel=${url#https://saigonhoreca.vn/}
  if [ -f "$rel" ]; then continue; fi
  mkdir -p "$(dirname "$rel")"
  curl -skL "$url" -o "$rel" 2>/dev/null
  [ -s "$rel" ] && echo "  ok  $rel" || { rm -f "$rel"; echo "  FAIL $url"; }
done
echo "Done $slug"
