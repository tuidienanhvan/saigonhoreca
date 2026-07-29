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
    var cooldownEnd = 0;
    var cooldownTick = null;
    var isSending = false;
    // The handle returned by PiChatbotUI.typeAnswerInto() for whichever answer is
    // CURRENTLY being typed out — null when nothing is typing. Reset must cancel this
    // explicitly: the typing loop runs on its own setTimeout chain outside the
    // send()/revealTraceThenAnswer() promise chain, so requestSeq alone (which only
    // gates callbacks INSIDE that chain) cannot stop it from continuing to type into a
    // bubble whose column may already be detached from a cleared transcript.
    var activeTyping = null;

    // 75s. The previous 45s was sized against the fast path's worst case (20s generator
    // timeout x 2 fallback keys). The adaptive max-accuracy path (see class-rest.php) adds
    // an LLM reranking pass and a fact check that can itself trigger one regeneration —
    // worst case roughly router 8s + rerank 15s + generate 40s + verify 12s. The server
    // raises its own limit to 60s for that path; the client must sit ABOVE the server's
    // ceiling, otherwise the browser aborts a request that was about to succeed and the
    // customer sees a timeout for work that actually completed.
    //
    // Note this does NOT slow the common case: an easy question still returns in ~10s.
    var HARD_TIMEOUT_MS = 75000;

    // Multi-turn context — how many PRIOR exchanges (user+bot pairs) are sent alongside a
    // new question, so the router can resolve a follow-up ("giá bao nhiêu?", "cái đó dày
    // bao nhiêu?") against the product already being discussed instead of treating every
    // message as if it arrived with no history. 2 pairs, not the whole session: enough for
    // the common "ask about a product, then ask a follow-up or two" pattern, without the
    // prompt (and therefore the LLM7 token cost + latency) growing with a long session.
    var MAX_HISTORY_TURNS = 2;
    // Per-message cap, not per-conversation — a single long answer (sources list, a past
    // detailed product description) must not alone blow the whole history payload past what
    // is useful context; the ROUTER only needs enough of a prior answer to know what product
    // was being discussed, not the full text.
    var MAX_HISTORY_CHARS = 300;

    /** Turns the display-oriented chatHistory (role/text/sources, already serialized to
     *  sessionStorage for re-rendering) into the minimal {role, text} payload sent to the
     *  server — sources are never sent (the server already has its own source-of-truth
     *  for what it retrieved last turn; forwarding the client's copy back would let a
     *  tampered client feed the router fabricated "prior sources"). Takes the LAST
     *  MAX_HISTORY_TURNS pairs, oldest first — a customer 10 messages into a conversation
     *  doesn't need turn #2 competing with turn #9 for the model's attention. */
    function buildHistoryPayload(history) {
        if (!history || !history.length) return [];
        var trimmed = history.slice(-MAX_HISTORY_TURNS * 2);
        return trimmed.map(function (m) {
            var t = String(m.text || '');
            if (t.length > MAX_HISTORY_CHARS) t = t.slice(0, MAX_HISTORY_CHARS) + '…';
            return { role: m.role, text: t };
        });
    }

    var FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), textarea, summary, [tabindex]:not([tabindex="-1"])';

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
                addMessage(msg.role, msg.text, msg.sources, msg.feedbackId);
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
            if (!hasUserMessage) addQuickReplies(PiChatbotUI.QUICK_REPLIES);
        } else {
            addMessage('bot', 'Hello!\nI\'m Saigon Horeca\'s AI assistant. What can I help you with today?');
            addQuickReplies(PiChatbotUI.QUICK_REPLIES);
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
        if (activeTyping) { activeTyping.cancel(); activeTyping = null; }
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

    function addMessage(role, text, sources, feedbackId) {
        if (!isRestoring && (role === 'user' || role === 'bot')) {
            chatHistory.push({ role: role, text: text, sources: sources || [], feedbackId: feedbackId || null });
            sessionStorage.setItem('pi_cb_history', JSON.stringify(chatHistory));
        }

        var wrap = document.createElement('div');
        wrap.className = 'pi-cb-msg-wrap pi-cb-msg-wrap--' + role;

        if (role === 'bot' || role === 'error') {
            var avatar = document.createElement('div');
            avatar.className = 'pi-cb-avatar';
            avatar.innerHTML = PiChatbotUI.BOT_SVG;
            wrap.appendChild(avatar);
        }

        var el = PiChatbotUI.buildMsgBubble(role, text, sources, feedbackId);
        wrap.appendChild(el);
        messages.appendChild(wrap);
        messages.scrollTop = messages.scrollHeight;
        return el;
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
        avatar.innerHTML = PiChatbotUI.BOT_SVG;
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
     *  addMessage(). The answer TYPES itself out (PiChatbotUI.typeAnswerInto()) rather
     *  than appearing all at once — the 3 progress rows already finalize one at a time
     *  to build anticipation, and the final answer dumping in as a single instant block
     *  broke that pacing. Only for a FRESH answer: restoring saved history on page
     *  reload uses the instant buildMsgBubble() path — a customer reopening the panel
     *  should see their past conversation immediately, not watch it retype itself. */
    function finishBotTurn(col, text, sources, feedbackId, showLeadForm, lastQuestion) {
        if (!isRestoring) {
            chatHistory.push({ role: 'bot', text: text, sources: sources || [], feedbackId: feedbackId || null });
            sessionStorage.setItem('pi_cb_history', JSON.stringify(chatHistory));
        }
        if (isRestoring) {
            // Lead form is deliberately NOT re-shown when restoring saved history on
            // reload — a visitor reopening the panel is reviewing past chat, not
            // being re-prompted for their phone number a second time for the same
            // price question.
            col.appendChild(PiChatbotUI.buildMsgBubble('bot', text, sources, feedbackId));
            messages.scrollTop = messages.scrollHeight;
            return;
        }
        var el = document.createElement('div');
        el.className = 'pi-cb-msg pi-cb-msg--bot';
        col.appendChild(el);
        activeTyping = PiChatbotUI.typeAnswerInto(el, text, sources, {
            feedbackId: feedbackId,
            showLeadForm: showLeadForm,
            lastQuestion: lastQuestion,
            onTick: function () { messages.scrollTop = messages.scrollHeight; },
            onDone: function () {
                activeTyping = null;
                messages.scrollTop = messages.scrollHeight;
            }
        });
        messages.scrollTop = messages.scrollHeight;
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
    function revealTraceThenAnswer(trace, answerText, sources, feedbackId, stillCurrent, lastQuestion) {
        setTyping(false);
        var col = beginBotTurn();
        // Lead capture (GLM 5.2 report recommendation C) triggers on intent='price'
        // ONLY — the one moment the router has already confirmed the visitor is
        // asking about cost, i.e. showing real buying intent, not on every answer.
        var showLeadForm = !!(trace && trace.intent === 'price');
        if (!trace) {
            finishBotTurn(col, answerText, sources, feedbackId, showLeadForm, lastQuestion);
            return Promise.resolve();
        }

        var card = PiChatbotUI.renderProgressCard(col);

        // Tier 1 — "Thinking": a real, always-true first step — the assistant
        // has RECEIVED the message and is about to process it — kept
        // deliberately generic (never claims a specific finding) so it stays
        // honest even before the router has run. Split out as its own tier
        // (2026-07-29 fix) because it was previously MERGED into tier 2's row
        // (the row opened as "Analyzing the question…" then silently morphed
        // into "Identified: X" once classification finished) — visually that
        // looked like only 2 categorized tiers (DATA LOOKUP/FACT CHECK) plus
        // one uncategorized row, an inconsistent look flagged live.
        var readStep = PiChatbotUI.renderStepRow(card, 'thinking', 'Thinking', 'Reading your question');
        setStatus('Reading your question…');

        return PiChatbotUI.wait(400).then(function () {
            if (!stillCurrent()) return;
            PiChatbotUI.finishStep(readStep, 'Question received', ['Preparing to analyze your message.']);
            readStep.details.open = false;

            // Tier 2 — the REAL classification result (router intent/keywords/
            // brand, or the fast-path fallback) — same describeThink() data as
            // before, now under its own category label for the same visual
            // treatment as tiers 3/4 below.
            var thinkStep = PiChatbotUI.renderStepRow(card, 'thinking', 'Identifying', 'Analyzing the question');
            setStatus('Analyzing the question…');

            return PiChatbotUI.wait(700).then(function () {
                if (!stillCurrent()) return;

                var think = PiChatbotUI.describeThink(trace);
                PiChatbotUI.finishStep(thinkStep, think.label, think.lines);
                thinkStep.details.open = false;

                // Tier 3 — searching the data (always shown, CONTINUING within the same card).
                setStatus('Searching the data…');
                var searchStep = PiChatbotUI.renderStepRow(card, 'search', 'Data lookup', 'Searching the data');

                return PiChatbotUI.wait(750).then(function () {
                    if (!stillCurrent()) return;

                    var search = PiChatbotUI.describeSearch(trace);
                    PiChatbotUI.finishStep(searchStep, search.label, search.lines);
                    searchStep.details.open = false;

                    if (!PiChatbotUI.needsVerifyStep(trace)) {
                        // Tier 4 — answer bubble, sharing the column/avatar with the card above.
                        finishBotTurn(col, answerText, sources, feedbackId, showLeadForm, lastQuestion);
                        return;
                    }

                    // Optional tier — the fact check, shown ONLY when it actually ran, so
                    // the customer never sees a "checked" claim on an unchecked answer.
                    setStatus('Checking the answer…');
                    var verifyStep = PiChatbotUI.renderStepRow(card, 'search', 'Fact check', 'Checking the answer against the data');

                    return PiChatbotUI.wait(600).then(function () {
                        if (!stillCurrent()) return;
                        var v = PiChatbotUI.describeVerify(trace);
                        PiChatbotUI.finishStep(verifyStep, v.label, v.lines);
                        verifyStep.details.open = false;
                        finishBotTurn(col, answerText, sources, feedbackId, showLeadForm, lastQuestion);
                    });
                });
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

    /** The pre-existing buffered POST /ask path — unchanged behavior, just
     *  extracted into its own function so send() can try streaming FIRST and
     *  fall back to this exact flow when the server declines to stream (see
     *  trySendStreaming()'s docblock for why a decline is common and
     *  expected, not an error). $timedOutState is a 1-key object instead of a
     *  plain variable so the shared hardTimer's callback (armed once in
     *  send(), before either path runs) can flip a flag this function still
     *  sees after control has passed to it. */
    function sendBuffered(text, priorHistory, seq, timedOutState) {
        return fetch(PiChatbotConfig.restUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': PiChatbotConfig.nonce
            },
            body: JSON.stringify({ message: text, history: priorHistory }),
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
                // Clear the timer AS SOON AS we have a response — don't wait for
                // resetSendState() in .finally() (now delayed until the animation
                // finishes, ~1.1s). Without clearing it early, hardTimer could fire an
                // abort() right in the middle of displaying the progress card for a
                // question that's ALREADY done.
                clearTimers();
                if (r.ok) {
                    if (r.data && typeof r.data.answer === 'string') {
                        // Return the promise of the sequential animation so the .finally()
                        // below waits for it to finish before returning the panel to idle —
                        // otherwise resetSendState() (removing readonly, re-enabling send)
                        // would run BEFORE the 2 "Searched"/"Analyzed" blocks finish animating.
                        return revealTraceThenAnswer(r.data.trace, r.data.answer, r.data.sources, r.data.feedback_id, function () {
                            return seq === requestSeq;
                        }, text);
                    }
                    addMessage('error', 'Something went wrong, please try again.');
                    return;
                }
                if (r.status === 429) startCooldown(60000);
                addMessage('error', errorTextFor(r.status, r.data));
            })
            .catch(function () {
                if (seq !== requestSeq) return;
                if (timedOutState.timedOut) {
                    addMessage('error', 'The assistant is taking longer than usual to respond. Please try again or call our hotline.');
                } else {
                    // Only a genuine network error reaches here — a request aborted by
                    // Reset already changed seq in the branch above, so it never falls
                    // through to this point.
                    addMessage('error', 'Could not connect to the AI assistant. Please try again later.');
                }
            });
    }

    /**
     * SSE attempt — tries POST /ask-stream first, so a question the server
     * judges safe to stream (see class-rest.php ask_stream()'s docblock:
     * conversational replies with no retrieved product context, where the
     * faithfulness check can never apply) shows its first characters within
     * ~1-2s instead of only once the whole answer is composed server-side
     * (up to ~9.6s measured — see Log::stats_latency()'s docblock).
     *
     * The SERVER decides eligibility, not this function — most real
     * questions (anything that retrieves product data) are correctly
     * declined, and that decline is the EXPECTED, common outcome, not a
     * failure: this function resolves `false` in that case and send() falls
     * straight through to the existing, unchanged sendBuffered() path with
     * no visible difference to the customer.
     *
     * Resolves `true` when this function fully handled the turn (streamed an
     * answer, or showed a genuine LLM error for the rare mid-stream
     * failure) — send() must NOT also run sendBuffered() in that case, which
     * would ask the same question twice. Resolves `false` for every case
     * that should fall back: not eligible, older/unsupported browser (no
     * fetch streaming body reader), or a network-level failure reaching the
     * stream endpoint at all (the buffered endpoint gets its own honest
     * attempt rather than assuming the whole connection is down).
     */
    function trySendStreaming(text, priorHistory, seq) {
        if (!PiChatbotConfig.streamUrl || !window.fetch || typeof ReadableStream === 'undefined') {
            return Promise.resolve(false);
        }

        return fetch(PiChatbotConfig.streamUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': PiChatbotConfig.nonce
            },
            body: JSON.stringify({ message: text, history: priorHistory }),
            signal: controller.signal
        }).then(function (res) {
            if (seq !== requestSeq) return true; // stale — swallow, do not also try buffered
            var contentType = res.headers.get('content-type') || '';
            if (!res.ok || contentType.indexOf('text/event-stream') === -1 || !res.body) {
                // Not eligible (server replied plain JSON {eligible:false}) or this
                // response can't be streamed — fall back, this is the common path.
                return false;
            }

            clearTimers();
            setTyping(false);
            var col = beginBotTurn();

            var reader = res.body.getReader();
            var decoder = new TextDecoder();
            var buffer = '';
            var fullText = '';
            var streamEnded = false;
            var finalPayload = null;
            var traceEvent = null;
            var traceResolve;
            // Resolved the moment the 'meta' event arrives (near-instant — the
            // server sends it before generation even starts) — the tier
            // animation below awaits this instead of a raw event flag so it
            // can start the SAME instant the trace is known, same as the
            // buffered path's revealTraceThenAnswer() does with data it
            // already has synchronously.
            var tracePromise = new Promise(function (resolve) { traceResolve = resolve; });

            function handleEvent(payload) {
                if (payload.type === 'meta') {
                    traceEvent = payload.trace;
                    traceResolve();
                } else if (payload.type === 'delta' && typeof payload.text === 'string') {
                    fullText += payload.text;
                } else if (payload.type === 'done' || payload.type === 'error') {
                    finalPayload = payload;
                }
            }

            function pump() {
                return reader.read().then(function (chunk) {
                    if (seq !== requestSeq) return;
                    if (chunk.done) { streamEnded = true; return; }
                    buffer += decoder.decode(chunk.value, { stream: true });
                    var parts = buffer.split('\n\n');
                    buffer = parts.pop(); // last part may be incomplete, kept for the next chunk
                    parts.forEach(function (part) {
                        var line = part.trim();
                        if (line.indexOf('data:') !== 0) return;
                        var payload;
                        try { payload = JSON.parse(line.slice(5).trim()); } catch (e) { return; }
                        handleEvent(payload);
                    });
                    return pump();
                });
            }
            // Runs in the background, filling fullText/finalPayload as bytes
            // arrive — deliberately not awaited before revealing text (see
            // stagedAnimation.then() below). A read() failure mid-stream
            // (dropped connection) must still let revealLoop() finish instead
            // of spinning forever waiting for a streamEnded that will never
            // come otherwise.
            pump().catch(function () { streamEnded = true; });

            /** Reveals `fullText` at the SAME steady cadence typeAnswerInto()
             *  uses for the buffered path (TYPE_CHARS_PER_TICK/TYPE_TICK_MS) —
             *  instead of dumping el.textContent = fullText on every raw
             *  network read, which arrives in bursty, unevenly-sized chunks
             *  (OS/TCP buffering has nothing to do with how many characters
             *  the model actually wrote in one moment) and looked jumpy/
             *  inconsistent rather than typed. This decouples "how the network
             *  delivered bytes" from "how the text visibly appears", the same
             *  separation of concerns typeAnswerInto already has between a
             *  known string and its reveal pace — reveal never gets AHEAD of
             *  what has actually arrived, only ever catches up to it. */
            function revealLoop(el, onDone) {
                var shown = 0;
                function tick() {
                    if (seq !== requestSeq) return;
                    var target = fullText.length;
                    if (shown < target) {
                        shown = Math.min(target, shown + PiChatbotUI.TYPE_CHARS_PER_TICK);
                        el.textContent = fullText.slice(0, shown);
                        messages.scrollTop = messages.scrollHeight;
                    }
                    if (shown < fullText.length || !streamEnded) {
                        setTimeout(tick, PiChatbotUI.TYPE_TICK_MS);
                    } else {
                        onDone();
                    }
                }
                tick();
            }

            // Same staged "Analyzing" -> "Searching" progress tiers the buffered
            // path shows (revealTraceThenAnswer()), built from the SAME real
            // trace data (now arriving via the 'meta' event instead of already
            // being in hand) — this is what was MISSING from the first version
            // of this function: streaming replaced the whole progress card with
            // a bare bubble instead of keeping the reasoning steps the customer
            // is used to seeing. Runs CONCURRENTLY with pump() (both are async
            // and just interleave on the event loop) — generation is already
            // happening server-side while this ~1.45s animation plays, so it
            // costs no extra wall-clock time on top of streaming, unlike the
            // buffered path where the full request must finish FIRST and only
            // then does this same animation run.
            var stagedAnimation = tracePromise.then(function () {
                if (seq !== requestSeq || !traceEvent) return;
                var card = PiChatbotUI.renderProgressCard(col);
                // Same 4-tier shape as the buffered path's revealTraceThenAnswer()
                // (Thinking -> Identifying -> Data lookup -> [Fact check]) — kept
                // in sync so the customer sees an identical progress card whether
                // this question streamed or not.
                var readStep = PiChatbotUI.renderStepRow(card, 'thinking', 'Thinking', 'Reading your question');
                setStatus('Reading your question…');
                return PiChatbotUI.wait(400).then(function () {
                    if (seq !== requestSeq) return;
                    PiChatbotUI.finishStep(readStep, 'Question received', ['Preparing to analyze your message.']);
                    readStep.details.open = false;

                    var thinkStep = PiChatbotUI.renderStepRow(card, 'thinking', 'Identifying', 'Analyzing the question');
                    setStatus('Analyzing the question…');
                    return PiChatbotUI.wait(700).then(function () {
                        if (seq !== requestSeq) return;
                        var think = PiChatbotUI.describeThink(traceEvent);
                        PiChatbotUI.finishStep(thinkStep, think.label, think.lines);
                        thinkStep.details.open = false;

                        setStatus('Searching the data…');
                        var searchStep = PiChatbotUI.renderStepRow(card, 'search', 'Data lookup', 'Searching the data');
                        return PiChatbotUI.wait(750).then(function () {
                            if (seq !== requestSeq) return;
                            var search = PiChatbotUI.describeSearch(traceEvent);
                            PiChatbotUI.finishStep(searchStep, search.label, search.lines);
                            searchStep.details.open = false;
                        });
                    });
                });
            });

            // Once the tiers finish, start REVEALING whatever text has already
            // arrived (fullText keeps growing in the background via pump()) at
            // the steady typewriter pace — revealLoop() naturally keeps going
            // past what's arrived SO FAR and catches up as more comes in
            // (its own loop condition checks `!streamEnded` too), so this does
            // NOT wait for the whole answer to finish generating first; it
            // only waits for the progress tiers, which are usually the
            // limiting factor for a short conversational reply anyway.
            return stagedAnimation.then(function () {
                if (seq !== requestSeq) return true;
                return new Promise(function (resolveTurn) {
                    var el = document.createElement('div');
                    el.className = 'pi-cb-msg pi-cb-msg--bot';
                    col.appendChild(el);
                    revealLoop(el, function () {
                        if (seq !== requestSeq) { resolveTurn(); return; }
                        if (finalPayload && finalPayload.type === 'error') {
                            // Rare: generation started streaming then failed mid-flight
                            // (an LLM7 connectivity blip). The partial bubble already
                            // shown cannot be un-shown, so it's removed and replaced
                            // with the same error bubble the buffered path would show.
                            col.parentNode.removeChild(col);
                            addMessage('error', errorTextFor(502, null));
                            resolveTurn();
                            return;
                        }
                        var sources = (finalPayload && finalPayload.sources) || [];
                        var feedbackId = finalPayload && finalPayload.feedback_id;
                        PiChatbotUI.finalizeAnswerContent(el, fullText, sources, feedbackId, {});
                        if (!isRestoring) {
                            chatHistory.push({ role: 'bot', text: fullText, sources: sources, feedbackId: feedbackId || null });
                            sessionStorage.setItem('pi_cb_history', JSON.stringify(chatHistory));
                        }
                        messages.scrollTop = messages.scrollHeight;
                        resolveTurn();
                    });
                });
            }).then(function () { return true; });
        }).catch(function () {
            if (seq !== requestSeq) return true;
            // A network-level failure reaching /ask-stream itself (not a mid-stream
            // error, which is handled above) — fall back and give the buffered
            // endpoint its own honest attempt, same as if streaming had simply
            // declined.
            return false;
        });
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
        var timedOutState = { timedOut: false };

        // Snapshot BEFORE addMessage() pushes this turn's own question — chatHistory is
        // otherwise mutated in place, and the point here is the customer's PRIOR turns
        // (so the backend can resolve "cái này"/"giá bao nhiêu" against the product
        // already being discussed), not the message being sent right now.
        var priorHistory = buildHistoryPayload(chatHistory);

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
        hardTimer = setTimeout(function () {
            if (seq !== requestSeq) return;
            timedOutState.timedOut = true;
            controller.abort();
        }, HARD_TIMEOUT_MS);

        trySendStreaming(text, priorHistory, seq)
            .then(function (handled) {
                if (seq !== requestSeq) return;
                if (handled) return;
                return sendBuffered(text, priorHistory, seq, timedOutState);
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
