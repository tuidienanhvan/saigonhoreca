# -*- coding: utf-8 -*-
"""Kiểm hành vi uninstall.php mà KHÔNG động vào DB nào.

Chạy uninstall-harness.php ($wpdb giả ghi lại mọi câu SQL) ở 3 chế độ — thường,
PI_API_KEEP_DATA, multisite — rồi khẳng định: drop đúng 21 bảng của pi-api, không động vào
bảng pi-chatbot hay bảng pi-api chỉ đọc, không xoá option sgh_* của theme, LIKE escape `_`.

Chạy:  python tests/check-uninstall.py        (exit 1 nếu sai)
Mutation đã thử 13/09/2026: bản uninstall.php trước đó trượt 10 khẳng định.

Thêm bảng mới vào plugin thì phải thêm vào cả uninstall.php lẫn tập BANG dưới đây.
"""
import glob
import json
import os
import re
import subprocess
import sys

TESTS = os.path.dirname(os.path.abspath(__file__))
HARNESS = os.path.join(TESTS, "uninstall-harness.php")
FILE = os.environ.get("UNINSTALL_FILE") or os.path.join(os.path.dirname(TESTS), "uninstall.php")
PHP = os.environ.get("PHP_BIN") or (sorted(glob.glob(os.path.expandvars(
    r"%APPDATA%\Local\lightning-services\php-*\bin\win64\php.exe"))) or ["php"])[-1]

BANG = {
    "pi_ai_cloud_transactions", "pi_analytics_events", "pi_analytics_pageviews", "pi_analytics_sessions",
    "pi_api_keys", "pi_audit_log", "pi_backup_schedules", "pi_backups", "pi_cron_logs", "pi_email_campaigns",
    "pi_email_templates", "pi_form_submissions", "pi_forms", "pi_indexing_queue", "pi_lead_activities",
    "pi_login_history", "pi_multilang_slug_batches", "pi_multilang_slugs", "pi_og_templates",
    "pi_saved_queries", "pi_webhook_deliveries",
}

loi = []


def chay(mode):
    r = subprocess.run([PHP, HARNESS, mode], capture_output=True, text=True,
                       env=dict(os.environ, UNINSTALL_FILE=FILE), stdin=subprocess.DEVNULL)
    if r.returncode != 0:
        print(r.stdout, r.stderr)
        raise SystemExit(f"php lỗi ở chế độ {mode}")
    return json.loads(r.stdout)


def ok(dk, msg):
    print(("  [OK]  " if dk else "  [SAI] ") + msg)
    if not dk:
        loi.append(msg)


print("== chế độ thường ==")
n = chay("normal")
sql = [s for _, s in n["sql"]]
drops = {re.search(r"`wp_(\w+)`", s).group(1) for s in sql if s.startswith("DROP TABLE")}
ok(drops == BANG, f"drop đúng 21 bảng của pi-api (drop {len(drops)})")
ok(not any("chatbot" in s for s in sql), "KHÔNG động vào wp_pi_chatbot_*")
ok("pi_analytics" not in drops and not any(t in d for d in drops for t in ("pi_leads", "sh_chat", "sh_analytics")),
   "KHÔNG drop wp_pi_leads / wp_pi_analytics / wp_sh_*")
likes = [re.search(r"LIKE '(.*)'", s).group(1) for s in sql if "LIKE" in s]
ok(not any(x.startswith("sgh") for x in likes), "KHÔNG xoá option sgh_* (theme dùng chung)")
ok(bool(likes) and all("\\_" in x for x in likes), "mọi mẫu LIKE đều escape dấu _ (esc_like)")
ok("pi\\_ai\\_%" in likes and "sh\\_ai\\_%" in likes, "có xoá pi_ai_* và sh_ai_* (khoá LLM)")
ok("\\_transient\\_pi\\_api\\_%" in likes, "có dọn transient pi_api_*")
ok(len(n["cron"]) == 6, f"clear 6 cron hook (thấy {len(n['cron'])})")
ok(sorted(n["meta"]) == sorted(["user:pi_2fa_secret:all", "user:pi_2fa_recovery:all",
                                 "user:pi_last_login:all", "user:pi_user_status:all"]),
   "xoá 4 user_meta trên mọi user")
ok(n["flush"] == 1, "wp_cache_flush đúng 1 lần")

print("== PI_API_KEEP_DATA ==")
k = chay("keep")
ok(k["sql"] == [], "không chạy câu SQL nào")
ok(len(k["cron"]) == 6, "vẫn clear cron")
ok(k["meta"] == [] and k["flush"] == 0, "không xoá user_meta, không flush")

print("== multisite 2 blog ==")
m = chay("multi")
theo_prefix = {}
for pre, s in m["sql"]:
    if s.startswith("DROP TABLE"):
        theo_prefix[pre] = theo_prefix.get(pre, 0) + 1
ok(theo_prefix == {"wp_": 21, "wp_2_": 21}, f"mỗi blog drop 21 bảng đúng prefix ({theo_prefix})")
ok(any("wp_2_options" in s for _, s in m["sql"]), "blog 2 xoá option trong wp_2_options")
ok(len(m["meta"]) == 4, "user_meta chỉ xoá 1 lần cho cả network")

print()
print("KẾT QUẢ:", "ĐẠT" if not loi else f"{len(loi)} SAI")
sys.exit(1 if loi else 0)
