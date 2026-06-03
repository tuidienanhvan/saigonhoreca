# PI API - WordPress Plugin

## Hooks Reference

### Actions
```php
pi_api_report_usage($tokens_in, $tokens_out, $jwt)
```

### Filters
```php
pi_api_backend_url    // Override API URL
pi_api_mock_mode      // Enable test mode
```

## REST Routes
All under `/wp-json/pi/v1/`

| Endpoint | Method | Purpose |
|----------|--------|---------|
| /content | GET | Query posts |
| /ai/generate | POST | AI content |
| /stats | GET | Site stats |
| /usage | POST | Report tokens |

## Constants
- `PI_API_VERSION` - Plugin version
- `PI_API_BACKEND_URL` - Default: https://pi-dashboard-wordpress.vercel.app (override in wp-config.php to use a custom domain)