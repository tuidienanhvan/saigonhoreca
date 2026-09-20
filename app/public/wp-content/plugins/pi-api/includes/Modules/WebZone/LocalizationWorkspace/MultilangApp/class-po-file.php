<?php
if (!defined('ABSPATH')) exit;

/**
 * Đọc và sửa file .po TẠI CHỖ (surgical edit), không ghi lại toàn bộ file.
 *
 * Vì sao không dùng thẳng PO::export_to_file() của WordPress:
 * export_to_file() sinh lại TOÀN BỘ file từ object, nghĩa là sửa 1 msgstr sẽ
 * viết lại cả 1319 entry — mất comment/reference gốc, đổi thứ tự, và tạo ra
 * một diff khổng lồ che mất thay đổi thật. Yêu cầu nghiệm thu là "sửa 1 msgstr
 * -> .po đổi ĐÚNG dòng đó", nên ở đây giữ nguyên mảng dòng gốc và chỉ splice
 * đúng khoảng dòng của msgstr cần đổi. Mọi byte khác giữ nguyên.
 *
 * WordPress PO/MO (wp-includes/pomo/) vẫn được dùng — nhưng cho việc nó làm
 * đúng và không cần sửa: biên dịch .po -> .mo (xem Pi_Multilang_UI_Strings).
 *
 * Parser này CÓ CHỦ ĐÍCH phủ cả msgctxt / msgid_plural / msgstr[N] dù file
 * hiện tại chưa dùng tới. Lý do: chỉ cần một chuỗi _x() hay _n() được thêm vào
 * theme là file .po sẽ có các field đó; một parser chỉ biết msgid/msgstr sẽ
 * âm thầm gộp nhầm hoặc ghi hỏng entry. Xử lý đúng cả lớp đầu vào ngay từ đầu
 * rẻ hơn nhiều so với vá sau khi file đã hỏng.
 */
class Pi_Multilang_PO_File {

    /** @var string */
    private $path;
    /** @var string[] Dòng gốc, KHÔNG kèm ký tự xuống dòng. */
    private $lines = [];
    /** @var string "\n" hoặc "\r\n" — giữ đúng EOL gốc của file. */
    private $eol = "\n";
    /** @var bool File gốc có BOM hay không — giữ nguyên trạng thái, không tự thêm. */
    private $has_bom = false;
    /** @var array[] */
    private $entries = [];
    /** @var bool */
    private $loaded = false;
    /** @var bool Có thay đổi chưa ghi xuống đĩa. */
    private $dirty = false;

    public function __construct($path) {
        $this->path = $path;
    }

    public function get_path() {
        return $this->path;
    }

    public function load() {
        if (!is_readable($this->path)) return false;
        $raw = file_get_contents($this->path);
        if ($raw === false) return false;

        if (substr($raw, 0, 3) === "\xEF\xBB\xBF") {
            $this->has_bom = true;
            $raw = substr($raw, 3);
        }
        $this->eol   = (strpos($raw, "\r\n") !== false) ? "\r\n" : "\n";
        $this->lines = explode($this->eol, $raw);
        $this->parse();
        $this->loaded = true;
        return true;
    }

    /**
     * Tách file thành các entry, ghi lại khoảng dòng của TỪNG msgstr để sau
     * này splice chính xác.
     */
    private function parse() {
        $this->entries = [];
        $cur   = null;
        $field = null; // khoá của field đang đọc dở, để nối dòng continuation

        $flush = function () use (&$cur) {
            if ($cur !== null && $cur['msgid'] !== null) {
                $this->entries[] = $cur;
            }
            $cur = null;
        };

        $count = count($this->lines);
        for ($i = 0; $i < $count; $i++) {
            $line = $this->lines[$i];
            $t    = trim($line);

            if ($t === '') {           // dòng trống = hết entry
                $flush();
                $field = null;
                continue;
            }
            if ($t[0] === '#') {       // comment / reference
                if ($cur !== null && !empty($cur['msgstr'])) {
                    $flush();          // comment sau msgstr => mở đầu entry mới
                }
                $field = null;
                continue;
            }

            // msgid_plural phải đứng TRƯỚC msgid trong alternation, nếu không
            // "msgid_plural" sẽ bị khớp nhầm thành "msgid".
            if (preg_match('/^(msgctxt|msgid_plural|msgid)[ \t]+"(.*)"[ \t]*$/', $t, $m)) {
                $key = $m[1];
                if ($key === 'msgid') {
                    if ($cur !== null && $cur['msgid'] !== null) $flush();
                    if ($cur === null) $cur = self::new_entry();
                    $cur['msgid_line'] = $i;
                } elseif ($cur === null) {
                    $cur = self::new_entry();
                }
                $cur[$key]        = self::decode($m[2]);
                $cur['_raw'][$key] = ['start' => $i, 'end' => $i];
                $field = $key;
                continue;
            }

            if (preg_match('/^msgstr(?:\[(\d+)\])?[ \t]+"(.*)"[ \t]*$/', $t, $m)) {
                if ($cur === null) $cur = self::new_entry();
                $idx = ($m[1] === '' || !isset($m[1])) ? 0 : (int) $m[1];
                $cur['msgstr'][$idx] = [
                    'value'   => self::decode($m[2]),
                    'start'   => $i,
                    'end'     => $i,
                    'indexed' => ($m[1] !== '' && isset($m[1])),
                ];
                $field = 'msgstr:' . $idx;
                continue;
            }

            // Dòng nối tiếp của chuỗi nhiều dòng: "....."
            if ($field !== null && preg_match('/^"(.*)"[ \t]*$/', $t, $m)) {
                if (strpos($field, 'msgstr:') === 0) {
                    $idx = (int) substr($field, 7);
                    $cur['msgstr'][$idx]['value'] .= self::decode($m[1]);
                    $cur['msgstr'][$idx]['end']    = $i;
                } else {
                    $cur[$field]            .= self::decode($m[1]);
                    $cur['_raw'][$field]['end'] = $i;
                }
                continue;
            }

            $field = null;
        }
        $flush();
    }

    private static function new_entry() {
        return [
            'msgctxt'      => null,
            'msgid'        => null,
            'msgid_plural' => null,
            'msgstr'       => [],
            'msgid_line'   => -1,
            '_raw'         => [],
        ];
    }

    /**
     * Entry thật để hiển thị: bỏ header (msgid rỗng) vì nó là metadata,
     * không phải chuỗi giao diện.
     */
    public function entries() {
        $out = [];
        foreach ($this->entries as $e) {
            if ($e['msgid'] === null || $e['msgid'] === '') continue;
            $out[] = [
                'key'          => self::make_key($e['msgid'], $e['msgctxt']),
                'msgctxt'      => $e['msgctxt'],
                'msgid'        => $e['msgid'],
                'msgid_plural' => $e['msgid_plural'],
                'msgstr'       => isset($e['msgstr'][0]) ? $e['msgstr'][0]['value'] : '',
                'plural_count' => count($e['msgstr']),
                'line'         => $e['msgid_line'] + 1,
            ];
        }
        return $out;
    }

    /** Số entry thật (không tính header). */
    public function count_entries() {
        return count($this->entries());
    }

    /**
     * Khoá định danh một entry. Có msgctxt thì msgid không còn là duy nhất —
     * gettext phân biệt bằng cặp (context, msgid), nên khoá cũng phải vậy.
     */
    public static function make_key($msgid, $msgctxt = null) {
        return md5((($msgctxt === null) ? '' : $msgctxt . "\x04") . $msgid);
    }

    /**
     * Trích xuất các format specifier printf: %s, %d, %1$s, %2$d, etc.
     */
    public static function extract_format_specifiers($text) {
        $clean = str_replace('%%', '', (string) $text);
        if (!preg_match_all('/%(?:\d+\$)?[+-]?(?:0|\'.)?-?\d*(?:\.\d+)?[bcdeEfFgGosuxX]/', $clean, $matches)) {
            return [];
        }
        return $matches[0];
    }

    /**
     * Xác thực format specifiers giữa msgid và msgstr.
     * Trả về true nếu hợp lệ, WP_Error nếu có lỗi.
     */
    public static function validate_format_specifiers($msgid, $msgstr) {
        if ($msgstr === '' || $msgstr === null) return true;
        $src_specs = self::extract_format_specifiers($msgid);
        $dst_specs = self::extract_format_specifiers($msgstr);

        sort($src_specs);
        sort($dst_specs);

        if ($src_specs !== $dst_specs) {
            return new WP_Error(
                'invalid_format_specifiers',
                sprintf(
                    'Format specifiers không khớp: nguồn có [%s], bản dịch có [%s]. Sẽ gây fatal error trong sprintf().',
                    implode(', ', $src_specs),
                    implode(', ', $dst_specs)
                )
            );
        }
        return true;
    }

    /**
     * Đổi msgstr cho nhiều entry cùng lúc (batch update).
     * Sắp xếp theo dòng giảm dần (reverse line order) và splice,
     * sau đó chỉ parse lại ĐÚNG 1 LẦN cuối cùng để đạt O(N+M) thay vì O(N*M).
     *
     * @param array $pairs [key => new_value] hoặc [key => ['value' => ..., 'index' => 0]]
     * @return int|WP_Error Số lượng entry đã thay đổi, hoặc WP_Error nếu có lỗi validation
     */
    public function set_msgstrs_batch(array $pairs) {
        if (!$this->loaded) return 0;

        $key_map = [];
        foreach ($this->entries as $entry_idx => $e) {
            if ($e['msgid'] === null || $e['msgid'] === '') continue;
            $k = self::make_key($e['msgid'], $e['msgctxt']);
            $key_map[$k] = $entry_idx;
        }

        $plan = [];
        foreach ($pairs as $key => $item) {
            if (!isset($key_map[$key])) continue;
            $new_val = is_array($item) ? ($item['value'] ?? '') : (string) $item;
            $index   = is_array($item) ? (int) ($item['index'] ?? 0) : 0;

            $e = $this->entries[$key_map[$key]];
            if (!isset($e['msgstr'][$index])) continue;

            $slot = $e['msgstr'][$index];
            if ($slot['value'] === $new_val) continue;

            $valid = self::validate_format_specifiers($e['msgid'], $new_val);
            if (is_wp_error($valid)) {
                return $valid;
            }

            $label    = $slot['indexed'] ? 'msgstr[' . $index . ']' : 'msgstr';
            $new_line = $label . ' "' . self::encode($new_val) . '"';

            $plan[] = [
                'start'    => $slot['start'],
                'end'      => $slot['end'],
                'new_line' => $new_line,
            ];
        }

        if (empty($plan)) return 0;

        // Sắp xếp ngược: dòng cao xử lý trước để không làm lệch chỉ số dòng thấp
        usort($plan, function ($a, $b) {
            return $b['start'] <=> $a['start'];
        });

        foreach ($plan as $p) {
            array_splice(
                $this->lines,
                $p['start'],
                $p['end'] - $p['start'] + 1,
                [$p['new_line']]
            );
        }

        $this->parse();
        $this->dirty = true;

        return count($plan);
    }

    /**
     * Đổi msgstr của đúng một entry. Trả về true nếu file đã được sửa,
     * false nếu không tìm thấy entry hoặc giá trị không đổi.
     */
    public function set_msgstr($key, $new_value, $index = 0) {
        $res = $this->set_msgstrs_batch([$key => ['value' => $new_value, 'index' => $index]]);
        if (is_wp_error($res)) return false;
        return $res > 0;
    }

    public function is_dirty() {
        return $this->dirty;
    }

    public function save() {
        if (!$this->loaded) return false;
        $out = implode($this->eol, $this->lines);
        if ($this->has_bom) $out = "\xEF\xBB\xBF" . $out;
        $ok = (file_put_contents($this->path, $out, LOCK_EX) !== false);
        if ($ok) $this->dirty = false;
        return $ok;
    }

    /** Escape theo chuẩn PO. Thứ tự quan trọng: backslash phải xử lý trước. */
    public static function encode($s) {
        return str_replace(
            ['\\',   '"',   "\t",  "\r",  "\n"],
            ['\\\\', '\\"', '\\t', '\\r', '\\n'],
            (string) $s
        );
    }

    /** Giải escape PO. Quét 1 lượt để không xử lý chồng lên nhau. */
    public static function decode($s) {
        return preg_replace_callback('/\\\\(.)/s', function ($m) {
            switch ($m[1]) {
                case 'n': return "\n";
                case 't': return "\t";
                case 'r': return "\r";
                case '"': return '"';
                case '\\': return '\\';
                default:  return $m[0]; // Giữ nguyên ký tự escape không xác định thay vì nuốt mất backslash
            }
        }, (string) $s);
    }
}
