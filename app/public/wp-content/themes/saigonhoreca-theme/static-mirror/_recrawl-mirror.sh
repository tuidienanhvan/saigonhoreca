#!/usr/bin/env bash
# recrawl-mirror.sh — crawl 1 project mirror ĐÚNG chuẩn §G (download images + localize + clean project-only + strip).
# Usage: bash _recrawl-mirror.sh <vn-slug> <token-regex> [com-slug|VNONLY]
#   vn-slug    : folder slug (vd the-royal-all-day-dining)
#   token-regex: lọc ảnh project (vd 'royal|the-royal')
#   com-slug   : English slug trên saigonhoreca.com, hoặc VNONLY nếu không có .com
set -uo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.." || exit 1   # → theme root
SLUG="$1"; TOKEN="$2"; COM="${3:-VNONLY}"
stem(){ sed -E 's#-[0-9]+x[0-9]+##; s#\.[A-Za-z0-9]+$##'; }

crawl_one(){
  local domain="$1" url="$2" host="$3"
  local dir="static-mirror/$domain/$SLUG" raw
  rm -rf "$dir"; mkdir -p "$dir/images" "$dir/css"; raw="$dir/_raw.html"
  echo "──── $domain ← $url ────"
  curl -ksL --max-time 60 -A "Mozilla/5.0 (mirror-bot)" "$url" -o "$raw"
  local code=$(curl -ks -o /dev/null -w '%{http_code}' --max-time 15 "$url")
  echo "  http=$code fetched=$(wc -c <"$raw")B $(grep -oiE '<html[^>]*lang=\"[a-z-]+\"' "$raw"|head -1)"
  # collect image URLs (src/data-src/srcset)
  : > "$dir/_img.txt"
  grep -oiE "(src|data-src|data-lazy-src|content)=\"https://$host/wp-content/uploads/[^\"]+\.(jpg|jpeg|png|webp|gif|svg)\"" "$raw" | sed -E 's/^[a-z-]+=\"//I;s/\"$//' >> "$dir/_img.txt"
  grep -oiE 'srcset="[^"]+"' "$raw" | sed -E 's/srcset="//I;s/"$//' | tr ',' '\n' | grep -oiE "https://$host/wp-content/uploads/[^ ]+\.(jpg|jpeg|png|webp|gif|svg)" >> "$dir/_img.txt"
  sort -u "$dir/_img.txt" -o "$dir/_img.txt"
  local ok=0; while IFS= read -r iu; do [ -z "$iu" ] && continue; local fn=$(basename "${iu%%\?*}")
    [ -f "$dir/images/$fn" ] || { curl -ksL --max-time 40 "$iu" -o "$dir/images/$fn" 2>/dev/null && [ -s "$dir/images/$fn" ] && ok=$((ok+1)) || rm -f "$dir/images/$fn"; }
  done < "$dir/_img.txt"
  # css same-domain
  for cu in $(grep -oiE "https://$host/[^\" )']+\.css(\?[^\" )']*)?" "$raw" | sort -u); do local b=$(basename "${cu%%\?*}"); [ -f "$dir/css/$b" ] || curl -ksL --max-time 30 "$cu" -o "$dir/css/$b" 2>/dev/null; done
  # rewrite → local
  sed -E "s#https://$host/wp-content/uploads/[^\"' )]*/([^\"'/ )]+\.(jpg|jpeg|png|webp|gif|svg))#images/\1#Ig; s#https://$host/[^\"' )]*/([^\"'/ )]+\.css)(\?[^\"' )]*)?#css/\1#Ig" "$raw" > "$dir/index.html"
  # CLEAN images: content-region + token
  local H="$dir/index.html" c r keep="$dir/_keep.txt"
  c=$(grep -aob 'data-elementor-type' "$H"|head -1|cut -d: -f1); [ -z "$c" ] && c=$(grep -aob '<main' "$H"|head -1|cut -d: -f1)
  r=$(grep -aob 'elementor-widget-portfolio' "$H"|head -1|cut -d: -f1); [ -z "$r" ] && r=$(grep -aob 'pp-related' "$H"|head -1|cut -d: -f1); [ -z "$r" ] && r=$(grep -aob '<footer' "$H"|head -1|cut -d: -f1)
  : > "$keep"; dd if="$H" bs=1 skip=$c count=$((r-c)) 2>/dev/null | grep -oiE 'images/[^"'"'"' )]+\.(jpg|jpeg|png|webp|gif|svg)' | sed 's#images/##' | stem >> "$keep"; sort -u "$keep" -o "$keep"
  local del=0 kept=0; for f in "$dir/images"/*; do [ -f "$f" ] || continue; local bn=$(basename "$f") st=$(basename "$f"|stem)
    if grep -qxF "$st" "$keep" || echo "$bn"|grep -qiE "$TOKEN"; then kept=$((kept+1)); else rm -f "$f"; del=$((del+1)); fi
  done
  # STRIP header/footer/related
  local bodyoff cdiv port relsec bodyend slice rel
  bodyoff=$(grep -aob '<body' "$H"|head -1|cut -d: -f1)
  cdiv=$(grep -aob '<div data-elementor-type="wp-page"' "$H"|head -1|cut -d: -f1); [ -z "$cdiv" ] && cdiv=$(grep -aob '<main' "$H"|head -1|cut -d: -f1); [ -z "$cdiv" ] && cdiv=$c
  port=$(grep -aob 'elementor-widget-portfolio' "$H"|head -1|cut -d: -f1); [ -z "$port" ] && port=$(grep -aob 'pp-related' "$H"|head -1|cut -d: -f1)
  relsec=$(grep -aob '<section' "$H"|awk -F: -v p="$port" '$1<p{x=$1}END{print x}'); [ -z "$relsec" ] && relsec="$port"; [ -z "$relsec" ] && relsec=$(grep -aob '<footer' "$H"|head -1|cut -d: -f1)
  slice=$(dd if="$H" bs=1 skip="$bodyoff" count=600 2>/dev/null); rel=$(awk -v s="$slice" 'BEGIN{print index(s,">")}'); bodyend=$((bodyoff+rel))
  { dd if="$H" bs=1 count="$bodyend" 2>/dev/null; printf '\n'; dd if="$H" bs=1 skip="$cdiv" count=$((relsec-cdiv)) 2>/dev/null; printf '\n</div></body>\n</html>\n'; } > "$H.new"; mv "$H.new" "$H"
  local broken=0; for rr in $(grep -oiE 'images/[^"'"'"' )]+\.(jpg|jpeg|png|webp|gif|svg)' "$H"|sed 's#images/##'|sort -u); do [ -f "$dir/images/$rr" ] || broken=$((broken+1)); done
  rm -f "$raw" "$dir/_img.txt" "$keep"
  printf "  ✓ images kept=%s dropped=%s | css=%s | index=%sB | broken=%s | markers=%s\n" "$kept" "$del" "$(find "$dir/css" -type f|wc -l)" "$(wc -c<"$H")" "$broken" "$(grep -c '<footer\|elementor-widget-portfolio\|pp-related' "$H")"
}

crawl_one "saigonhoreca.vn" "https://saigonhoreca.local/du-an/$SLUG/" "saigonhoreca.local"
if [ "$COM" != "VNONLY" ]; then
  crawl_one "saigonhoreca.com" "https://saigonhoreca.com/$COM/" "saigonhoreca.com"
else
  echo "  (.com vn-only — bỏ qua)"
fi
