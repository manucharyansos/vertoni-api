# Custom Orders backend pass

Added backend support for real bespoke/custom orders:

- Custom orders now accept material, reference URL and source.
- Product records now have `allow_custom_order` toggle for product-level bespoke requests.
- Custom order API accepts up to 6 attachments: jpg, jpeg, png, webp, pdf.
- Attachments are saved to `storage/app/public/custom-orders` and linked to the custom order.
- Filament admin shows custom order material, source, reference link and uploaded files.
- Product admin has a toggle to enable/disable bespoke request button per product.

Run after installing dependencies:

```bash
composer install
php artisan migrate
php artisan storage:link
```
