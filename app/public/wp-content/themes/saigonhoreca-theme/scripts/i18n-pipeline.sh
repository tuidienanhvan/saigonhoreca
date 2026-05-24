#!/usr/bin/env bash
set -e
cd "$(dirname "$0")/.."

# 1. Extract
wp i18n make-pot . languages/saigonhoreca.pot --domain=saigonhoreca \
    --exclude=node_modules,vendor,static-mirror,scripts,assets/css/dist \
    --skip-js --skip-block-json --skip-theme-json

# 2. Merge into existing (preserve translations)
if [ -f languages/en_US.po ]; then
    msgmerge -U --no-fuzzy-matching --backup=off languages/en_US.po languages/saigonhoreca.pot
else
    cp languages/saigonhoreca.pot languages/en_US.po
    sed -i 's/"Language: \\n"/"Language: en_US\\n"/' languages/en_US.po
fi

# 3. Compile
if command -v msgfmt &>/dev/null; then
    msgfmt languages/en_US.po -o languages/en_US.mo
else
    wp i18n make-mo languages/
fi

echo "Done. languages/{saigonhoreca.pot, en_US.po, en_US.mo}"
