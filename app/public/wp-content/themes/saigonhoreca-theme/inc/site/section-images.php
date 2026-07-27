<?php
/**
 * Section Images Helper
 *
 * Pulls real images from WordPress Media Library for static template sections
 * that would otherwise show placeholder.svg.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * Cached pool of recent attachment IDs.
 *
 * @param int $limit Max number of IDs.
 * @return array<int>
 */
function sh_image_id_pool($limit = 120) {
    static $pool_cache = [];

    $limit = max(1, (int) $limit);
    if (isset($pool_cache[$limit])) {
        return $pool_cache[$limit];
    }

    $ids = get_posts([
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);

    $pool_cache[$limit] = is_array($ids) ? array_values(array_unique(array_map('intval', $ids))) : [];
    return $pool_cache[$limit];
}

/**
 * Track already-used image IDs in current request to reduce duplicates across sections.
 *
 * @param int|null $mark_id Optional ID to mark as used.
 * @param bool $reset Reset registry.
 * @return array<int,bool>
 */
function sh_section_used_images($mark_id = null, $reset = false) {
    static $used = [];

    if ($reset) {
        $used = [];
    }

    if ($mark_id !== null) {
        $mark_id = (int) $mark_id;
        if ($mark_id > 0) {
            $used[$mark_id] = true;
        }
    }

    return $used;
}

/**
 * Score attachment image quality for section usage.
 * Higher score = better candidate.
 *
 * @param int $id Attachment ID.
 * @return int Negative value means unusable.
 */
function sh_section_image_score($id) {
    $id = (int) $id;
    if ($id <= 0) {
        return -1;
    }

    $mime = (string) get_post_mime_type($id);
    if ($mime === '' || strpos($mime, 'image/') !== 0) {
        return -1;
    }

    $meta = wp_get_attachment_metadata($id);
    if (!is_array($meta)) {
        return -1;
    }

    $width = isset($meta['width']) ? (int) $meta['width'] : 0;
    $height = isset($meta['height']) ? (int) $meta['height'] : 0;
    if ($width < 640 || $height < 480) {
        return -1;
    }

    $ratio = $width / max(1, $height);
    if ($ratio < 0.55 || $ratio > 2.4) {
        return -1;
    }

    // Prefer photo-like formats to avoid transparent artwork placeholders.
    $mime_bonus = 0;
    if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
        $mime_bonus = 30;
    } elseif ($mime === 'image/webp') {
        $mime_bonus = 24;
    } elseif ($mime === 'image/png') {
        $mime_bonus = 8;
    } else {
        $mime_bonus = 4;
    }

    $area = $width * $height;
    $area_bonus = min(35, (int) ($area / 350000));
    $ratio_penalty = (int) (abs($ratio - 1.35) * 10);

    return $mime_bonus + $area_bonus - $ratio_penalty;
}

/**
 * Rank and deduplicate candidate IDs by quality score.
 *
 * @param array<int,mixed> $ids Attachment IDs.
 * @return array<int>
 */
function sh_rank_section_image_ids(array $ids) {
    $ranked = [];

    foreach ($ids as $raw_id) {
        $id = (int) $raw_id;
        if ($id <= 0) {
            continue;
        }
        $score = sh_section_image_score($id);
        if ($score < 0) {
            continue;
        }
        if (!isset($ranked[$id]) || $score > $ranked[$id]) {
            $ranked[$id] = $score;
        }
    }

    if (empty($ranked)) {
        return [];
    }

    arsort($ranked, SORT_NUMERIC);
    return array_map('intval', array_keys($ranked));
}

/**
 * Pick one image ID from ranked list, preferring unused IDs in current request.
 *
 * @param array<int> $ranked_ids Ranked IDs.
 * @param int $index Index offset.
 * @return int
 */
function sh_pick_section_image_id(array $ranked_ids, $index = 0) {
    if (empty($ranked_ids)) {
        return 0;
    }

    $used = sh_section_used_images();
    $unused = array_values(array_filter($ranked_ids, function ($id) use ($used) {
        return !isset($used[(int) $id]);
    }));

    $pick_from = !empty($unused) ? $unused : $ranked_ids;
    $pick_index = max(0, (int) $index) % count($pick_from);
    $picked_id = (int) $pick_from[$pick_index];

    if ($picked_id > 0) {
        sh_section_used_images($picked_id);
    }

    return $picked_id;
}

/**
 * Get a real image URL from the media library by searching attachment titles/filenames.
 * Results are cached per page load via static variable.
 *
 * @param string $search  Keyword to search in attachment title/filename.
 * @param string $fallback Fallback image URL (default: placeholder.svg).
 * @param int    $index   Which result to return (0-based) when multiple matches exist.
 * @return string Image URL.
 */
function sh_section_image($search, $fallback = '', $index = 0) {
    static $cache = [];

    // Premium external fallbacks based on common keywords
    $external_fallbacks = [
        'thi cong'  => 'https://images.unsplash.com/photo-1541888946425-d81bb19480c5?q=80&w=1200&auto=format&fit=crop',
        'noi that'  => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?q=80&w=1200&auto=format&fit=crop',
        'kien truc' => 'https://images.unsplash.com/photo-1511818966892-d7d671e672a2?q=80&w=1200&auto=format&fit=crop',
        'biet thu'  => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=1200&auto=format&fit=crop',
        'xay dung'  => 'https://images.unsplash.com/photo-1503387762-592dea58ef23?q=80&w=1200&auto=format&fit=crop',
    ];

    if (empty($fallback)) {
        // Try to match search term to a premium fallback
        foreach ($external_fallbacks as $key => $url) {
            if (stripos($search, $key) !== false) {
                $fallback = $url;
                break;
            }
        }
        if (empty($fallback)) {
            $fallback = get_template_directory_uri() . '/assets/images/placeholder.svg';
        }
    }

    $cache_key = $search;

    if (!isset($cache[$cache_key])) {
        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => 20,
            's'              => $search,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];
        $ids = get_posts($args);
        $cache[$cache_key] = is_array($ids) ? $ids : [];
    }

    $matched_ids = $cache[$cache_key];
    
    // If no keyword match, try picking from the general pool instead of going straight to fallback
    $pool_ids = sh_image_id_pool(140);
    $candidate_ids = array_values(array_unique(array_map('intval', array_merge($matched_ids, $pool_ids))));
    
    if (empty($candidate_ids)) {
        return $fallback;
    }

    $ranked_ids = sh_rank_section_image_ids($candidate_ids);
    
    // If we have keyword matches, prioritize them. 
    // If not, sh_rank_section_image_ids already ranked the pool by quality.
    $picked_id = sh_pick_section_image_id($ranked_ids, $index);

    if ($picked_id > 0) {
        $url = wp_get_attachment_image_url($picked_id, 'large');
        if ($url) {
            return $url;
        }
    }

    return $fallback;
}

/**
 * Get multiple distinct images from the media library.
 *
 * @param string $search   Keyword to search.
 * @param int    $count    Number of images needed.
 * @param string $fallback Fallback URL.
 * @return array Array of image URLs.
 */
function sh_section_images($search, $count = 4, $fallback = '') {
    if (empty($fallback)) {
        $fallback = get_template_directory_uri() . '/assets/images/placeholder.svg';
    }

    $count = max(1, (int) $count);

    $matched_ids = get_posts([
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => max(30, $count * 10),
        's'              => $search,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);

    $pool_ids = sh_image_id_pool(max(140, $count * 30));
    $candidate_ids = array_values(array_unique(array_map('intval', array_merge(
        is_array($matched_ids) ? $matched_ids : [],
        $pool_ids
    ))));

    $ranked_ids = sh_rank_section_image_ids($candidate_ids);
    $used = sh_section_used_images();
    $unused_ranked = array_values(array_filter($ranked_ids, function ($id) use ($used) {
        return !isset($used[(int) $id]);
    }));

    $ordered = array_values(array_unique(array_merge($unused_ranked, $ranked_ids)));
    $selected_ids = array_slice($ordered, 0, $count);

    foreach ($selected_ids as $id) {
        sh_section_used_images((int) $id);
    }

    $urls = [];
    foreach ($selected_ids as $id) {
        $url = wp_get_attachment_image_url($id, 'large');
        if ($url) {
            $urls[] = $url;
        }
    }

    while (count($urls) < $count) {
        $urls[] = $fallback;
    }

    return array_slice($urls, 0, $count);
}

/**
 * Get a random image from the media library.
 * Useful for sections that just need any real construction/architecture image.
 *
 * @param string $fallback Fallback URL.
 * @return string Image URL.
 */
function sh_random_image($fallback = '') {
    static $pointer = 0;

    if (empty($fallback)) {
        $fallback = get_template_directory_uri() . '/assets/images/placeholder.svg';
    }

    $pool = sh_rank_section_image_ids(sh_image_id_pool(160));
    if (empty($pool)) {
        return $fallback;
    }

    // Rotate through image pool for stable non-repeating picks.
    $id = (int) $pool[$pointer % count($pool)];
    $pointer++;

    $url = wp_get_attachment_image_url($id, 'large');
    return $url ? $url : $fallback;
}
