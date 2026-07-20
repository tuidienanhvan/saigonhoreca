/**
 * Pi Chatbot — panel gắn vào nút #sh-toggle (markup + CSS hook đã có sẵn trong theme).
 */
(function () {
    var toggle = document.getElementById('sh-toggle');
    if (!toggle || typeof PiChatbotConfig === 'undefined') return;

    var iconChat = document.getElementById('sh-ic-chat');
    var iconX = document.getElementById('sh-ic-x');
    var badge = document.getElementById('sh-badge');

    var panel = document.createElement('div');
    panel.id = 'pi-chatbot-panel';
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-labelledby', 'pi-cb-title');
    // KHÔNG còn aria-modal="true" — panel không thực sự modal (không có inert/
    // aria-hidden nào áp lên phần còn lại trang, khách vẫn đọc/cuộn trang nền được
    // trong lúc chat, đây là hành vi CỐ Ý — vừa đọc thông số vừa hỏi). aria-modal
    // đặt sai là lời hứa giả với công nghệ hỗ trợ (AT sẽ ẩn hẳn phần nền đi).
    panel.hidden = true;
    panel.innerHTML =
        '<div class="pi-cb-header">' +
        '  <div class="pi-cb-icon"><svg class="pi-cb-bot-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1M20 11a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1" /><path d="M12 6V3M10 3h4" /><rect x="5" y="6" width="14" height="12" rx="3" /><circle cx="9" cy="11" r="1" fill="currentColor" /><circle cx="15" cy="11" r="1" fill="currentColor" /><path d="M10 14a2 2 0 0 0 4 0" /></svg></div>' +
        '  <div class="pi-cb-title" id="pi-cb-title">Trợ lý AI Saigon Horeca<small><span class="pi-cb-dot"></span>Online — Phản hồi ngay</small></div>' +
        '  <div class="pi-cb-actions">' +
        '    <button type="button" class="pi-cb-reset" aria-label="Làm mới"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></button>' +
        '    <button type="button" class="pi-cb-close" aria-label="Đóng"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>' +
        '  </div>' +
        '</div>' +
        '<div class="pi-cb-messages" role="log" aria-live="polite" aria-busy="false"></div>' +
        '<div class="pi-cb-footer">' +
        '  <p class="pi-cb-sr-only" id="pi-cb-status" role="status" aria-live="polite"></p>' +
        '  <p class="pi-cb-cooldown" id="pi-cb-cooldown" hidden></p>' +
        '  <div class="pi-cb-input-row">' +
        '    <label for="pi-cb-input" class="pi-cb-sr-only">Nhập câu hỏi</label>' +
        '    <input type="text" id="pi-cb-input" class="pi-cb-input" placeholder="Nhập câu hỏi..." maxlength="500" aria-label="Nhập câu hỏi">' +
        '    <button type="button" class="pi-cb-send" aria-label="Gửi">' +
        '      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>' +
        '    </button>' +
        '  </div>' +
        '  <div class="pi-cb-engine-row">' +
        '    <span class="pi-cb-engine-label">' +
        '      <svg class="pi-cb-bot-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '        <path d="M3 11a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1M20 11a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1" />' +
        '        <path d="M12 6V3M10 3h4" />' +
        '        <rect x="5" y="6" width="14" height="12" rx="3" />' +
        '        <circle cx="9" cy="11" r="1" fill="currentColor" />' +
        '        <circle cx="15" cy="11" r="1" fill="currentColor" />' +
        '        <path d="M10 14a2 2 0 0 0 4 0" />' +
        '      </svg>' +
        '      Vận hành bởi Pi Digital' +
        '    </span>' +
        '  </div>' +
        '</div>';
    // Gắn thẳng vào <body> — KHÔNG còn là con của #sh-floating-buttons nữa (bug thật
    // tìm thấy khi audit: cụm nút nổi có pointerdown/move/up + setPointerCapture cho
    // kéo-thả riêng; panel làm con của nó khiến cử chỉ TRONG panel — kéo chọn chữ
    // trong ô nhập, vuốt cuộn khung tin nhắn — bị nhầm thành thao tác kéo cụm nút,
    // và panel kế thừa user-select:none của cụm khiến không copy được câu trả lời).
    // `position:fixed` của panel vốn đã neo theo viewport (không phụ thuộc DOM cha)
    // nên tách ra không ảnh hưởng gì tới vị trí hiển thị.
    document.body.appendChild(panel);

    var messages = panel.querySelector('.pi-cb-messages');
    var input = panel.querySelector('.pi-cb-input');
    var sendBtn = panel.querySelector('.pi-cb-send');
    var closeBtn = panel.querySelector('.pi-cb-close');
    var resetBtn = panel.querySelector('.pi-cb-reset');
    var statusEl = panel.querySelector('#pi-cb-status');
    var cooldownEl = panel.querySelector('#pi-cb-cooldown');
    // Giới hạn ký tự lấy từ PHP (Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH qua wp_localize_script)
    // thay vì số cứng "500" riêng ở đây — 1 nguồn duy nhất, đổi giới hạn phía server
    // không cần sửa thêm file JS này.
    input.maxLength = PiChatbotConfig.maxLength || 500;

    var isOpen = false;
    var greeted = false;

    // === Trạng thái gửi — gom về DUY NHẤT resetSendState() sở hữu ===============
    // Trước đây isSending/sendBtn.disabled rải rác trong 3 callback (.then/.catch/
    // .finally) — 1 request bị treo (mất mạng giữa chừng, không lỗi không timeout)
    // không callback nào chạy, panel kẹt vĩnh viễn ở trạng thái "đang gửi": 3 chấm
    // nhảy mãi, nút gửi mờ đi, ô nhập chờ vô ích. Nút Làm mới cũ chỉ xoá DOM, không
    // đụng tới isSending — bấm xong panel TRÔNG như đã hồi phục nhưng vẫn âm thầm
    // từ chối gửi. requestSeq là chìa khoá: mỗi request giữ 1 số thứ tự, mọi
    // callback tự kiểm "mình có còn là request mới nhất không" trước khi làm gì —
    // đây là thứ chặn callback của 1 request đã bị Làm mới huỷ chèn bong bóng lỗi
    // vào transcript vừa được xoá sạch.
    var requestSeq = 0;
    var controller = null;
    var hardTimer = null;
    var slowTimer = null;
    var cooldownEnd = 0;
    var cooldownTick = null;
    var isSending = false;

    var SLOW_NOTICE_MS = 12000;
    // 45s = biên an toàn phía trên mô hình 40s tệ nhất (20s timeout x 2 key dự
    // phòng — xem class-generator.php TIMEOUT_SECONDS/MAX_KEY_ATTEMPTS). Số 502 đo
    // được thực tế ở 10,25s không khớp mô hình 40s — có thể do gateway/CDN có
    // timeout riêng ngắn hơn PHP, hoặc lỗi đến từ chính LLM7 rồi bị khoác lại thành
    // 502 của plugin. Giữ 45s cho an toàn; nếu theo dõi thực tế thấy request không
    // bao giờ vượt ~15s, hạ xuống 25s sẽ cho trải nghiệm lỗi tốt hơn nhiều.
    var HARD_TIMEOUT_MS = 45000;

    var FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), textarea, summary, [tabindex]:not([tabindex="-1"])';

    // Nhãn hiển thị TÁCH khỏi câu thật sự gửi đi — trước đây nút gửi thẳng nhãn kèm
    // emoji làm câu hỏi ("💰 Báo giá"), sau lọc stopword chỉ còn "báo" (vì "giá" đã
    // là stopword) → khớp bừa 6 sản phẩm ngẫu nhiên làm "Nguồn" (bug thật đo được).
    // Đồng thời tách cache theo emoji ở đúng 4 đường vào nóng nhất. 4 câu bên dưới
    // đã kiểm chứng qua Pi_Chatbot_Retriever: mỗi câu trúng đúng 1 ý định (giá/dự
    // án/dịch vụ/liên hệ) và phần dư sau khi bóc cụm từ ý định rơi hết về stopword
    // — không còn đính nguồn sản phẩm giả nào.
    var QUICK_REPLIES = [
        { label: '💰 Báo giá', query: 'Cho mình xin báo giá ạ' },
        { label: '🏭 Mẫu thiết kế', query: 'Cho mình mẫu thiết kế đã thi công ạ' },
        { label: '📋 Dịch vụ', query: 'Bên mình có dịch vụ gì ạ' },
        { label: '📞 Liên hệ', query: 'Cho mình xin liên hệ ạ' }
    ];

    function setStatus(text) {
        statusEl.textContent = text || '';
    }

    function setBusy(on) {
        messages.setAttribute('aria-busy', on ? 'true' : 'false');
    }

    function cooldownMessage() {
        var secs = Math.max(0, Math.ceil((cooldownEnd - Date.now()) / 1000));
        return 'Bạn gửi câu hỏi hơi nhanh, vui lòng chờ ' + secs + 's.';
    }

    function setSendEnabled(on) {
        // aria-disabled (KHÔNG phải thuộc tính disabled thật) — disabled thật loại
        // element khỏi tab order, khiến tập tab-stop mà focus trap tính toán đổi
        // hình dạng ngay giữa 1 request (bug SEV-3), và nếu focus đang đứng ở nút
        // gửi lúc bị disable thì focus rơi tọt về <body>. aria-disabled giữ nguyên
        // trong tab order, chỉ báo cho AT biết tạm thời không dùng được.
        sendBtn.setAttribute('aria-disabled', on ? 'false' : 'true');
    }

    function clearTimers() {
        if (hardTimer) { clearTimeout(hardTimer); hardTimer = null; }
        if (slowTimer) { clearTimeout(slowTimer); slowTimer = null; }
    }

    function showSlowHint(on) {
        var el = messages.querySelector('.pi-cb-slow-hint');
        if (on && !el) {
            var typingEl = messages.querySelector('.pi-cb-typing');
            if (typingEl) {
                el = document.createElement('div');
                el.className = 'pi-cb-slow-hint';
                el.textContent = 'Vẫn đang tìm thông tin, câu hỏi này cần thêm chút thời gian…';
                typingEl.parentNode.appendChild(el);
            }
        } else if (!on && el) {
            el.remove();
        }
    }

    /** Nơi DUY NHẤT đưa widget về trạng thái rảnh, chấp nhận câu hỏi mới. */
    function resetSendState() {
        isSending = false;
        clearTimers();
        controller = null;
        setTyping(false);
        setBusy(false);
        setStatus('');
        input.readOnly = false;
        input.removeAttribute('aria-disabled');
        if (Date.now() >= cooldownEnd) setSendEnabled(true);
    }

    function startCooldown(ms) {
        cooldownEnd = Date.now() + ms;
        setSendEnabled(false);
        cooldownEl.hidden = false;
        if (cooldownTick) clearInterval(cooldownTick);
        cooldownTick = setInterval(function () {
            var left = cooldownEnd - Date.now();
            if (left <= 0) {
                clearInterval(cooldownTick);
                cooldownTick = null;
                cooldownEl.hidden = true;
                cooldownEl.textContent = '';
                if (!isSending) setSendEnabled(true);
                return;
            }
            cooldownEl.textContent = 'Vui lòng chờ ' + Math.ceil(left / 1000) + 's trước khi gửi tiếp.';
        }, 1000);
        cooldownEl.textContent = 'Vui lòng chờ ' + Math.ceil(ms / 1000) + 's trước khi gửi tiếp.';
    }

    function setOpen(open) {
        isOpen = open;
        panel.hidden = !open;
        panel.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (iconChat) iconChat.style.opacity = open ? '0' : '1';
        if (iconX) iconX.style.opacity = open ? '1' : '0';
        if (open) {
            if (badge) badge.style.display = 'none';
            if (!greeted) {
                greeted = true;
                renderGreeting();
            }
            focusPanelOnOpen();
        } else {
            // Trả focus về nút mở — người dùng bàn phím không bị lạc vào <body>.
            toggle.focus();
        }
    }

    /** Trên thiết bị cảm ứng KHÔNG auto-focus ô nhập — bàn phím ảo bật lên nuốt
     *  gần nửa panel vốn đã thấp trước khi khách kịp đọc lời chào. Vẫn phải đưa
     *  focus vào TRONG panel (dùng tabindex=-1 tạm) để focus trap có điểm neo. */
    function focusPanelOnOpen() {
        var isTouch = window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)').matches;
        if (isTouch) {
            panel.setAttribute('tabindex', '-1');
            panel.focus({ preventScroll: true });
        } else {
            input.focus();
        }
    }

    function renderGreeting() {
        addMessage('bot', 'Xin chào! 👋\nEm là trợ lý AI của Saigon Horeca. Anh/chị cần tư vấn gì ạ?');
        addQuickReplies(QUICK_REPLIES);
    }

    toggle.addEventListener('click', function () {
        setOpen(!isOpen);
    });
    closeBtn.addEventListener('click', function () {
        setOpen(false);
    });
    resetBtn.addEventListener('click', function () {
        // Thứ tự bắt buộc: tăng requestSeq TRƯỚC (mọi callback của request đang
        // treo tự nhận ra mình đã lỗi thời và không làm gì cả) -> abort() -> xoá
        // DOM -> resetSendState(). Nếu abort trước khi tăng seq, .catch của request
        // bị abort vẫn có thể chạy trước khi ta kịp xoá DOM và chèn nhầm bong bóng
        // lỗi vào transcript vừa xoá.
        requestSeq++;
        if (controller) controller.abort();
        cooldownEnd = 0;
        if (cooldownTick) { clearInterval(cooldownTick); cooldownTick = null; }
        cooldownEl.hidden = true;
        messages.innerHTML = '';
        resetSendState();
        renderGreeting();
        input.value = '';
        input.focus();
    });

    // Escape đóng panel — bind ở document (không phải panel): trước đây bind trên
    // panel nên khách bấm ra ngoài panel (đọc trang nền — luồng CỐ Ý panel không
    // modal) là Escape chết hẳn, phải bấm lại vào trong panel mới đóng được.
    document.addEventListener('keydown', function (e) {
        if (!isOpen) return;
        if (e.key === 'Escape') {
            setOpen(false);
            return;
        }
        if (e.key !== 'Tab') return;
        if (!panel.contains(document.activeElement)) return;

        var focusable = getFocusable();
        if (!focusable.length) return;
        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });

    /** Lọc theo getClientRects().length > 0 — KHÔNG dùng offsetParent (trả null với
     *  position:fixed, misclassify sai trên chính panel này). Loại link nằm trong
     *  <details> nguồn đang ĐÓNG (trước đây querySelectorAll tĩnh đếm cả link không
     *  hiển thị, "last" trỏ vào element vô hình, Shift+Tab từ đầu panel không làm
     *  gì cả — trap gãy đúng lúc panel ở trạng thái phổ biến nhất: details đóng). */
    function getFocusable() {
        return Array.prototype.filter.call(
            panel.querySelectorAll(FOCUSABLE_SELECTOR),
            function (el) { return el.getClientRects().length > 0; }
        );
    }

    /** Chỉ chấp nhận link http/https — nguồn tới từ get_permalink() nội bộ nên hiện
     * luôn an toàn, nhưng dựng qua DOM API + kiểm scheme thay vì nối chuỗi vào
     * innerHTML để nhất quán với cách title đã được escape (audit flag: inconsistency
     * giữa title escaped và url nối thô là latent XSS nếu nguồn dữ liệu đổi sau này). */
    function isSafeUrl(url) {
        return typeof url === 'string' && /^https?:\/\//i.test(url);
    }

    function addQuickReplies(items) {
        var el = document.createElement('div');
        el.className = 'pi-cb-quick-replies';
        items.forEach(function (item) {
            var btn = document.createElement('button');
            btn.className = 'pi-cb-qr-btn';
            btn.textContent = item.label;
            btn.addEventListener('click', function () {
                var qrs = messages.querySelectorAll('.pi-cb-quick-replies');
                qrs.forEach(function (qr) { qr.remove(); });
                send(item.query);
            });
            el.appendChild(btn);
        });
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
    }

    function addMessage(role, text, sources) {
        var wrap = document.createElement('div');
        wrap.className = 'pi-cb-msg-wrap pi-cb-msg-wrap--' + role;

        if (role === 'bot' || role === 'error') {
            var avatar = document.createElement('div');
            avatar.className = 'pi-cb-avatar';
            avatar.innerHTML = '<svg class="pi-cb-bot-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1M20 11a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1" /><path d="M12 6V3M10 3h4" /><rect x="5" y="6" width="14" height="12" rx="3" /><circle cx="9" cy="11" r="1" fill="currentColor" /><circle cx="15" cy="11" r="1" fill="currentColor" /><path d="M10 14a2 2 0 0 0 4 0" /></svg>';
            wrap.appendChild(avatar);
        }

        var el = document.createElement('div');
        el.className = 'pi-cb-msg pi-cb-msg--' + role;
        el.textContent = text;
        if (sources && sources.length) {
            var srcWrap = document.createElement('details');
            srcWrap.className = 'pi-cb-sources';

            var summary = document.createElement('summary');
            summary.textContent = 'Nguồn tham khảo (' + sources.length + ')';
            srcWrap.appendChild(summary);

            var srcList = document.createElement('div');
            srcList.className = 'pi-cb-sources-list';

            sources.forEach(function (s) {
                var item = document.createElement('div');
                item.className = 'pi-cb-source-item';
                if (isSafeUrl(s.url)) {
                    var a = document.createElement('a');
                    a.href = s.url;
                    a.target = '_blank';
                    a.rel = 'noopener';
                    a.textContent = s.title;
                    item.appendChild(a);
                } else {
                    item.textContent = s.title;
                }
                srcList.appendChild(item);
            });
            srcWrap.appendChild(srcList);
            el.appendChild(srcWrap);
        }
        wrap.appendChild(el);
        messages.appendChild(wrap);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }

    function setTyping(on) {
        var existing = messages.querySelector('.pi-cb-typing-wrap');
        if (existing) existing.remove();
        if (on) {
            var wrap = document.createElement('div');
            wrap.className = 'pi-cb-msg-wrap pi-cb-msg-wrap--bot pi-cb-typing-wrap';
            wrap.setAttribute('aria-hidden', 'true'); // 3 chấm là trang trí thuần tuý —
            // trạng thái THẬT cho trình đọc màn hình nằm ở #pi-cb-status (setStatus()),
            // không phải ở đây. Trước đây đây là node duy nhất trong vùng aria-live
            // nhưng không chứa chữ nào — vùng live không có gì để đọc suốt 8-40 giây.

            var avatar = document.createElement('div');
            avatar.className = 'pi-cb-avatar';
            avatar.innerHTML = '<svg class="pi-cb-bot-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1M20 11a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1" /><path d="M12 6V3M10 3h4" /><rect x="5" y="6" width="14" height="12" rx="3" /><circle cx="9" cy="11" r="1" fill="currentColor" /><circle cx="15" cy="11" r="1" fill="currentColor" /><path d="M10 14a2 2 0 0 0 4 0" /></svg>';
            wrap.appendChild(avatar);

            var el = document.createElement('div');
            el.className = 'pi-cb-msg pi-cb-msg--bot pi-cb-typing';
            el.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';

            wrap.appendChild(el);
            messages.appendChild(wrap);
            messages.scrollTop = messages.scrollHeight;
        }
    }

    /** Diễn giải lỗi HTTP thành 1 câu tiếng Việt — thay cho việc unconditionally
     *  đọc JSON rồi phó mặc cho .catch. Chuỗi 502/504 CỐ Ý trùng y hệt chuỗi server
     *  đang dùng (class-rest.php) — với người dùng đó là cùng 1 sự kiện dù lỗi đến
     *  từ gateway/CDN (trả HTML, không phải JSON) hay từ chính plugin. */
    function errorTextFor(status, data) {
        if (data && typeof data.message === 'string' && data.code !== 'rest_cookie_invalid_nonce') {
            return data.message; // chuỗi tiếng Việt server đã soạn sẵn — giữ nguyên
        }
        if (status === 403) return 'Phiên làm việc đã hết hạn, vui lòng tải lại trang rồi thử lại.';
        if (status === 429) return 'Bạn gửi câu hỏi hơi nhanh, vui lòng chờ 1 phút.';
        if (status === 502 || status === 503 || status === 504) {
            return 'Trợ lý đang bận, vui lòng thử lại sau ít phút hoặc gọi hotline.';
        }
        return 'Có lỗi xảy ra, vui lòng thử lại.';
    }

    function send(overrideText) {
        // Guard chống gửi trùng — trước đây Enter không kiểm tra trạng thái nút đã
        // disable (chỉ áp cho click chuột), bấm Enter liên tục lúc đang chờ LLM trả
        // lời bắn nhiều fetch song song. isSending áp dụng chung cho cả 2 đường.
        if (isSending) { setStatus('Đang chờ trả lời, vui lòng đợi trước khi gửi câu hỏi mới.'); return; }
        if (Date.now() < cooldownEnd) { setStatus(cooldownMessage()); return; }

        // typeof === 'string', KHÔNG phải truthy check — sendBtn.addEventListener
        // ('click', send) truyền thẳng MouseEvent làm tham số đầu; kiểm truthy sẽ
        // gửi "[object MouseEvent]" lên LLM mỗi lần bấm nút gửi bằng chuột.
        var text = (typeof overrideText === 'string' ? overrideText : input.value).trim();
        if (!text) return;

        var seq = ++requestSeq;
        var timedOut = false;

        isSending = true;
        addMessage('user', text);
        if (typeof overrideText !== 'string') input.value = '';
        input.readOnly = true;
        input.setAttribute('aria-disabled', 'true');
        setSendEnabled(false);
        setBusy(true);
        setStatus('Đang gửi câu hỏi, trợ lý đang soạn câu trả lời…');
        setTyping(true);

        controller = new AbortController();
        slowTimer = setTimeout(function () {
            if (seq !== requestSeq) return;
            setStatus('Vẫn đang tìm thông tin, câu hỏi này cần thêm chút thời gian…');
            showSlowHint(true);
        }, SLOW_NOTICE_MS);
        hardTimer = setTimeout(function () {
            if (seq !== requestSeq) return;
            timedOut = true;
            controller.abort();
        }, HARD_TIMEOUT_MS);

        fetch(PiChatbotConfig.restUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': PiChatbotConfig.nonce
            },
            body: JSON.stringify({ message: text }),
            signal: controller.signal
        })
            .then(function (res) {
                // Đọc dạng text rồi tự parse JSON trong try/catch — thay cho res.json()
                // vô điều kiện. Trước đây 1 trang lỗi HTML thật (nginx/Cloudflare/PHP-FPM
                // trả 502/504 dạng HTML trong worst-case ~40s) làm promise reject, rơi
                // thẳng vào .catch báo "không kết nối được" — SAI, server CÓ trả lời,
                // chỉ là không phải JSON.
                return res.text().then(function (raw) {
                    var data = null;
                    try { data = raw ? JSON.parse(raw) : null; } catch (e) { /* trang lỗi HTML */ }
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (r) {
                if (seq !== requestSeq) return;
                if (r.ok) {
                    if (r.data && typeof r.data.answer === 'string') {
                        addMessage('bot', r.data.answer, r.data.sources);
                    } else {
                        addMessage('error', 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                    return;
                }
                if (r.status === 429) startCooldown(60000);
                addMessage('error', errorTextFor(r.status, r.data));
            })
            .catch(function () {
                if (seq !== requestSeq) return;
                if (timedOut) {
                    addMessage('error', 'Trợ lý phản hồi lâu hơn bình thường. Vui lòng thử lại hoặc gọi hotline.');
                } else {
                    // Chỉ còn là lỗi mạng THẬT — request bị abort do Làm mới thì seq đã
                    // đổi ở nhánh trên nên không rơi tới đây.
                    addMessage('error', 'Không kết nối được tới trợ lý AI. Vui lòng thử lại sau.');
                }
            })
            .finally(function () {
                if (seq !== requestSeq) return;
                resetSendState();
            });
    }

    sendBtn.addEventListener('click', function () { send(); });
    input.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        if (isSending) { setStatus('Đang chờ trả lời, vui lòng đợi trước khi gửi câu hỏi mới.'); return; }
        if (Date.now() < cooldownEnd) { setStatus(cooldownMessage()); return; }
        send();
    });
})();
