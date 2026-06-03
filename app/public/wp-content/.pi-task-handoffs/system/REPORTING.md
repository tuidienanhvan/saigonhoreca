# 📊 Reporting Protocol — v4.3

This document establishes the mandatory standards for all Worker Agents' reports within the Pi Ecosystem.

---

## I. 🛡️ The Golden Rules of Reporting

### 1.1 🧪 Evidence-First Rule
No claim of "Success" is valid without raw terminal output evidence. If a command was run, the output MUST be pasted.

### 1.2 🚫 No Paraphrasing
Do not summarize terminal results. Copy and paste the **entire** block from the terminal.

### 1.3 ✂️ Absolute Delta
Report exactly how many lines were added (`+`) and removed (`-`).

---

## II. 📤 The Standard REPORT Block
Every Worker must reply with this exact structure.

```markdown
=== <TASK_ID> REPORT ===
STATUS: <pass | fail | pass with warnings>
SUMMARY: <Detailed 3-5 sentence summary of technical changes.>

FILES_MODIFIED:
- <path/to/file> (+X, -Y lines)

FILES_CREATED:
- <path/to/file>

FILES_DELETED:
- <path/to/file>

VERIFY EVIDENCE:
$ <command_1>
<PASTE FULL RAW TERMINAL OUTPUT HERE>
# 📤 Quy chuẩn báo cáo / Reporting Standards — v3.1

Báo cáo kết quả là bằng chứng duy nhất để Orchestrator phê duyệt task. Mọi báo cáo PHẢI tuân thủ định dạng này.

---

## I. 📝 Cấu trúc khối báo cáo / Report Block Structure
Sử dụng mẫu `PROMPT.md` §5. Các mục bắt buộc:
1. **STATUS**: `pass` hoặc `fail`.
2. **SUMMARY**: Tóm tắt kỹ thuật 3-5 câu (Tiếng Việt hoặc Song ngữ).
3. **FILES_MODIFIED**: Danh sách file kèm theo số dòng thay đổi (+X, -Y).
4. **VERIFY EVIDENCE**: Phần quan trọng nhất. Paste **RAW terminal output**.

---

## II. 🧪 Quy tắc cung cấp bằng chứng / Evidence Rules

### 2.1 🚫 Không diễn giải / No Summarization
Cấm viết "Build passed". PHẢI paste toàn bộ output từ lệnh build.

### 2.2 🚫 Không làm sạch / No Sanitization
Giữ nguyên các cảnh báo (Warnings) hoặc thông báo hệ thống để Orchestrator có cái nhìn trung thực nhất.

### 2.3 🛠️ Lệnh bắt buộc / Mandatory Commands
Phải có bằng chứng cho ít nhất:
- `npm run lint`
- `npm run build`
- `git status --short`

---

## III. 💡 Lưu ý kỹ thuật và Cảnh báo / Technical Notes & Warnings

### 3.1 💡 Technical Caveats
Mô tả các trade-off hoặc các trường hợp đặc biệt (Edge cases) mà bạn đã xử lý.

### 3.2 🧹 Remaining Warnings
Nếu còn warning chưa fix, hãy giải thích lý do tại sao (ví dụ: lỗi từ thư viện bên thứ 3).

### 3.3 👑 Orchestrator Notice
Các yêu cầu hoặc lưu ý đặc biệt gửi đến người điều phối.
