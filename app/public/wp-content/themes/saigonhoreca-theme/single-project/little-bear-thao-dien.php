<?php
/**
 * Single Project Template — LITTLE BEAR
 * Thumbnail: little-bear-thao-dien/little-bear-thao-dien-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/little-bear-thao-dien/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--lbear" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/hero');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/intro');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/concept');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/partnership');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/specs');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const images = document.querySelectorAll('.pp--lbear .pp-image-container-shared img');
  
  images.forEach((img, index) => {
    const container = img.closest('.pp-image-container-shared');
    if (!container) return;
    
    // Gán index ngẫu sinh 1, 2, 3 cho các mẫu mây khác nhau
    const type = (index % 3) + 1;
    const clipId = `lb-cloud-clip-unique-${index}`;
    
    // Định nghĩa 3 cặp path morphing (normal & hover) có cùng số điểm neo
    const paths = {
      1: {
        normal: "M 0.25,0.08 C 0.38,0.02, 0.62,0.02, 0.75,0.08 C 0.86,0.04, 0.96,0.14, 0.92,0.28 C 0.98,0.38, 0.98,0.62, 0.92,0.72 C 0.96,0.86, 0.86,0.96, 0.75,0.92 C 0.62,0.98, 0.38,0.98, 0.25,0.92 C 0.14,0.96, 0.04,0.86, 0.08,0.72 C 0.02,0.62, 0.02,0.38, 0.08,0.28 C 0.04,0.14, 0.14,0.04, 0.25,0.08 Z",
        hover: "M 0.25,0.11 C 0.38,0.06, 0.62,0.04, 0.75,0.11 C 0.84,0.08, 0.93,0.17, 0.90,0.30 C 0.95,0.40, 0.96,0.58, 0.90,0.68 C 0.93,0.80, 0.84,0.91, 0.75,0.88 C 0.62,0.94, 0.38,0.93, 0.25,0.88 C 0.16,0.91, 0.07,0.80, 0.10,0.68 C 0.04,0.58, 0.05,0.40, 0.10,0.30 C 0.07,0.17, 0.16,0.08, 0.25,0.11 Z"
      },
      2: {
        normal: "M 0.3,0.06 C 0.45,0.02, 0.65,0.04, 0.78,0.12 C 0.88,0.08, 0.97,0.2, 0.93,0.35 C 0.99,0.48, 0.95,0.68, 0.88,0.78 C 0.91,0.9, 0.78,0.97, 0.65,0.93 C 0.5,0.99, 0.3,0.95, 0.2,0.88 C 0.09,0.93, 0.03,0.8, 0.07,0.66 C 0.01,0.52, 0.04,0.32, 0.12,0.22 C 0.08,0.1, 0.18,0.03, 0.3,0.06 Z",
        hover: "M 0.3,0.10 C 0.45,0.08, 0.65,0.08, 0.78,0.16 C 0.85,0.14, 0.93,0.24, 0.90,0.37 C 0.95,0.48, 0.92,0.64, 0.86,0.72 C 0.88,0.82, 0.78,0.89, 0.65,0.87 C 0.52,0.91, 0.35,0.89, 0.25,0.82 C 0.15,0.87, 0.10,0.76, 0.12,0.66 C 0.07,0.56, 0.09,0.42, 0.16,0.32 C 0.12,0.22, 0.20,0.14, 0.3,0.10 Z"
      },
      3: {
        normal: "M 0.22,0.1 C 0.35,0.04, 0.65,0.04, 0.78,0.1 C 0.88,0.06, 0.95,0.18, 0.94,0.32 C 0.99,0.45, 0.99,0.55, 0.94,0.68 C 0.95,0.82, 0.88,0.94, 0.78,0.9 C 0.65,0.96, 0.35,0.96, 0.22,0.9 C 0.12,0.94, 0.05,0.82, 0.06,0.68 C 0.01,0.55, 0.01,0.45, 0.06,0.32 C 0.05,0.18, 0.12,0.06, 0.22,0.1 Z",
        hover: "M 0.22,0.14 C 0.35,0.12, 0.65,0.12, 0.78,0.14 C 0.85,0.12, 0.91,0.22, 0.90,0.34 C 0.95,0.45, 0.95,0.55, 0.90,0.64 C 0.91,0.76, 0.85,0.86, 0.78,0.84 C 0.65,0.90, 0.35,0.90, 0.22,0.84 C 0.15,0.86, 0.09,0.76, 0.10,0.64 C 0.05,0.55, 0.05,0.45, 0.10,0.34 C 0.09,0.22, 0.15,0.12, 0.22,0.14 Z"
      }
    };
    
    // Tạo phần tử SVG ẩn để định nghĩa clipPath
    const svgNS = "http://www.w3.org/2000/svg";
    const svgDef = document.createElementNS(svgNS, "svg");
    svgDef.setAttribute("width", "0");
    svgDef.setAttribute("height", "0");
    svgDef.style.position = "absolute";
    svgDef.style.width = "0";
    svgDef.style.height = "0";
    svgDef.style.overflow = "hidden";
    svgDef.setAttribute("aria-hidden", "true");
    
    const defs = document.createElementNS(svgNS, "defs");
    const clipPath = document.createElementNS(svgNS, "clipPath");
    clipPath.setAttribute("id", clipId);
    clipPath.setAttribute("clipPathUnits", "objectBoundingBox");
    
    const clipPathEl = document.createElementNS(svgNS, "path");
    clipPathEl.setAttribute("d", paths[type].normal);
    clipPathEl.setAttribute("class", "lb-cloud-clip-path");
    
    clipPath.appendChild(clipPathEl);
    defs.appendChild(clipPath);
    svgDef.appendChild(defs);
    container.appendChild(svgDef);
    
    // Áp dụng clip-path cho thẻ img với độ ưu tiên important để thắng rule trong _caption.css
    img.style.setProperty("clip-path", `url(#${clipId})`, "important");
    
    // Tạo phần tử SVG hiển thị viền vàng mạ vàng (border) đè lên ảnh
    const svgBorder = document.createElementNS(svgNS, "svg");
    svgBorder.setAttribute("viewBox", "0 0 1 1");
    svgBorder.style.position = "absolute";
    svgBorder.style.inset = "0";
    svgBorder.style.width = "100%";
    svgBorder.style.height = "100%";
    svgBorder.style.pointerEvents = "none";
    svgBorder.style.zIndex = "3";
    
    const borderPath = document.createElementNS(svgNS, "path");
    borderPath.setAttribute("d", paths[type].normal);
    borderPath.setAttribute("fill", "none");
    borderPath.setAttribute("stroke-width", "0.008");
    borderPath.setAttribute("class", "lb-cloud-border-path");
    
    svgBorder.appendChild(borderPath);
    container.appendChild(svgBorder);
    
    // Lắng nghe sự kiện hover trên container để thay đổi path động
    container.addEventListener('mouseenter', () => {
      clipPathEl.setAttribute("d", paths[type].hover);
      borderPath.setAttribute("d", paths[type].hover);
    });
    
    container.addEventListener('mouseleave', () => {
      clipPathEl.setAttribute("d", paths[type].normal);
      borderPath.setAttribute("d", paths[type].normal);
    });
  });
});
</script>

<?php get_footer();
