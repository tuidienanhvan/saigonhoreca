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

    /**
     * P2 audit 2026-07-20 — sửa bug: bonus phẳng 0.01/từ ở verify_and_score()
     * không bao giờ đảo được thứ hạng thật (gap giữa các hạng đo được 0.1-0.3,
     * bonus tối đa cũ 4×0.01=0.04 — nhỏ hơn 1 bậc). Đồng thời từ 2 ký tự
     * (MIN_TOKEN_LENGTH) không index được qua FULLTEXT (innodb_ft_min_token_size=3)
     * nên KHÔNG THỂ đưa thẳng vào MATCH AGAINST — sửa đúng phải là: (a) đo độ
     * hiếm thật (IDF) cho mọi từ kể cả từ ngắn, (b) từ ngắn hiếm được thêm làm
     * điều kiện LIKE bắt buộc (không chỉ cộng điểm), (c) trọng số điểm theo
     * IDF × vị trí trường (title ≫ brand/category > content) thay vì phẳng.
     * Câu bug gốc: "máy làm đá tự động" → long=[máy,động] short=[đá,tự].
     * "đá" hiếm (66/381 SP, IDF cao) phải thắng "động" phổ biến (IDF thấp).
     */
    const IDF_RARE_THRESHOLD = 1.0;  // log(N/df) > 1.0 ~ df dưới ~37% tổng số SP
    const IDF_FLOOR = 0.15;          // sàn — từ cực phổ biến vẫn có chút trọng số
    const FIELD_WEIGHT_TITLE = 1.0;
    const FIELD_WEIGHT_BRAND_CATEGORY = 0.6;
    const FIELD_WEIGHT_CONTENT = 0.3;

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

        // Tầng 0 — mã sản phẩm (P2 audit 2026-07-20, Bước 3 của plan). Mã kiểu
        // "FT-126MA-S" là ĐỊNH DANH, không phải cụm từ ngữ nghĩa — tokenizer
        // thường cắt theo mọi ký tự không phải chữ/số nên vỡ thành ft/126ma/s,
        // và hậu tố 1 ký tự "s" bị extract_keyword_list() vứt bỏ hoàn toàn
        // (< 2 ký tự) → không còn phân biệt được "FT-126MA-S" với biến thể
        // "FT-126MA-S-3D"/"FT-126MA-S-ML". Khớp trực tiếp vào title trước khi
        // vào FULLTEXT giải quyết đúng gốc, không phải vá thêm luật cho tokenizer.
        $codes = self::extract_product_codes($question);
        if ($codes) {
            $code_results = self::search_by_product_code($codes, $top_k);
            if ($code_results) return $code_results;
        }

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
     * Tổng số sản phẩm sống trong index — mẫu số cho IDF. Cache 1 request
     * (search() chỉ gọi hàm này gián tiếp 1 lần/câu hỏi qua compute_idf()).
     */
    private static $product_count_cache = null;

    private static function total_product_count() {
        if (self::$product_count_cache !== null) return self::$product_count_cache;
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        self::$product_count_cache = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table} WHERE post_type = 'product'"
        );
        return self::$product_count_cache;
    }

    /**
     * Đo độ hiếm THẬT (document frequency) cho từng từ khoá — cả $long lẫn
     * $short — rồi quy ra IDF. Đây là số liệu dùng chung cho cả việc quyết
     * định từ nào bắt buộc trong SQL lẫn trọng số chấm điểm ở verify_and_score(),
     * để 2 nơi không lệch nhau. Tốn 1 query COUNT/từ khoá (thường 2-5 từ/câu
     * hỏi) — chấp nhận được, đã đo COUNT(*) trên post_type qua chỉ mục dưới 5ms
     * ở quy mô 381-10.000 SP.
     *
     * @return array<string,array{df:int,idf:float}> key = từ đã hạ chữ thường
     */
    private static function compute_idf($words) {
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $total = max(1, self::total_product_count());
        $out = [];
        foreach ($words as $w) {
            $key = mb_strtolower($w);
            if (isset($out[$key])) continue;
            $like = '%' . $wpdb->esc_like($key) . '%';
            // P2 audit 2026-07-20 — bắt được collation mặc định (utf8mb4_unicode_520_ci)
            // so khớp LỎNG dấu tiếng Việt: LIKE '%đá%' khớp NHẦM cả "Bàn đông" (không
            // hề chứa "đá"). Đo thật: df("đá") = 164 qua collation mặc định nhưng chỉ
            // 66-80 sản phẩm thật chứa "đá" (đếm bằng mb_strpos PHP, so khớp chính xác
            // ở verify_and_score()). df bị thổi phồng → IDF tụt sai (0.84, dưới ngưỡng
            // "hiếm" 1.0) → chính từ cần cứu lại KHÔNG được coi là hiếm. Ép COLLATE
            // utf8mb4_bin cho LIKE để đếm DF khớp CHÍNH XÁC dấu, nhất quán với cách
            // verify_and_score() đang so khớp (mb_strpos PHP không bị ảnh hưởng bởi
            // collation MySQL, vốn đã "đúng" — SQL COUNT() phải theo, không phải ngược lại).
            $df = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE post_type = 'product' AND
                 (LOWER(title) LIKE %s COLLATE utf8mb4_bin OR LOWER(brand) LIKE %s COLLATE utf8mb4_bin
                  OR LOWER(category) LIKE %s COLLATE utf8mb4_bin OR LOWER(content_text) LIKE %s COLLATE utf8mb4_bin)",
                $like, $like, $like, $like
            ));
            $df = max(1, $df);
            $out[$key] = ['df' => $df, 'idf' => max(self::IDF_FLOOR, log($total / $df))];
        }
        return $out;
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
                // LOWER(...) COLLATE utf8mb4_bin: khớp CHÍNH XÁC dấu (bin) nhưng vẫn
                // không phân biệt hoa/thường (token đã lowercase, tiêu đề SP thì viết
                // hoa mã model như "HR-", "FT-" — bin thuần không LOWER() sẽ lệch case
                // và mất luôn kết quả đúng, đã bắt lỗi này khi test HR-126MA-S).
                $like_query .= " AND (LOWER(title) LIKE %s COLLATE utf8mb4_bin OR LOWER(brand) LIKE %s COLLATE utf8mb4_bin
                                  OR LOWER(category) LIKE %s COLLATE utf8mb4_bin OR LOWER(content_text) LIKE %s COLLATE utf8mb4_bin)";
                $pattern = '%' . $wpdb->esc_like(mb_strtolower($w)) . '%';
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

        // P2 audit 2026-07-20 — IDF thật cho MỌI từ khoá (kể cả $short), dùng
        // chung cho việc xây SQL lẫn chấm điểm để 2 nơi nhất quán với nhau.
        $idf = self::compute_idf($all_words);

        // Tầng 1 — FULLTEXT trên $long: từ HIẾM (idf cao) mới +bắt buộc, từ phổ
        // biến để tuỳ chọn (P2: nới AND cứng — trước đây MỌI $long đều bắt
        // buộc, câu tự nhiên nhiều từ như "tủ lạnh công nghiệp inox 4 cánh" dễ
        // trả rỗng oan vì không SP nào đủ cả 4 từ).
        // Kèm LIKE bắt buộc cho $short HIẾM — đây là chỗ sửa đúng bug "đá":
        // innodb_ft_min_token_size=3 khiến từ <3 ký tự KHÔNG THỂ đưa vào MATCH
        // AGAINST, nhưng vẫn phải LỌC được kết quả thật, không chỉ cộng điểm
        // 0.01 vô nghĩa như bản cũ.
        $rare_long = array_values(array_filter($long, function ($w) use ($idf) {
            return ($idf[mb_strtolower($w)]['idf'] ?? self::IDF_FLOOR) > self::IDF_RARE_THRESHOLD;
        }));
        $and_terms = array_map(function ($w) use ($rare_long) {
            return (in_array($w, $rare_long, true) ? '+' : '') . $w . '*';
        }, $long);
        $fulltext_query = implode(' ', $and_terms);

        $rare_short = array_values(array_filter($short, function ($w) use ($idf) {
            return ($idf[mb_strtolower($w)]['idf'] ?? self::IDF_FLOOR) > self::IDF_RARE_THRESHOLD;
        }));

        // P2 audit 2026-07-20 (vá regression) — MATCH()...AGAINST() tự nó CHƯA
        // BAO GIỜ được sửa collation (chỉ các mệnh đề LIKE phụ trợ trong
        // compute_idf()/mandatory_like được ép utf8mb4_bin) — FULLTEXT index
        // dùng thẳng collation mặc định của cột (utf8mb4_unicode_520_ci, khớp
        // lỏng dấu) để build token lúc index, không sửa được bằng COLLATE ở
        // phía câu query. Đo thật bắt được: 'cửa' = 'của' COLLATE mặc định → 1
        // (SQL coi bằng nhau!) — "+cửa*" trong AND bắt buộc lặng lẽ khớp NHẦM
        // "của" (từ đệm cực phổ biến, gần như câu tiếng Việt nào cũng có).
        // verify_and_score() cũ chỉ đòi TỔNG có ≥1 từ khớp thật (accent-sensitive
        // qua mb_strpos) — không đòi TỪNG từ + bắt buộc phải khớp thật — nên 1
        // sản phẩm chỉ thật sự chứa "giờ"/"mở" (khớp ngẫu nhiên trong content,
        // không liên quan) vẫn lọt qua nhờ "của" (không phải "cửa") đã mở khoá
        // AND ở tầng SQL. Sửa đúng gốc: sau khi SQL trả candidate, đòi TỪNG từ
        // bắt buộc (rare_long + rare_short) phải khớp CHÍNH XÁC dấu qua PHP —
        // khôi phục đúng lời hứa "AND" mà tầng 1 tuyên bố.
        $rows = self::run_fulltext_query($table, $fulltext_query, $brand, $fetch_k, $rare_short);
        $rows = self::verify_and_score($rows, $long, $short, $idf);
        $product_rows = self::require_literal_all(
            self::dedupe_by_content(self::only_products($rows)),
            array_merge($rare_long, $rare_short)
        );
        if ($product_rows) {
            return self::format_rows(array_slice($product_rows, 0, $top_k), $all_words);
        }

        // Fallback A — bỏ ràng buộc LIKE bắt buộc của $short hiếm (có thể quá
        // chặt với câu mô tả gián tiếp không dùng đúng chữ đó). "đá" vẫn ảnh
        // hưởng điểm qua verify_and_score() dù không còn lọc cứng ở đây. Vẫn
        // giữ require_literal_all cho $rare_long — nới $short, không nới nghĩa AND.
        if ($rare_short) {
            $rows = self::run_fulltext_query($table, $fulltext_query, $brand, $fetch_k, []);
            $rows = self::verify_and_score($rows, $long, $short, $idf);
            $product_rows = self::require_literal_all(
                self::dedupe_by_content(self::only_products($rows)),
                $rare_long
            );
            if ($product_rows) {
                return self::format_rows(array_slice($product_rows, 0, $top_k), $all_words);
            }
        }

        if (count($long) < 2) return [];

        // Tầng 2 — quorum thuần (không dấu +) trên $long, benchmark theo điểm
        // đã cộng IDF thay vì điểm MySQL thô như bản cũ.
        $or_query = implode(' ', array_map(function ($w) { return $w . '*'; }, $long));
        $rows = self::run_fulltext_query($table, $or_query, $brand, $fetch_k);
        if (!$rows) return [];

        $rows = self::verify_and_score($rows, $long, $short, $idf);
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

        // P2 audit 2026-07-20 (vá regression) — Tier 2 (quorum) là tầng RỦI RO
        // NHẤT trong file: không dấu +, chấp nhận khớp từng phần. Ngưỡng tương
        // đối (35% điểm cao nhất) không đủ khi TOÀN BỘ ứng viên đều là khớp
        // ngẫu nhiên trong content_text — không có tín hiệu "sản phẩm thật liên
        // quan" nào để so 35% với. Lọc trước: chỉ giữ sản phẩm có ÍT NHẤT 1 từ
        // khớp ở title/brand/category (has_strong_field_hit, xem verify_and_score()).
        // Nếu không còn sản phẩm nào qua được lọc này → trả rỗng, đúng tinh thần
        // "thà chưa có thông tin còn hơn đưa nhầm nguồn" đã ghi trong docblock
        // của hàm này.
        $products = array_values(array_filter($products, function ($r) {
            return !empty($r->has_strong_field_hit);
        }));
        if (!$products) return [];

        $top_score = max(array_map(function ($r) { return (float) $r->score; }, $products));
        $products = array_values(array_filter($products, function ($r) use ($top_score) {
            return $top_score > 0 && ((float) $r->score) >= $top_score * self::OR_FALLBACK_SCORE_RATIO;
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

    /**
     * P2 audit 2026-07-20 — thay bonus phẳng 0.01/từ (không bao giờ đảo được
     * thứ hạng thật, gap đo được 0.1-0.3 giữa các hạng liền kề) bằng điểm có
     * trọng số 2 chiều: (a) THEO TRƯỜNG — khớp ở title đáng giá hơn brand/
     * category, đáng giá hơn nữa so với content_text (mô tả phụ trợ); (b) THEO
     * ĐỘ HIẾM (IDF) — từ chỉ xuất hiện ở vài SP (như "đá", 66/381) đáng giá
     * hơn nhiều so với từ phổ biến (như "động", xuất hiện trong nhiều SP qua
     * "tự động"). $idf tính sẵn ở search_fulltext() qua compute_idf() — truyền
     * vào để tránh tính lại COUNT() nhiều lần cho cùng 1 câu hỏi; nếu gọi rời
     * (không qua search_fulltext, ví dụ test) thì tự tính.
     */
    private static function verify_and_score($rows, $long, $short, $idf = null) {
        if (!is_array($rows)) return [];
        $all_words = array_merge($long, $short);
        if (!$all_words) return [];
        if ($idf === null) $idf = self::compute_idf($all_words);

        $kept = [];
        foreach ($rows as $r) {
            $title_l = mb_strtolower((string) $r->title);
            $brand_l = mb_strtolower((string) $r->brand);
            $cat_l   = mb_strtolower((string) $r->category);
            $body_l  = mb_strtolower((string) $r->content_text);

            $weighted = 0.0;
            $hits = 0;
            // P2 audit 2026-07-20 (vá regression) — sàn điểm tuyệt đối cũ
            // (OR_FALLBACK_MIN_ABS_SCORE) bị xoá khi đổi sang thang điểm IDF mà
            // không thay bằng gì tương đương — tái mở bug lớp F7: câu hoàn toàn
            // không liên quan sản phẩm ("cho hỏi giờ mở cửa") khớp "giờ"/"mở"
            // thuần tuý NGẪU NHIÊN trong content_text (vd "mỗi giờ", "mở rộng
            // khả năng" — không hề nói về sản phẩm) vẫn đủ điểm qua ngưỡng
            // tương đối 35%. Đo thật: field content-only 2 từ này cộng dồn
            // 2.588 điểm, đủ lọt. Sửa đúng gốc: theo dõi có ít nhất 1 từ khớp
            // ở TITLE/BRAND/CATEGORY hay không — tín hiệu sản phẩm THỰC SỰ
            // liên quan, khác hẳn từ trùng hợp lọt vào giữa content_text dài.
            $has_strong_field_hit = false;
            foreach ($all_words as $w) {
                $wl = mb_strtolower($w);
                if (mb_strpos($title_l, $wl) !== false) {
                    $field_w = self::FIELD_WEIGHT_TITLE;
                    $has_strong_field_hit = true;
                } elseif (mb_strpos($brand_l, $wl) !== false || mb_strpos($cat_l, $wl) !== false) {
                    $field_w = self::FIELD_WEIGHT_BRAND_CATEGORY;
                    $has_strong_field_hit = true;
                } elseif (mb_strpos($body_l, $wl) !== false) {
                    $field_w = self::FIELD_WEIGHT_CONTENT;
                } else {
                    continue;
                }
                $hits++;
                $weighted += $field_w * ($idf[$wl]['idf'] ?? self::IDF_FLOOR);
            }
            if ($hits === 0) continue;

            $r->score = (float) $r->score + $weighted;
            $r->has_strong_field_hit = $has_strong_field_hit;
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
     * Đòi TỪNG từ trong $mandatory_words phải khớp CHÍNH XÁC dấu (mb_strpos,
     * không bị collation MySQL bỏ dấu) ở ĐÂU ĐÓ trong title/brand/category/
     * content — không chỉ "tổng có ≥1 từ khớp" như verify_and_score(). Khôi
     * phục đúng nghĩa "AND" mà +word* trong FULLTEXT tuyên bố nhưng không giữ
     * được do MATCH()...AGAINST() dùng collation mặc định của cột (khớp lỏng
     * dấu — "cửa"="của"). $mandatory_words rỗng → không lọc gì (không có từ
     * nào bắt buộc ở tầng này).
     */
    private static function require_literal_all($rows, $mandatory_words) {
        if (!$mandatory_words) return $rows;
        return array_values(array_filter($rows, function ($r) use ($mandatory_words) {
            return self::all_words_literally_present($r, $mandatory_words);
        }));
    }

    private static function all_words_literally_present($row, $words) {
        $haystack = mb_strtolower(
            (string) $row->title . ' ' . (string) $row->brand . ' ' .
            (string) $row->category . ' ' . (string) $row->content_text
        );
        foreach ($words as $w) {
            if (mb_strpos($haystack, mb_strtolower($w)) === false) return false;
        }
        return true;
    }

    /**
     * 1 lần prepare() duy nhất (không nest prepare() lồng nhau nữa — pattern cũ tuy
     * an toàn trên WP hiện tại vì $brand luôn lấy từ whitelist cột DB, nhưng vẫn là
     * smell dễ gây hiểu lầm khi review — audit 2026-07-18 flag). Điều kiện brand
     * được gộp thẳng vào SQL + mảng args thay vì string nối sẵn.
     *
     * @param string[] $mandatory_like P2: từ ngắn HIẾM không index được qua FULLTEXT
     *   (innodb_ft_min_token_size=3) — thêm làm điều kiện LIKE AND bắt buộc để
     *   chúng thật sự LỌC được kết quả (xem search_fulltext()).
     */
    private static function run_fulltext_query($table, $match_query, $brand, $top_k, $mandatory_like = []) {
        global $wpdb;
        $sql = "SELECT post_id, post_type, title, brand, category, url, content_text,
                       MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE) AS score
                FROM {$table}
                WHERE MATCH(title, brand, category, content_text) AGAINST (%s IN BOOLEAN MODE)";
        $args = [$match_query, $match_query];

        foreach ($mandatory_like as $w) {
            // Cùng lý do COLLATE utf8mb4_bin như compute_idf() — collation mặc định
            // của cột khớp lỏng dấu tiếng Việt (vd "đông" khớp nhầm "%đá%"), lọc
            // "bắt buộc" mà khớp lỏng thì không còn lọc được gì. LOWER(...) trước
            // COLLATE bin để vẫn không phân biệt hoa/thường (mã model viết hoa như
            // "HR-126MA-S" nhưng token đã lowercase — bin thuần không LOWER() làm
            // lệch case và mất kết quả đúng, bắt lỗi này khi test HR-126MA-S).
            $sql .= " AND (LOWER(title) LIKE %s COLLATE utf8mb4_bin OR LOWER(brand) LIKE %s COLLATE utf8mb4_bin
                      OR LOWER(category) LIKE %s COLLATE utf8mb4_bin OR LOWER(content_text) LIKE %s COLLATE utf8mb4_bin)";
            $pattern = '%' . $wpdb->esc_like(mb_strtolower($w)) . '%';
            array_push($args, $pattern, $pattern, $pattern, $pattern);
        }

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
    /**
     * Trích chuỗi dạng mã sản phẩm (chữ + số, có thể có gạch nối) ra khỏi câu hỏi
     * THÔ (chưa lowercase/chuẩn hoá) — "FT-126MA-S", "UX-100", "DCM-60KE", "D650".
     * Yêu cầu bắt đầu bằng 1-4 chữ cái rồi có ít nhất 1 chữ số — loại được từ
     * tiếng Việt/Anh thường (không chứa số) mà không cần whitelist ký tự.
     */
    private static function extract_product_codes($question) {
        if (!preg_match_all('/\b[A-Za-z]{1,4}-?\d[A-Za-z0-9-]{0,14}\b/u', $question, $m)) {
            return [];
        }
        return array_values(array_unique($m[0]));
    }

    /**
     * Khớp trực tiếp từng mã vào cột title (LIKE, không qua FULLTEXT — mã sản
     * phẩm là chuỗi ASCII, không dính bug collation dấu tiếng Việt, nhưng vẫn ép
     * LOWER()+bin để nhất quán và tránh MySQL tự ý coi các ký tự gần giống nhau
     * là bằng nhau). Ranking: khớp NGUYÊN TỪ (có ranh giới ký tự không phải
     * chữ/số hai bên, hoặc đầu/cuối chuỗi) được ưu tiên tuyệt đối so với khớp
     * substring của một mã dài hơn — đây chính là cách phân biệt "RT-126MA-S"
     * (khớp nguyên từ) với "RT-126MA-SSG"/"RT-126MA-S-3D" (chỉ là substring).
     * Trong cùng nhóm ưu tiên, tiêu đề NGẮN HƠN xếp trước — biến thể có hậu tố
     * thêm luôn dài hơn bản gốc.
     */
    private static function search_by_product_code($codes, $top_k) {
        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $candidates = [];
        foreach ($codes as $code) {
            $code_l = mb_strtolower($code);
            $pattern = '%' . $wpdb->esc_like($code_l) . '%';
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT post_id, post_type, title, brand, category, url, content_text, 0.0 AS score
                 FROM {$table}
                 WHERE post_type = 'product' AND LOWER(title) LIKE %s COLLATE utf8mb4_bin
                 LIMIT 50",
                $pattern
            ));
            foreach ($rows as $r) {
                $title_l = mb_strtolower($r->title);
                $is_exact = (bool) preg_match(
                    '/(?<![a-z0-9])' . preg_quote($code_l, '/') . '(?![a-z0-9])/u',
                    $title_l
                );
                $score = ($is_exact ? 1000.0 : 100.0) - min(90.0, mb_strlen($title_l) * 0.1);
                if (!isset($candidates[$r->post_id]) || $candidates[$r->post_id]->score < $score) {
                    $r->score = $score;
                    $candidates[$r->post_id] = $r;
                }
            }
        }
        if (!$candidates) return [];

        $rows = array_values($candidates);
        usort($rows, function ($a, $b) { return (float) $b->score <=> (float) $a->score; });
        $rows = self::dedupe_by_content($rows);
        return self::format_rows(array_slice($rows, 0, $top_k), $codes);
    }

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
