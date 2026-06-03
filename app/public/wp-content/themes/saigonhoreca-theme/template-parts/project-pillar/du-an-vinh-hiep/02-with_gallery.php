<?php
/**
 * Project Pillar â€” du-an-vinh-hiep
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text pp-text--center">
      <div class="pp-text__body">
      <p><?php echo esc_html__('Trong ngÃ nh cÃ  phÃª, cháº¥t lÆ°á»£ng khÃ´ng chá»‰ náº±m á»Ÿ háº¡t cÃ  phÃª â€“ mÃ  cÃ²n á»Ÿ cÃ¡ch ngÆ°á»i ta tÃ´n trá»ng vÃ  tÃ´n vinh giÃ¡ trá»‹ cá»§a nÃ³. Vá»›i VÄ©nh Hiá»‡p, doanh nghiá»‡p xuáº¥t kháº©u cÃ  phÃª nhÃ¢n sá»‘ 1 Viá»‡t Nam niÃªn vá»¥ 2023â€“2024, má»—i khÃ´ng gian trong nhÃ  mÃ¡y khÃ´ng Ä‘Æ¡n thuáº§n lÃ  nÆ¡i váº­n hÃ nh, mÃ  cÃ²n lÃ  má»™t pháº§n cá»§a cÃ¢u chuyá»‡n thÆ°Æ¡ng hiá»‡u khi tiáº¿p Ä‘Ã³n Ä‘á»‘i tÃ¡c toÃ n cáº§u.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Dá»± Ã¡n Coffee Lab â€“ NhÃ  mÃ¡y cháº¿ biáº¿n cÃ  phÃª xuáº¥t kháº©u VÄ©nh Hiá»‡p lÃ  má»™t trong nhá»¯ng dá»± Ã¡n tiÃªu biá»ƒu thá»ƒ hiá»‡n rÃµ triáº¿t lÃ½ Ä‘Ã³. VÃ  Saigon Horeca Ä‘Æ°á»£c lá»±a chá»n khÃ´ng pháº£i Ä‘á»ƒ "láº¯p thiáº¿t bá»‹", mÃ  Ä‘á»ƒ tÆ° váº¥n vÃ  kiáº¿n táº¡o má»™t há»‡ thá»‘ng báº¿p â€“ showroom tÆ°Æ¡ng xá»©ng vá»›i vá»‹ tháº¿ thÆ°Æ¡ng hiá»‡u.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Coffee Lab táº¡i VÄ©nh Hiá»‡p khÃ´ng chá»‰ phá»¥c vá»¥ má»¥c Ä‘Ã­ch ná»™i bá»™. ÄÃ¢y lÃ  nÆ¡i Ä‘Ã³n tiáº¿p cÃ¡c Ä‘oÃ n khÃ¡ch quá»‘c táº¿, Ä‘á»‘i tÃ¡c thÆ°Æ¡ng máº¡i, chuyÃªn gia cÃ  phÃª, Ä‘á»“ng thá»i lÃ  khÃ´ng gian Ä‘á»ƒ trÃ¬nh diá»…n quy trÃ¬nh, gu tháº©m má»¹ vÃ  chuáº©n má»±c váº­n hÃ nh cá»§a má»™t doanh nghiá»‡p xuáº¥t kháº©u hÃ ng Ä‘áº§u.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Äiá»u Ä‘Ã³ Ä‘áº·t ra má»™t yÃªu cáº§u ráº¥t cao: KhÃ´ng gian pháº£i chÃ­nh xÃ¡c vá» cÃ´ng nÄƒng, chuáº©n má»±c vá» ká»¹ thuáº­t, nhÆ°ng Ä‘á»“ng thá»i tinh táº¿ vá» hÃ¬nh áº£nh vÃ  cáº£m xÃºc. Má»i chi tiáº¿t â€“ tá»« báº¿p, khu cháº¿ biáº¿n, Ä‘áº¿n bÃ n cupping â€“ Ä‘á»u pháº£i gÃ³p pháº§n ká»ƒ cÃ¢u chuyá»‡n vá» cháº¥t lÆ°á»£ng vÃ  sá»± chá»‰n chu.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery pp-gallery--cols-4" style="margin-top:2rem;">
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-6.jpg'); ?>" alt="sheh-fung (6)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-8.jpg'); ?>" alt="sheh-fung (8)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-9.jpg'); ?>" alt="sheh-fung (9)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-7.jpg'); ?>" alt="sheh-fung (7)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-1-1.jpg'); ?>" alt="<?php echo esc_attr__('10 lá»—i thÆ°á»ng gáº·p khi sá»­ dá»¥ng tá»§ láº¡nh cÃ´ng nghiá»‡p vÃ  cÃ¡ch kháº¯c phá»¥c', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>

