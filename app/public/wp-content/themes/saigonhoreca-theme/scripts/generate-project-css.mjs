#!/usr/bin/env node
/**
 * generate-project-css.mjs — sinh tự động 2 file "khung" per-project CSS
 * (thay vì viết tay 31×2 = 62 file gần giống hệt nhau):
 *
 *   1. assets/css/imports/_imports-project-<slug>.css
 *   2. assets/css/entrypoints/style-project-<slug>.css
 *
 * Nguồn sự thật duy nhất: thư mục `template-parts/project-pillar/<slug>/`
 * (phải có đủ 6 file hero/intro/concept/partnership/specs/gallery.css)
 * + `single-project/<slug>.css` tồn tại.
 *
 * Chạy trước bước build CSS (build.mjs gọi script này ở đầu). Idempotent —
 * chạy lại không đổi gì nếu cấu trúc thư mục không đổi. Thêm project mới:
 * chỉ cần tạo đủ 7 file nguồn (6 template-parts + 1 single-project), KHÔNG
 * cần tự viết 2 file CSS khung này nữa.
 */
import { readdir, stat, writeFile, readFile, mkdir } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const PILLAR_DIR = path.join(ROOT, 'template-parts', 'project-pillar');
const SINGLE_DIR = path.join(ROOT, 'single-project');
const IMPORTS_DIR = path.join(ROOT, 'assets', 'css', 'imports');
const ENTRYPOINTS_DIR = path.join(ROOT, 'assets', 'css', 'entrypoints');

const SECTIONS = ['hero', 'intro', 'concept', 'partnership', 'specs', 'gallery'];

async function exists(p) {
  try { await stat(p); return true; } catch { return false; }
}

function importsTemplate(slug) {
  const lines = [`/* Project bundle — ${slug} only. */`];
  for (const s of SECTIONS) {
    lines.push(`@import "../../../template-parts/project-pillar/${slug}/${s}.css";`);
  }
  lines.push(`@import "../../../single-project/${slug}.css";`);
  return lines.join('\n') + '\n';
}

function entrypointTemplate(slug) {
  return [
    '@import "../core/_tokens.css";',
    '@import "../imports/_imports-project-base.css";',
    `@import "../imports/_imports-project-${slug}.css";`,
    '@import "../core/_tailwind-project.css";',
    '',
  ].join('\n');
}

/**
 * NEVER overwrites an existing file — some per-project bundles have
 * deliberate customizations (extra imports like `_caption-overrides.css`,
 * or a different import order than the 6-section default) that a naive
 * "regenerate everyone" pass would silently destroy. Only fills in the
 * file for a BRAND NEW project slug that doesn't have one yet.
 */
async function createIfMissing(filePath, content) {
  if (await exists(filePath)) return 'unchanged';
  await writeFile(filePath, content, 'utf8');
  return 'created';
}

export async function generateProjectCss({ log = () => {} } = {}) {
  const entries = await readdir(PILLAR_DIR, { withFileTypes: true });
  const slugs = entries.filter((e) => e.isDirectory()).map((e) => e.name).sort();

  const report = { slugs: [], skipped: [], created: [], unchanged: [] };

  await mkdir(IMPORTS_DIR, { recursive: true });
  await mkdir(ENTRYPOINTS_DIR, { recursive: true });

  for (const slug of slugs) {
    const dir = path.join(PILLAR_DIR, slug);
    const hasAllSections = (
      await Promise.all(SECTIONS.map((s) => exists(path.join(dir, `${s}.css`))))
    ).every(Boolean);
    const hasSingle = await exists(path.join(SINGLE_DIR, `${slug}.css`));

    if (!hasAllSections || !hasSingle) {
      report.skipped.push({ slug, hasAllSections, hasSingle });
      continue;
    }

    report.slugs.push(slug);

    const importsPath = path.join(IMPORTS_DIR, `_imports-project-${slug}.css`);
    const entryPath = path.join(ENTRYPOINTS_DIR, `style-project-${slug}.css`);

    const rImports = await createIfMissing(importsPath, importsTemplate(slug));
    const rEntry = await createIfMissing(entryPath, entrypointTemplate(slug));

    report[rImports].push(`imports/_imports-project-${slug}.css`);
    report[rEntry].push(`entrypoints/style-project-${slug}.css`);
  }

  log(`generate-project-css: ${report.slugs.length} project(s), ` +
      `${report.created.length} created (new projects — never overwrites existing files), ` +
      `${report.unchanged.length} already existed` +
      (report.skipped.length ? `, ${report.skipped.length} skipped (incomplete source files)` : ''));

  if (report.skipped.length) {
    for (const s of report.skipped) log(`  SKIP ${s.slug}: hasAllSections=${s.hasAllSections} hasSingle=${s.hasSingle}`);
  }

  return report;
}

// Allow running standalone: `node scripts/generate-project-css.mjs`
if (import.meta.url === `file://${process.argv[1]}`) {
  generateProjectCss({ log: console.log }).catch((err) => {
    console.error(err);
    process.exit(1);
  });
}
