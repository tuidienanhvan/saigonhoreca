#!/usr/bin/env node
/**
 * perf-sweep.mjs — Lighthouse sweep TOÀN BỘ trang, xuất scoreboard.
 *
 * Lấy URL từ WP sitemap (wp-sitemap.xml) của site local, chạy Lighthouse
 * lần lượt từng URL (tuần tự — chạy song song làm sai lệch số đo),
 * xuất docs/perf-baselines/scoreboard.md + scoreboard.json.
 *
 * Cách dùng:
 *   node scripts/perf-sweep.mjs                    # mobile, mọi URL trong sitemap
 *   node scripts/perf-sweep.mjs --desktop          # thêm lượt desktop
 *   node scripts/perf-sweep.mjs --limit 5          # chỉ 5 URL đầu (test nhanh)
 *   node scripts/perf-sweep.mjs --filter du-an     # chỉ URL chứa chuỗi này
 *   node scripts/perf-sweep.mjs --label baseline   # tên file output
 *   node scripts/perf-sweep.mjs --sample           # mẫu đại diện: home + pages +
 *                                                  # TẤT CẢ project + 5 post + 5 product + taxonomy
 */
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import { mkdir, writeFile, readFile, rm } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const execFileP = promisify(execFile);
const __dirname = path.dirname(fileURLToPath(import.meta.url));
const THEME = path.resolve(__dirname, '..');
const ORIGIN = process.env.SGH_ORIGIN || 'https://saigonhoreca.local';

// Site local dùng cert self-signed (Local by Flywheel) — chỉ nới TLS cho *.local
if (/\.local(\/|$)/.test(ORIGIN)) process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
const OUT_DIR = path.join(THEME, 'docs', 'perf-baselines');
const LH_CLI = path.join(THEME, 'node_modules', 'lighthouse', 'cli', 'index.js');

const args = process.argv.slice(2);
const getFlag = (name) => args.includes(`--${name}`);
const getOpt = (name, dflt) => {
  const i = args.indexOf(`--${name}`);
  return i >= 0 && args[i + 1] ? args[i + 1] : dflt;
};

const LIMIT = parseInt(getOpt('limit', '0'), 10);
const FILTER = getOpt('filter', '');
const LABEL = getOpt('label', 'sweep');
const DESKTOP = getFlag('desktop');

async function fetchText(url) {
  const res = await fetch(url, { redirect: 'follow' });
  if (!res.ok) throw new Error(`${url} -> HTTP ${res.status}`);
  return res.text();
}

function extractLocs(xml) {
  return [...xml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1].trim());
}

async function collectUrls() {
  const index = await fetchText(`${ORIGIN}/wp-sitemap.xml`);
  const subSitemaps = extractLocs(index).map((u) => u.replace(/^https?:\/\/[^/]+/, ORIGIN));
  // Gom theo loại sitemap để sample được: posts-post, posts-page, posts-project, posts-product, taxonomies-*
  const groups = { home: [`${ORIGIN}/`], post: [], page: [], project: [], product: [], taxonomy: [] };
  for (const sm of subSitemaps) {
    const kind = sm.includes('posts-post-') ? 'post'
      : sm.includes('posts-page-') ? 'page'
      : sm.includes('posts-project-') ? 'project'
      : sm.includes('posts-product-') ? 'product'
      : 'taxonomy';
    try {
      for (const loc of extractLocs(await fetchText(sm))) {
        groups[kind].push(loc.replace(/^https?:\/\/[^/]+/, ORIGIN));
      }
    } catch (e) {
      console.warn(`  ! bỏ qua ${sm}: ${e.message}`);
    }
  }
  let list;
  if (getFlag('sample')) {
    // Mẫu đại diện: template giống nhau thì không cần đo hết —
    // nhưng project pillar mỗi trang CSS/nội dung riêng nên đo TẤT CẢ.
    list = [
      ...groups.home,
      ...groups.page,
      ...groups.project,
      ...groups.post.slice(0, 5),
      ...groups.product.slice(0, 5),
      ...groups.taxonomy.slice(0, 6),
    ];
  } else {
    list = [...new Set([...groups.home, ...groups.page, ...groups.project, ...groups.post, ...groups.product, ...groups.taxonomy])];
  }
  if (FILTER) list = list.filter((u) => u.includes(FILTER));
  if (LIMIT > 0) list = list.slice(0, LIMIT);
  return [...new Set(list)];
}

async function runLighthouse(url, mode) {
  const tmpJson = path.join(OUT_DIR, `_tmp-lh.json`);
  const lhArgs = [
    LH_CLI,
    url,
    '--output=json',
    `--output-path=${tmpJson}`,
    '--only-categories=performance',
    '--chrome-flags=--headless=new --ignore-certificate-errors',
    '--quiet',
  ];
  if (mode === 'desktop') lhArgs.push('--preset=desktop');
  await execFileP(process.execPath, lhArgs, { timeout: 180000 });
  const report = JSON.parse(await readFile(tmpJson, 'utf8'));
  await rm(tmpJson, { force: true });
  const audits = report.audits;
  return {
    score: Math.round((report.categories.performance.score ?? 0) * 100),
    lcp: Math.round(audits['largest-contentful-paint']?.numericValue ?? -1),
    tbt: Math.round(audits['total-blocking-time']?.numericValue ?? -1),
    cls: +(audits['cumulative-layout-shift']?.numericValue ?? -1).toFixed(3),
    si: Math.round(audits['speed-index']?.numericValue ?? -1),
  };
}

function fmtRow(r) {
  const flag = r.score >= 100 ? '✅' : r.score >= 90 ? '⚠️' : '❌';
  return `| ${flag} ${r.score} | ${r.mode} | ${r.lcp} | ${r.tbt} | ${r.cls} | ${r.si} | ${r.url.replace(ORIGIN, '')} |`;
}

async function main() {
  if (!existsSync(LH_CLI)) {
    console.error(`Không thấy lighthouse tại ${LH_CLI} — chạy: npm i -D lighthouse`);
    process.exit(1);
  }
  await mkdir(OUT_DIR, { recursive: true });
  const urls = await collectUrls();
  const modes = DESKTOP ? ['mobile', 'desktop'] : ['mobile'];
  console.log(`Sweep ${urls.length} URL × ${modes.join('+')} — tuần tự để số đo chuẩn...`);

  const results = [];
  let i = 0;
  for (const url of urls) {
    for (const mode of modes) {
      i++;
      process.stdout.write(`[${i}/${urls.length * modes.length}] ${mode} ${url} ... `);
      try {
        const r = await runLighthouse(url, mode);
        results.push({ url, mode, ...r });
        console.log(`${r.score} (LCP ${r.lcp}ms, TBT ${r.tbt}ms, CLS ${r.cls})`);
      } catch (e) {
        results.push({ url, mode, score: -1, lcp: -1, tbt: -1, cls: -1, si: -1, error: e.message.slice(0, 120) });
        console.log(`LỖI: ${e.message.slice(0, 120)}`);
      }
    }
  }

  const stamp = new Date().toISOString().slice(0, 16).replace(/[T:]/g, '-');
  const sorted = [...results].sort((a, b) => a.score - b.score);
  const below = sorted.filter((r) => r.score >= 0 && r.score < 100);
  const md = [
    `# Scoreboard — ${LABEL} (${stamp})`,
    '',
    `Origin: ${ORIGIN} · ${urls.length} URL · mode: ${modes.join('+')}`,
    `**${results.filter((r) => r.score === 100).length}/${results.length} lượt đạt 100** · ${below.length} lượt dưới 100 · ${results.filter((r) => r.score < 0).length} lỗi`,
    '',
    '| Điểm | Mode | LCP ms | TBT ms | CLS | SI ms | URL |',
    '|---|---|---|---|---|---|---|',
    ...sorted.map(fmtRow),
    '',
  ].join('\n');

  await writeFile(path.join(OUT_DIR, `scoreboard-${LABEL}.md`), md, 'utf8');
  await writeFile(path.join(OUT_DIR, `scoreboard-${LABEL}.json`), JSON.stringify(results, null, 2), 'utf8');
  console.log(`\nXong. → docs/perf-baselines/scoreboard-${LABEL}.md`);
  if (below.length) {
    console.log(`Các trang DƯỚI 100:`);
    below.forEach((r) => console.log(`  ${r.score} ${r.mode} ${r.url}`));
  }
}

main().catch((e) => { console.error(e); process.exit(1); });
