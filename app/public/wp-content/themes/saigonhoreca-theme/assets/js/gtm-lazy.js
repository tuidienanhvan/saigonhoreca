(function () {
    var config = window.SH_GTM || {};
    var containerId = config.id || '';

    if (!containerId || window.__shGtmLoaded) {
        return;
    }

    function hasConsent() {
        try {
            return localStorage.getItem('sh_cookies_accepted') === '1';
        } catch (e) {
            return false;
        }
    }

    function loadGtm() {
        if (window.__shGtmLoaded) {
            return;
        }
        if (!hasConsent()) {
            return;
        }

        window.__shGtmLoaded = true;
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'gtm.start': Date.now(),
            event: 'gtm.js'
        });

        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(containerId);
        document.head.appendChild(script);
    }

    if (!hasConsent()) {
        window.addEventListener('sh:cookies-accepted', loadGtm, { once: true });
        return;
    }

    var shouldDefer = config.defer !== false;
    if (!shouldDefer) {
        loadGtm();
        return;
    }

    var timeoutMs = Number(config.timeoutMs) || 3500;
    var triggerEvents = ['pointerdown', 'keydown', 'touchstart', 'scroll', 'mousemove'];
    var timerId = null;

    function removeListeners() {
        for (var i = 0; i < triggerEvents.length; i += 1) {
            window.removeEventListener(triggerEvents[i], onTrigger, { passive: true });
        }
    }

    function onTrigger() {
        if (timerId) {
            clearTimeout(timerId);
        }
        removeListeners();
        loadGtm();
    }

    for (var i = 0; i < triggerEvents.length; i += 1) {
        window.addEventListener(triggerEvents[i], onTrigger, { passive: true, once: true });
    }

    timerId = setTimeout(onTrigger, timeoutMs);
})();

