<?php
/**
 * Home — Đối tác của Saigon Horeca.
 *
 * Danh sách hãng TƯỜNG MINH (tên + file logo) thay cho vòng lặp số 1..12 mù trước
 * đây (cấu trúc cũ không map được tên → brand, và cứ render đủ 12 slot kể cả khi
 * file logo không tồn tại → ô vỡ). Nay mỗi hãng có tên rõ ràng, và CHỈ render hãng
 * nào thật sự có file logo trong uploads — thêm/bớt đối tác = sửa mảng này, không
 * còn phụ thuộc số thứ tự file.
 *
 * Cập nhật 2026-07-18 theo yêu cầu khách: GỠ Hobart, Berjaya, BFC, Fiorenzato,
 * Anfim — THÊM Josper, Precision, Jemi, Kolb, Halton.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$sh_partners = [
    // ── 7 đối tác GIỮ LẠI (logo webp có sẵn trong uploads/saigonhoreca) ──
    ['name' => 'Sammic',        'file' => 'saigonhoreca/SGH-partner00001.jpg'],
    ['name' => 'Fujimak',       'file' => 'saigonhoreca/SGH-partner00002.jpg'],
    ['name' => 'Hoshizaki',     'file' => 'saigonhoreca/SGH-partner00003.jpg'],
    ['name' => 'Winterhalter',  'file' => 'saigonhoreca/SGH-partner00004.jpg'],
    ['name' => 'Mahlkönig',     'file' => 'saigonhoreca/SGH-partner00008.jpg'],
    ['name' => 'Rational',      'file' => 'saigonhoreca/SGH-partner00009.jpg'],
    ['name' => 'Giorik',        'file' => 'saigonhoreca/SGH-partner00011.jpg'],
    // ── 5 đối tác THÊM MỚI ──
    ['name' => 'Josper',        'file' => 'saigonhoreca/partner-josper.png'],
    ['name' => 'Precision Refrigeration', 'file' => 'saigonhoreca/partner-precision.png'],
    ['name' => 'Jemi',          'file' => 'saigonhoreca/partner-jemi.png'],
    ['name' => 'Kolb',          'file' => 'saigonhoreca/partner-kolb.png'],
    ['name' => 'Halton',        'file' => 'saigonhoreca/partner-halton.png'],
];

/**
 * Chỉ render đối tác có file logo THẬT (base hoặc webp) — tránh ô vỡ khi thiếu file.
 * (Vd Jemi/Precision đang chờ file logo màu; sẽ tự hiện ngay khi thả file vào.)
 */
$sh_uploads_dir = function_exists('sgh_uploads_base_dir') ? sgh_uploads_base_dir() : '';
$sh_partner_exists = function ($rel) use ($sh_uploads_dir) {
  if ($sh_uploads_dir === '') return true; // không rõ dir → cứ render (an toàn)
    $base = $sh_uploads_dir . '/' . ltrim($rel, '/');
  if (file_exists($base)) return true;
    $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $base);
  return $webp && file_exists($webp);
};
$sh_partners = array_values(array_filter($sh_partners, function ($p) use ($sh_partner_exists) {
  return $sh_partner_exists($p['file']);
}));
?>
<section class="sh-partners">
  <div class="sh-partners__inner">
  <header class="sh-partners__header">
  <span class="sh-partners__accent" aria-hidden="true"></span>
  <h2 class="sh-partners__title"><?php esc_html_e('Đối tác của Saigon Horeca', 'saigonhoreca'); ?></h2>
  </header>
  <div class="sh-partners__grid">
  <?php foreach ($sh_partners as $sh_p) : ?>
  <div class="sh-partners__cell">
  <img src="<?php echo esc_url(sgh_img($sh_p['file'])); ?>"
  alt="<?php echo esc_attr(sprintf(__('Đối tác %s — Saigon Horeca', 'saigonhoreca'), $sh_p['name'])); ?>"
  width="200" height="200"
  loading="lazy" decoding="async">
  </div>
  <?php endforeach; ?>
  </div>
  </div>
</section>
