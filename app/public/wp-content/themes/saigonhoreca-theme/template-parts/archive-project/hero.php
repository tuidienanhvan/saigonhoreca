<?php
/**
 * /du-an/ — Hero featured projects carousel.
 * Hero component for project archive
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$uri  = get_template_directory_uri();
$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

$slides_raw = [
    [
        'title' => 'Sol Kitchen & Bar D7',
        'desc'  => [
            'vi' => 'Tiếp nối sự thành công của nhà hàng Sol quận 1 khi lọt vào Michelin Guide 2023, Saigon Horeca vinh hạnh được chủ đầu tư Sol Kitchen & Bar tin tưởng lựa chọn là đơn vị uy tín triển khai hệ thống bếp cao cấp cho nhà hàng tại quận 7, HCMC.',
            'en' => 'Following Sol D1\'s Michelin Guide 2023 recognition, Saigon Horeca was proudly chosen by Sol Kitchen & Bar to engineer their premier commercial kitchen system in District 7, HCMC.',
            'ja' => 'Sol 1区店のミシュランガイド2023選出に続き、Saigon HorecaはSol Kitchen & Bar 7区店の最高級業務用厨房システムの施工パートナーとして選出されました。',
        ],
        'img'   => 'renovate-sol-kitchen-bar-quan-7/sol-kitchen-bar-q7-toan-canh-bep-bar-inox-hoan-thien.webp',
        'slug'  => 'renovate-sol-kitchen-bar-d7',
    ],
    [
        'title' => 'Little Bear',
        'desc'  => [
            'vi' => 'Little Bear Thảo Điền — không gian ẩm thực ấm cúng đậm chất Âu, được Saigon Horeca thi công trọn gói hệ thống bếp công nghiệp và quầy bar chuyên nghiệp.',
            'en' => 'Little Bear Thao Dien — an intimate European dining experience, featuring a turnkey commercial kitchen and professional bar crafted by Saigon Horeca.',
            'ja' => 'Little Bear（タオディエン）— 居心地の良い欧州ダイニング。Saigon Horecaが業務用厨房設備およびバーを一括施工いたしました。',
        ],
        'img'   => 'little-bear-thao-dien/Little-bear-00.webp',
        'slug'  => 'little-bear-thao-dien',
    ],
    [
        'title' => 'Bambino',
        'desc'  => [
            'vi' => 'Bambino — nhà hàng Ý đạt giải B.A.R Award 2023, được Saigon Horeca tin tưởng triển khai hệ thống bếp cao cấp và quầy bar đẳng cấp Michelin.',
            'en' => 'Bambino — B.A.R Award 2023 winning Italian restaurant, trusted Saigon Horeca for high-end kitchen and Michelin-tier bar installations.',
            'ja' => 'Bambino — B.A.R Award 2023受賞のイタリアンレストラン。Saigon Horecaが最高峰の厨房およびミシュラン規格のバー設備を担当。',
        ],
        'img'   => 'bambino/bambino-saigon-horeca-khong-gian-fine-dining-buoi-toi.webp',
        'slug'  => 'bambino-saigonhoreca',
    ],
    [
        'title' => 'Yuzu Omakase',
        'desc'  => [
            'vi' => 'Yuzu Omakase — trải nghiệm fine-dining Nhật Bản tinh tế, với bếp Teppanyaki và sushi bar được Saigon Horeca thiết kế trọn gói theo tiêu chuẩn quốc tế.',
            'en' => 'Yuzu Omakase — exquisite Japanese fine-dining with custom Teppanyaki stations and sushi bar engineered by Saigon Horeca to international standards.',
            'ja' => 'Yuzu Omakase — 洗練された日本の高級鮨・鉄板焼きダイニング。Saigon Horecaが国際基準に準拠した特注厨房・寿司カウンターを設計施工。',
        ],
        'img'   => 'yuzu-omakase/yuzu-omakase-nghe-nhan-sushi-quay-bieu-dien.webp',
        'slug'  => 'yuzu-omakase',
    ],
    [
        'title' => 'Godmother Friendship',
        'desc'  => [
            'vi' => 'Godmother Friendship — không gian Á-Âu đương đại, hệ thống bếp công nghiệp đầy đủ tiện nghi được Saigon Horeca thi công đúng tiến độ và chuẩn vệ sinh ATTP.',
            'en' => 'Godmother Friendship — contemporary Asian-Western dining, outfitted by Saigon Horeca with a full commercial kitchen meeting strict hygiene and safety standards.',
            'ja' => 'Godmother Friendship — 現代的なアジア・欧州フュージョン空間。食品衛生安全基準を満たす充実した厨房設備をSaigon Horecaが施工。',
        ],
        'img'   => 'godmother-friendship/Godmother-friendship-1.webp',
        'slug'  => 'godmother-friendship',
    ],
    [
        'title' => 'Sol Kitchen & Bar Q1',
        'desc'  => [
            'vi' => 'Nhà hàng Sol Kitchen & Bar quận 1 — vinh dự được lọt Michelin Guide 2023. Saigon Horeca tự hào là đơn vị thiết kế và thi công hệ thống bếp công nghiệp cao cấp.',
            'en' => 'Sol Kitchen & Bar District 1 — honored in the Michelin Guide 2023. Saigon Horeca proudly engineered and fabricated the premium kitchen systems.',
            'ja' => 'Sol Kitchen & Bar 1区店 — ミシュランガイド2023選出。Saigon Horecaがハイエンド業務用厨房システムの設計・施工を手がけました。',
        ],
        'img'   => 'sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-saigon-horeca-khong-gian-quay-bar-bistro.webp',
        'slug'  => 'sol-kitchen-bar-d1',
    ],
];

$slides = [];
foreach ($slides_raw as $item) {
    $item['desc'] = $item['desc'][$lang] ?? ($item['desc']['en'] ?? $item['desc']['vi']);
    $slides[] = $item;
}

foreach ($slides as &$s) {
    $img_url = '';
    
    $proj_post = get_page_by_path($s['slug'], OBJECT, 'project');
    if ($proj_post) {
        if (function_exists('sgh_project_cover_ensure')) {
            $thumb_id = sgh_project_cover_ensure($proj_post->ID);
        } else {
            $thumb_id = get_post_thumbnail_id($proj_post->ID);
        }
        if ($thumb_id) {
            $img_url = wp_get_attachment_image_url($thumb_id, 'large');
        }
    }
    
    // Fallback array hardcoded
    if (!$img_url) {
        $img_url = sgh_img($s['img']);
    }
    $s['final_img'] = $img_url;
}
unset($s);

$detail_label = ($lang === 'ja') ? 'プロジェクト詳細' : (($lang === 'en') ? 'Project Details' : 'Chi Tiết Dự Án');
$hero_aria    = ($lang === 'ja') ? 'Saigon Horecaの注目プロジェクト' : (($lang === 'en') ? 'Featured Projects by Saigon Horeca' : 'Dự án nổi bật Saigon Horeca');
?>
<section class="sh-archive-hero" aria-label="<?php echo esc_attr($hero_aria); ?>">

    <!-- Background slideshow layer (fade qua các ảnh) -->
    <div class="sh-archive-hero__bg-stack" aria-hidden="true">
        <?php foreach ($slides as $i => $s) : ?>
            <div class="sh-archive-hero__bg <?php echo $i === 0 ? 'is-active' : ''; ?>"
                 data-index="<?php echo (int) $i; ?>"
                 style="background-image: url('<?php echo esc_url($s['final_img']); ?>');"></div>
        <?php endforeach; ?>
    </div>

    <!-- Dark overlay gradient -->
    <div class="sh-archive-hero__overlay" aria-hidden="true"></div>

    <!-- Content (text left + thumbnails right) -->
    <div class="sh-archive-hero__inner">

        <!-- LEFT: Text + CTA (rotating sync với slide hiện tại) -->
        <div class="sh-archive-hero__text-stack">
            <?php foreach ($slides as $i => $s) : ?>
                <div class="sh-archive-hero__text <?php echo $i === 0 ? 'is-active' : ''; ?>"
                     data-index="<?php echo (int) $i; ?>">
                    <h1 class="sh-archive-hero__title"><?php echo esc_html($s['title']); ?></h1>
                    <p class="sh-archive-hero__desc"><?php echo esc_html($s['desc']); ?></p>
                    <a href="<?php echo esc_url(sgh_url('projects_index', $s['slug'])); ?>" class="sh-archive-hero__cta">
                        <span><?php echo esc_html($detail_label); ?></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- RIGHT: Project preview cards (clickable navigation) -->
        <div class="sh-archive-hero__cards">
            <?php foreach ($slides as $i => $s) : ?>
                <button class="sh-archive-hero__card <?php echo $i === 0 ? 'is-active' : ''; ?>"
                        data-index="<?php echo (int) $i; ?>"
                        type="button"
                        aria-label="<?php echo esc_attr($s['title']); ?>">
                    <img src="<?php echo esc_url($s['final_img']); ?>"
                         alt=""
                         loading="lazy" decoding="async">
                    <span class="sh-archive-hero__card-name"><?php echo esc_html($s['title']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Dots — group role since each child is a navigation button (not a tab panel). -->
    <div class="sh-archive-hero__dots" role="group" aria-label="Carousel navigation">
        <?php foreach ($slides as $i => $s) : ?>
            <button class="sh-archive-hero__dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                    data-index="<?php echo (int) $i; ?>"
                    type="button"
                    aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
</section>

<script nonce="<?php echo esc_attr($GLOBALS['sgh_csp_nonce'] ?? ''); ?>">
/* Auto-rotate slider /du-an/ hero — pure JS, no deps. */
(function () {
    var sec = document.querySelector('.sh-archive-hero');
    if (!sec) return;
    var bgs   = sec.querySelectorAll('.sh-archive-hero__bg');
    var texts = sec.querySelectorAll('.sh-archive-hero__text');
    var cards = sec.querySelectorAll('.sh-archive-hero__card');
    var dots  = sec.querySelectorAll('.sh-archive-hero__dot');
    var total = bgs.length;
    var current = 0;
    var timer;

    function goTo(i) {
        i = ((i % total) + total) % total;
        [bgs, texts, cards, dots].forEach(function (group) {
            group.forEach(function (el, idx) {
                el.classList.toggle('is-active', idx === i);
            });
        });
        // Scroll active card vào view BÊN TRONG cards container (horizontal),
        // KHÔNG dùng scrollIntoView vì sẽ scroll cả page vertical.
        var cardsWrap = sec.querySelector('.sh-archive-hero__cards');
        if (cardsWrap && cards[i]) {
            var wrapRect = cardsWrap.getBoundingClientRect();
            var cardRect = cards[i].getBoundingClientRect();
            var delta = cardRect.left - wrapRect.left + cardsWrap.scrollLeft;
            // Center the card inside the wrap horizontally.
            var center = delta - (cardsWrap.clientWidth - cards[i].clientWidth) / 2;
            cardsWrap.scrollTo({ left: Math.max(0, center), behavior: 'smooth' });
        }
        current = i;
    }
    function next() { goTo(current + 1); }
    function start() { timer = setInterval(next, 6000); }
    function stop()  { clearInterval(timer); }

    [cards, dots].forEach(function (group) {
        group.forEach(function (el, idx) {
            el.addEventListener('click', function () {
                goTo(idx);
                stop(); start(); // reset auto-rotate
            });
        });
    });

    sec.addEventListener('mouseenter', stop);
    sec.addEventListener('mouseleave', start);
    start();
})();
</script>
