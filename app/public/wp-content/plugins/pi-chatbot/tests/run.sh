#!/usr/bin/env bash
# run.sh — WP-CLI wrapper for this LocalWP site, so pi-chatbot's tests can be
# run without re-deriving the PHP/MySQL invocation every time.
#
# Two things about this specific LocalWP install make plain `wp` unusable:
#   1. The bundled PHP has no php.ini and needs its extensions (mysqli,
#      mbstring, etc.) passed explicitly on the command line.
#   2. MySQL is NOT on the default port 3306 — LocalWP assigns a random one
#      per site. Find yours via `cat ~/AppData/Roaming/Local/sites.json` and
#      look for this site's services.mysql.ports.MYSQL, or check
#      `netstat -ano | grep mysqld` if sites.json's format has changed.
#
# Usage (run from anywhere; paths below assume this file's own location):
#   tests/run.sh eval-file wp-content/plugins/pi-chatbot/tests/run-all.php
#   tests/run.sh eval-file wp-content/plugins/pi-chatbot/tests/eval-retrieval.php compare=baseline.json
#
# Adjust PHPDIR/MYSQL_PORT below if the PHP version or MySQL port ever changes
# (Local > this site > Database/PHP tabs will show the current values).

PHPDIR="/c/Users/Administrator/AppData/Roaming/Local/lightning-services/php-8.2.30+1/bin/win64"
EXT='C:\Users\Administrator\AppData\Roaming\Local\lightning-services\php-8.2.30+1\bin\win64\ext'
MYSQL_PORT=10011
WP_ROOT="C:/Users/Administrator/Local Sites/saigonhoreca/app/public"

cd "$WP_ROOT" || exit 1
"$PHPDIR/php.exe" -n \
  -d extension_dir="$EXT" \
  -d extension=mysqli -d extension=mbstring -d extension=openssl -d extension=curl \
  -d extension=exif -d extension=fileinfo -d extension=gd -d extension=zip -d extension=intl \
  -d mysqli.default_port="$MYSQL_PORT" \
  -d error_reporting=0 -d display_errors=0 -d memory_limit=1G \
  -d max_execution_time=0 \
  ~/wp-cli.phar "$@"

# Note on max_execution_time=0 above: it must be passed on the command line (a
# PHP_INI_ALL directive applied at startup), not left to a script's own
# set_time_limit(0) call. On this Windows PHP build, set_time_limit() at
# runtime was observed to have NO effect — a long-running eval-file (a
# 107-question live LLM audit) was still killed at exactly 60s despite calling
# set_time_limit(0) near the top of the script. The -d flag does not have that
# problem since it's applied before the script starts.
