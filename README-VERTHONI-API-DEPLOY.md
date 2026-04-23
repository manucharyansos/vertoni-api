# Verthoni API deployment pass

## Production backend env

Use `.env.production.example` as the base for verthoni.com.

Important values:

```env
APP_URL=https://verthoni.com
FRONTEND_URL=https://verthoni.com
CORS_ALLOWED_ORIGINS=https://verthoni.com,https://www.verthoni.com
MAIL_FROM_ADDRESS=info@verthoni.com
FILESYSTEM_DISK=public
```

## Local backend env

Use `.env.local.example` while working locally.

```env
APP_URL=http://127.0.0.1:8000
FRONTEND_URL=http://localhost:3000
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
```

## Added/updated

- Added `newsletter_subscriptions` table.
- Added `POST /api/v1/newsletter-subscriptions`.
- Added Filament admin resource for newsletter subscribers.
- Added CORS config for production and local frontend origins.
