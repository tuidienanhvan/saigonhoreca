/**
 * Pi Chatbot — admin page enhancements.
 *
 * initKeyList() turns a plain "one key per line" textarea into a list of
 * individual, maskable input rows with add/remove buttons. The textarea
 * itself is NOT removed — it stays in the DOM (visually hidden once JS
 * confirms it's running) as the actual field the form submits, kept in sync
 * on every change. That is deliberate: with JavaScript disabled, the admin
 * still sees and can edit the original plain textarea exactly as before —
 * there is no code path where a key can only be entered through JS.
 */
(function () {
    'use strict';

    function splitKeys(raw) {
        return String(raw || '')
            .split(/\r\n|\r|\n/)
            .map(function (s) { return s.trim(); })
            .filter(function (s) { return s !== ''; });
    }

    function initKeyList(textarea) {
        if (!textarea || textarea.dataset.piEnhanced) return;
        textarea.dataset.piEnhanced = '1';

        var keys = splitKeys(textarea.value);

        var wrap = document.createElement('div');
        wrap.className = 'pi-key-list';

        var emptyNote = document.createElement('p');
        emptyNote.className = 'pi-key-empty';
        emptyNote.textContent = 'No keys yet — click "Add key" to enter one.';

        function sync() {
            var rows = wrap.querySelectorAll('.pi-key-row input');
            var values = [];
            rows.forEach(function (input) {
                var v = input.value.trim();
                if (v !== '') values.push(v);
            });
            textarea.value = values.join('\n');
            emptyNote.hidden = rows.length > 0;
            wrap.querySelectorAll('.pi-key-idx').forEach(function (el, i) {
                el.textContent = (i + 1) + '.';
            });
        }

        function addRow(value) {
            var row = document.createElement('div');
            row.className = 'pi-key-row';

            var idx = document.createElement('span');
            idx.className = 'pi-key-idx';

            var input = document.createElement('input');
            input.type = 'password';
            input.value = value || '';
            input.placeholder = 'sk-... / LLM7 key';
            input.autocomplete = 'off';
            input.spellcheck = false;
            input.addEventListener('input', sync);

            var toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.className = 'pi-key-icon-btn pi-key-toggle';
            toggleBtn.setAttribute('aria-label', 'Show/hide key');
            toggleBtn.innerHTML = '<span class="dashicons dashicons-visibility" aria-hidden="true"></span>';
            toggleBtn.addEventListener('click', function () {
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                toggleBtn.querySelector('.dashicons').className =
                    showing ? 'dashicons dashicons-visibility' : 'dashicons dashicons-hidden';
            });

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'pi-key-icon-btn pi-key-remove';
            removeBtn.setAttribute('aria-label', 'Remove this key');
            removeBtn.innerHTML = '<span class="dashicons dashicons-no-alt" aria-hidden="true"></span>';
            removeBtn.addEventListener('click', function () {
                row.remove();
                sync();
            });

            row.appendChild(idx);
            row.appendChild(input);
            row.appendChild(toggleBtn);
            row.appendChild(removeBtn);
            wrap.insertBefore(row, emptyNote);
            sync();
            return input;
        }

        var addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'pi-key-add';
        addBtn.innerHTML = '<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span> Add key';
        addBtn.addEventListener('click', function () {
            var input = addRow('');
            input.type = 'text'; // a freshly added empty row is easiest to verify unmasked while typing
            input.focus();
        });

        // emptyNote must already be a CHILD of wrap before the loop below runs —
        // addRow() calls wrap.insertBefore(row, emptyNote), and insertBefore()
        // throws NotFoundError (uncaught, aborting the rest of this function
        // silently) if the reference node isn't in the tree yet. This was a
        // real bug, not theoretical: confirmed live — every textarea got its
        // dataset.piEnhanced flag set (the very first line of this function)
        // but never got the 'pi-enhanced' class or its key-list UI, because
        // execution died on the FIRST addRow() call, right here.
        wrap.appendChild(emptyNote);
        keys.forEach(function (k) { addRow(k); });
        wrap.appendChild(addBtn);
        sync();

        textarea.insertAdjacentElement('afterend', wrap);
        textarea.classList.add('pi-enhanced');

        // The surrounding <form> submits the textarea's value normally — no
        // submit handler needed. sync() has already kept it current on every
        // keystroke/add/remove by the time the browser serializes the form.
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('textarea.pi-key-list-source').forEach(initKeyList);
    });
})();
