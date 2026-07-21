/**
 * Pi Chatbot — panel attached to the #sh-toggle button (markup + CSS hook already exist in the theme).
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
    // NO LONGER has aria-modal="true" — the panel isn't truly modal (no inert/
    // aria-hidden is applied to the rest of the page, the visitor can still read/scroll
    // the background page while chatting — this is INTENTIONAL behavior, so they can read
    // specs and ask questions at the same time). Setting aria-modal would be a false promise
    // to assistive technology (AT would hide the background entirely).
    panel.hidden = true;
    panel.innerHTML =
        '<div class="pi-cb-header">' +
        '  <div class="pi-cb-icon"><svg class="pi-cb-bot-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1M20 11a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1" /><path d="M12 6V3M10 3h4" /><rect x="5" y="6" width="14" height="12" rx="3" /><circle cx="9" cy="11" r="1" fill="currentColor" /><circle cx="15" cy="11" r="1" fill="currentColor" /><path d="M10 14a2 2 0 0 0 4 0" /></svg></div>' +
        '  <div class="pi-cb-title" id="pi-cb-title">Saigon Horeca AI Assistant<small><span class="pi-cb-dot"></span>Online — Instant reply</small></div>' +
        '  <div class="pi-cb-actions">' +
        '    <button type="button" class="pi-cb-reset" aria-label="Reset"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></button>' +
        '    <button type="button" class="pi-cb-close" aria-label="Close"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>' +
        '  </div>' +
        '</div>' +
        '<div class="pi-cb-messages" role="log" aria-live="polite" aria-busy="false"></div>' +
        '<div class="pi-cb-footer">' +
        '  <p class="pi-cb-sr-only" id="pi-cb-status" role="status" aria-live="polite"></p>' +
        '  <p class="pi-cb-cooldown" id="pi-cb-cooldown" hidden></p>' +
        '  <div class="pi-cb-input-row">' +
        '    <label for="pi-cb-input" class="pi-cb-sr-only">Type your question</label>' +
        '    <input type="text" id="pi-cb-input" class="pi-cb-input" placeholder="Type your question..." maxlength="500" aria-label="Type your question">' +
        '    <button type="button" class="pi-cb-send" aria-label="Send">' +
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
        '      Powered by Pi Digital' +
        '    </span>' +
        '  </div>' +
        '</div>';
    // Appended directly to <body> — NO LONGER a child of #sh-floating-buttons (a real bug
    // found during audit: the floating-button cluster has its own pointerdown/move/up +
    // setPointerCapture for drag; with the panel as its child, gestures INSIDE the panel —
    // dragging to select text in the input, swipe-scrolling the message list — were
    // misread as drag operations on the button cluster, and the panel inherited the
    // cluster's user-select:none, which meant answers couldn't be copied).
    // The panel's `position:fixed` was already anchored to the viewport (independent of
    // its parent in the DOM), so detaching it doesn't affect its displayed position at all.
    document.body.appendChild(panel);

    var messages = panel.querySelector('.pi-cb-messages');
    var input = panel.querySelector('.pi-cb-input');
    var sendBtn = panel.querySelector('.pi-cb-send');
    var closeBtn = panel.querySelector('.pi-cb-close');
    var resetBtn = panel.querySelector('.pi-cb-reset');
    var statusEl = panel.querySelector('#pi-cb-status');
    var cooldownEl = panel.querySelector('#pi-cb-cooldown');
    // Character limit is taken from PHP (Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH via
    // wp_localize_script) instead of a separate hardcoded "500" here — a single source
    // of truth, so changing the limit server-side needs no further edit to this JS file.
    input.maxLength = PiChatbotConfig.maxLength || 500;

    var isOpen = sessionStorage.getItem('pi_cb_isOpen') === 'true';
    var greeted = false;

    var chatHistory = [];
    try {
        var savedHistory = sessionStorage.getItem('pi_cb_history');
        if (savedHistory) chatHistory = JSON.parse(savedHistory);
    } catch (e) {}
    var isRestoring = false;


    // === Sending state — consolidated into a SINGLE owner, resetSendState() ===============
    // Previously isSending/sendBtn.disabled were scattered across 3 callbacks (.then/.catch/
    // .finally) — if 1 request hung (network dropped mid-flight, no error, no timeout),
    // none of the callbacks ran, and the panel got stuck forever in "sending" state: the 3
    // dots bounced endlessly, the send button stayed dimmed, the input waited uselessly.
    // The old Reset button only cleared the DOM without touching isSending — after clicking
    // it, the panel LOOKED recovered but was still silently refusing to send. requestSeq is
    // the key: every request holds a sequence number, and every callback checks for itself
    // "am I still the latest request?" before doing anything — this is what stops a callback
    // from a request that Reset already cancelled from injecting an error bubble into the
    // transcript that was just cleared.
    var requestSeq = 0;
    var controller = null;
    var hardTimer = null;
    var slowTimer = null;
    var cooldownEnd = 0;
    var cooldownTick = null;
    var isSending = false;

    var SLOW_NOTICE_MS = 12000;
    // 45s = safety margin above the model's worst-case 40s (20s timeout x 2 fallback keys —
    // see class-generator.php TIMEOUT_SECONDS/MAX_KEY_ATTEMPTS). The 502s actually observed
    // in the field came at 10.25s, which doesn't match the 40s model — possibly a
    // gateway/CDN with its own shorter timeout, or an error originating from LLM7 itself
    // that got reclad as the plugin's 502. Keeping 45s to stay safe; if real-world
    // monitoring shows requests never exceed ~15s, lowering it to 25s would give a much
    // better error experience.
    var HARD_TIMEOUT_MS = 45000;

    var FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), textarea, summary, [tabindex]:not([tabindex="-1"])';

    // Displayed label is SEPARATE from the text actually sent — previously the send button
    // sent the label with its emoji straight through as the question ("[emoji] Quote"), which
    // after stopword filtering left only "quote" (since other words were stopwords) → matched
    // 6 random products as "Sources" (a real, measured bug). Cache is also split by emoji at
    // exactly these 4 highest-traffic entry points. The 4 phrases below have been verified via
    // Pi_Chatbot_Retriever: each hits exactly 1 intent (price/project/service/contact) and the
    // remainder after stripping the intent phrase falls entirely into stopwords — no more
    // spurious product sources get attached.
    //
    // The icon is a stroke SVG — NOT an emoji (emoji render differently across OS fonts; SVG
    // inherits currentColor so it automatically matches the --cb-accent theme token).
    var ICON_TAG_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13 21a2 2 0 0 1-2.83 0L3 13.83a2 2 0 0 1 0-2.83L10.59 3.4A2 2 0 0 1 12 2.83H19a2 2 0 0 1 2 2v6.17a2 2 0 0 1-.41 1.41Z"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>';
    var ICON_IMAGE_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.35-4.35a2 2 0 0 0-2.83 0L6 18"/></svg>';
    var ICON_CLIPBOARD_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>';
    var ICON_PHONE_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>';
    var QUICK_REPLIES = [
        { icon: ICON_TAG_SVG, label: 'Get a Quote', query: 'Please give me a quote' },
        { icon: ICON_IMAGE_SVG, label: 'Project Samples', query: 'Show me your completed project samples' },
        { icon: ICON_CLIPBOARD_SVG, label: 'Services', query: 'What services do you offer' },
        { icon: ICON_PHONE_SVG, label: 'Contact', query: 'Please give me your contact information' }
    ];

    function setStatus(text) {
        statusEl.textContent = text || '';
    }

    function setBusy(on) {
        messages.setAttribute('aria-busy', on ? 'true' : 'false');
    }

    function cooldownMessage() {
        var secs = Math.max(0, Math.ceil((cooldownEnd - Date.now()) / 1000));
        return 'You are sending messages too quickly, please wait ' + secs + 's.';
    }

    function setSendEnabled(on) {
        // aria-disabled (NOT the real disabled attribute) — a real disabled attribute
        // removes the element from the tab order, which would change the shape of the
        // tab-stop set the focus trap computes in the middle of a request (a SEV-3 bug),
        // and if focus was sitting on the send button when it got disabled, focus would
        // fall all the way back to <body>. aria-disabled keeps it in the tab order and
        // only tells AT that it's temporarily unusable.
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
                el.textContent = 'Still looking up the information, this question needs a bit more time…';
                typingEl.parentNode.appendChild(el);
            }
        } else if (!on && el) {
            el.remove();
        }
    }

    /** The ONLY place that returns the widget to idle, ready to accept a new question. */
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
            cooldownEl.textContent = 'Please wait ' + Math.ceil(left / 1000) + 's before sending another message.';
        }, 1000);
        cooldownEl.textContent = 'Please wait ' + Math.ceil(ms / 1000) + 's before sending another message.';
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
            // Return focus to the toggle button — keyboard users don't get lost at <body>.
            toggle.focus();
        }
    }

    /** On touch devices do NOT auto-focus the input — the virtual keyboard popping up
     *  would swallow nearly half of the already-short panel before the visitor could even
     *  read the greeting. Focus must still move INSIDE the panel (using a temporary
     *  tabindex=-1) so the focus trap has an anchor point. */
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
            // Quick-reply buttons should show whenever the customer hasn't actually
            // asked anything yet — not just on a brand-new session. Previously this
            // branch (restoring a NON-empty history) never showed them again, so a
            // page reload right after opening the panel (history = just the saved
            // greeting, no user message yet) silently lost the buttons, while the
            // Reset button — which clears history to [] and falls to the "else"
            // branch below — still showed them. Same starting state, inconsistent
            // outcome depending on reload vs. reset.
            var hasUserMessage = chatHistory.some(function (msg) { return msg.role === 'user'; });
            if (!hasUserMessage) addQuickReplies(QUICK_REPLIES);
        } else {
            addMessage('bot', 'Hello!\nI\'m Saigon Horeca\'s AI assistant. What can I help you with today?');
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
        // Required order: increment requestSeq FIRST (so any callback of an in-flight
        // request recognizes itself as stale and does nothing) -> abort() -> clear the
        // DOM -> resetSendState(). If we aborted before incrementing seq, the .catch of
        // the aborted request could still run before we finish clearing the DOM, and
        // wrongly insert an error bubble into the transcript we just cleared.
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

    // Escape closes the panel — bound on document (not on the panel): previously it was
    // bound on the panel, so if the visitor clicked outside the panel (reading the
    // background page — an INTENTIONAL flow since the panel isn't modal), Escape was
    // completely dead and they had to click back inside the panel first for it to close.
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

    /** Filters using getClientRects().length > 0 — NOT offsetParent (returns null for
     *  position:fixed, which would misclassify this very panel). Excludes links inside a
     *  CLOSED <details> for sources (previously querySelectorAll's static result counted
     *  even invisible links, "last" pointed at an invisible element, and Shift+Tab from
     *  the top of the panel did nothing at all — the trap broke exactly in the panel's
     *  most common state: sources collapsed). */
    function getFocusable() {
        return Array.prototype.filter.call(
            panel.querySelectorAll(FOCUSABLE_SELECTOR),
            function (el) { return el.getClientRects().length > 0; }
        );
    }

    /** Only accepts http/https links — sources come from an internal get_permalink() call
     * so they're always safe today, but we still build them via the DOM API + scheme check
     * instead of concatenating into innerHTML, to stay consistent with how the title is
     * already escaped (audit flag: an inconsistency between an escaped title and a raw
     * concatenated url is a latent XSS if the data source ever changes). */
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

    /** Builds just the bubble part (without the wrap/avatar) — addMessage() uses this for
     *  the normal flow; revealTraceThenAnswer() reuses it to attach the answer bubble to
     *  the SAME avatar as the progress card, instead of creating a second avatar. */
    /** Turns raw http(s) URLs embedded in plain text into real clickable
     *  <a> elements — built via DOM (textContent per piece), never innerHTML,
     *  so this keeps the same security invariant as the rest of the file
     *  (no server-supplied string is ever parsed as HTML). Trailing
     *  punctuation right after a URL (".", ")", "!"...) is split back out as
     *  plain text so a sentence-ending period doesn't get swallowed into the
     *  link href. */
    var URL_RE = /(https?:\/\/[^\s<>"]+)/g;
    function appendLinkifiedText(el, text) {
        var lastIndex = 0;
        var match;
        URL_RE.lastIndex = 0;
        while ((match = URL_RE.exec(text)) !== null) {
            if (match.index > lastIndex) {
                el.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));
            }
            var url = match[0];
            var trail = '';
            var trailMatch = url.match(/[.,;:!?)\]}]+$/);
            if (trailMatch) {
                trail = trailMatch[0];
                url = url.slice(0, url.length - trail.length);
            }
            if (isSafeUrl(url)) {
                var a = document.createElement('a');
                a.href = url;
                a.target = '_blank';
                a.rel = 'noopener';
                a.textContent = url;
                el.appendChild(a);
            } else {
                el.appendChild(document.createTextNode(url));
            }
            if (trail) el.appendChild(document.createTextNode(trail));
            lastIndex = match.index + match[0].length;
        }
        if (lastIndex < text.length) {
            el.appendChild(document.createTextNode(text.slice(lastIndex)));
        }
    }

    function buildMsgBubble(role, text, sources) {
        var el = document.createElement('div');
        el.className = 'pi-cb-msg pi-cb-msg--' + role;
        appendLinkifiedText(el, text);
        if (sources && sources.length) {
            var srcWrap = document.createElement('details');
            srcWrap.className = 'pi-cb-sources';

            var summary = document.createElement('summary');
            summary.textContent = 'Sources (' + sources.length + ')';
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

    /** Opens 1 bot turn — a SINGLE avatar shared by both the progress card and the final
     *  answer bubble, instead of each tier being its own separate "chat message" with its
     *  own avatar (bug already fixed: previously renderStep() created a whole new
     *  wrap+avatar for every tier, making it look like 3 separate bot messages). Returns
     *  `col` — the vertical column holding the progress card followed by the answer
     *  bubble, sharing 1 row with the avatar. */
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

    /** Attaches the final answer bubble into the SAME column as the progress card (does
     *  not create a 2nd avatar/wrap) — still saved into history/sessionStorage just like
     *  addMessage(). */
    function finishBotTurn(col, text, sources) {
        if (!isRestoring) {
            chatHistory.push({ role: 'bot', text: text, sources: sources || [] });
            sessionStorage.setItem('pi_cb_history', JSON.stringify(chatHistory));
        }
        col.appendChild(buildMsgBubble('bot', text, sources));
        messages.scrollTop = messages.scrollHeight;
    }

    /** A SINGLE card holding both the "analyzing"/"searching" rows, joined together with a
     *  divider line (see CSS .pi-cb-step-row + .pi-cb-step-row) — NOT 2 separate blocks.
     *  No separate icon per row — 1 svg icon (the bot avatar) for the whole answer turn is
     *  enough; adding a secondary icon per row would only clutter the view. */
    function renderProgressCard(col) {
        var card = document.createElement('div');
        card.className = 'pi-cb-progress';
        col.appendChild(card);
        messages.scrollTop = messages.scrollHeight;
        return card;
    }

    /** now() in milliseconds, using performance.now() when available (higher resolution,
     *  not affected by system clock drift) — Date.now() as a fallback for older browsers. */
    function nowMs() {
        return (window.performance && typeof window.performance.now === 'function')
            ? window.performance.now()
            : Date.now();
    }

    /** 1 row in the progress card — a real <details> (can be reopened after it's done),
     *  starting in a "…ing" state with 3 bouncing dots, finalized with the real label by
     *  finishStep() once that step completes. `category` is the FIXED heading for the tier
     *  ("Thinking"/"Searching data") — kept separate from `label` (the REAL result, which
     *  changes per question) so the user always knows which tier they're looking at, not
     *  just the result text. Does NOT put aria-hidden on the whole row — so screen readers
     *  can enter the <details> just like everywhere else in the widget. */
    function renderStepRow(card, kind, category, label) {
        var details = document.createElement('details');
        details.className = 'pi-cb-step-row pi-cb-step-row--' + kind;
        details.open = true;

        var summary = document.createElement('summary');

        var textCol = document.createElement('span');
        textCol.className = 'pi-cb-step-text';
        var eyebrow = document.createElement('span');
        eyebrow.className = 'pi-cb-step-eyebrow';
        eyebrow.textContent = category;
        textCol.appendChild(eyebrow);
        var labelEl = document.createElement('span');
        labelEl.className = 'pi-cb-step-label-text';
        labelEl.textContent = label;
        textCol.appendChild(labelEl);
        summary.appendChild(textCol);

        var dots = document.createElement('span');
        dots.className = 'pi-cb-step-dots';
        dots.setAttribute('aria-hidden', 'true');
        dots.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
        summary.appendChild(dots);

        var timeEl = document.createElement('span');
        timeEl.className = 'pi-cb-step-time';
        summary.appendChild(timeEl);

        details.appendChild(summary);

        var body = document.createElement('div');
        body.className = 'pi-cb-step-body';
        details.appendChild(body);

        card.appendChild(details);
        messages.scrollTop = messages.scrollHeight;
        return { details: details, labelEl: labelEl, dots: dots, body: body, timeEl: timeEl, startedAt: nowMs() };
    }

    /** Finalizes the real label + detail lines + REAL elapsed seconds for 1 row (measured
     *  with nowMs() from when the row opened to when it was finalized — the REAL time that
     *  row was shown on screen, not a made-up number), and removes the "…ing" dots. */
    function finishStep(step, label, detailLines) {
        step.labelEl.textContent = label;
        step.dots.remove();
        var seconds = (nowMs() - step.startedAt) / 1000;
        step.timeEl.textContent = seconds.toFixed(1) + 's';
        step.body.innerHTML = '';
        (detailLines || []).forEach(function (line) {
            var p = document.createElement('div');
            p.className = 'pi-cb-step-line';
            p.textContent = line;
            step.body.appendChild(p);
        });
    }

    /** Label + detail for the "analyzing" row — a SINGLE function decides both the label
     *  and its content, so the two can never drift out of sync (bug already fixed: the old
     *  label "Identified: product question" showed up EVEN when found_count=0 and the
     *  message was just "hi" — wrong, since none of is_price/contact/service/
     *  project_intent() matched and search() also returned no results, meaning the system
     *  hadn't actually "identified" anything concrete).
     *  Branches follow the exact order the real request runs in: rule-based intent first,
     *  then the router (only runs when rule-based fails), then finally the no-match case —
     *  ALWAYS has at least 1 real detail line, never left empty. */
    function describeThink(trace) {
        switch (trace.intent) {
            case 'price':
                return { label: 'Identified: pricing question', lines: ['Applying the company\'s fixed pricing policy, no product data lookup needed.'] };
            case 'contact':
                return { label: 'Identified: contact question', lines: ['Using the company\'s existing contact information.'] };
            case 'service':
                return { label: 'Identified: services question', lines: ['Using the company\'s existing service description.'] };
            case 'project':
                return { label: 'Identified: completed-project question', lines: ['Searching the list of completed projects.'] };
        }
        if (trace.router_used) {
            var lines = [];
            if (trace.router_keywords && trace.router_keywords.length) {
                lines.push('Inferred keywords: ' + trace.router_keywords.join(', '));
            }
            if (trace.router_brand) lines.push('Brand detected: ' + trace.router_brand);
            if (!lines.length) lines.push('Did additional reasoning to identify the right product.');
            return { label: 'Did additional reasoning to find the right product', lines: lines };
        }
        if (trace.found_count > 0) {
            return { label: 'Identified: product question', lines: ['Searched directly by keywords in the question.'] };
        }
        // No intent matched AND nothing was found — the correct case for a greeting/small
        // talk message, must NOT be labeled "product question" (factually wrong, already
        // caught as a bug).
        return { label: 'No specific intent detected', lines: ['No price/contact/service/project intent matched and no related product was found — giving a general reply.'] };
    }

    /** Animates EXACTLY the 3 finalized tiers, joined under 1 avatar/1 card (not split into
     *  3 separate chat messages like the previous version): (1) analyzing the question —
     *  always shown, real intent from trace.intent or the router if it ran; (2) searching
     *  the data — always shown, real keywords + real result count; (3) the answer bubble,
     *  ATTACHED TO THE SAME column as the card above. Each row opens in a "…ing" state with
     *  3 bouncing dots FIRST, holds for a beat, then finalizes with the real label and
     *  collapses. Every row's content is ALWAYS REAL (trace from class-rest.php) — no
     *  reasoning is ever made up when the router didn't run; it only uses the intent
     *  already computed (is_price_intent()/is_contact_intent()/... run for EVERY question).
     *  `stillCurrent` is a closure that checks seq — if Reset is clicked mid-animation, it
     *  stops silently, without inserting anything into the already-cleared transcript. */
    function revealTraceThenAnswer(trace, answerText, sources, stillCurrent) {
        setTyping(false);
        var col = beginBotTurn();
        if (!trace) {
            finishBotTurn(col, answerText, sources);
            return Promise.resolve();
        }

        var card = renderProgressCard(col);

        // Tier 1 — analyzing the question (always shown).
        var thinkStep = renderStepRow(card, 'thinking', 'Thinking', 'Analyzing the question');
        setStatus('Analyzing the question…');

        return wait(700).then(function () {
            if (!stillCurrent()) return;

            var think = describeThink(trace);
            finishStep(thinkStep, think.label, think.lines);
            thinkStep.details.open = false;

            // Tier 2 — searching the data (always shown, CONTINUING within the same card).
            setStatus('Searching the data…');
            var searchStep = renderStepRow(card, 'search', 'Data lookup', 'Searching the data');

            return wait(750).then(function () {
                if (!stillCurrent()) return;

                var searchLabel = trace.found_count > 0
                    ? 'Found — ' + trace.found_count + ' related results'
                    : 'Searched — no matching results found';
                var searchLines = [];
                if (trace.search_query) searchLines.push('Keywords: ' + trace.search_query);
                finishStep(searchStep, searchLabel, searchLines);
                searchStep.details.open = false;

                // Tier 3 — answer bubble, sharing the column/avatar with the card above.
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
            wrap.setAttribute('aria-hidden', 'true'); // The 3 dots are pure decoration —
            // the REAL status for screen readers lives in #pi-cb-status (setStatus()),
            // not here. Previously this was the only node in the aria-live region but it
            // never contained any text — the live region had nothing to read for 8-40 seconds.

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

    /** Turns an HTTP error into 1 English sentence — instead of unconditionally reading the
     *  response as JSON and leaving it to .catch. The 502/504 strings are INTENTIONALLY
     *  identical to the strings the server itself uses (class-rest.php) — from the user's
     *  perspective it's the same event whether the error comes from a gateway/CDN
     *  (returning HTML, not JSON) or from the plugin itself. */
    function errorTextFor(status, data) {
        if (data && typeof data.message === 'string' && data.code !== 'rest_cookie_invalid_nonce') {
            return data.message; // pre-composed string from the server — keep as is
        }
        if (status === 403) return 'Your session has expired, please reload the page and try again.';
        if (status === 429) return 'You are sending messages too quickly, please wait 1 minute.';
        if (status === 502 || status === 503 || status === 504) {
            return 'The assistant is busy, please try again in a few minutes or call our hotline.';
        }
        return 'Something went wrong, please try again.';
    }

    function send(overrideText) {
        // Guard against duplicate sends — previously Enter didn't check the button's
        // disabled state (only click did), so pressing Enter repeatedly while waiting for
        // the LLM's reply fired multiple parallel fetches. isSending now applies to both paths.
        if (isSending) { setStatus('Waiting for a reply, please wait before sending another question.'); return; }
        if (Date.now() < cooldownEnd) { setStatus(cooldownMessage()); return; }

        // typeof === 'string', NOT a truthy check — sendBtn.addEventListener('click', send)
        // passes the MouseEvent directly as the first argument; a truthy check would send
        // "[object MouseEvent]" to the LLM every time the send button is clicked with the mouse.
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
        setStatus('Sending your question, the assistant is composing a reply…');
        setTyping(true);

        controller = new AbortController();
        slowTimer = setTimeout(function () {
            if (seq !== requestSeq) return;
            setStatus('Still looking up the information, this question needs a bit more time…');
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
                // Read as text and parse JSON manually in a try/catch — instead of calling
                // res.json() unconditionally. Previously a genuine HTML error page
                // (nginx/Cloudflare/PHP-FPM returning a 502/504 as HTML in the worst case
                // ~40s in) made the promise reject, falling straight into .catch reporting
                // "couldn't connect" — WRONG, the server DID respond, it just wasn't JSON.
                return res.text().then(function (raw) {
                    var data = null;
                    try { data = raw ? JSON.parse(raw) : null; } catch (e) { /* HTML error page */ }
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (r) {
                if (seq !== requestSeq) return;
                // Clear the timers AS SOON AS we have a response — don't wait for
                // resetSendState() in .finally() (now delayed until the animation
                // finishes, ~1.1s). Without clearing them early, slowTimer/hardTimer could
                // fire "still searching"/abort right in the middle of displaying the 2
                // "Searched"/"Analyzed" blocks for a question that's ALREADY done.
                clearTimers();
                showSlowHint(false);
                if (r.ok) {
                    if (r.data && typeof r.data.answer === 'string') {
                        // Return the promise of the sequential animation so the .finally()
                        // below waits for it to finish before returning the panel to idle —
                        // otherwise resetSendState() (removing readonly, re-enabling send)
                        // would run BEFORE the 2 "Searched"/"Analyzed" blocks finish animating.
                        return revealTraceThenAnswer(r.data.trace, r.data.answer, r.data.sources, function () {
                            return seq === requestSeq;
                        });
                    }
                    addMessage('error', 'Something went wrong, please try again.');
                    return;
                }
                if (r.status === 429) startCooldown(60000);
                addMessage('error', errorTextFor(r.status, r.data));
            })
            .catch(function () {
                if (seq !== requestSeq) return;
                if (timedOut) {
                    addMessage('error', 'The assistant is taking longer than usual to respond. Please try again or call our hotline.');
                } else {
                    // Only a genuine network error reaches here — a request aborted by
                    // Reset already changed seq in the branch above, so it never falls
                    // through to this point.
                    addMessage('error', 'Could not connect to the AI assistant. Please try again later.');
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
        if (isSending) { setStatus('Waiting for a reply, please wait before sending another question.'); return; }
        if (Date.now() < cooldownEnd) { setStatus(cooldownMessage()); return; }
        send();
    });

    // Initialize the starting state if the user already had the panel open on the previous page.
    if (isOpen) {
        setOpen(true);
    }
})();
