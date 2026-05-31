# V15 final backend fixes

Fixed:
- Analytics page duration is no longer always empty: `page_leave` events now update the latest page-view row with `time_on_page_seconds`.
- Analytics page views navigation labels are clearer: opened pages are shown directly in the `Բացված էջեր` resource.
- Visitor relationships now use the correct `analytics_visitor_id` foreign key.
- PHP syntax check passed for `app`, `database`, `routes`, and `config`.

Deploy:
```bash
cd /home/gpemureqjaea/api.verthoni.com

git pull origin main
composer install --no-dev --optimize-autoloader
composer dump-autoload -o
php artisan optimize:clear
php artisan migrate --force
php artisan filament:assets
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

Do not run `migrate:fresh` on production.
