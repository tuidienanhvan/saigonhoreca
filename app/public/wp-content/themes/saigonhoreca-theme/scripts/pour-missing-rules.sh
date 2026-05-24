#!/usr/bin/env bash
# Pour pp-gallery, pp-cta, pp-related rules vào every project CSS.
# Đây là 3 component dùng trong EVERY pillar (via _cta.php + _related.php
# shared partials + 'with_gallery' kind renderer), nên phải có ở mọi
# project CSS sau khi xóa shared files at parent level.

set -e
cd "$(dirname "$0")/.."

cat > /tmp/pp-missing.css <<'EOF'

/* ── Gallery (multi-image grid) ───────────────────────────── */
.pp-gallery {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}
@media (min-width: 640px) {
  .pp-gallery--cols-2 { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 768px) {
  .pp-gallery { grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
  .pp-gallery--cols-3 { grid-template-columns: repeat(3, 1fr); }
  .pp-gallery--cols-4 { grid-template-columns: repeat(4, 1fr); }
}
.pp-gallery__item {
  position: relative;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  border-radius: var(--radius-sm);
  background: var(--b2);
}
.pp-gallery__item img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--duration-normal) var(--ease-spring);
}
.pp-gallery__item:hover img { transform: scale(1.05); }

/* ── CTA trio (phone/address/email) ───────────────────────── */
.pp-cta {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}
@media (min-width: 768px) {
  .pp-cta { grid-template-columns: repeat(3, 1fr); gap: 2rem; }
}
.pp-cta__card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2.5rem 1.75rem;
  background: var(--b2);
  border: 1px solid var(--bd);
  border-radius: var(--radius-md);
  text-align: center;
  min-height: 280px;
  transition: border-color var(--duration-normal) var(--ease-spring);
}
.pp-cta__card:hover {
  border-color: color-mix(in srgb, var(--gold) 35%, transparent);
}
.pp-cta__icon {
  width: 3.5rem;
  height: 3.5rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-full);
  background: color-mix(in srgb, var(--gold) 12%, transparent);
  border: 1px solid color-mix(in srgb, var(--gold) 30%, transparent);
  color: var(--gold);
}
.pp-cta__icon svg { width: 1.5rem; height: 1.5rem; fill: currentColor; }
.pp-cta__title {
  margin: 0;
  font-family: var(--font-display);
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--bc);
}
.pp-cta__desc {
  margin: 0;
  font-size: 0.875rem;
  line-height: 1.6;
  color: var(--bc2);
}
.pp-cta__btn {
  margin-top: auto;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.625rem 1.5rem;
  font-size: 0.8125rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--pc);
  background: var(--gold);
  border-radius: var(--radius-sm);
  text-decoration: none;
  transition: background 0.2s, transform 0.15s;
}
.pp-cta__btn:hover {
  background: var(--secondary-hover);
  transform: translateY(-1px);
}

/* ── Related projects (3-card grid) ───────────────────────── */
.pp-related {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}
@media (min-width: 768px) {
  .pp-related { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1024px) {
  .pp-related { grid-template-columns: repeat(3, 1fr); gap: 2.5rem; }
}
.pp-related__card {
  position: relative;
  display: block;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  border-radius: var(--radius-md);
  background: var(--b2);
  border: 1px solid var(--bd);
  text-decoration: none;
  transition: border-color var(--duration-normal) var(--ease-spring);
}
.pp-related__card:hover {
  border-color: color-mix(in srgb, var(--bc) 15%, transparent);
}
.pp-related__img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center center;
  transition: transform var(--duration-normal) var(--ease-spring);
}
.pp-related__card:hover .pp-related__img { transform: scale(1.05); }
.pp-related__overlay {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 45%;
  background: linear-gradient(
    to top,
    color-mix(in srgb, var(--b1) 92%, transparent) 0%,
    color-mix(in srgb, var(--b1) 60%, transparent) 50%,
    transparent 100%
  );
  z-index: 2;
  pointer-events: none;
}
.pp-related__content {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 1.5rem 1.75rem;
  z-index: 3;
}
.pp-related__tag {
  margin: 0 0 0.375rem;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--gold);
}
.pp-related__title {
  margin: 0;
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 600;
  line-height: 1.4;
  color: var(--bc);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ── Text block (heading + paragraphs) ─────────────────────── */
.pp-text { max-width: 800px; margin: 0 auto; }
.pp-text--center { text-align: center; }
.pp-text__title {
  margin: 0 0 1rem;
  font-family: var(--font-display);
  font-size: clamp(1.5rem, 3vw, 2.25rem);
  font-weight: 700;
  line-height: 1.3;
  color: var(--bc);
}
.pp-text__divider {
  display: block;
  width: 56px;
  height: 2px;
  margin: 0 0 1.25rem;
  background: var(--gold);
}
.pp-text__divider--center { margin-left: auto; margin-right: auto; }
.pp-text__body p {
  margin: 0 0 1.25rem;
  font-size: 1rem;
  line-height: 1.8;
  color: var(--bc2);
}
.pp-text__body p:last-child { margin-bottom: 0; }
.pp-text__body a {
  color: var(--gold);
  text-decoration: none;
  border-bottom: 1px solid color-mix(in srgb, var(--gold) 40%, transparent);
}
EOF

for proj_css in template-parts/project-pillar/*/[a-z]*.css; do
  base=$(basename "$proj_css" .css)
  # Skip per-section files (with prefix NN-)
  case "$base" in
    [0-9][0-9]-*) continue ;;
  esac
  # Only target <slug>.css (project-level)
  cat /tmp/pp-missing.css >> "$proj_css"
  echo "  + appended $proj_css"
done

rm /tmp/pp-missing.css
echo "done"
