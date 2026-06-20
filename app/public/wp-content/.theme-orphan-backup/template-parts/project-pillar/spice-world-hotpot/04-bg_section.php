<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #4: bg_section — full-bleed background image with Parallax Clip-Path.
 * Technique: section clip-path:inset(0) confines a position:fixed __bg = GPU parallax (no JS).
 * Self-contained scoped <style> (unique pp-swh-bgband namespace) so it renders without shared CSS.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-swh-bgband">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed bị clip-path của section giữ lại) -->
  <div class="pp-swh-bgband__bg" style="background-image:url('<?php echo esc_url(sgh_img('spice-world-hotpot/Spice-World-Hot-Pot-02.jpg')); ?>');" aria-hidden="true"></div>
  <div class="pp-swh-bgband__overlay" aria-hidden="true"></div>

  <div class="pp-swh-bgband__content">
    <span class="pp-swh-bgband__divider" aria-hidden="true"></span>
    <h2 class="pp-swh-bgband__title"><?php echo esc_html__('Saigon Horeca tư vấn và cung cấp thiết bị bếp công nghiệp cho nhà hàng Spice World Hot Pot', 'saigonhoreca'); ?></h2>
    <div class="pp-swh-bgband__body">
      <p><?php echo esc_html__('Để phục vụ món ăn chất lượng cao với các thương hiệu nổi tiếng trên thế giới, Spice World Hot Pot Việt Nam đã tin tưởng vào Saigon Horeca như đơn vị thiết kế và thi công toàn bộ trang thiết bị nhà bếp của nhà hàng. Với diện tích nhà bếp lên đến 120 mét vuông, kết hợp với các điều kiện hiện có như thang máy và bố trí công trình, chúng tôi đã tính toán và phân bổ các khu vực chức năng theo tiêu chuẩn của một nhà hàng tiêu chuẩn quốc tế.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>

<style>
/* Spice World Hot Pot — bg_section Parallax Clip-Path (tokens only, scoped). */
.pp-swh-bgband {
  position: relative;
  isolation: isolate;
  min-height: 520px;
  display: grid;
  place-items: center;
  padding: clamp(5rem, 11vh, 8rem) 1.25rem;
  overflow: hidden;
  text-align: center;
  background-color: var(--b1);
}
/* Parallax bg: background-attachment:fixed = parallax dang tin cay (bg gan vao viewport, tu dong clip vao box nay) */
.pp-swh-bgband__bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  z-index: -2;
  pointer-events: none;
}
/* Light token overlay — giu anh sang/ro net, KHONG phai scrim den nang */
.pp-swh-bgband__overlay {
  position: absolute;
  inset: 0;
  z-index: -1;
  pointer-events: none;
  background: linear-gradient(
    180deg,
    color-mix(in srgb, var(--b1) 45%, transparent) 0%,
    color-mix(in srgb, var(--b1) 25%, transparent) 50%,
    color-mix(in srgb, var(--b1) 55%, transparent) 100%
  );
}
.pp-swh-bgband__content {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 880px;
  margin-inline: auto;
}
.pp-swh-bgband__divider {
  display: block;
  width: 72px;
  height: 2px;
  margin: 0 auto 1.5rem;
  background: linear-gradient(90deg, transparent, var(--gold), color-mix(in srgb, var(--er) 80%, var(--gold)), var(--gold), transparent);
  box-shadow: 0 0 14px color-mix(in srgb, var(--gold) 60%, transparent);
}
.pp-swh-bgband__title {
  font-family: var(--font-display);
  font-size: clamp(1.6rem, 3.2vw, 2.4rem);
  font-weight: 800;
  line-height: 1.28;
  color: var(--bc);
  margin: 0 auto;
  max-width: 820px;
  text-shadow: 0 3px 18px rgba(0, 0, 0, 0.55);
}
.pp-swh-bgband__body {
  margin-top: 1.5rem;
}
.pp-swh-bgband__body p {
  font-family: var(--font-sans);
  font-size: clamp(0.95rem, 1.1vw, 1.08rem);
  line-height: 1.85;
  color: color-mix(in srgb, var(--bc) 88%, transparent);
  margin: 0 auto;
  max-width: 760px;
  text-shadow: 0 2px 12px rgba(0, 0, 0, 0.5);
}
/* Mobile fallback: background-attachment:fixed bi giat tren mobile -> chuyen ve scroll */
@media (max-width: 768px) {
  .pp-swh-bgband__bg {
    background-attachment: scroll;
  }
}
</style>
