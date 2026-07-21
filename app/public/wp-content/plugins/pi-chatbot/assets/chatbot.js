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

    var isOpen = sessionStorage.getItem('pi_cb_isOpen') === 'true';
    var greeted = false;
    
    var chatHistory = [];
    try {
        var savedHistory = sessionStorage.getItem('pi_cb_history');
        if (savedHistory) chatHistory = JSON.parse(savedHistory);
    } catch (e) {}
    var isRestoring = false;


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
    // emoji làm câu hỏi ("[emoji] Báo giá"), sau lọc stopword chỉ còn "báo" (vì "giá" đã
    // là stopword) → khớp bừa 6 sản phẩm ngẫu nhiên làm "Nguồn" (bug thật đo được).
    // Đồng thời tách cache theo emoji ở đúng 4 đường vào nóng nhất. 4 câu bên dưới
    // đã kiểm chứng qua Pi_Chatbot_Retriever: mỗi câu trúng đúng 1 ý định (giá/dự
    // án/dịch vụ/liên hệ) và phần dư sau khi bóc cụm từ ý định rơi hết về stopword
    // — không còn đính nguồn sản phẩm giả nào.
    //
    // icon là SVG nét — KHÔNG dùng emoji (emoji hiển thị khác nhau tuỳ font hệ điều
    // hành, SVG kế thừa currentColor nên tự khớp theme --cb-accent).
    var ICON_TAG_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13 21a2 2 0 0 1-2.83 0L3 13.83a2 2 0 0 1 0-2.83L10.59 3.4A2 2 0 0 1 12 2.83H19a2 2 0 0 1 2 2v6.17a2 2 0 0 1-.41 1.41Z"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>';
    var ICON_IMAGE_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.35-4.35a2 2 0 0 0-2.83 0L6 18"/></svg>';
    var ICON_CLIPBOARD_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>';
    var ICON_PHONE_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>';
    var QUICK_REPLIES = [
        { icon: ICON_TAG_SVG, label: 'Báo giá', query: 'Cho mình xin báo giá ạ' },
        { icon: ICON_IMAGE_SVG, label: 'Mẫu thiết kế', query: 'Cho mình mẫu thiết kế đã thi công ạ' },
        { icon: ICON_CLIPBOARD_SVG, label: 'Dịch vụ', query: 'Bên mình có dịch vụ gì ạ' },
        { icon: ICON_PHONE_SVG, label: 'Liên hệ', query: 'Cho mình xin liên hệ ạ' }
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
        sessionStorage.setItem('pi_cb_isOpen', open ? 'true' : 'false');
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
        if (chatHistory && chatHistory.length > 0) {
            isRestoring = true;
            chatHistory.forEach(function (msg) {
                addMessage(msg.role, msg.text, msg.sources);
            });
            isRestoring = false;
        } else {
            addMessage('bot', 'Xin chào!\nEm là trợ lý AI của Saigon Horeca. Anh/chị cần tư vấn gì ạ?');
            addQuickReplies(QUICK_REPLIES);
        }
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
        chatHistory = [];
        sessionStorage.removeItem('pi_cb_history');
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
            var icon = document.createElement('span');
            icon.className = 'pi-cb-qr-icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.innerHTML = item.icon;
            btn.appendChild(icon);
            var label = document.createElement('span');
            label.textContent = item.label;
            btn.appendChild(label);
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

    var BOT_SVG = '<svg class="pi-cb-bot-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1M20 11a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1" /><path d="M12 6V3M10 3h4" /><rect x="5" y="6" width="14" height="12" rx="3" /><circle cx="9" cy="11" r="1" fill="currentColor" /><circle cx="15" cy="11" r="1" fill="currentColor" /><path d="M10 14a2 2 0 0 0 4 0" /></svg>';

    /** Dựng riêng phần bong bóng (không kèm wrap/avatar) — addMessage() dùng cho
     *  luồng thường; revealTraceThenAnswer() dùng lại để gắn bong bóng trả lời
     *  vào CHUNG 1 avatar với card tiến trình, thay vì tạo avatar thứ 2. */
    function buildMsgBubble(role, text, sources) {
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
        return el;
    }

    function addMessage(role, text, sources) {
        if (!isRestoring && (role === 'user' || role === 'bot')) {
            chatHistory.push({ role: role, text: text, sources: sources || [] });
            sessionStorage.setItem('pi_cb_history', JSON.stringify(chatHistory));
        }

        var wrap = document.createElement('div');
        wrap.className = 'pi-cb-msg-wrap pi-cb-msg-wrap--' + role;

        if (role === 'bot' || role === 'error') {
            var avatar = document.createElement('div');
            avatar.className = 'pi-cb-avatar';
            avatar.innerHTML = BOT_SVG;
            wrap.appendChild(avatar);
        }

        var el = buildMsgBubble(role, text, sources);
        wrap.appendChild(el);
        messages.appendChild(wrap);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }
    function wait(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    /** Mở 1 lượt trả lời của bot — 1 avatar DUY NHẤT dùng chung cho cả card tiến
     *  trình lẫn bong bóng trả lời cuối, thay vì mỗi tầng là 1 "đoạn chat" riêng
     *  với avatar riêng (bug đã sửa: trước đây renderStep() tạo hẳn 1 wrap+avatar
     *  mới mỗi tầng, trông như 3 tin nhắn tách rời của bot). Trả về `col` — cột
     *  dọc chứa card tiến trình rồi tới bong bóng trả lời, cùng 1 hàng với avatar. */
    function beginBotTurn() {
        var wrap = document.createElement('div');
        wrap.className = 'pi-cb-msg-wrap pi-cb-msg-wrap--bot';
        var avatar = document.createElement('div');
        avatar.className = 'pi-cb-avatar';
        avatar.innerHTML = BOT_SVG;
        wrap.appendChild(avatar);
        var col = document.createElement('div');
        col.className = 'pi-cb-msg-col';
        wrap.appendChild(col);
        messages.appendChild(wrap);
        messages.scrollTop = messages.scrollHeight;
        return col;
    }

    /** Gắn bong bóng trả lời cuối vào CHUNG cột với card tiến trình (không tạo
     *  avatar/wrap thứ 2) — vẫn lưu vào lịch sử/sessionStorage như addMessage(). */
    function finishBotTurn(col, text, sources) {
        if (!isRestoring) {
            chatHistory.push({ role: 'bot', text: text, sources: sources || [] });
            sessionStorage.setItem('pi_cb_history', JSON.stringify(chatHistory));
        }
        col.appendChild(buildMsgBubble('bot', text, sources));
        messages.scrollTop = messages.scrollHeight;
    }

    /** 1 card DUY NHẤT chứa cả 2 hàng "phân tích"/"tìm kiếm", nối liền nhau bằng
     *  đường kẻ chia (xem CSS .pi-cb-step-row + .pi-cb-step-row) — KHÔNG phải 2
     *  khối tách rời. Không icon riêng cho từng hàng — 1 icon svg (avatar bot)
     *  cho toàn bộ lượt trả lời là đủ, thêm icon phụ mỗi hàng chỉ rối mắt. */
    function renderProgressCard(col) {
        var card = document.createElement('div');
        card.className = 'pi-cb-progress';
        col.appendChild(card);
        messages.scrollTop = messages.scrollHeight;
        return card;
    }

    /** 1 hàng trong card tiến trình — <details> thật (bấm mở lại được sau khi
     *  xong), bắt đầu ở trạng thái "Đang…" có 3 chấm nhảy, chốt nhãn thật bằng
     *  finishStep() khi bước đó xong. KHÔNG gắn aria-hidden lên cả hàng — để
     *  trình đọc màn hình vào <details> được như mọi nơi khác trong widget. */
    function renderStepRow(card, kind, label) {
        var details = document.createElement('details');
        details.className = 'pi-cb-step-row pi-cb-step-row--' + kind;
        details.open = true;

        var summary = document.createElement('summary');
        var labelEl = document.createElement('span');
        labelEl.className = 'pi-cb-step-label-text';
        labelEl.textContent = label;
        summary.appendChild(labelEl);
        var dots = document.createElement('span');
        dots.className = 'pi-cb-step-dots';
        dots.setAttribute('aria-hidden', 'true');
        dots.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
        summary.appendChild(dots);
        details.appendChild(summary);

        var body = document.createElement('div');
        body.className = 'pi-cb-step-body';
        details.appendChild(body);

        card.appendChild(details);
        messages.scrollTop = messages.scrollHeight;
        return { details: details, labelEl: labelEl, dots: dots, body: body };
    }

    /** Chốt nhãn thật + danh sách chi tiết cho 1 hàng, tắt 3 chấm "đang…". */
    function finishStep(step, label, detailLines) {
        step.labelEl.textContent = label;
        step.dots.remove();
        step.body.innerHTML = '';
        (detailLines || []).forEach(function (line) {
            var p = document.createElement('div');
            p.className = 'pi-cb-step-line';
            p.textContent = line;
            step.body.appendChild(p);
        });
    }

    /** Nhãn + chi tiết cho hàng "phân tích" — 1 hàm DUY NHẤT quyết định cả nhãn lẫn
     *  nội dung, để 2 thứ không bao giờ lệch pha nhau (bug đã sửa: nhãn cũ "Đã xác
     *  định: câu hỏi về sản phẩm" hiện CẢ khi found_count=0 và câu chỉ là "chào" —
     *  sai, vì is_price/contact/service/project_intent() đều KHÔNG khớp và search()
     *  cũng không ra kết quả nào, tức hệ thống chưa hề "xác định" được gì cụ thể).
     *  Nhánh theo đúng thứ tự request thật đã chạy: ý định rule-based trước, rồi
     *  router (chỉ chạy khi rule-based thất bại), rồi mới tới trường hợp không
     *  khớp gì — LUÔN có ít nhất 1 dòng chi tiết thật, không để trống. */
    function describeThink(trace) {
        switch (trace.intent) {
            case 'price':
                return { label: 'Đã xác định: câu hỏi về báo giá', lines: ['Áp dụng chính sách báo giá cố định của công ty, không tra dữ liệu sản phẩm.'] };
            case 'contact':
                return { label: 'Đã xác định: câu hỏi về liên hệ', lines: ['Dùng thông tin liên hệ có sẵn của công ty.'] };
            case 'service':
                return { label: 'Đã xác định: câu hỏi về dịch vụ', lines: ['Dùng mô tả dịch vụ có sẵn của công ty.'] };
            case 'project':
                return { label: 'Đã xác định: câu hỏi về dự án đã thi công', lines: ['Tìm trong danh sách dự án đã thi công.'] };
        }
        if (trace.router_used) {
            var lines = [];
            if (trace.router_keywords && trace.router_keywords.length) {
                lines.push('Từ khoá suy ra: ' + trace.router_keywords.join(', '));
            }
            if (trace.router_brand) lines.push('Thương hiệu nhận diện: ' + trace.router_brand);
            if (!lines.length) lines.push('Đã suy luận thêm để xác định sản phẩm phù hợp.');
            return { label: 'Đã suy luận thêm để tìm đúng sản phẩm', lines: lines };
        }
        if (trace.found_count > 0) {
            return { label: 'Đã xác định: câu hỏi về sản phẩm', lines: ['Tìm trực tiếp theo từ khoá trong câu hỏi.'] };
        }
        // Không khớp ý định nào VÀ không tìm thấy gì — đúng trường hợp câu chào/chit-
        // chat, KHÔNG được gọi là "câu hỏi về sản phẩm" (sai thực tế, đã bị bắt lỗi).
        return { label: 'Không phát hiện ý định cụ thể', lines: ['Không khớp ý định giá/liên hệ/dịch vụ/dự án nào và không tìm thấy sản phẩm liên quan — trả lời chung.'] };
    }

    /** Diễn hoạt ĐÚNG 3 tầng đã chốt, gộp chung 1 avatar/1 card liên kết (không
     *  chia 3 đoạn chat như bản trước): (1) phân tích câu hỏi — luôn hiện, ý định
     *  thật từ trace.intent hoặc router nếu có chạy; (2) tìm trong dữ liệu — luôn
     *  hiện, từ khoá + số kết quả thật; (3) bong bóng trả lời, GẮN VÀO CÙNG cột
     *  với card ở trên. Mỗi hàng mở ra ở trạng thái "Đang…" có 3 chấm TRƯỚC, giữ
     *  1 nhịp rồi mới chốt nhãn thật và thu gọn. Nội dung mỗi hàng LUÔN LÀ THẬT
     *  (trace từ class-rest.php) — không bịa suy luận nào khi router không chạy,
     *  chỉ dùng ý định đã có sẵn (is_price_intent()/is_contact_intent()/... đã
     *  chạy cho MỌI câu hỏi). `stillCurrent` là closure kiểm seq — bấm Làm mới
     *  giữa lúc diễn hoạt thì dừng lặng lẽ, không chèn gì vào transcript đã xoá. */
    function revealTraceThenAnswer(trace, answerText, sources, stillCurrent) {
        setTyping(false);
        var col = beginBotTurn();
        if (!trace) {
            finishBotTurn(col, answerText, sources);
            return Promise.resolve();
        }

        var card = renderProgressCard(col);

        // Tầng 1 — phân tích câu hỏi (luôn hiện).
        var thinkStep = renderStepRow(card, 'thinking', 'Đang phân tích câu hỏi');
        setStatus('Đang phân tích câu hỏi…');

        return wait(700).then(function () {
            if (!stillCurrent()) return;

            var think = describeThink(trace);
            finishStep(thinkStep, think.label, think.lines);
            thinkStep.details.open = false;

            // Tầng 2 — tìm trong dữ liệu (luôn hiện, NỐI TIẾP trong cùng card).
            setStatus('Đang tìm trong dữ liệu…');
            var searchStep = renderStepRow(card, 'search', 'Đang tìm trong dữ liệu');

            return wait(750).then(function () {
                if (!stillCurrent()) return;

                var searchLabel = trace.found_count > 0
                    ? 'Đã tìm — ' + trace.found_count + ' kết quả liên quan'
                    : 'Đã tìm — không thấy kết quả phù hợp';
                var searchLines = [];
                if (trace.search_query) searchLines.push('Từ khoá: ' + trace.search_query);
                finishStep(searchStep, searchLabel, searchLines);
                searchStep.details.open = false;

                // Tầng 3 — bong bóng trả lời, cùng cột/avatar với card ở trên.
                finishBotTurn(col, answerText, sources);
            });
        });
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
                // Dọn timer NGAY khi có phản hồi — không đợi resetSendState() ở .finally()
                // (giờ bị trì hoãn tới lúc diễn hoạt xong ~1.1s). Không dọn sớm thì
                // slowTimer/hardTimer có thể bắn nhầm "vẫn đang tìm"/abort ngay giữa
                // lúc đang hiện 2 khối "Đã tìm kiếm"/"Đã suy nghĩ" của MỘT câu đã
                // xong rồi.
                clearTimers();
                showSlowHint(false);
                if (r.ok) {
                    if (r.data && typeof r.data.answer === 'string') {
                        // Trả về promise của diễn hoạt tuần tự để .finally() phía dưới
                        // đợi nó xong rồi mới đưa panel về trạng thái rảnh — nếu không,
                        // resetSendState() (gỡ readonly, mở lại nút gửi) sẽ chạy TRƯỚC
                        // khi 2 khối "Đã tìm kiếm"/"Đã suy nghĩ" kịp diễn xong.
                        return revealTraceThenAnswer(r.data.trace, r.data.answer, r.data.sources, function () {
                            return seq === requestSeq;
                        });
                    }
                    addMessage('error', 'Có lỗi xảy ra, vui lòng thử lại.');
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

    // Khởi tạo trạng thái ban đầu nếu người dùng đang mở panel ở trang trước
    if (isOpen) {
        setOpen(true);
    }
})();
