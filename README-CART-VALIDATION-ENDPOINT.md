# Cart validation endpoint

Added:
- `POST /api/v1/cart/validate`

The endpoint receives current local cart items and returns only products/variants that still exist, are active, and have stock.
It also updates product name, slug, image, price, stock, size, and color from the database.
