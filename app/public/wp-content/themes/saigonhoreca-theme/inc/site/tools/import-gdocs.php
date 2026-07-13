<?php
/**
 * Import Tin Tức từ Google Docs — wp-admin UI.
 *
 * Dán link 1 Google Doc (đã share "Anyone with the link can view") viết
 * theo template nhãn cố định → tool tự parse thành 1 bài "post" (category
 * Tin tức), tải ảnh nhúng về Media Library, set featured image + SEO/OG
 * meta (dùng đúng field hệ thống SEO có sẵn của theme: _pi_seo_title,
 * _pi_seo_description, _pi_og_image...).
 *
 * TEMPLATE DOC (nhãn đặt tên ĐÚNG cột/meta_key thật trong DB WordPress,
 * mỗi trường 1 dòng, dấu ':' ngăn cách):
 *
 *   post_title: Tiêu đề bài viết
 *   category: tin-tuc                (để trống = mặc định tin-tuc)
 *   post_tag: tag1, tag2, tag3        (để trống = không gắn tag)
 *   post_excerpt: Excerpt 1-2 câu     (để trống = tự lấy đầu bài)
 *   _pi_seo_title:                    (để trống = theme tự sinh SEO title)
 *   _pi_seo_description:              (để trống = theme tự sinh meta description)
 *
 *   NỘI DUNG BÀI VIẾT   ← 1 dòng heading, viết nội dung NGAY BÊN DƯỚI
 *   (toàn bộ nội dung bài tính từ ngay sau dòng heading này đến hết doc.
 *   Chèn ảnh trực tiếp bằng Insert > Image. Ảnh ĐẦU TIÊN gặp trong phần
 *   này tự động thành ảnh đại diện (featured image) + OG image.)
 *
 * Bài luôn tạo dạng DRAFT để duyệt tay trước khi Publish thật.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('SGH_GDocs_Import')) {

    class SGH_GDocs_Import {

        const PAGE_SLUG = 'sgh-import-gdocs';
        const NONCE     = 'sgh_import_gdocs_nonce';

        public static function init() {
            add_action('admin_menu', [__CLASS__, 'register_menu']);
        }

        public static function register_menu() {
            add_posts_page(
                'Import từ Google Docs',
                'Import Google Docs',
                'edit_posts',
                self::PAGE_SLUG,
                [__CLASS__, 'render_page']
            );
        }

        public static function render_page() {
            if (!current_user_can('edit_posts')) {
                wp_die('Bạn không có quyền dùng chức năng này.');
            }

            $result = null;
            if (
                isset($_POST['sgh_gdocs_url'])
                && isset($_POST[self::NONCE])
                && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE])), self::NONCE)
            ) {
                $url    = esc_url_raw(trim(wp_unslash($_POST['sgh_gdocs_url'])));
                $result = self::run_import($url);
            }

            echo '<div class="wrap">';
            echo '<h1>Import Tin Tức từ Google Docs</h1>';

            if ($result) {
                if (!empty($result['error'])) {
                    echo '<div class="notice notice-error"><p><strong>Lỗi:</strong> ' . esc_html($result['error']) . '</p></div>';
                } else {
                    $edit_link = get_edit_post_link($result['post_id'], '');
                    echo '<div class="notice notice-success"><p>';
                    echo 'Đã tạo bài <strong>' . esc_html($result['title']) . '</strong> ở dạng Nháp. ';
                    echo '<a href="' . esc_url($edit_link) . '">Mở bài để duyệt &amp; Publish &rarr;</a>';
                    echo '</p></div>';
                }
            }
            ?>
            <form method="post">
                <?php wp_nonce_field(self::NONCE, self::NONCE); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="sgh_gdocs_url">Link Google Doc</label></th>
                        <td>
                            <input type="url" id="sgh_gdocs_url" name="sgh_gdocs_url" class="regular-text"
                                   placeholder="https://docs.google.com/document/d/.../edit" required
                                   style="min-width:480px;">
                            <p class="description">
                                Doc phải bật chia sẻ <strong>"Anyone with the link" → Viewer</strong>.
                                Bài tạo ra sẽ ở dạng <strong>Nháp</strong> — bạn duyệt lại rồi Publish tay.
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Import bài'); ?>
            </form>

            <h2>Template Doc chuẩn</h2>
            <p class="description">Nhãn đặt tên đúng cột/meta_key thật trong DB WordPress.</p>
            <pre style="background:#fff;border:1px solid #ccd0d4;padding:14px 18px;max-width:720px;overflow:auto;">post_title: Tiêu đề bài viết
category: tin-tuc
post_tag: tag1, tag2, tag3
post_excerpt: Excerpt 1-2 câu (để trống = tự lấy đầu bài)
_pi_seo_title: (để trống = tự sinh)
_pi_seo_description: (để trống = tự sinh)

NỘI DUNG BÀI VIẾT
Toàn bộ nội dung bài viết ngay bên dưới dòng heading trên, đến hết doc.
Chèn ảnh trực tiếp bằng Insert > Image.
Ảnh ĐẦU TIÊN → ảnh đại diện + OG image (theme tự hiện nó ở đầu bài).
Các ảnh SAU giữ nguyên đúng vị trí trong bài.</pre>
            <?php
            echo '</div>';
        }

        /**
         * Fetch + parse + tạo post. Trả về ['post_id'=>, 'title'=>] hoặc ['error'=>].
         */
        public static function run_import($doc_url) {
            $doc_id = self::extract_doc_id($doc_url);
            if (!$doc_id) {
                return ['error' => 'Không nhận ra ID Google Doc từ link này.'];
            }

            $export_url = "https://docs.google.com/document/d/{$doc_id}/export?format=html";
            $resp = wp_remote_get($export_url, [
                'timeout'   => 30,
                'sslverify' => true,
            ]);

            if (is_wp_error($resp)) {
                return ['error' => 'Không tải được doc: ' . $resp->get_error_message()];
            }
            $code = wp_remote_retrieve_response_code($resp);
            if ($code !== 200) {
                return ['error' => "Doc trả về HTTP $code — kiểm tra lại đã Share \"Anyone with the link\" chưa."];
            }

            $html = wp_remote_retrieve_body($resp);
            if (strlen($html) < 50) {
                return ['error' => 'Doc rỗng hoặc không đọc được nội dung.'];
            }

            $parsed = self::parse_doc_html($html);
            if (empty($parsed['title'])) {
                return ['error' => 'Không tìm thấy dòng TIEU_DE: trong doc — kiểm tra lại template.'];
            }
            if ($parsed['content'] === '') {
                return ['error' => 'Không tìm thấy khối ---NOI_DUNG--- ... ---HET--- trong doc.'];
            }

            // 1. Tạo post (draft).
            $postarr = [
                'post_title'   => $parsed['title'],
                'post_content' => wp_slash($parsed['content_html']),
                'post_excerpt' => wp_slash($parsed['excerpt']),
                'post_status'  => 'draft',
                'post_type'    => 'post',
            ];
            $post_id = wp_insert_post($postarr, true);
            if (is_wp_error($post_id)) {
                return ['error' => 'Tạo bài thất bại: ' . $post_id->get_error_message()];
            }

            // 2. Category (mặc định tin-tuc nếu để trống / không tồn tại).
            $cat_slug = $parsed['category'] !== '' ? $parsed['category'] : 'tin-tuc';
            $term = term_exists($cat_slug, 'category');
            if (!$term) {
                $term = term_exists('tin-tuc', 'category');
            }
            if ($term) {
                wp_set_post_terms($post_id, [(int) $term['term_id']], 'category', false);
            }

            // 3. Tags.
            if (!empty($parsed['tags'])) {
                wp_set_post_terms($post_id, $parsed['tags'], 'post_tag', false);
            }

            // 4. Ảnh nhúng: upload hết về Media Library.
            //    - Ảnh ĐẦU TIÊN → featured image (theme single.php đã render nó
            //      ngay đầu bài qua `.sh-single__featured-img`, nên gỡ khỏi thân
            //      bài để không hiện 2 lần liên tiếp).
            //    - Ảnh CÒN LẠI → thẻ <img> responsive thật (kèm srcset + alt)
            //      chèn đúng vị trí placeholder trong bài.
            $content_final = get_post_field('post_content', $post_id);
            $first_attachment_id = 0;
            foreach ($parsed['images'] as $idx => $img) {
                $att_id = self::sideload_base64_image($img['data'], $img['ext'], $post_id, $parsed['title'] . '-' . ($idx + 1));
                $ph = '<p>' . $img['placeholder'] . '</p>';

                if (!$att_id) {
                    // Upload lỗi → gỡ placeholder, đừng để chuỗi [[SGH_IMG_n]] lộ ra bài.
                    $content_final = str_replace($ph, '', $content_final);
                    continue;
                }

                update_post_meta($att_id, '_wp_attachment_image_alt', $parsed['title']);

                if ($idx === 0) {
                    $first_attachment_id = $att_id;
                    $content_final = str_replace($ph, '', $content_final);
                    continue;
                }

                $img_html = wp_get_attachment_image($att_id, 'large', false, [
                    'class'    => 'sgh-imported-img',
                    'loading'  => 'lazy',
                    'decoding' => 'async',
                ]);
                $content_final = str_replace(
                    $ph,
                    $img_html ? '<figure class="sgh-imported-figure">' . $img_html . '</figure>' : '',
                    $content_final
                );
            }
            // Quét sót: placeholder nào còn lại vì lý do bất thường cũng gỡ sạch.
            $content_final = preg_replace('/<p>\[\[SGH_IMG_\d+\]\]<\/p>/', '', $content_final);
            wp_update_post(['ID' => $post_id, 'post_content' => wp_slash($content_final)]);

            if ($first_attachment_id) {
                set_post_thumbnail($post_id, $first_attachment_id);
            }

            // 5. SEO/OG meta — chỉ ghi nếu người viết có điền (để trống thì hệ
            //    thống SEO auto-generate (SGH_SEO_Meta_Description) tự lo.
            if ($parsed['seo_title'] !== '') {
                update_post_meta($post_id, '_pi_seo_title', $parsed['seo_title']);
            }
            if ($parsed['seo_description'] !== '') {
                update_post_meta($post_id, '_pi_seo_description', $parsed['seo_description']);
            }
            // _pi_og_image: không set — SGH_SEO_Social tự fallback về featured
            // image (has_post_thumbnail) nếu để trống, đúng ý "ảnh đầu = OG image".

            return ['post_id' => $post_id, 'title' => $parsed['title']];
        }

        /** Bỏ dấu tiếng Việt + lowercase — dùng để nhận diện heading dù gõ hoa/thường/có-không dấu. */
        public static function normalize_vn($s) {
            $s = remove_accents((string) $s);
            return strtolower(trim(preg_replace('/\s+/', ' ', $s)));
        }

        /** Lấy Google Doc ID từ nhiều dạng URL. */
        public static function extract_doc_id($url) {
            if (preg_match('#/document/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
                return $m[1];
            }
            if (preg_match('#^[a-zA-Z0-9_-]{20,}$#', trim($url))) {
                return trim($url);
            }
            return '';
        }

        /**
         * Parse HTML export của Google Docs thành mảng field + content HTML sạch.
         */
        public static function parse_doc_html($html) {
            $out = [
                'title' => '', 'category' => '', 'tags' => [], 'excerpt' => '',
                'seo_title' => '', 'seo_description' => '',
                'content' => '', 'content_html' => '', 'images' => [],
            ];

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8"?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
            libxml_clear_errors();

            $body = $dom->getElementsByTagName('body')->item(0);
            if (!$body) return $out;

            // Duyệt các <p>/<div> con trực tiếp trong <body> theo thứ tự để
            // vừa đọc field nhãn, vừa xác định ranh giới NOI_DUNG..HET.
            $nodes = [];
            foreach ($body->childNodes as $n) {
                $nodes[] = $n;
            }

            $in_content = false;
            $content_html = '';
            $img_counter  = 0;

            // Nhãn field = ĐÚNG tên cột/meta_key thật trong DB WordPress
            // (wp_posts.post_title/post_excerpt, taxonomy category/post_tag,
            // wp_postmeta._pi_seo_title/_pi_seo_description) — không đặt tên
            // hiển thị wp-admin, không mã hoá riêng.
            $labels = [
                'post_title'            => 'title',
                'category'              => 'category',
                'post_tag'              => 'tags_raw',
                'post_excerpt'          => 'excerpt',
                '_pi_seo_title'         => 'seo_title',
                '_pi_seo_description'   => 'seo_description',
            ];
            // Khớp nhãn dài nhất trước (vd "post_excerpt" phải thử trước
            // "post" nếu có nhãn ngắn hơn trùng tiền tố).
            uksort($labels, static fn($a, $b) => strlen($b) <=> strlen($a));
            $tags_raw = '';

            foreach ($nodes as $node) {
                $text = trim(preg_replace('/\s+/u', ' ', $node->textContent ?? ''));

                if (!$in_content) {
                    // Heading "NỘI DUNG BÀI VIẾT" (không phân biệt hoa/thường
                    // hoặc có/không dấu) đánh dấu điểm bắt đầu nội dung —
                    // mọi thứ SAU dòng này đến hết doc đều là nội dung bài.
                    if (self::normalize_vn($text) === 'noi dung bai viet') {
                        $in_content = true;
                        continue;
                    }
                    foreach ($labels as $label => $key) {
                        if (stripos($text, $label . ':') === 0) {
                            $val = trim(substr($text, strlen($label) + 1));
                            if ($key === 'tags_raw') { $tags_raw = $val; } else { $out[$key] = $val; }
                            continue 2;
                        }
                    }
                    continue;
                }

                // Trong khối nội dung: xử lý ảnh base64 + đoạn văn, đến hết doc.
                $content_html .= self::node_to_html($node, $out['images'], $img_counter);
            }

            if ($tags_raw !== '') {
                $out['tags'] = array_filter(array_map('trim', explode(',', $tags_raw)));
            }
            $out['content_html'] = $content_html;
            $out['content']      = wp_strip_all_tags($content_html);

            if ($out['excerpt'] === '' && $out['content'] !== '') {
                $out['excerpt'] = wp_trim_words($out['content'], 30, '…');
            }

            return $out;
        }

        /**
         * Convert 1 DOM node (trong khối NOI_DUNG) sang HTML sạch cho post_content.
         * Ảnh base64 → tách data ra $images[], thay bằng placeholder text để
         * remap sang URL thật SAU KHI đã có $post_id (sideload cần post_id).
         */
        protected static function node_to_html(DOMNode $node, array &$images, int &$img_counter) {
            // Tìm mọi <img data:...> lồng bên trong node này.
            $html = '';
            if ($node->nodeType === XML_ELEMENT_NODE) {
                $imgs = [];
                if (strtolower($node->nodeName) === 'img') {
                    $imgs[] = $node;
                } else {
                    foreach ($node->getElementsByTagName('img') as $im) $imgs[] = $im;
                }

                if ($imgs) {
                    foreach ($imgs as $img) {
                        $src = $img->getAttribute('src');
                        if (preg_match('#^data:image/(png|jpe?g|gif);base64,(.+)$#', $src, $m)) {
                            $ext = ($m[1] === 'jpeg') ? 'jpg' : $m[1];
                            $placeholder = '[[SGH_IMG_' . $img_counter . ']]';
                            $images[] = ['data' => base64_decode($m[2]), 'ext' => $ext, 'placeholder' => $placeholder];
                            $img_counter++;
                            $html .= '<p>' . $placeholder . '</p>';
                        }
                    }
                    return $html;
                }

                $tag  = strtolower($node->nodeName);

                // Bảng (Google Docs table): giữ nguyên cấu trúc hàng/cột thay
                // vì gộp hết text thành 1 đoạn văn lộn xộn. Hàng đầu tiên coi
                // như header (<th>).
                if ($tag === 'table') {
                    $rows_html = '';
                    $row_idx = 0;
                    foreach ($node->getElementsByTagName('tr') as $tr) {
                        $cell_tag = ($row_idx === 0) ? 'th' : 'td';
                        $cells_html = '';
                        foreach ($tr->childNodes as $cell) {
                            if (!in_array(strtolower($cell->nodeName ?? ''), ['td', 'th'], true)) continue;
                            $cell_text = trim(preg_replace('/\s+/u', ' ', $cell->textContent));
                            $cells_html .= "<{$cell_tag}>" . esc_html($cell_text) . "</{$cell_tag}>";
                        }
                        if ($cells_html !== '') $rows_html .= '<tr>' . $cells_html . '</tr>';
                        $row_idx++;
                    }
                    return $rows_html ? '<table class="sgh-imported-table">' . $rows_html . '</table>' : '';
                }

                $text = trim(preg_replace('/\s+/u', ' ', $node->textContent));
                if ($text === '') return '';

                $is_bold = (bool) preg_match('/font-weight:\s*(bold|[7-9]00)/i', $node->ownerDocument->saveHTML($node));

                if (preg_match('/^h[1-6]$/', $tag)) {
                    return '<h3>' . esc_html($text) . '</h3>';
                }
                if (in_array($tag, ['ul', 'ol'], true)) {
                    $items = '';
                    foreach ($node->getElementsByTagName('li') as $li) {
                        $items .= '<li>' . esc_html(trim($li->textContent)) . '</li>';
                    }
                    return $items ? "<{$tag}>{$items}</{$tag}>" : '';
                }

                return $is_bold ? '<p><strong>' . esc_html($text) . '</strong></p>' : '<p>' . esc_html($text) . '</p>';
            }
            return '';
        }

        /** Upload 1 ảnh (bytes) làm attachment gắn vào $post_id. Trả về attachment ID hoặc 0. */
        protected static function sideload_base64_image($bytes, $ext, $post_id, $name_hint) {
            if (!function_exists('wp_handle_sideload')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';
            }
            $filename = sanitize_file_name(sanitize_title($name_hint) . '.' . $ext);
            $upload = wp_upload_bits($filename, null, $bytes);
            if (!empty($upload['error'])) return 0;

            $filetype = wp_check_filetype($upload['file'], null);
            $attachment = [
                'post_mime_type' => $filetype['type'],
                'post_title'     => sanitize_file_name($name_hint),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];
            $att_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
            if (is_wp_error($att_id) || !$att_id) return 0;

            $meta = wp_generate_attachment_metadata($att_id, $upload['file']);
            wp_update_attachment_metadata($att_id, $meta);

            return $att_id;
        }
    }
}

SGH_GDocs_Import::init();
