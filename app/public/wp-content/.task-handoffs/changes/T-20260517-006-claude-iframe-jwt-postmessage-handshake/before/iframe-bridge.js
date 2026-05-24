/**
 * pi-api iframe bridge — proxies postMessage between the parent WP admin
 * page and the embedded dashboard webapp.
 *
 * Config (window.PiApiIframe, set by wp_localize_script):
 *   backendOrigin  string  Expected origin of the iframe (PI_API_DASHBOARD_URL host)
 *   ajaxUrl        string  admin-ajax.php URL
 *   refreshAction  string  Ajax `action` for JWT refresh (pi_api_refresh_jwt)
 *   nonce          string  WP nonce for the refresh action
 *   siteUrl        string  Parent site URL forwarded to the webapp
 *   wpVersion      string  WP version for telemetry
 *   loaderTimeout  number  Milliseconds before showing the connection-error UI
 *   isDevMode      bool    Surfaced to the webapp via wp-context
 */
(function () {
    const cfg = window.PiApiIframe || {};
    const iframe = document.getElementById('pi-api-iframe');
    if (!iframe || !cfg.backendOrigin) {
        return;
    }

    const expectedOrigin = cfg.backendOrigin;
    const ajaxUrl = cfg.ajaxUrl || (typeof window.ajaxurl === 'string' ? window.ajaxurl : '');
    const refreshAction = cfg.refreshAction || 'pi_api_refresh_jwt';
    const ajaxNonce = cfg.nonce || '';
    const loaderTimeoutMs = Number(cfg.loaderTimeout) > 0 ? Number(cfg.loaderTimeout) : 8000;

    const loader = document.getElementById('pi-api-loader');
    const errorContainer = document.getElementById('pi-api-error');

    // Show the error UI if the webapp hasn't signaled `pi-api/ready` within
    // the configured timeout. Cancelled once the handshake completes.
    const loadTimeout = setTimeout(function () {
        if (loader && !loader.classList.contains('is-hidden')) {
            loader.style.display = 'none';
            if (errorContainer) {
                errorContainer.style.display = 'flex';
            }
            console.error('[pi-api] Dashboard connection timeout (' + loaderTimeoutMs + 'ms)');
        }
    }, loaderTimeoutMs);

    function postToFrame(payload) {
        if (iframe.contentWindow) {
            iframe.contentWindow.postMessage(payload, expectedOrigin);
        }
    }

    // Use the URL parser so attacker-controlled prefixes like
    // `https://evil.example/?fake=https://wp.local/` can't trick indexOf.
    function isSameOriginUrl(rawUrl) {
        try {
            const parsed = new URL(rawUrl, window.location.origin);
            return parsed.origin === window.location.origin;
        } catch (e) {
            return false;
        }
    }

    window.addEventListener('message', function (event) {
        if (!expectedOrigin || event.origin !== expectedOrigin) {
            return;
        }

        const data = event.data || {};
        switch (data.type) {
            case 'pi-api/ready':
                clearTimeout(loadTimeout);
                if (loader) {
                    loader.classList.add('is-hidden');
                }
                postToFrame({
                    type: 'pi-api/wp-context',
                    siteUrl: cfg.siteUrl || window.location.origin,
                    wpVersion: cfg.wpVersion || '',
                    isDevMode: !!cfg.isDevMode,
                });
                break;

            case 'pi-api/navigate':
                if (typeof data.url === 'string' && isSameOriginUrl(data.url)) {
                    window.location.href = new URL(data.url, window.location.origin).href;
                }
                break;

            case 'pi-api/refresh-jwt':
                if (!ajaxUrl || !ajaxNonce) {
                    postToFrame({ type: 'pi-api/jwt-refresh-failed', error: 'no-ajax-config' });
                    break;
                }
                fetch(ajaxUrl + '?action=' + encodeURIComponent(refreshAction) +
                      '&_wpnonce=' + encodeURIComponent(ajaxNonce), {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }
                        return response.json();
                    })
                    .then(function (payload) {
                        // wp_send_json_success wraps as { success: true, data: {...} }
                        const body = (payload && payload.success && payload.data) || {};
                        if (typeof body.jwt !== 'string' || body.jwt === '' || !(body.expires_in > 0)) {
                            postToFrame({ type: 'pi-api/jwt-refresh-failed', error: 'invalid-response' });
                            return;
                        }
                        postToFrame({
                            type: 'pi-api/jwt-refreshed',
                            jwt: body.jwt,
                            expires_in: body.expires_in,
                        });
                    })
                    .catch(function (error) {
                        const msg = (error && error.message) ? error.message : String(error);
                        console.error('[pi-api] jwt refresh failed:', msg);
                        postToFrame({ type: 'pi-api/jwt-refresh-failed', error: msg });
                    });
                break;

            case 'pi-api/error':
                console.error('[pi-api] iframe error:', data.error);
                break;

            case 'pi-api/session-expired':
                // Reload the admin page so IframeRenderer mints a fresh JWT
                // via Settings::ensureValidJwt(). Don't log the user out.
                console.warn('[pi-api] Session expired — reloading admin page');
                window.location.reload();
                break;
        }
    });
})();
