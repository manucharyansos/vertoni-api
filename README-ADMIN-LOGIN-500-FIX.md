# Admin login 500 / duplicate markdown or Ns JavaScript errors

The JavaScript errors `Identifier 'markdown' has already been declared` and `Identifier 'Ns' has already been declared` are not the real cause. They appear because Laravel/Filament is rendering an error page.

The real problems are usually:

1. Missing Laravel runtime directories after a Git clone/pull:
   - `storage/framework/views`
   - `storage/framework/sessions`
   - `storage/framework/cache/data`
   - `storage/logs`
   - `bootstrap/cache`

2. Wrong local `.env` session/cache settings copied from production.

Local `.env` should use:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
SESSION_DRIVER=file
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Run:

```bash
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
php artisan optimize:clear
php artisan config:clear
php artisan view:clear
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan serve
```

On Windows PowerShell:

```powershell
New-Item -ItemType Directory -Force storage/framework/cache/data, storage/framework/sessions, storage/framework/views, storage/logs, bootstrap/cache
php artisan optimize:clear
php artisan config:clear
php artisan view:clear
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan serve
```

If it still fails, read the real error:

```bash
tail -n 80 storage/logs/laravel.log
```

PowerShell:

```powershell
Get-Content storage/logs/laravel.log -Tail 80
```
