#!/usr/bin/env node
/**
 * package-deploy.mjs — Đóng gói theme để deploy trực tiếp (KHÔNG qua git).
 *
 * Lý do có script này: .gitignore loại dist/ + toàn bộ ảnh/font (xem
 * DEPLOY-NOTES.txt) — deploy qua git sẽ vỡ style. Script này build lại,
 * chạy cổng perf (fail Lighthouse = KHÔNG đóng gói), rồi zip đúng bộ file
 * cần cho production (gồm dist/, images, fonts — loại node_modules/,
 * scripts/, scratch/, docs/, *.bak*, project-data JSON).
 *
 * Dùng:
 *   node scripts/package-deploy.mjs                # build + gate + zip
 *   node scripts/package-deploy.mjs --skip-gate     # bỏ qua Lighthouse gate (KHÔNG khuyến khích)
 *   node scripts/package-deploy.mjs --skip-build    # dùng dist/ hiện có, không build lại
 */
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import { mkdir, rm, readdir, stat, cp } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const execFileP = promisify(execFile);
const __dirname = path.dirname(fileURLToPath(import.meta.url));
const THEME = path.resolve(__dirname, '..');
// Ngoài wp-content hẳn — tránh WordPress/webserver quét nhầm thư mục output.
const OUT_DIR = path.join(THEME, '..', '..', '..', '..', '..', '_sgh-deploy-packages');

const args = process.argv.slice(2);
const SKIP_GATE = args.includes('--skip-gate');
const SKIP_BUILD = args.includes('--skip-build');

// Loại khỏi gói deploy — build tooling, dev-only, nợ kỹ thuật đã ghi nhận.
const EXCLUDE_TOP = new Set([
  'node_modules', 'scripts', 'scratch', 'docs', '.git', '.gitignore',
  'package.json', 'package-lock.json', 'lighthouserc.json', 'budget.json',
  'DEPLOY-NOTES.txt', 'SEO-DEPLOY.md',
]);
const EXCLUDE_FILE_RE = /\.bak(-\d+)?$|\.py$/i;

// ⚠️ 2026-08-02 — scripts/project-data KHÔNG được loại: nó là DỮ LIỆU RUNTIME,
// không phải build tooling.
//   • template-parts/project-pillar/_related.php:23 glob cả thư mục này để dựng
//     khối "Dự án liên quan" — có trên MỌI trang pillar (29 trang).
//   • inc/engine/pillar-routes.php:259 đọc <slug>.json lấy tiêu đề route ảo.
// Bản cũ loại nó ở CẢ 2 tầng (EXCLUDE_TOP 'scripts' + EXCLUDE_PATH_PARTS
// 'project-data'), nên gói deploy thiếu 32 file JSON → production mất sạch khối
// "Dự án liên quan" trong khi local vẫn hiện 3 card. Lỗi chỉ lộ sau khi deploy.
const KEEP_PATHS = ['scripts/project-data'];

async function run(cmd, cmdArgs, opts = {}) {
  console.log(`$ ${cmd} ${cmdArgs.join(' ')}`);
  return execFileP(cmd, cmdArgs, { cwd: THEME, ...opts });
}

async function copyFiltered(src, dest) {
  await mkdir(dest, { recursive: true });
  const entries = await readdir(src, { withFileTypes: true });
  for (const e of entries) {
    const rel = path.relative(THEME, path.join(src, e.name)).split(path.sep).join('/');
    const top = rel.split('/')[0];
    // Nằm TRONG một KEEP_PATH -> giữ vô điều kiện.
    const insideKeep = KEEP_PATHS.some((k) => rel === k || rel.startsWith(k + '/'));
    // Là TỔ TIÊN của một KEEP_PATH -> phải đi xuyên qua, nhưng các nhánh con
    // khác của nó vẫn bị loại bình thường (vd: giữ scripts/ để tới
    // project-data, nhưng scripts/build.mjs vẫn không vào gói).
    const ancestorOfKeep = KEEP_PATHS.some((k) => k.startsWith(rel + '/'));
    if (!insideKeep) {
      // So theo segment gốc của đường dẫn, không so theo tên entry — nhờ vậy
      // cả nhánh con của thư mục bị loại cũng bị loại, kể cả khi ta buộc phải
      // đi xuyên qua thư mục cha.
      if (EXCLUDE_TOP.has(top) && !ancestorOfKeep) continue;
      if (EXCLUDE_FILE_RE.test(e.name)) continue;
    }
    const s = path.join(src, e.name);
    const d = path.join(dest, e.name);
    if (e.isDirectory()) {
      await copyFiltered(s, d);
    } else {
      await mkdir(path.dirname(d), { recursive: true });
      await cp(s, d);
    }
  }
}

async function main() {
  console.log('=== package-deploy.mjs ===\n');

  if (!SKIP_BUILD) {
    console.log('[1/4] Build CSS + JS (force)...');
    const { stdout, stderr } = await run(process.execPath, [path.join(THEME, 'scripts', 'build.mjs'), '--force']);
    console.log(stdout);
    if (stderr) console.error(stderr);
  } else {
    console.log('[1/4] Bỏ qua build (--skip-build)');
  }

  if (!SKIP_GATE) {
    console.log('\n[2/4] Chạy Lighthouse gate (npm run perf)...');
    try {
      await run(process.platform === 'win32' ? 'npm.cmd' : 'npm', ['run', 'perf']);
      console.log('Gate PASS — perf ≥ 0.99 trên 3 route chính.');
    } catch (e) {
      console.error('\nGATE FAIL — Lighthouse không đạt ngưỡng. KHÔNG đóng gói.');
      console.error('Sửa xong rồi chạy lại, hoặc dùng --skip-gate nếu chắc chắn muốn bỏ qua (không khuyến khích).');
      process.exit(1);
    }
  } else {
    console.log('\n[2/4] Bỏ qua Lighthouse gate (--skip-gate) — KHÔNG khuyến khích cho deploy thật.');
  }

  console.log('\n[3/4] Gom file vào staging...');
  await mkdir(OUT_DIR, { recursive: true });
  const stamp = new Date().toISOString().slice(0, 16).replace(/[T:]/g, '-');
  const stagingDir = path.join(OUT_DIR, `staging-${stamp}`);
  await rm(stagingDir, { recursive: true, force: true });
  await copyFiltered(THEME, stagingDir);

  const themeFolderName = path.basename(THEME);
  const stagingThemeDir = path.join(OUT_DIR, `_zip-root-${stamp}`, themeFolderName);
  await mkdir(path.dirname(stagingThemeDir), { recursive: true });
  await cp(stagingDir, stagingThemeDir, { recursive: true });
  await rm(stagingDir, { recursive: true, force: true });

  console.log('\n[4/4] Nén thành zip...');
  const zipPath = path.join(OUT_DIR, `${themeFolderName}-${stamp}.zip`);
  await rm(zipPath, { force: true });
  const zipRoot = path.dirname(stagingThemeDir);
  await run('powershell.exe', [
    '-NoProfile', '-Command',
    `Compress-Archive -Path '${zipRoot}\\*' -DestinationPath '${zipPath}' -CompressionLevel Optimal`,
  ]);
  await rm(zipRoot, { recursive: true, force: true });

  const { size } = await stat(zipPath);
  console.log(`\n✓ Xong: ${zipPath} (${(size / 1024 / 1024).toFixed(1)} MB)`);
  console.log('Checklist deploy: xem DEPLOY-NOTES.txt — verify .htaccess.production đã upload,'
    + ' PSI trên saigonhoreca.vn (home + 2 pillar + /lien-he/, mobile+desktop = 100).');
}

main().catch((e) => { console.error(e); process.exit(1); });
