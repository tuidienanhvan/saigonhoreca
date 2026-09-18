# -*- coding: utf-8 -*-
"""Chạy rescore-harness.php: gọi Pi_Api_Leads::rescore_leads THẬT với $wpdb giả và lead "chậm".

Kiểm: body {} không còn 400; lead_ids cụ thể không bị coi là 'all'; ngân sách thời gian cắt
đúng ở ~20s và trả next_after_id; đi theo con trỏ tới hết; last_rescore chỉ ghi khi xong.
Mất ~45 giây vì mô phỏng lead chậm bằng sleep thật.

Chạy: python tests/check-rescore.py     (exit 1 nếu sai)
"""
import glob
import os
import subprocess
import sys

TESTS = os.path.dirname(os.path.abspath(__file__))
F = os.path.join(os.path.dirname(TESTS), "includes", "api", "endpoints", "web_zone",
                 "marketing_workspace", "leads", "class-leads.php")
PHP = os.environ.get("PHP_BIN") or (sorted(glob.glob(os.path.expandvars(
    r"%APPDATA%\Local\lightning-services\php-*\bin\win64\php.exe"))) or ["php"])[-1]
r = subprocess.run([PHP, os.path.join(TESTS, "rescore-harness.php")], env=dict(os.environ, F=F),
                   stdin=subprocess.DEVNULL)
sys.exit(r.returncode)
