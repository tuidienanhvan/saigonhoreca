/**
 * Pi Chatbot — presentational layer. Every function here is a pure DOM builder or pure
 * trace->text mapper: no chat history, no timers, no network, no reference to the panel's
 * own state. Exposed on window.PiChatbotUI so chatbot.js (which owns all of that state)
 * can call into it without either file needing a bundler or ES modules. This split exists
 * so the "components" (message bubble, progress card/step rows, quick-reply buttons, the
 * copy that describes what the router/retriever did) can be read and changed without also
 * reading the network/state-machine code in chatbot.js, and vice versa.
 */
(function () {
    var BOT_SVG = '<svg class="pi-cb-bot-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1M20 11a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1" /><path d="M12 6V3M10 3h4" /><rect x="5" y="6" width="14" height="12" rx="3" /><circle cx="9" cy="11" r="1" fill="currentColor" /><circle cx="15" cy="11" r="1" fill="currentColor" /><path d="M10 14a2 2 0 0 0 4 0" /></svg>';

    // Displayed label is SEPARATE from the text actually sent — previously the send button
    // sent the label with its emoji straight through as the question ("[emoji] Quote"), which
    // after stopword filtering left only "quote" (since other words were stopwords) → matched
    // 6 random products as "Sources" (a real, measured bug). Cache is also split by emoji at
    // exactly these 4 highest-traffic entry points.
    //
    // Each `query` string below must never surface a spurious product: it is the raw text
    // Pi_Chatbot_Rest::ask() falls back to searching directly (no LLM router) whenever the
    // router is unavailable (disabled / circuit-open / LLM7 down) — see class-rest.php's
    // "Router unavailable" branch. This invariant is NOT hand-verified once and trusted —
    // that was tried and it silently went stale (a catalog import added a product whose
    // description happened to say "...for all YOUR meat ageing needs", and the fallback
    // started surfacing it for the Contact button, because "your" wasn't yet filtered as an
    // English stopword). It is now enforced by an automated test that re-checks these exact
    // 4 strings against the live catalog on every run:
    // tests/suite-retrieval.php, "the widget quick-reply buttons never leak a product". If
    // you add a 5th quick reply, add its `query` string to that test too.
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

    /** now() in milliseconds, using performance.now() when available (higher resolution,
     *  not affected by system clock drift) — Date.now() as a fallback for older browsers. */
    function nowMs() {
        return (window.performance && typeof window.performance.now === 'function')
            ? window.performance.now()
            : Date.now();
    }

    function wait(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    /** Only accepts http/https links — sources come from an internal get_permalink() call
     * so they're always safe today, but we still build them via the DOM API + scheme check
     * instead of concatenating into innerHTML, to stay consistent with how the title is
     * already escaped (audit flag: an inconsistency between an escaped title and a raw
     * concatenated url is a latent XSS if the data source ever changes). */
    function isSafeUrl(url) {
        return typeof url === 'string' && /^https?:\/\//i.test(url);
    }

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

    /** Builds just the bubble part (without the wrap/avatar) — chatbot.js's addMessage()
     *  uses this for the normal flow; revealTraceThenAnswer() reuses it to attach the
     *  answer bubble to the SAME avatar as the progress card, instead of creating a
     *  second avatar. Pure: takes role/text/sources, returns a detached element, touches
     *  no chat history or DOM outside what it returns. */
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

    /** A SINGLE card holding the "analyzing"/"searching"/"fact check" rows, joined
     *  together with a divider line (see CSS .pi-cb-step-row + .pi-cb-step-row) — NOT
     *  separate blocks. No separate icon per row — 1 svg icon (the bot avatar) for the
     *  whole answer turn is enough. */
    function renderProgressCard(col) {
        var card = document.createElement('div');
        card.className = 'pi-cb-progress';
        col.appendChild(card);
        return card;
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
            // Only stated when the server actually took the max-accuracy path — this
            // reads to the customer as an explanation for a longer wait, so it must
            // never appear on a request that in fact took the fast path.
            if (trace.mode === 'max') {
                lines.push(trace.ambiguity_reason
                    ? 'This question needs care: ' + trace.ambiguity_reason
                    : 'This question has more than one possible answer, so a deeper search is running.');
            }
            if (!lines.length) lines.push('Did additional reasoning to identify the right product.');
            return {
                label: trace.mode === 'max'
                    ? 'Ran a deeper analysis to find the right product'
                    : 'Did additional reasoning to find the right product',
                lines: lines
            };
        }
        if (trace.found_count > 0) {
            return { label: 'Identified: product question', lines: ['Searched directly by keywords in the question.'] };
        }
        // No intent matched AND nothing was found — the correct case for a greeting/small
        // talk message, must NOT be labeled "product question" (factually wrong, already
        // caught as a bug).
        return { label: 'No specific intent detected', lines: ['No price/contact/service/project intent matched and no related product was found — giving a general reply.'] };
    }

    /** The fact-check row is shown when the server reports a verdict (true OR false) or
     *  when it deliberately withheld the retrieved results as irrelevant. `verified` is
     *  null both on the fast path and when the check could not run — neither of those may
     *  render a row, which is why this tests for null explicitly rather than truthiness. */
    function needsVerifyStep(trace) {
        return trace.mode === 'max' && (trace.verified === true || trace.verified === false || trace.low_confidence === true);
    }

    function describeVerify(trace) {
        if (trace.low_confidence) {
            return {
                label: 'No confident match — answering honestly',
                lines: ['None of the products found were a genuine match for your question, so nothing was guessed.']
            };
        }
        if (trace.verified === false) {
            return {
                label: 'Rewritten after the fact check',
                lines: ['A claim was not backed by the product data, so the answer was written again from the data only.']
            };
        }
        return {
            label: 'Checked — every detail comes from the product data',
            lines: ['The answer was compared against the retrieved products; nothing was invented.']
        };
    }

    window.PiChatbotUI = {
        BOT_SVG: BOT_SVG,
        QUICK_REPLIES: QUICK_REPLIES,
        nowMs: nowMs,
        wait: wait,
        isSafeUrl: isSafeUrl,
        appendLinkifiedText: appendLinkifiedText,
        buildMsgBubble: buildMsgBubble,
        renderProgressCard: renderProgressCard,
        renderStepRow: renderStepRow,
        finishStep: finishStep,
        describeThink: describeThink,
        needsVerifyStep: needsVerifyStep,
        describeVerify: describeVerify
    };
})();
