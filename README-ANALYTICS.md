# Vertoni custom analytics

This project includes first-party analytics for the public Nuxt site and Filament admin.

## What is tracked

- Page views: URL, path, title, locale, referrer, UTM tags, device type, browser, OS, screen/viewport, load time.
- Visitors: anonymous browser visitor ID, session count, page view count, first/last seen, last opened page.
- Events: clicks on public links/buttons and page-leave duration events.

Raw IP addresses are not stored. The backend stores only an HMAC hash of the IP using `APP_KEY`.

## Admin

Filament menu: `Վերլուծություն`

- `Այցեր և էջեր`
- `Հաճախողներ`
- `Գործողություններ`

## Deploy

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
