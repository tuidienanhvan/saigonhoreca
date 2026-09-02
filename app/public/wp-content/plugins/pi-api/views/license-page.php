<?php
if (!defined('ABSPATH')) {
    exit;
}

use PiApi\Settings;

$message = isset($_GET['pi_api_msg']) ? sanitize_text_field((string) $_GET['pi_api_msg']) : '';
$type = isset($_GET['pi_api_type']) ? sanitize_key((string) $_GET['pi_api_type']) : 'success';
?>
<div class="wrap pi-api-license">
    <h1><?php esc_html_e('Pi API - License', 'pi-api'); ?></h1>

    <?php if ($message !== '') : ?>
        <div class="notice notice-<?php echo $type === 'error' ? 'error' : 'success'; ?> is-dismissible">
            <p><?php echo esc_html($message === 'activated' ? 'Kích hoạt thành công.' : ($message === 'deactivated' ? 'Đã hủy kích hoạt.' : $message)); ?></p>
        </div>
    <?php endif; ?>

    <?php if (defined('PI_API_MOCK_MODE') && PI_API_MOCK_MODE) : ?>
        <div class="notice notice-info">
            <p><?php esc_html_e('Mock mode đang bật cho local/dev. Test key: TESTING-12345-DEMO0-KEYAA', 'pi-api'); ?></p>
        </div>
    <?php endif; ?>

    <div class="pi-api-license__card">
        <?php if (Settings::isActive()) : ?>
            <h2><?php esc_html_e('Đã kích hoạt', 'pi-api'); ?></h2>
            <p>License: <code><?php echo esc_html(Settings::maskKey(Settings::getLicenseKey())); ?></code></p>
            <p>Tier: <strong><?php echo esc_html(ucfirst(Settings::getTier())); ?></strong></p>
            <p>Features: <?php echo esc_html(implode(', ', Settings::getFeatures())); ?></p>
            <p>
                Token quota:
                <strong>
                    <?php
                    $quota = Settings::getTokenQuota();
                    echo $quota < 0 ? esc_html__('Không giới hạn', 'pi-api') : esc_html(number_format($quota) . '/tháng');
                    ?>
                </strong>
            </p>
            <p>Last heartbeat: <?php echo esc_html(get_option('pi_api_last_heartbeat') ? wp_date('Y-m-d H:i:s', (int) get_option('pi_api_last_heartbeat')) : 'Chưa có'); ?></p>

            <p>
                <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=pi-api')); ?>">
                    <?php esc_html_e('Mở Pi Dashboard', 'pi-api'); ?>
                </a>
            </p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_api_deactivate'); ?>
                <input type="hidden" name="action" value="pi_api_action" />
                <button type="submit" name="pi_api_action" value="deactivate" class="button">
                    <?php esc_html_e('Hủy kích hoạt', 'pi-api'); ?>
                </button>
            </form>
        <?php else : ?>
            <p><?php esc_html_e('Nhập license key để kết nối site này với Pi Dashboard.', 'pi-api'); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_api_activate'); ?>
                <input type="hidden" name="action" value="pi_api_action" />
                <p>
                    <label for="license_key"><strong><?php esc_html_e('License key', 'pi-api'); ?></strong></label><br />
                    <input type="text" name="license_key" id="license_key" class="regular-text" placeholder="pi_xxxxxxxxxxxxxxxx" required />
                </p>
                <p>
                    <button type="submit" name="pi_api_action" value="activate" class="button button-primary">
                        <?php esc_html_e('Kích hoạt', 'pi-api'); ?>
                    </button>
                    <a href="https://store.pi-ecosystem.com/pricing" target="_blank" rel="noreferrer">
                        <?php esc_html_e('Mua license', 'pi-api'); ?>
                    </a>
                </p>
            </form>
        <?php endif; ?>
    </div>
</div>
