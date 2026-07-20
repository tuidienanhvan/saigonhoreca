<?php
/**
 * Retriever — full-text search có trọng số, không embedding/vector DB.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Retriever {

    // Gom hằng số dùng lặp lại nhiều nơi trong file (audit flag: trước đây là số
    // cứng rải rác — 3 xuất hiện ở cả extract_keyword_list lẫn docblock, 400 chỉ
    // ở make_excerpt nhưng đặt tên cho rõ nghĩa, dễ chỉnh 1 chỗ khi cần).
    const MIN_TOKEN_LENGTH = 3; // khớp innodb_ft_min_token_size mặc định của MySQL
    const EXCERPT_LENGTH = 400;
    const OR_FALLBACK_SCORE_RATIO = 0.35; // quorum: giữ kết quả ≥35% điểm cao nhất
    const OR_FALLBACK_MIN_ABS_SCORE = 0.5;

    /**
     * Stopword tiếng Việt phổ biến — MySQL FULLTEXT chỉ có stopword list tiếng Anh
     * mặc định nên "của/và/có/không/về..." không bị lọc, làm lệch điểm về phía các
     * trang dài (project) lặp nhiều từ chức năng thay vì trang thật sự liên quan.
     * Lọc thủ công ở tầng ứng dụng trước khi build câu truy vấn FULLTEXT.
     */
    private static $stopwords = [
        'là', 'và', 'của', 'có', 'không', 'về', 'cho', 'với', 'các', 'những',
        'tôi', 'bạn', 'mình', 'này', 'đó', 'kia', 'để', 'khi', 'trong', 'ngoài',
        'theo', 'đã', 'sẽ', 'đang', 'một', 'hỏi', 'muốn', 'xin', 'cho hỏi',
        'ạ', 'nhé', 'nha', 'à', 'ơi', 'vậy', 'thì', 'mà', 'rằng', 'được',
        'như', 'từ', 'trên', 'dưới', 'nào', 'gì', 'sao', 'làm', 'bị', 'còn',
        'hay', 'hoặc', 'nếu', 'vì', 'nên', 'do', 'bởi', 'lại', 'đây', 'kìa',
        'bên', 'ấy', 'vầy', 'ha', 'nhỉ',
        // Danh từ chung chung xuất hiện gần như trong mọi bản ghi (không phân biệt được
        // đối tượng cụ thể) — lọc riêng khỏi stopword ngữ pháp ở trên. "công"/"nghiệp"
        // thêm sau audit 2026-07-18: "thiết bị bếp công nghiệp" là tagline của site,
        // lặp lại trong hầu hết trang dự án lẫn sản phẩm — ở tầng quorum (OR fallback,
        // xem search_fulltext) 2 từ này một mình đủ khiến trang dự án dài thắng điểm
        // sản phẩm thật (bug thật bắt được khi test lại câu hỏi mẫu trong audit: "tủ
        // lạnh công nghiệp inox 4 cánh" trả về toàn trang dự án, không ra tủ lạnh nào).
        'sản', 'phẩm', 'loại', 'cái', 'chiếc', 'giá', 'bao', 'nhiêu', 'hàng', 'đồ',
        'công', 'nghiệp',
        // Chào hỏi/xã giao — các trang (đặc biệt trang dự án, giới thiệu) hay lặp
        // "xin chào", "cảm ơn quý khách" trong nội dung, nên câu chào đơn thuần dễ
        // khớp trúng những trang không liên quan (bug đã gặp: hỏi "chào" ra nguồn
        // lung tung). Coi như stopword để câu chào thuần không kéo theo nguồn giả.
        'chào', 'chao', 'hi', 'hello', 'alo', 'dạ', 'vâng', 'ok', 'oke', 'okay',
        'cảm', 'ơn', 'cám', 'thanks', 'hey',
        // Đại từ xưng hô + động từ tán gẫu thông thường — bug thật bắt được: "Anh
        // thích em" (câu tán gẫu, không phải hỏi sản phẩm) vẫn còn lại "anh"+"thích"
        // sau lọc, cả 2 đều là từ rất phổ biến trong văn bản marketing/mô tả sản
        // phẩm ("anh/chị", "khách hàng yêu thích"...) nên khớp bừa ra nguồn sản phẩm
        // không liên quan. Coi là stopword — câu tán gẫu thuần sẽ không còn từ khoá
        // nào, rơi về context rỗng, AI trả lời xã giao bình thường không kèm nguồn.
        'anh', 'chị', 'cô', 'chú', 'bác', 'ông', 'bà', 'em', 'tao', 'mày', 'nó', 'ta',
        'thích', 'yêu', 'ghét', 'nhớ', 'thương', 'đẹp', 'xấu', 'giỏi', 'vui', 'buồn', 'cưng',
        'ghê', 'quá', 'lắm', 'trời', 'ối', 'ủa', 'ừ', 'ừm', 'dạo',
        // "người" — bug thật bắt được: "Tôi muốn có người yêu" chỉ còn lại "người"
        // sau lọc, nhưng đây là danh từ CỰC kỳ chung chung, xuất hiện tự nhiên trong
        // rất nhiều mô tả sản phẩm thật ("phù hợp phục vụ đông người", "tiết kiệm
        // nhân công"...). Không phải lỗi project-vs-product (only_products() đã lọc
        // đúng) — sản phẩm THẬT cũng chứa từ này nên vẫn khớp. Phải chặn ở tầng từ
        // vựng vì bản thân từ không mang tín hiệu phân biệt sản phẩm nào cả.
        'người',
    ];

    /**
     * Từ khoá nhận diện ý định "hỏi thông tin liên hệ" — hotline/địa chỉ/email/liên hệ.
     * Trang "Liên Hệ" thật (`/lien-he/`) chỉ có ~10 ký tự trong index (nội dung là
     * form/shortcode, `post_content` gần rỗng) nên KHÔNG BAO GIỜ thắng full-text so
     * với các trang dự án dài lỡ nhắc "liên hệ" nhiều lần trong bài (CTA cuối bài) —
     * bug thật đã gặp: hỏi "liên hệ như nào" ra toàn dự án không liên quan làm nguồn.
     * Nhận diện riêng, trả thẳng dữ liệu liên hệ THẬT (saigonhouse_get_contact_info,
     * theme đã có sẵn) thay vì để full-text tự đoán.
     */
    private static $contact_keywords = ['liên hệ', 'lien he', 'hotline', 'số điện thoại', 'sdt', 'sđt', 'địa chỉ', 'dia chi', 'email'];

    /**
     * Từ khoá nhận diện ý định "hỏi giá". Đo trực tiếp trên dữ liệu thật trước khi
     * thêm 'giá' trần: 0/381 tiêu đề sản phẩm dùng "giá" như danh từ (giá inox/giá
     * kệ/giá đỡ...) — an toàn dùng bare "giá" không sợ cướp mất câu hỏi kệ/giá đỡ
     * thật. Kho hàng có 0/381 sản phẩm chứa giá tiền (VNĐ/VND/mẫu số tiền đều = 0)
     * nên KHÔNG có đường nào trả lời giá đúng bằng full-text — phải trả lời thật
     * thà là giá tuỳ cấu hình, mời gọi hotline, thay vì để full-text đính bừa 6 sản
     * phẩm ngẫu nhiên làm "Nguồn" (bug thật đo được: "Báo giá" → top là "Tủ đông
     * làm lạnh nhanh AT-5 Sammic", không liên quan gì tới câu hỏi).
     */
    private static $price_keywords = [
        'báo giá', 'bao gia', 'bảng giá', 'bang gia', 'giá bao nhiêu', 'gia bao nhieu',
        'bao nhiêu tiền', 'bao nhieu tien', 'giá cả', 'gia ca', 'giá thành', 'gia thanh',
        'giá sỉ', 'gia si', 'giá lẻ', 'gia le', 'chi phí', 'chi phi', 'mấy tiền', 'may tien',
        'chiết khấu', 'chiet khau', 'khuyến mãi', 'khuyen mai', 'giá',
    ];

    /**
     * Từ khoá nhận diện ý định "hỏi dịch vụ" — dịch vụ thật (tư vấn thiết kế, thi
     * công lắp đặt, cung cấp thiết bị, tư vấn vận hành) nằm trong mô tả công ty của
     * theme, KHÔNG nằm trong bảng index: 5/8 bản ghi post_type=page gần như rỗng
     * (3 trang 0 ký tự), không đủ để full-text trả lời.
     */
    private static $service_keywords = ['dịch vụ', 'dich vu', 'lắp đặt', 'lap dat', 'bảo hành', 'bao hanh', 'bảo trì', 'bao tri', 'tư vấn thiết kế', 'tu van thiet ke'];

    /**
     * Từ khoá nhận diện ý định "hỏi mẫu thiết kế / dự án đã thi công" — nội dung
     * này nằm trong 30 bản ghi post_type=project (trung bình 5.234 ký tự — sâu nhất
     * site), nhưng only_products() chặn project khỏi MỌI đường retrieval khác.
     * Khoá ý định là cách MỞ đường riêng cho project mà KHÔNG nới lỏng only_products()
     * ở đường sản phẩm: project chỉ thi đấu với project, không bao giờ trộn vào câu
     * hỏi sản phẩm thường.
     */
    private static $project_keywords = ['mẫu thiết kế', 'mau thiet ke', 'dự án', 'du an', 'công trình', 'cong trinh', 'đã thi công', 'da thi cong', 'mẫu bếp', 'mau bep'];

    /** So khớp câu hỏi (đã hạ chữ thường) với 1 danh sách từ khoá — dùng chung cho
     *  mọi intent (contact/price/service/project) thay vì lặp lại cùng 1 vòng lặp. */
    private static function match_intent($question, $keywords) {
        $q = mb_strtolower(trim((string) $question));
        foreach ($keywords as $kw) {
            if (mb_strpos($q, $kw) !== false) return true;
        }
        return false;
    }

    public static function is_contact_intent($question) {
        return self::match_intent($question, self::$contact_keywords);
    }

    public static function is_price_intent($question) {
        return self::match_intent($question, self::$price_keywords);
    }

    public static function is_service_intent($question) {
        return self::match_intent($question, self::$service_keywords);
    }

    public static function is_project_intent($question) {
        return self::match_intent($question, self::$project_keywords);
    }

    /**
     * Bỏ các cụm từ đã kích hoạt intent (liên hệ/giá/dịch vụ) khỏi câu hỏi trước khi
     * đưa vào retrieval — phần còn lại mới là mô tả sản phẩm thật. Không bóc thì
     * "báo giá" sau lọc stopword chỉ còn "báo" (vì "giá" đã là stopword) → truy vấn
     * +báo* khớp bừa (bug thật đo được). "báo giá tủ đông Hoshizaki" → bóc còn "tủ
     * đông Hoshizaki" → vẫn ra đúng sản phẩm, không mất phần mô tả thật.
     */
    public static function strip_intent_phrases($question) {
        $result = (string) $question;
        $all = array_merge(self::$contact_keywords, self::$price_keywords, self::$service_keywords, self::$project_keywords);
        // Sort dài trước để "báo giá" bị bóc trước "giá" (tránh bóc 1 phần rồi để
        // sót ký tự rác giữa câu).
        usort($all, function ($a, $b) { return mb_strlen($b) - mb_strlen($a); });
        foreach ($all as $kw) {
            $result = str_ireplace($kw, ' ', $result);
        }
        return trim(preg_replace('/\s+/u', ' ', $result));
    }

    /** Context tổng hợp từ dữ liệu liên hệ thật của theme — không qua full-text. */
    public static function contact_context() {
        if (!function_exists('saigonhouse_get_contact_info')) return [];
        $c = saigonhouse_get_contact_info();
        $excerpt = sprintf(
            'Hotline: %s (hoặc %s). Email: %s. Địa chỉ: %s. Website: %s.',
            $c['hotline'] ?? '', $c['hotline_alt'] ?? '', $c['email_primary'] ?? '',
            $c['address'] ?? '', $c['website'] ?? ''
        );
        return [[
            'post_id'  => 0,
            'title'    => 'Thông tin liên hệ Saigon Horeca',
            'brand'    => '',
            'category' => '',
            'url'      => home_url('/lien-he/'),
            'excerpt'  => $excerpt,
            'score'    => 999,
        ]];
    }

    /**
     * Context trung thực cho câu hỏi giá — KHÔNG bịa số, KHÔNG đính sản phẩm ngẫu
     * nhiên làm nguồn (bug cũ). Đây phải NẰM TRONG context (không chỉ trong system
     * prompt) vì SYSTEM_PROMPT bắt model chỉ trả lời dựa trên context — thiếu dữ
     * liệu ở đây thì model sẽ tự sáng tác thay vì nói thật là chưa có giá niêm yết.
     */
    public static function price_context() {
        if (!function_exists('saigonhouse_get_contact_info')) return [];
        $c = saigonhouse_get_contact_info();
        $excerpt = sprintf(
            'Giá thiết bị bếp công nghiệp phụ thuộc cấu hình, kích thước và số lượng đặt hàng nên không niêm yết cố định trên website. '
            . 'Quý khách vui lòng liên hệ hotline %s (hoặc %s) hoặc email %s để được báo giá chính xác theo nhu cầu.',
            $c['hotline'] ?? '', $c['hotline_alt'] ?? '', $c['email_primary'] ?? ''
        );
        return [[
            'post_id'  => 0,
            'title'    => 'Chính sách báo giá Saigon Horeca',
            'brand'    => '',
            'category' => '',
            'url'      => home_url('/lien-he/'),
            'excerpt'  => $excerpt,
            'score'    => 999,
        ]];
    }

    /** Context mô tả dịch vụ thật của công ty — chính xác hơn 8 trang page (5/8 gần rỗng). */
    public static function service_context() {
        if (!function_exists('saigonhouse_get_contact_info')) return [];
        $c = saigonhouse_get_contact_info();
        $desc = $c['description'] ?? '';
        if ($desc === '') return [];
        return [[
            'post_id'  => 0,
            'title'    => 'Dịch vụ Saigon Horeca',
            'brand'    => '',
            'category' => '',
            'url'      => home_url('/gioi-thieu/'),
            'excerpt'  => $desc,
            'score'    => 999,
        ]];
    }

    /**
     * Tìm trong 30 bản ghi post_type=project — CHỈ chạy khi is_project_intent() đã
     * xác nhận khách hỏi đúng về mẫu thiết kế/dự án. Project không bao giờ trộn vào
     * đường sản phẩm (only_products() vẫn nguyên vẹn ở mọi nơi khác) — đây là
     * đường RIÊNG, khoá bằng ý định, không phải nới lỏng luật cũ.
     */
    public static function search_projects($question, $top_k = 4) {
        list($long, $short) = self::extract_keyword_list($question);
        $words = array_merge($long, $short);
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();

        if ($words) {
            $match_query = implode(' ', array_map(function ($w) { return $w . '*'; }, $words));
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT post_id, post_type, title, brand, category, url, content_text,
                        MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE) AS score
                 FROM {$table}
                 WHERE post_type = 'project'
                   AND MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE)
                 ORDER BY score DESC LIMIT %d",
                $match_query, $match_query, $top_k
            ));
            if ($rows) return self::format_rows($rows, $words);
        }

        // Không khớp từ khoá cụ thể nào (câu hỏi kiểu "cho xem mẫu thiết kế" chung
        // chung) — vẫn trả vài dự án thật thay vì rơi vào "không tìm thấy" oan uổng,
        // giống hệt tinh thần search_within_brand().
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, post_type, title, brand, category, url, content_text, 1.0 AS score
             FROM {$table} WHERE post_type = 'project'
             ORDER BY CHAR_LENGTH(content_text) DESC LIMIT %d",
            $top_k
        ));
        return self::format_rows($rows);
    }

    /**
     * Trả top-K bản ghi liên quan nhất tới câu hỏi.
     *
     * Chiến lược "entity-first" (đúc kết sau nghiên cứu RAG cho dữ liệu nhỏ, không
     * vector DB — xem class-entities.php): nếu câu hỏi nhắc TÊN THƯƠNG HIỆU thật đã
     * biết, lọc CỨNG theo cột `brand` trước (WHERE brand = X — chính xác 100%), rồi
     * mới dùng full-text để XẾP HẠNG trong phạm vi đó. Full-text bag-of-words thuần
     * (coi brand ngang hàng mọi từ khác trong 1 điều kiện AND lớn) từng gây sai lệch
     * khi câu hỏi có thêm từ phổ biến — lọc cứng theo brand loại bỏ hẳn rủi ro đó.
     * Không nhận diện brand thì mới rơi về full-text thuần trên toàn bộ index.
     */
    public static function search($question, $top_k = 6) {
        $question = trim(wp_strip_all_tags($question));
        if ($question === '') return [];

        list($long, $short) = self::extract_keyword_list($question);
        if (!$long && !$short) return [];

        // Pi_Chatbot_Entities luôn được require_once trước Retriever ở pi-chatbot.php
        // (thứ tự load cố định) — không cần class_exists() phòng thủ.
        $brands = Pi_Chatbot_Entities::extract_brands($question);
        if ($brands) {
            return self::search_within_brands($brands, $long, $short, $top_k);
        }

        return self::search_fulltext($long, $short, $top_k, '');
    }

    /**
     * Nhận diện NHIỀU brand trong 1 câu hỏi (trước đây chỉ lấy brand đầu tiên khớp,
     * brand thứ 2 trở đi bị bỏ qua hoàn toàn — audit flag: câu so sánh kiểu
     * "Hoshizaki với Giorik cái nào tốt hơn" chỉ lọc cứng theo 1 trong 2 brand).
     * 1 brand: xử lý như cũ. ≥2 brand: chia đều $top_k cho từng brand rồi gộp kết
     * quả — đảm bảo MỖI brand đều có đại diện trong context, tránh brand có điểm
     * full-text cao hơn lấn át hoàn toàn brand còn lại trong câu hỏi so sánh.
     */
    private static function search_within_brands($brands, $long, $short, $top_k) {
        if (count($brands) === 1) {
            return self::search_within_brand($brands[0], $long, $short, $top_k);
        }
        $per_brand = max(2, (int) floor($top_k / count($brands)));
        $results = [];
        foreach ($brands as $brand) {
            $results = array_merge($results, self::search_within_brand($brand, $long, $short, $per_brand));
        }
        return $results;
    }

    /**
     * Tìm trong phạm vi 1 brand đã nhận diện chắc chắn. Thử full-text trên các từ
     * khoá còn lại trước (xếp hạng đúng sản phẩm cụ thể hơn); nếu rỗng (câu hỏi chỉ
     * nhắc brand không kèm mô tả, hoặc mô tả không khớp từ nào) vẫn trả về vài sản
     * phẩm THẬT của brand đó (không lọc từ khoá) — AI luôn có dữ liệu thật để trả
     * lời thay vì rơi vào "không tìm thấy" một cách oan uổng.
     */
    private static function search_within_brand($brand, $long, $short, $top_k) {
        if ($long || $short) {
            $results = self::search_fulltext($long, $short, $top_k, $brand);
            if ($results) return $results;
        }

        // Fallback: full-text không khớp gì trong phạm vi brand (câu hỏi chỉ nhắc
        // brand không kèm mô tả, hoặc mô tả không khớp từ nào). Trước đây thiếu
        // "AND post_type = 'product'" và gọi thẳng format_rows() bỏ qua
        // only_products() — vi phạm nguyên tắc chỉ-sản-phẩm mà mọi đường khác trong
        // file này tuân thủ, để lọt project/page có gắn brand làm nguồn. Thêm điều
        // kiện SQL + vẫn lọc lại bằng only_products() (đai + dây), và truyền $words
        // vào format_rows() để make_excerpt() cắt đúng chỗ khớp thay vì lấy đoạn
        // intro đầu bài.
        //
        // ORDER BY CHAR_LENGTH DESC thay vì post_id DESC: đường này không có tín
        // hiệu relevance nào theo định nghĩa (full-text đã thử và không khớp), nên
        // "sản phẩm có mô tả đầy đủ nhất" là đại diện tốt hơn "sản phẩm tạo gần đây
        // nhất" — đo được 43,6% sản phẩm dưới 200 ký tự chỉ là spec thô.
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, post_type, title, brand, category, url, content_text, 1.0 AS score
             FROM {$table} WHERE brand = %s AND post_type = 'product'
             ORDER BY CHAR_LENGTH(content_text) DESC LIMIT %d",
            $brand, $top_k
        ));
        return self::format_rows(self::only_products($rows), array_merge($long, $short));
    }

    /**
     * Full-text BOOLEAN MODE — 2 tầng, không còn ép AND cho MỌI câu hỏi (bug đã tìm
     * thấy khi audit: câu hỏi tự nhiên nhiều từ như "tủ lạnh công nghiệp inox 4 cánh"
     * tạo ~4 token bắt buộc, không record nào đủ cả 4 thì trả rỗng oan dù có sản phẩm
     * khớp 3/4).
     *
     * Tầng 1 — AND (mỗi từ +bắt buộc): thử trước, LUÔN lọc lại chỉ giữ post_type=
     *   product trong kết quả — không chỉ "kiểm tra có sản phẩm nào không rồi trả
     *   nguyên cả rows" như bản trước (2 bug thật bắt được từ cách làm cũ):
     *     (a) AND khớp cả product LẪN project cùng lúc — bản cũ trả nguyên rows nên
     *         lẫn cả project không liên quan vào context dù có sản phẩm thật đi kèm.
     *     (b) Đường tắt "chỉ còn 1 từ khoá thì bỏ qua kiểm tra sản phẩm" (audit cũ)
     *         hoá ra KHÔNG an toàn: câu tán gẫu "Tôi muốn có người yêu" sau khi lọc
     *         stopword chỉ còn "người" — từ cực phổ biến trong mọi mô tả sản phẩm
     *         ("phù hợp cho nhiều người", "đội ngũ nhân viên"...) — bản cũ nhận bừa
     *         kết quả AND 1-từ này làm nguồn dù chẳng liên quan gì tới sản phẩm.
     *   Sửa: BỎ HẲN đường tắt theo count($words), luôn lọc post_type=product bất kể
     *   còn lại mấy từ khoá — 1 từ khoá phổ biến giờ cũng phải tự chứng minh khớp
     *   được sản phẩm thật mới được coi là kết quả.
     * Tầng 2 — quorum (OR có trọng số theo số từ khớp, không dấu +): chạy khi tầng 1
     *   không có sản phẩm nào VÀ có ≥2 từ khoá (1 từ thì AND=OR, không cần fallback,
     *   đã biết chắc không có sản phẩm nào khớp). Lọc theo ngưỡng điểm tương đối
     *   (≥35% điểm cao nhất) rồi CHỈ GIỮ LẠI sản phẩm thật — không tái diễn bug
     *   "chào"/"bên"/"xe máy điện"/"người" cũ. Nếu quorum cũng không có sản phẩm nào
     *   → trả rỗng, thà "chưa có thông tin" còn hơn đưa nhầm nguồn không liên quan.
     *
     * @param string[] $words Từ khoá đã lọc stopword (chưa gắn +/*).
     * @param string   $brand Nếu khác rỗng, giới hạn thêm WHERE brand = $brand.
     */
    private static function search_fulltext($long, $short, $top_k, $brand = '') {
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $all_words = array_merge($long, $short);
        // Over-fetch: dedupe_by_content() có thể co ngót kết quả (nhóm 8 sản phẩm
        // trùng content_text chỉ giữ 2 đại diện) — lấy dư trước LIMIT thật rồi cắt
        // lại đúng $top_k sau khi gộp, để 1 nhóm trùng không làm hụt số kết quả.
        $fetch_k = $top_k * 3;

        if (empty($long)) {
            // Quét CẢ 4 cột mà verify_and_score() dùng làm haystack (title, brand,
            // category, content_text) — trước đây chỉ lọc content_text, nên token 2
            // ký tự chỉ nằm ở tiêu đề (mã hàng: "hr", "se", "ob") bị SQL loại trước
            // khi PHP kịp xác minh. Nguyên tắc: SQL là bộ lọc RECALL, verify_and_score()
            // là bộ lọc PRECISION — recall hẹp hơn precision là sai chiều.
            $like_query = "";
            $args = [];
            foreach ($short as $w) {
                $like_query .= " AND (title LIKE %s OR brand LIKE %s OR category LIKE %s OR content_text LIKE %s)";
                $pattern = '%' . $wpdb->esc_like($w) . '%';
                array_push($args, $pattern, $pattern, $pattern, $pattern);
            }
            $sql = "SELECT post_id, post_type, title, brand, category, url, content_text, 1.0 AS score
                    FROM {$table} WHERE 1=1 " . $like_query;
            if ($brand !== '') {
                $sql .= " AND brand = %s";
                $args[] = $brand;
            }
            $sql .= " ORDER BY (post_type = 'product') DESC LIMIT %d";
            $args[] = $fetch_k;
            $rows = $wpdb->get_results($wpdb->prepare($sql, $args));
            $rows = self::verify_and_score($rows, $long, $short);
            $product_rows = self::dedupe_by_content(self::only_products($rows));
            if ($product_rows) return self::format_rows(array_slice($product_rows, 0, $top_k), $all_words);
            return [];
        }

        $and_query = implode(' ', array_map(function ($w) { return '+' . $w . '*'; }, $long));
        $rows = self::run_fulltext_query($table, $and_query, $brand, $fetch_k);
        $rows = self::verify_and_score($rows, $long, $short);
        $product_rows = self::dedupe_by_content(self::only_products($rows));
        if ($product_rows) {
            return self::format_rows(array_slice($product_rows, 0, $top_k), $all_words);
        }

        if (count($long) < 2) return [];

        $or_query = implode(' ', array_map(function ($w) { return $w . '*'; }, $long));
        $rows = self::run_fulltext_query($table, $or_query, $brand, $fetch_k);
        if (!$rows) return [];

        $rows = self::verify_and_score($rows, $long, $short);
        if (!$rows) return [];

        // Benchmark điểm CHỈ so trong nhóm product với nhau — không phải toàn bộ
        // $rows (product lẫn project). verify_and_score() resort thuần theo điểm,
        // không còn giữ thứ tự "product trước project" mà run_fulltext_query() cố
        // tình xếp (xem comment ở đó); nếu lấy $top_score từ $rows[0] sau resort,
        // một trang project bị thổi điểm cao do lặp từ khoá có thể đẩy ngưỡng lên,
        // loại oan product hợp lệ điểm thấp hơn — tái diễn đúng bug mà thứ tự SQL
        // cũ được thiết kế để chặn.
        $products = self::only_products($rows);
        if (!$products) return [];

        $top_score = max(array_map(function ($r) { return (float) $r->score; }, $products));
        $products = array_values(array_filter($products, function ($r) use ($top_score) {
            return $top_score > 0
                && ((float) $r->score) >= $top_score * self::OR_FALLBACK_SCORE_RATIO
                && ((float) $r->score) >= self::OR_FALLBACK_MIN_ABS_SCORE;
        }));
        $products = self::dedupe_by_content($products);
        return self::format_rows(array_slice($products, 0, $top_k), $all_words);
    }

    /**
     * Gộp bản ghi có content_text TRÙNG Y HỆT (chuẩn hoá khoảng trắng) — đo được
     * trên dữ liệu thật: 28 sản phẩm / 9 nhóm, nhóm lớn nhất 8 sản phẩm (cùng
     * model, khác cấu hình ngăn kéo/cửa — biến thể THẬT, khác tiêu đề, nhưng
     * content_text giống hệt). Không gộp thì 1 truy vấn trúng nhóm này có thể
     * chiếm trọn top-K bằng 1 thông số duy nhất lặp 8 lần.
     * Giữ tối đa MAX_PER_DUPLICATE_GROUP đại diện điểm cao nhất mỗi nhóm — vẫn
     * cho khách thấy có nhiều lựa chọn, không giả vờ chỉ có 1 sản phẩm.
     */
    const MAX_PER_DUPLICATE_GROUP = 2;

    private static function dedupe_by_content($rows) {
        if (!is_array($rows)) return [];
        $seen = [];
        $kept = [];
        foreach ($rows as $r) {
            $key = md5(trim(preg_replace('/\s+/u', ' ', (string) $r->content_text)));
            $count = $seen[$key] ?? 0;
            if ($count >= self::MAX_PER_DUPLICATE_GROUP) continue;
            $seen[$key] = $count + 1;
            $kept[] = $r;
        }
        return $kept;
    }

    private static function verify_and_score($rows, $long, $short) {
        if (!is_array($rows)) return [];
        $all_words = array_merge($long, $short);
        $kept = [];
        foreach ($rows as $r) {
            $haystack = mb_strtolower(
                $r->title . ' ' . $r->brand . ' ' .
                $r->category . ' ' . $r->content_text
            );
            $literal_hits = 0;
            foreach ($all_words as $w) {
                if (mb_strpos($haystack, mb_strtolower($w)) !== false) {
                    $literal_hits++;
                }
            }
            if ($literal_hits === 0) continue;

            $r->score = (float) $r->score + ($literal_hits * 0.01);
            $kept[] = $r;
        }
        
        usort($kept, function($a, $b) {
            return (float)$b->score <=> (float)$a->score;
        });
        
        return $kept;
    }

    private static function only_products($rows) {
        if (!is_array($rows)) return [];
        return array_values(array_filter($rows, function ($r) { return $r->post_type === 'product'; }));
    }

    /**
     * 1 lần prepare() duy nhất (không nest prepare() lồng nhau nữa — pattern cũ tuy
     * an toàn trên WP hiện tại vì $brand luôn lấy từ whitelist cột DB, nhưng vẫn là
     * smell dễ gây hiểu lầm khi review — audit 2026-07-18 flag). Điều kiện brand
     * được gộp thẳng vào SQL + mảng args thay vì string nối sẵn.
     */
    private static function run_fulltext_query($table, $match_query, $brand, $top_k) {
        global $wpdb;
        $sql = "SELECT post_id, post_type, title, brand, category, url, content_text,
                       MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE) AS score
                FROM {$table}
                WHERE MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE)";
        $args = [$match_query, $match_query];

        if ($brand !== '') {
            $sql .= ' AND brand = %s';
            $args[] = $brand;
        }

        // Ưu tiên post_type='product' trước khi xếp theo score — khách hỏi "có bán X
        // không" gần như luôn muốn sản phẩm, trong khi trang dự án (project) dài, văn
        // xuôi tự do dễ vô tình đạt điểm ngang/cao hơn ở tầng quorum (OR) chỉ vì lặp
        // từ khoá nhiều lần trong bài, không thực sự "về" chủ đề khách hỏi.
        $sql .= " ORDER BY (post_type = 'product') DESC, score DESC LIMIT %d";
        $args[] = $top_k;

        $rows = $wpdb->get_results($wpdb->prepare($sql, $args));
        // get_results() trả null (không phải []) khi query lỗi — log 1 chỗ duy nhất
        // ở boundary này thay vì để mỗi hàm downstream tự warning rời rạc, thứ chỉ
        // che mất nguyên nhân thật (bảng thiếu, FULLTEXT index hỏng...).
        if ($rows === null && $wpdb->last_error) {
            error_log('[pi-chatbot] retrieval query that failed: ' . $wpdb->last_error);
        }
        return $rows;
    }

    private static function format_rows($rows, $words = []) {
        if (!is_array($rows)) return [];
        $results = [];
        foreach ($rows as $row) {
            $results[] = [
                'post_id'  => (int) $row->post_id,
                'title'    => $row->title,
                'brand'    => $row->brand,
                'category' => $row->category,
                'url'      => $row->url,
                'excerpt'  => self::make_excerpt($row->content_text, self::EXCERPT_LENGTH, $words),
                'score'    => isset($row->score) ? (float) $row->score : 0.0,
            ];
        }
        return $results;
    }

    /**
     * Bỏ stopword tiếng Việt, trả DANH SÁCH từ khoá thô (chưa gắn +/* — search_fulltext()
     * tự xây cả 2 phiên bản AND/OR từ list này). Tách từ theo đúng ranh giới mà MySQL
     * FULLTEXT parser mặc định dùng (mọi ký tự không phải chữ/số đều là dấu phân cách —
     * kể cả gạch nối) để mã sản phẩm dạng "FT-126MA-S" khớp đúng token đã index
     * ("ft", "126ma", "s") thay vì bị dính liền thành 1 từ sai.
     *
     * Lọc token < 3 ký tự: MySQL (innodb_ft_min_token_size mặc định = 3) không index
     * token ngắn hơn ngưỡng này — nếu vẫn đưa "ft" (2 ký tự) vào làm điều kiện bắt
     * buộc, token đó sẽ không bao giờ khớp được gì và cả câu AND sẽ luôn trả rỗng dù các từ khoá
     * còn lại (vd "126ma") thực ra khớp đúng sản phẩm.
     */
    private static function extract_keyword_list($question) {
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower($question));
        $words = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        $long = []; $short = [];
        foreach ($words as $word) {
            if (in_array($word, self::$stopwords, true)) continue;
            if (mb_strlen($word) < 2) continue;
            if (mb_strlen($word) < self::MIN_TOKEN_LENGTH) {
                $short[] = $word;
            } else {
                $long[] = $word;
            }
        }
        return [$long, $short];
    }

    /**
     * Trích excerpt QUANH vị trí khớp từ khoá đầu tiên tìm thấy, thay vì luôn lấy
     * $len ký tự đầu content_text (bug đã tìm thấy khi audit: đoạn thực sự khớp câu
     * hỏi — spec, công suất, giá — thường nằm sâu trong body, còn đầu chuỗi thường
     * chỉ là intro/marketing chung chung; LLM nhận nhầm phần vô ích, sản phẩm xếp
     * hạng đúng nhưng trả lời vẫn yếu hoặc "chưa có thông tin"). Không có $words
     * (vd fallback brand không lọc từ khoá) thì giữ hành vi cũ — lấy đầu chuỗi.
     */
    private static function make_excerpt($text, $len, $words = []) {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if (mb_strlen($text) <= $len) return $text;

        $match_pos = null;
        if ($words) {
            $lower = mb_strtolower($text);
            foreach ($words as $w) {
                $pos = mb_strpos($lower, mb_strtolower($w));
                if ($pos !== false && ($match_pos === null || $pos < $match_pos)) {
                    $match_pos = $pos;
                }
            }
        }

        if ($match_pos === null) {
            return mb_substr($text, 0, $len) . '…';
        }

        // Cửa sổ quanh vị trí khớp: 1/4 chiều dài trước, phần còn lại sau — giữ chút
        // ngữ cảnh đứng trước từ khoá thay vì cắt đúng ngay tại từ khoá.
        $before = (int) floor($len * 0.25);
        $start = max(0, $match_pos - $before);
        $snippet = mb_substr($text, $start, $len);
        $prefix = $start > 0 ? '…' : '';
        $suffix = ($start + $len) < mb_strlen($text) ? '…' : '';
        return $prefix . trim($snippet) . $suffix;
    }
}
