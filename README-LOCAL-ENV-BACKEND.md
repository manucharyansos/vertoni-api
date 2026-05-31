# Backend local .env

Use `localhost` consistently while developing. Do not mix `localhost:3000` with `127.0.0.1:8000` unless CORS is configured for both.

Recommended local `.env` values:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
ASSET_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

SESSION_DRIVER=file
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax

SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000,localhost:8000,127.0.0.1:8000
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
CORS_SUPPORTS_CREDENTIALS=false

CACHE_STORE=file
QUEUE_CONNECTION=sync
```

After changing `.env`:

```bash
php artisan optimize:clear
php artisan config:clear
php artisan migrate --force
php artisan serve --host=localhost --port=8000
```
