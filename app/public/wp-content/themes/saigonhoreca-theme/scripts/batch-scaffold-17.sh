#!/usr/bin/env bash
# Batch scaffold 17 root-level pillar projects.
# Pipeline per project: extract JSON → render NN-*.php → finalize 8 slots → suffix → imports.
set -e
cd "$(dirname "$0")/.."

# slug:suffix:label
declare -a PROJECTS=(
  "du-nam-an-an:nan:Du Nam An An"
  "du-an-vinh-hiep:vhp:Du an Vinh Hiep - Coffee Lab"
  "moa-moa:mmo:Moa Moa"
  "casa-maria:cma:Casa Maria"
  "tales-by-chapter:tbc:Tales by Chapter"
  "the-cheezy-time:tct:The Cheezy Time"
  "pho-24:p24:Pho 24"
  "bep-an-truong-mam-non-tu-thuc-trinh-vuong:tvg:Truong Mam Non Trinh Vuong"
  "hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien:hmd:Hemma Desserts"
  "g-cup-coffee-bistro:gcb:G-Cup Coffee & Bistro"
  "mam-mam-eatery-lounge-nang-tam-mam-com-viet:mml:Mam Mam Eatery & Lounge"
  "ganh-hao-noi-hon-bien-trong-tung-net-kien-truc:gha:Ganh Hao"
  "bep-canteen-nha-may-sheh-fung:shf:Sheh Fung"
  "du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca:rtn:KDL Rung Thong Nui Voi"
  "du-an-bep-cang-tin-cong-ty-nhat-nichiyo:nic:Nichiyo Canteen"
  "bling-bling-club:bbc:Bling Bling Club"
  "renovate-sol-kitchen-bar-quan-7:rsk:Renovate Sol Q7"
)

IMPORTS="assets/css/_imports-project.css"

for entry in "${PROJECTS[@]}"; do
  IFS=':' read -r slug suffix label <<< "$entry"
  echo ""
  echo "════════ $slug (-$suffix) — $label ════════"

  # 1. Extract JSON
  PYTHONIOENCODING=utf-8 python scripts/project-extract.py "$slug" 2>&1 | tail -2 || { echo "extract failed"; continue; }

  # 2. Render NN-*.php + slug.css
  PYTHONIOENCODING=utf-8 python scripts/project-render.py "$slug" 2>&1 | tail -2 || { echo "render failed"; continue; }

  # 3. Finalize to 8 semantic slots
  PYTHONIOENCODING=utf-8 python scripts/finalize-new-pillar.py "$slug" "$label" 2>&1 | tail -3 || { echo "finalize failed"; continue; }

  # 4. Apply namespace suffix
  PYTHONIOENCODING=utf-8 python scripts/namespace-suffix.py "$slug" "$suffix" 2>&1 | tail -2 || { echo "suffix failed"; continue; }

  # 5. Append to imports if not already
  if ! grep -q "/$slug/hero.css" "$IMPORTS" 2>/dev/null; then
    {
      echo ""
      echo "/* $slug */"
      for s in hero intro concept partnership specs gallery related cta; do
        echo "@import \"../../template-parts/project-pillar/$slug/$s.css\";"
      done
      echo "@import \"../../page-templates/page-project-$slug.css\";"
    } >> "$IMPORTS"
    echo "  added to imports"
  else
    echo "  imports already include this slug"
  fi
done

echo ""
echo "════════ Build + verify ════════"
npm run build:project 2>&1 | tail -3

for entry in "${PROJECTS[@]}"; do
  IFS=':' read -r slug suffix label <<< "$entry"
  touch "template-parts/project-pillar/$slug/hero.php" 2>/dev/null
  code=$(curl -skL -o /dev/null -w "%{http_code}" "http://saigonhoreca.local/$slug/")
  echo "HTTP $code  /$slug/"
done
