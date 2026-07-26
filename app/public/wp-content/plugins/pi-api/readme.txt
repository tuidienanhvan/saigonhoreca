=== Pi API ===
Contributors: piecosystem
Tags: dashboard, analytics, seo, ai, crm
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WordPress site to Pi Dashboard with license activation, heartbeat validation, and secure iframe access.

== Description ==

Pi API is a thin connector plugin for the Pi Ecosystem SaaS dashboard. It lets a WordPress administrator activate a license key and open Pi Dashboard inside WP Admin.

== Installation ==

1. Upload the `pi-api` folder to `/wp-content/plugins/`.
2. Activate the plugin in WordPress.
3. Open Settings > Pi API.
4. Enter your license key.

== Development Mock Mode ==

Add this to `wp-config.php`:

`define('PI_API_MOCK_MODE', true);`

Use test key:

`TESTING-12345-DEMO0-KEYAA`

== Changelog ==

= 1.0.0 =
* Initial MVP connector.
