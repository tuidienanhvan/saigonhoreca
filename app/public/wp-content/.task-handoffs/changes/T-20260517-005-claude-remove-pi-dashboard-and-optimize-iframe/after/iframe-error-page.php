<?php
if (!defined('ABSPATH')) {
    exit;
}

// Required scope (set by IframeRenderer::renderPage):
//   $error_code     string  Token from Settings::ensureValidJwt() error field
//   $backend_origin string  Backend scheme://host for retry instructions

$messages = [
    'no_license'        => __('Site này chưa được kích hoạt license. Vui lòng vào trang License để nhập key.', 'pi-api'),
    'jwt_empty'         => __('Backend trả về JWT rỗng. Kiểm tra trạng thái license hoặc liên hệ hỗ trợ.', 'pi-api'),
    'jwt_issue_failed'  => __('Không phát hành được JWT. Kiểm tra kết nối tới Pi backend.', 'pi-api'),
];
$message = $messages[$error_code] ?? sprintf(
    /* translators: %s: backend error string */
    __('Không thể kết nối với Pi backend: %s', 'pi-api'),
    $error_code
);
$license_url = admin_url('admin.php?page=pi-api-license');
?>
<div class="wrap pi-api-iframe-error-page">
    <h1><?php esc_html_e('Pi Dashboard — Không thể kết nối', 'pi-api'); ?></h1>
    <div class="notice notice-error">
        <p><strong><?php esc_html_e('Lỗi:', 'pi-api'); ?></strong> <?php echo esc_html($message); ?></p>
    </div>
    <p>
        <?php
        printf(
            /* translators: %s: backend origin URL */
            esc_html__('Backend: %s', 'pi-api'),
            '<code>' . esc_html($backend_origin) . '</code>'
        );
        ?>
    </p>
    <p>
        <?php if ($error_code === 'no_license') : ?>
            <a class="button button-primary" href="<?php echo esc_url($license_url); ?>">
                <?php esc_html_e('Đi tới trang License', 'pi-api'); ?>
            </a>
        <?php else : ?>
            <button type="button" class="button button-primary" onclick="window.location.reload();">
                <?php esc_html_e('Thử lại', 'pi-api'); ?>
            </button>
            <a class="button" href="<?php echo esc_url($license_url); ?>">
                <?php esc_html_e('Kiểm tra License', 'pi-api'); ?>
            </a>
        <?php endif; ?>
    </p>
</div>
