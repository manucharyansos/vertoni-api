# V11 analytics/admin fixes

Fixed:
- Frontend `analytics.client.ts` no longer calls `useI18n()` inside a Nuxt plugin. Locale is detected from route/localStorage/html lang instead.
- Filament/Livewire aliases were added for analytics pages and the analytics stats widget.
- Analytics table filters now avoid querying missing analytics tables before migrations finish.
- Analytics stats widget shows a clear message if analytics migrations have not been run yet.

Required after deploy:

```bash
composer install --no-dev --optimize-autoloader
composer dump-autoload -o
php artisan optimize:clear
php artisan migrate --force
php artisan filament:assets
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Do not open the analytics admin pages before `php artisan migrate --force` has created:
- analytics_visitors
- analytics_page_views
- analytics_events
