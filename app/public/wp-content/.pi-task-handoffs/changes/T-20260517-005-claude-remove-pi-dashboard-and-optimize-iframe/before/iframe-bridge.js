(function () {
    const iframe = document.getElementById('pi-api-iframe');
    if (!iframe || !window.PiApiIframe) {
        return;
    }

    const expectedOrigin = window.PiApiIframe.backendOrigin || '';
    const ajaxNonce = window.PiApiIframe.nonce || '';
    const loader = document.getElementById('pi-api-loader');
    const errorContainer = document.getElementById('pi-api-error');

    // 8-second timeout for the initial handshake
    const loadTimeout = setTimeout(() => {
        if (loader && !loader.classList.contains('is-hidden')) {
            loader.style.display = 'none';
            if (errorContainer) {
                errorContainer.style.display = 'flex';
                console.error('[pi-api] Connection timeout — Dashboard failed to respond.');
            }
        }
    }, 8000);

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
                iframe.contentWindow.postMessage({
                    type: 'pi-api/wp-context',
                    siteUrl: window.PiApiIframe.siteUrl || window.location.origin,
                    wpVersion: window.PiApiIframe.wpVersion || '',
                }, expectedOrigin);
                break;

            case 'pi-api/navigate':
                if (typeof data.url === 'string' && data.url.indexOf(window.location.origin) === 0) {
                    window.location.href = data.url;
                }
                break;

            case 'pi-api/refresh-jwt':
                fetch(window.ajaxurl + '?action=pi_api_refresh_jwt&_wpnonce=' + encodeURIComponent(ajaxNonce), {
                    credentials: 'same-origin',
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        const body = payload && payload.data ? payload.data : payload;
                        iframe.contentWindow.postMessage({
                            type: 'pi-api/jwt-refreshed',
                            jwt: body.jwt || '',
                            expires_in: body.expires_in || 900,
                        }, expectedOrigin);
                    })
                    .catch(function (error) {
                        console.error('[pi-api] jwt refresh failed:', error);
                    });
                break;

            case 'pi-api/error':
                console.error('[pi-api] iframe error:', data.error);
                break;

            case 'pi-api/session-expired':
                // Không logout WordPress — chỉ reload trang admin để iframe
                // nhận JWT mới từ IframeRenderer::renderPage().
                console.warn('[pi-api] Session expired — reloading admin page for fresh JWT.');
                window.location.reload();
                break;
        }
    });
})();
