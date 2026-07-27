#!/usr/bin/env node
/**
 * html-snapshot.mjs — chụp HTML mọi URL trong sitemap, normalize phần động,
 * để diff trước/sau mỗi phase refactor (kỳ vọng: byte-identical).
 *
 *   node scripts/html-snapshot.mjs --label baseline
 *   node scripts/html-snapshot.mjs --label phase1 --diff baseline
 */
import { mkdir, writeFile, readFile, readdir } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ORIGIN = process.env.SGH_ORIGIN || 'https://saigonhoreca.local';
if (/\.local(\/|$)/.test(ORIGIN)) process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
const SNAP_ROOT = 'C:/Users/Administrator/_sgh-html-snapshots';

const args = process.argv.slice(2);
const getOpt = (n, d) => { const i = args.indexOf(`--${n}`); return i >= 0 && args[i + 1] ? args[i + 1] : d; };
const LABEL = getOpt('label', 'snap');
const DIFF_AGAINST = getOpt('diff', '');

async function fetchText(url) {
  const res = await fetch(url, { redirect: 'follow' });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.text();
}
const extractLocs = (xml) => [...xml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1].trim());

async function collectUrls() {
  const index = await fetchText(`${ORIGIN}/wp-sitemap.xml`);
  const urls = new Set([`${ORIGIN}/`]);
  for (const sm of extractLocs(index).map((u) => u.replace(/^https?:\/\/[^/]+/, ORIGIN))) {
    try { extractLocs(await fetchText(sm)).forEach((l) => urls.add(l.replace(/^https?:\/\/[^/]+/, ORIGIN))); }
    catch { /* skip */ }
  }
  return [...urls].sort();
}

/** Loại bỏ phần thay đổi mỗi request để diff có nghĩa */
function normalize(html) {
  return html
    .replace(/\?ver=[0-9a-f.]+/gi, '?ver=X')
    .replace(/nonce="[^"]*"/gi, 'nonce="X"')
    .replace(/name="_wpnonce" value="[^"]*"/gi, 'name="_wpnonce" value="X"')
    .replace(/<!-- (Cached|Generated|Rendered)[^>]*-->/gi, '')
    .replace(/\b\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[^"'< ]*/g, 'TIMESTAMP');
}

const slugify = (url) => {
  const p = url.replace(ORIGIN, '').replace(/^\/|\/$/g, '');
  return (p === '' ? '_home' : p.replace(/[^a-z0-9-]/gi, '_')).slice(0, 120) + '.html';
};

async function main() {
  const outDir = path.join(SNAP_ROOT, LABEL);
  await mkdir(outDir, { recursive: true });
  const urls = await collectUrls();
  console.log(`Chụp ${urls.length} URL → ${outDir}`);
  for (const url of urls) {
    try { await writeFile(path.join(outDir, slugify(url)), normalize(await fetchText(url)), 'utf8'); }
    catch (e) { console.warn(`  ! ${url}: ${e.message}`); }
  }

  if (DIFF_AGAINST) {
    const baseDir = path.join(SNAP_ROOT, DIFF_AGAINST);
    if (!existsSync(baseDir)) { console.error(`Không có snapshot '${DIFF_AGAINST}'`); process.exit(1); }
    let same = 0; const changed = [], missing = [];
    for (const f of await readdir(baseDir)) {
      const a = path.join(baseDir, f), b = path.join(outDir, f);
      if (!existsSync(b)) { missing.push(f); continue; }
      (await readFile(a, 'utf8')) === (await readFile(b, 'utf8')) ? same++ : changed.push(f);
    }
    console.log(`\nDIFF vs '${DIFF_AGAINST}': ${same} identical, ${changed.length} KHÁC, ${missing.length} thiếu`);
    changed.forEach((f) => console.log(`  KHÁC: ${f}`));
    missing.forEach((f) => console.log(`  THIẾU: ${f}`));
    process.exitCode = changed.length || missing.length ? 2 : 0;
  }
}
main().catch((e) => { console.error(e); process.exit(1); });
