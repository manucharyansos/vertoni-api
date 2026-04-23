# Vertoni fullscreen + CMS homepage pass

## Backend
- Added admin-controlled homepage categories: show on home, home order, home title/description/image.
- Added admin-controlled homepage products: show on home + home order for Highlights.
- Added editable `home_sections` for campaign/editorial blocks like Stefano Ricci's Equestrian line.
- Added public site settings using the existing `settings` table.
- Added Filament admin resources for Homepage Sections and Site Settings.
- Added API endpoint: `GET /api/v1/homepage`.

Run after pulling backend:

```bash
composer install
php artisan migrate
php artisan storage:link
```

## Frontend
- Homepage now reads from `/api/v1/homepage`.
- Full-screen responsive homepage structure: hero, 3 category tiles, highlights, editorial sections, settings/service strip.
- Removed the public style switcher from storefront.
- Footer contact values can come from public settings.

Run after pulling frontend:

```bash
npm install
npm run build
```

Dependency folders are not included.
