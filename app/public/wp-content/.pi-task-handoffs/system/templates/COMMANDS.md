# Command Blocks / Mẫu Lệnh

## I. 📊 Dashboard

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-dashboard-webapp"
npm run lint
npm run test:run
npm run build
```

## II. 📌 Store

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
npm run lint
npm run test
npm run build
```

## III. 📌 PHP Syntax

```powershell
php -l "path\to\file.php"
```

## IV. 📌 Handoff Docs Mojibake Check / Kiểm Lỗi Font

```powershell
$patterns = @(
  ([char]0x00C3 + '.'),
  ([char]0x00C2 + '.'),
  ([char]0x00E2 + [char]0x20AC),
  ([char]0x00F0 + [char]0x0178),
  ('T' + [char]0x00E1 + [char]0x00BB),
  ([char]0x00C4 + [char]0x2018),
  ([char]0x00C4 + [char]0x0090),
  ([char]0x00C6)
)
Select-String -Path ".task-handoffs\*.md",".task-handoffs\**\*.md" -Pattern ($patterns -join "|") -CaseSensitive
```

## V. 🟢 Git Status

```powershell
git status --short
```
