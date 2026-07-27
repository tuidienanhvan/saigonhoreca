#!/usr/bin/env node
/**
 * build.mjs — Build pipeline hợp nhất (thay 44 npm script tuần tự).
 *
 * 1. CSS  : auto-discover assets/css/style.css + entrypoints/style-*.css
 *           → dist/theme*.css, Tailwind v4 CLI --minify, chạy song song theo pool CPU,
 *           incremental theo mtime (bỏ qua bundle chưa đổi input).
 * 2. JS   : esbuild --minify assets/js/*.js → assets/js/dist/ (sgh_js_path() tự ưu tiên dist).
 * 3. LINT : animation CPU (keyframe/transition animate thuộc tính layout, transition:all)
 *           + engine purity (inc/engine/ không được chứa brand string).
 *
 * Dùng:
 *   node scripts/build.mjs               # incremental
 *   node scripts/build.mjs --force       # build lại tất cả
 *   node scripts/build.mjs --only home   # chỉ bundle tên chứa chuỗi
 *   node scripts/build.mjs --lint-only   # chỉ chạy lint, không build
 *   node scripts/build.mjs --strict-anim # lint animation fail = exit 1 (bật từ Phase 6)
 */
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import { readdir, stat, readFile, mkdir } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const execFileP = promisify(execFile);
const __dirname = path.dirname(fileURLToPath(import.meta.url));
const THEME = path.resolve(__dirname, '..');
const CSS = path.join(THEME, 'assets', 'css');
const DIST = path.join(CSS, 'dist');
const JS_SRC = path.join(THEME, 'assets', 'js');
const JS_DIST = path.join(JS_SRC, 'dist');
const TAILWIND = path.join(THEME, 'node_modules', '@tailwindcss', 'cli', 'dist', 'index.mjs');

const args = process.argv.slice(2);
const FORCE = args.includes('--force');
const LINT_ONLY = args.includes('--lint-only');
const STRICT_ANIM = args.includes('--strict-anim');
const STRICT_PURITY = args.includes('--strict-purity');
const ONLY = (() => { const i = args.indexOf('--only'); return i >= 0 ? args[i + 1] : ''; })();
const POOL = Math.max(2, Math.min(16, os.cpus().length - 2));

/* ---------- helpers ---------- */
async function mtime(p) { try { return (await stat(p)).mtimeMs; } catch { return 0; } }

/** Mtime mới nhất của entrypoint + toàn bộ cây @import của nó */
async function latestInputMtime(entry, seen = new Set()) {
  if (seen.has(entry) || !existsSync(entry)) return 0;
  seen.add(entry);
  let latest = await mtime(entry);
  const src = await readFile(entry, 'utf8').catch(() => '');
  for (const m of src.matchAll(/@import\s+(?:url\()?["']([^"')]+)["']/g)) {
    const ref = m[1];
    if (/^https?:/.test(ref) || ref.startsWith('tailwindcss')) continue;
    const child = path.resolve(path.dirname(entry), ref);
    latest = Math.max(latest, await latestInputMtime(child, seen));
  }
  return latest;
}

async function runPool(jobs, worker) {
  const queue = [...jobs];
  const results = [];
  await Promise.all(Array.from({ length: Math.min(POOL, queue.length) }, async () => {
    while (queue.length) results.push(await worker(queue.shift()));
  }));
  return results;
}

/* ---------- 1. CSS ---------- */
async function discoverCssJobs() {
  const jobs = [{ entry: path.join(CSS, 'core', 'style.css'), out: path.join(DIST, 'theme.css'), name: 'theme' }];
  const epDir = path.join(CSS, 'entrypoints');
  for (const f of (await readdir(epDir)).filter((f) => /^style-.+\.css$/.test(f))) {
    const name = f.replace(/^style-/, 'theme-').replace(/\.css$/, '');
    jobs.push({ entry: path.join(epDir, f), out: path.join(DIST, `${name}.css`), name });
  }
  return ONLY ? jobs.filter((j) => j.name.includes(ONLY)) : jobs;
}

async function buildCss() {
  const jobs = await discoverCssJobs();
  const todo = [];
  for (const j of jobs) {
    if (FORCE || (await latestInputMtime(j.entry)) > (await mtime(j.out))) todo.push(j);
  }
  console.log(`CSS: ${jobs.length} bundle, build ${todo.length} (skip ${jobs.length - todo.length} chưa đổi), pool ${POOL}`);
  const t0 = Date.now();
  let fail = 0;
  await runPool(todo, async (j) => {
    try {
      await execFileP(process.execPath, [TAILWIND, '-i', j.entry, '-o', j.out, '--minify'], { timeout: 300000 });
      console.log(`  ✓ ${j.name}`);
    } catch (e) {
      fail++;
      console.error(`  ✗ ${j.name}: ${(e.stderr || e.message).toString().slice(0, 300)}`);
    }
  });
  console.log(`CSS xong ${(Date.now() - t0) / 1000}s${fail ? ` — ${fail} LỖI` : ''}`);
  return fail === 0;
}

/* ---------- 2. JS ---------- */
async function buildJs() {
  await mkdir(JS_DIST, { recursive: true });
  const files = (await readdir(JS_SRC)).filter((f) => f.endsWith('.js'));
  const todo = [];
  for (const f of files) {
    const src = path.join(JS_SRC, f), out = path.join(JS_DIST, f);
    if (FORCE || (await mtime(src)) > (await mtime(out))) todo.push({ src, out, name: f });
  }
  console.log(`JS: ${files.length} file, minify ${todo.length}`);
  if (!todo.length) return true;
  const esbuild = await import('esbuild');
  let fail = 0;
  await runPool(todo, async (j) => {
    try {
      await esbuild.build({ entryPoints: [j.src], outfile: j.out, minify: true, charset: 'utf8', logLevel: 'silent' });
      console.log(`  ✓ ${j.name}`);
    } catch (e) {
      fail++;
      console.error(`  ✗ ${j.name}: ${e.message.slice(0, 300)}`);
    }
  });
  return fail === 0;
}

/* ---------- 3a. Lint animation CPU ---------- */
const LAYOUT_PROPS = ['top', 'left', 'right', 'bottom', 'width', 'height', 'margin[a-z-]*', 'padding[a-z-]*', 'background-position', 'max-height', 'max-width', 'min-height', 'letter-spacing', 'font-size'];
const LAYOUT_RE = new RegExp(`^\\s*(${LAYOUT_PROPS.join('|')})\\s*:`, 'i');

async function* walkCss(dir) {
  for (const e of await readdir(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) { if (e.name !== 'dist' && e.name !== 'node_modules') yield* walkCss(p); }
    else if (e.name.endsWith('.css')) yield p;
  }
}

async function lintAnimations() {
  const violations = [];
  const roots = [CSS, path.join(THEME, 'template-parts'), path.join(THEME, 'single-project')];
  for (const root of roots) {
    if (!existsSync(root)) continue;
    for await (const file of walkCss(root)) {
      const src = await readFile(file, 'utf8');
      const rel = path.relative(THEME, file);
      // keyframes animate thuộc tính layout
      for (const kf of src.matchAll(/@keyframes\s+([\w-]+)\s*\{([\s\S]*?)\}\s*\}/g)) {
        for (const line of kf[2].split(/[;\n]/)) {
          if (LAYOUT_RE.test(line)) violations.push(`${rel} @keyframes ${kf[1]}: animate "${line.trim().slice(0, 60)}"`);
        }
      }
      // transition: all
      for (const m of src.matchAll(/transition\s*:\s*all[\s,;0-9.]/g)) {
        violations.push(`${rel}: transition:all`);
      }
      // transition chứa thuộc tính layout
      for (const m of src.matchAll(/transition\s*:\s*([^;{}]+)[;}]/g)) {
        for (const prop of LAYOUT_PROPS) {
          if (new RegExp(`(^|[,\\s])${prop}(\\s|$|,)`, 'i').test(m[1])) {
            violations.push(`${rel}: transition trên "${m[1].trim().slice(0, 60)}"`);
            break;
          }
        }
      }
    }
  }
  const uniq = [...new Set(violations)];
  if (uniq.length) {
    console.log(`\nANIMATION LINT: ${uniq.length} vi phạm (animate thuộc tính layout / transition:all):`);
    uniq.slice(0, 60).forEach((v) => console.log(`  ⚠ ${v}`));
    if (uniq.length > 60) console.log(`  ... và ${uniq.length - 60} chỗ nữa`);
  } else {
    console.log('\nANIMATION LINT: sạch ✓');
  }
  return uniq.length;
}

/* ---------- 3b. Lint engine purity ---------- */
async function lintEnginePurity() {
  const engineDir = path.join(THEME, 'inc', 'engine');
  if (!existsSync(engineDir)) return 0; // chưa tới Phase 4
  const BRAND_RE = /saigonhoreca|Hồ Chí Minh|GTM-[A-Z0-9]+|du-an|san-pham|lien-he/;
  const violations = [];
  async function* walk(dir) {
    for (const e of await readdir(dir, { withFileTypes: true })) {
      const p = path.join(dir, e.name);
      if (e.isDirectory()) yield* walk(p); else if (e.name.endsWith('.php')) yield p;
    }
  }
  for await (const file of walk(engineDir)) {
    const lines = (await readFile(file, 'utf8')).split('\n');
    lines.forEach((line, i) => {
      if (BRAND_RE.test(line)) violations.push(`${path.relative(THEME, file)}:${i + 1}`);
    });
  }
  if (violations.length) {
    console.log(`\nENGINE PURITY: ${violations.length} brand string trong inc/engine/ (nợ kỹ thuật đã biết —`
      + ` chỉ chặn build khi chạy --strict-purity):`);
    violations.slice(0, 30).forEach((v) => console.log(`  ✗ ${v}`));
  } else {
    console.log('ENGINE PURITY: sạch ✓');
  }
  return violations.length;
}

/* ---------- main ---------- */
async function main() {
  let ok = true;
  if (!LINT_ONLY) {
    const { generateProjectCss } = await import('./generate-project-css.mjs');
    await generateProjectCss({ log: console.log });
    ok = (await buildCss()) && ok;
    ok = (await buildJs()) && ok;
  }
  const animCount = await lintAnimations();
  const purityCount = await lintEnginePurity();
  if (!ok) process.exit(1);
  if (STRICT_PURITY && purityCount > 0) process.exit(1);
  if (STRICT_ANIM && animCount > 0) process.exit(1);
}
main().catch((e) => { console.error(e); process.exit(1); });
