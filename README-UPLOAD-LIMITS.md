# Admin video upload limits

Filament admin file uploads go through Livewire first. Livewire has its own temporary upload validation before the file reaches the Filament field.

This project sets:

- `config/livewire.php` -> `temporary_file_upload.rules` -> `max:204800` (200MB)
- `BannerResource` video/media upload -> `maxSize(204800)`
- `HomeSectionResource` video upload -> `maxSize(204800)`

For local XAMPP, also edit `php.ini`:

```ini
upload_max_filesize=200M
post_max_size=220M
max_execution_time=300
memory_limit=512M
```

Then restart Apache and run:

```bash
php artisan optimize:clear
```

Do not open `/livewire/upload-file` directly in the browser. It is a POST-only endpoint used by Livewire JavaScript.
