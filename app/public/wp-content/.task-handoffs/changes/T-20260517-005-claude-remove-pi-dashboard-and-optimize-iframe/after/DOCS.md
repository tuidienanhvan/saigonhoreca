# PI API WordPress Plugin Documentation

WordPress plugin that connects sites to PI ecosystem backend.

## Overview

- **Plugin File**: `pi-api.php`
- **Namespace**: `PiApi\`
- **Requires**: WordPress 6.0+, PHP 8.3

---

## Core Classes

### `PiApi\Settings`
- License key management
- Plugin settings page
- Connection status

### `PiApi\Heartbeat`
- Periodic license verification
- Site activation/deactivation
- Health checks

### `PiApi\IframeRenderer`
- Dashboard iframe embedding
- Admin page integration

### `PiApi\ApiBootstrap`
- REST API endpoints for:
  - Content management
  - SEO data
  - Analytics
  - AI operations

### `PiApi\BackendClient`
- HTTP client to PI backend
- Token usage reporting
- JWT authentication

### `PiApi\CorsBridge`
- CORS headers for iframe communication
- Application Password support
- Filter `pi_api_cors_allowed_origins(array $origins, string $request_origin)` to add staging/dev domains

### `PiApi\JwtAjax`
- admin-ajax handler for JWT refresh from inside the iframe
- Action: `wp_ajax_pi_api_refresh_jwt`, nonce: `pi_api_iframe`
- Calls `Settings::ensureValidJwt()` and returns `{ jwt, expires_in }`

---

## Subscription Tiers

The PI Ecosystem operates on a 3-tier model:

1. **Free**:
   - 20,000 Pi tokens/month
   - Access to basic models (Groq, Gemini)
   - 1 Site activation

2. **Pro**:
   - 2,000,000 Pi tokens/month
   - Balanced models (Mix of quality providers)
   - 3-5 Site activations
   - Priority support

3. **Max**:
   - 10,000,000 Pi tokens/month
   - Best quality models (GPT-4o, Claude 3.5 Sonnet)
   - 20+ Site activations
   - White-label features

## REST API Endpoints

All routes under `/wp-json/pi/v1/`

### GET `/content`
Query posts/pages with filters

### POST `/ai/generate`
AI content generation

### GET `/stats`
Site statistics

### POST `/usage`
Report token usage

---

## Hooks

### Actions
```php
do_action('pi_api_report_usage', $tokens_in, $tokens_out, $jwt);
```

### Filters
- `pi_api_backend_url` - Override backend URL
- `pi_api_mock_mode` - Enable mock responses
- `pi_api_iframe_loader_timeout(int $ms)` - Iframe handshake timeout (default 8000ms)
- `pi_api_cors_allowed_origins(array, string)` - Whitelist additional origins for REST credentials

---

## Configuration Constants

```php
PI_API_BACKEND_URL  // Default: https://app.pi-ecosystem.com
PI_API_MOCK_MODE    // Default: false  (BackendClient returns mock data when true)
PI_API_DEV_MODE     // Default: undefined  (enables iframe dev-mock JWT bypass; also honored: WP_DEBUG)
```

### Dev mode

When `PI_API_DEV_MODE` (or `WP_DEBUG`) is true, the iframe page:
- Bypasses the license-active check (renders the iframe even on an unactivated site)
- Generates a local mock JWT if backend issuance fails

In production neither happens — JWT failure renders `views/iframe-error-page.php`.

---

## Installation

1. Upload plugin to `/wp-content/plugins/`
2. Activate in WordPress admin
3. Enter license key
4. Configure API settings
5. Access dashboard via Pi menu