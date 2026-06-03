<?php
if (!defined('ABSPATH')) {
    exit;
}

// Required scope (set by IframeRenderer::renderPage):
//   $iframe_url     string  Full src URL incl. `?t=<jwt>&iframe=1`
//   $backend_origin string  Scheme+host of the backend (logo, etc.)
//   $expires_in     int     Seconds until JWT expiry (hint for the bridge)
?>
<div class="pi-api-iframe-wrap" data-expires-in="<?php echo esc_attr((string) ($expires_in ?? 0)); ?>">
    <div id="pi-api-loader" class="pi-api-loader">
        <div class="pi-api-loader-content">
            <div class="pi-api-loader-brand">
                <img src="<?php echo esc_url($backend_origin . '/logo-optimized.svg'); ?>" alt="Pi Logo" class="pi-api-loader-logo">
                <div class="pi-api-spinner"></div>
            </div>
            <div class="pi-api-loader-text">
                <span class="pi-api-loader-title">Pi Dashboard</span>
                <span class="pi-api-loader-status">Đang thiết lập kết nối bảo mật...</span>
            </div>
        </div>
    </div>
    <div id="pi-api-error" class="pi-api-error" style="display: none;">
        <div class="pi-api-error-content">
            <img src="<?php echo esc_url($backend_origin . '/logo-optimized.svg'); ?>" alt="Pi Logo" class="pi-api-error-logo">
            <span class="pi-api-error-icon">⚠️</span>
            <span class="pi-api-error-title">Không thể kết nối với Dashboard</span>
            <span class="pi-api-error-desc">Vui lòng kiểm tra kết nối mạng hoặc thử lại sau.</span>
            <button type="button" class="pi-api-retry-btn" onclick="window.location.reload();">Thử lại ngay</button>
        </div>
    </div>
    <?php
    /*
     * sandbox notes:
     *   `allow-scripts allow-same-origin` is normally a sandbox-escape combo,
     *   BUT this iframe is cross-origin (PI_API_DASHBOARD_URL ≠ admin origin).
     *   The browser enforces SOP, so the iframe cannot read the parent DOM
     *   even with same-origin granted — same-origin is needed so the embedded
     *   webapp can use its own localStorage / cookies on its origin.
     *   allow-forms/popups/modals/downloads are required by the dashboard UI.
     *
     * referrerpolicy="no-referrer":
     *   Prevents the URL (which carries the JWT in `?t=`) from leaking into
     *   the Referer header of any sub-resource the webapp fetches.
     */
    ?>
    <iframe
        id="pi-api-iframe"
        src="<?php echo esc_url($iframe_url); ?>"
        title="<?php esc_attr_e('Pi Dashboard', 'pi-api'); ?>"
        sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox allow-modals allow-downloads"
        allow="clipboard-read; clipboard-write"
        loading="lazy"
        referrerpolicy="no-referrer"
    ></iframe>
</div>
