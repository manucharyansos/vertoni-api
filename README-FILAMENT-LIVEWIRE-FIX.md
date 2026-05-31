# Filament / Livewire component fix

If the admin edit pages show this error:

```text
Unable to find component: [app.filament.admin.resources.product-resource.pages.edit-product]
Unable to find component: [app.filament.admin.resources.category-resource.pages.edit-category]
```

then the browser or server is still using stale Livewire/Filament component metadata after a code update.

This package explicitly registers the Filament resource page aliases in `App\Providers\AppServiceProvider`, but after deployment you still must clear caches and rebuild autoload:

```bash
composer dump-autoload -o
php artisan filament:clear-cached-components || true
php artisan livewire:discover || true
php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

Then hard refresh the browser or clear cookies/cache for the admin domain.

For production deployment use:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
composer dump-autoload -o
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan db:seed --class=VertoniCatalogSeeder --force
php artisan filament:assets
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```
