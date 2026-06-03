<?php
/**
 * Template Part: Construction Diary (Cinematic Timeline)
 *
 * @package SaigonHouse
 */
?>
<section class="sh-diary-section sgh-cv-auto" id="nhat-ky-thi-cong">
    <div class="sh-diary-noise" aria-hidden="true"></div>

    <div class="sh-diary-container">
        <div class="sh-diary-header sh-grid-8-4-end" data-aos="bounce-up">
            <div>
                <span class="sh-diary-eyebrow">Thước Phim Kiến Tạo</span>
                <h2 class="sh-diary-title-main">
                    Nhật Ký Thi Công
                    <span>Từ Bản Vẽ Đến Hiện Thực</span>
                </h2>
                <p class="sh-diary-lead">
                    Đồng hành cùng chúng tôi gọt giũa từng khối bê tông trần. Ghi lại chân thực mọi công đoạn phác họa nên không gian sống của bạn rực rỡ qua mỗi ngày.
                </p>
            </div>
            <div class="sh-diary-cta-wrap">
                <a href="/category/thiet-ke/" class="sh-diary-cta">
                    <span>Xem Toàn Bộ Nhật Ký</span>
                    <span class="sh-diary-cta-icon" aria-hidden="true">
                        <?php echo sh_icon('arrow-right', 'sh-diary-cta-arrow'); ?>
                    </span>
                </a>
            </div>
        </div>

        <?php
        $placeholder_image = get_template_directory_uri() . '/assets/images/placeholder.svg';
        $default_videos = [
            [
                'title' => 'Thiết Kế Thi Công Biệt Thự Phố 2 Tầng Bán Cổ Điển Pháp Tại Bình Thạnh',
                'video_url' => 'https://www.youtube.com/embed/fKJ7I4I7bT0',
                'image' => $placeholder_image,
                'date' => wp_date('d/m/Y'),
                'status' => 'Hoàn thiện',
            ],
            [
                'title' => 'Thiết Kế Thi Công Biệt Thự Phong Cách Gỗ',
                'video_url' => 'https://www.youtube.com/embed/Avwi_LHOp1E',
                'image' => $placeholder_image,
                'date' => wp_date('d/m/Y', strtotime('-1 days')),
                'status' => 'Phần thô',
            ],
            [
                'title' => 'Thi Công Công Trình Nhà Xưởng Lớn',
                'video_url' => 'https://www.youtube.com/embed/5qJWr8NrhkQ',
                'image' => $placeholder_image,
                'date' => wp_date('d/m/Y', strtotime('-2 days')),
                'status' => 'Nội thất',
            ],
            [
                'title' => 'Thi Công Dàn Cột',
                'video_url' => 'https://www.youtube.com/embed/wVsA1wPzxxM',
                'image' => $placeholder_image,
                'date' => wp_date('d/m/Y', strtotime('-3 days')),
                'status' => 'Móng cọc',
            ],
            [
                'title' => 'Hoàn Thiện Ngoại Thất Biệt Thự',
                'video_url' => 'https://www.youtube.com/embed/2-9bIAG-th4',
                'image' => $placeholder_image,
                'date' => wp_date('d/m/Y', strtotime('-5 days')),
                'status' => 'Thi công',
            ],
        ];

        $videos = get_transient('sh_home_youtube_videos_live');
        if (false === $videos) {
            $youtube_handle = '@saigonhouse2550';
            $channel_url = "https://www.youtube.com/" . $youtube_handle;
            $response = wp_remote_get($channel_url, ['timeout' => 10]);
            $fetched_videos = [];
            
            if (!is_wp_error($response)) {
                $html = wp_remote_retrieve_body($response);
                if (preg_match('/channel_id=([a-zA-Z0-9_-]+)/', $html, $matches)) {
                    $channel_id = $matches[1];
                    $rss_url = "https://www.youtube.com/feeds/videos.xml?channel_id=" . $channel_id;
                    $rss_response = wp_remote_get($rss_url, ['timeout' => 10]);
                    
                    if (!is_wp_error($rss_response)) {
                        $xml_string = wp_remote_retrieve_body($rss_response);
                        $xml = @simplexml_load_string($xml_string);
                        if ($xml && isset($xml->entry)) {
                            foreach ($xml->entry as $entry) {
                                $namespaces = $entry->getNamespaces(true);
                                $media = isset($namespaces['media']) ? $entry->children($namespaces['media']) : null;
                                
                                $thumbnail = '';
                                if ($media && isset($media->group) && isset($media->group->thumbnail)) {
                                    $thumbnail = (string) $media->group->thumbnail->attributes()['url'];
                                }
                                
                                $vid_link = '';
                                if (isset($entry->link) && isset($entry->link->attributes()['href'])) {
                                    $vid_link = (string) $entry->link->attributes()['href'];
                                }
                                
                                if (!empty($vid_link)) {
                                    $fetched_videos[] = [
                                        'title' => (string) $entry->title,
                                        'video_url' => $vid_link,
                                        'image' => $thumbnail,
                                        'date' => wp_date('d/m/Y', strtotime((string)$entry->published)),
                                        'status' => 'Dự Án Mới',
                                    ];
                                }
                                // T-016: cap to 2 videos — each rail item is ~10 nodes.
                                if (count($fetched_videos) >= 2) break;
                            }
                        }
                    }
                }
            }
            if (empty($fetched_videos)) {
                $videos = get_theme_mod('saigonhouse_video_reviews', $default_videos);
                set_transient('sh_home_youtube_videos_live', $videos, 1 * HOUR_IN_SECONDS); // Cache lỗi ngắn hơn
            } else {
                $videos = $fetched_videos;
                set_transient('sh_home_youtube_videos_live', $videos, 12 * HOUR_IN_SECONDS); // Cache 12h do đỡ gọi nhiều lần
            }
        }

        $extract_youtube_id = static function ($url) {
            $url = trim((string) $url);
            if ($url === '') {
                return '';
            }

            if (preg_match('~(?:youtube\.com/(?:embed/|shorts/|watch\?v=)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $matches)) {
                return (string) $matches[1];
            }

            $parts = wp_parse_url($url);
            if (!is_array($parts) || empty($parts['host'])) {
                return '';
            }

            $host = strtolower((string) $parts['host']);
            if (strpos($host, 'youtube.com') === false) {
                return '';
            }

            if (!empty($parts['query'])) {
                parse_str((string) $parts['query'], $query);
                if (!empty($query['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $query['v'])) {
                    return (string) $query['v'];
                }
            }

            return '';
        };

        $build_embed_url = static function ($url) use ($extract_youtube_id) {
            $id = $extract_youtube_id($url);
            $base = $id !== '' ? 'https://www.youtube.com/embed/' . $id : trim((string) $url);

            if ($base === '') {
                $base = 'https://www.youtube.com/embed/fKJ7I4I7bT0';
            }

            return add_query_arg(
                [
                    'controls' => 1,
                    'modestbranding' => 1,
                    'rel' => 0,
                ],
                $base
            );
        };

        $build_thumb_url = static function ($url, $fallback) use ($extract_youtube_id) {
            $id = $extract_youtube_id($url);
            if ($id !== '') {
                // T-013: serve YouTube webp variant (saves ~12KB per thumb vs jpg).
                // YouTube CDN exposes webp under /vi_webp/<ID>/hqdefault.webp.
                // Browsers without webp support degrade silently to broken image —
                // acceptable since global webp support is >97% (caniuse.com).
                return 'https://i.ytimg.com/vi_webp/' . $id . '/hqdefault.webp';
            }

            return (string) $fallback;
        };

        $normalized_videos = [];
        foreach ($videos as $index => $vid) {
            $video_url_raw = isset($vid['video_url']) ? trim((string) $vid['video_url']) : '';
            if ($video_url_raw === '' && isset($default_videos[$index]['video_url'])) {
                $video_url_raw = (string) $default_videos[$index]['video_url'];
            }
            if ($video_url_raw === '') {
                continue;
            }

            $video_title = isset($vid['title']) ? trim(wp_strip_all_tags((string) $vid['title'])) : '';
            if ($video_title === '') {
                $video_title = 'Công Trình ' . ($index + 1);
            }

            $video_date = isset($vid['date']) ? trim(wp_strip_all_tags((string) $vid['date'])) : '';
            if ($video_date === '') {
                $video_date = 'Cập nhật mới';
            }

            $video_status = isset($vid['status']) ? trim(wp_strip_all_tags((string) $vid['status'])) : '';
            if ($video_status === '') {
                $video_status = 'Đang thi công';
            }

            // T-013: ALWAYS rebuild to webp variant — RSS feed gives jpg URLs
            // which Lighthouse flags as ~12KB waste per thumbnail. webp save 60%+.
            // RSS-provided URL only used as fallback if extract_youtube_id fails.
            $rss_thumb = isset($vid['image']) ? trim((string) $vid['image']) : '';
            $rss_fallback = ($rss_thumb !== '' && strpos($rss_thumb, 'placeholder.svg') === false)
                ? $rss_thumb
                : $placeholder_image;
            $thumb = $build_thumb_url($video_url_raw, $rss_fallback);

            $normalized_videos[] = [
                'title' => $video_title,
                'date' => $video_date,
                'status' => $video_status,
                'thumb' => $thumb,
                'embed_url' => $build_embed_url($video_url_raw),
            ];
        }

        if (empty($normalized_videos)) {
            // T-016: cap to 2 videos to match the live RSS fetch limit
            // and keep `.sh-diary-rail` lightweight (~10 nodes each).
            foreach (array_slice($default_videos, 0, 2) as $vid) {
                $video_url_raw = (string) $vid['video_url'];
                $normalized_videos[] = [
                    'title' => (string) $vid['title'],
                    'date' => isset($vid['date']) ? (string) $vid['date'] : 'Cập nhật mới',
                    'status' => isset($vid['status']) ? (string) $vid['status'] : 'Đang thi công',
                    'thumb' => $build_thumb_url($video_url_raw, $placeholder_image),
                    'embed_url' => $build_embed_url($video_url_raw),
                ];
            }
        }

        // Safety: cap normalized to 2 in case fetched returned more.
        $normalized_videos = array_slice($normalized_videos, 0, 2);

        $main_video = $normalized_videos[0];
        ?>

        <div class="sh-diary-shell">
            <article class="sh-diary-player" data-aos="fade-right">
                <div class="sh-diary-player-head">
                    <div class="sh-diary-live">
                        <span class="sh-diary-live-dot" aria-hidden="true"></span>
                        <span>Trực Tuyến Từ Công Trình</span>
                    </div>
                    <span class="sh-diary-total"><?php echo esc_html(count($normalized_videos)); ?> PHÂN ĐOẠN</span>
                </div>

                <div class="sh-diary-stage" id="sh-video-stage">
                    <div
                        class="sh-diary-bg"
                        id="sh-main-video-bg"
                        style="background-image:url('<?php echo esc_url($main_video['thumb']); ?>');"
                        aria-hidden="true">
                    </div>

                    <!-- Facade Overlay: Click to load YouTube -->
                    <div class="sh-diary-facade" id="sh-video-facade" role="button" tabindex="0" aria-label="Phát video: <?php echo esc_attr($main_video['title']); ?>" title="Click để phát video">
                        <div class="sh-diary-play-huge">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <iframe
                        id="sh-main-video-iframe"
                        src="about:blank"
                        data-src="<?php echo esc_url($main_video['embed_url']); ?>"
                        title="<?php echo esc_attr($main_video['title']); ?>"
                        style="display: none;"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>
                    <noscript>
                        <iframe
                            src="<?php echo esc_url($main_video['embed_url']); ?>"
                            title="<?php echo esc_attr($main_video['title']); ?>"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen>
                        </iframe>
                    </noscript>

                    <div class="sh-diary-overlay">
                        <span class="sh-diary-overlay-date" id="sh-main-video-date"><?php echo esc_html($main_video['date']); ?></span>
                        <h3 id="sh-main-video-title"><?php echo esc_html($main_video['title']); ?></h3>
                        <div class="sh-diary-overlay-meta">
                            <span>Hạng Mục Thi Công</span>
                            <span class="sh-diary-overlay-status" id="sh-main-video-status"><?php echo esc_html($main_video['status']); ?></span>
                        </div>
                    </div>
                </div>
            </article>

            <aside class="sh-diary-side" data-aos="fade-left">
                <div class="sh-diary-side-head">
                    <h4>Tiến Trình Xây Dựng</h4>
                    <p>Chọn một cột mốc để quan sát ngôi nhà đang thành hình qua từng giai đoạn.</p>
                </div>

                <div class="sh-diary-rail" role="listbox" aria-label="Danh sách video thi công">
                    <?php foreach ($normalized_videos as $index => $vid): ?>
                        <?php $is_active = $index === 0; ?>
                        <button
                            type="button"
                            class="sh-diary-item <?php echo $is_active ? 'is-active' : ''; ?>"
                            role="option"
                            aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                            aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
                            aria-label="Chọn video: <?php echo esc_attr($vid['title']); ?>"
                            title="<?php echo esc_attr($vid['title']); ?>"
                            data-url="<?php echo esc_url($vid['embed_url']); ?>"
                            data-thumb="<?php echo esc_url($vid['thumb']); ?>"
                            data-title="<?php echo esc_attr($vid['title']); ?>"
                            data-date="<?php echo esc_attr($vid['date']); ?>"
                            data-status="<?php echo esc_attr($vid['status']); ?>">
                            <span class="sh-diary-index"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>

                            <span class="sh-diary-thumb-wrap">
                                <img
                                    src="<?php echo esc_url($vid['thumb']); ?>"
                                    alt="<?php echo esc_attr($vid['title']); ?>"
                                    loading="lazy"
                                    decoding="async"
                                    sizes="112px">
                                <span class="sh-diary-play-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M8 5v14l11-7z"></path>
                                    </svg>
                                </span>
                            </span>

                            <span class="sh-diary-copy">
                                <span class="sh-diary-date"><?php echo esc_html($vid['date']); ?></span>
                                <span class="sh-diary-item-title"><?php echo esc_html($vid['title']); ?></span>
                                <span class="sh-diary-item-status"><?php echo esc_html($vid['status']); ?></span>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var section = document.getElementById('nhat-ky-thi-cong');
            if (!section) return;

            var player = section.querySelector('#sh-main-video-iframe');
            var facade = section.querySelector('#sh-video-facade');
            var playerBackground = section.querySelector('#sh-main-video-bg');
            var playerTitle = section.querySelector('#sh-main-video-title');
            var playerDate = section.querySelector('#sh-main-video-date');
            var playerStatus = section.querySelector('#sh-main-video-status');
            var playerCard = section.querySelector('.sh-diary-player');
            var items = section.querySelectorAll('.sh-diary-item');

            if (!player || !items.length) return;

            var loadVideo = function (url) {
                if (!player) return;
                
                // Hide facade, show iframe
                if (facade) facade.style.display = 'none';
                player.style.display = 'block';

                // Add autoplay if interaction-based
                var videoUrl = url || player.getAttribute('data-src') || '';
                if (videoUrl && videoUrl.indexOf('autoplay=1') === -1) {
                    videoUrl += (videoUrl.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
                }
                
                player.src = videoUrl;
            };

            if (facade) {
                facade.addEventListener('click', function() { loadVideo(); });
                facade.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        loadVideo();
                    }
                });
            }

            var setActiveItem = function (activeItem) {
                items.forEach(function (item) {
                    item.classList.remove('is-active');
                    item.setAttribute('aria-selected', 'false');
                    item.setAttribute('aria-pressed', 'false');
                });

                activeItem.classList.add('is-active');
                activeItem.setAttribute('aria-selected', 'true');
                activeItem.setAttribute('aria-pressed', 'true');

                var url = activeItem.getAttribute('data-url');
                var thumb = activeItem.getAttribute('data-thumb');
                var title = activeItem.getAttribute('data-title');
                var date = activeItem.getAttribute('data-date');
                var status = activeItem.getAttribute('data-status');

                // If video already loaded, update src immediately. 
                // Otherwise update data-src and facade aria-label.
                if (player.src !== 'about:blank' && player.style.display !== 'none') {
                    if (url) {
                        var autoplayUrl = url + (url.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
                        player.src = autoplayUrl;
                    }
                } else {
                    if (url) player.setAttribute('data-src', url);
                    if (facade && title) facade.setAttribute('aria-label', 'Phát video: ' + title);
                }

                if (thumb && playerBackground) {
                    playerBackground.style.backgroundImage = "url('" + thumb + "')";
                }
                if (title && playerTitle) {
                    playerTitle.textContent = title;
                    player.setAttribute('title', title);
                }
                if (date && playerDate) {
                    playerDate.textContent = date;
                }
                if (status && playerStatus) {
                    playerStatus.textContent = status;
                }

                if (playerCard) {
                    playerCard.classList.add('is-switching');
                    window.setTimeout(function () {
                        playerCard.classList.remove('is-switching');
                    }, 220);
                }
            };

            items.forEach(function (item) {
                item.addEventListener('click', function () {
                    setActiveItem(item);
                });
            });
        });
        </script>
    </div>

    <!-- CSS loaded via enqueue in inc/core/enqueue.php -->
</section>
