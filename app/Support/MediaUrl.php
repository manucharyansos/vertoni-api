<?php

namespace App\Support;

class MediaUrl
{
    public static function fromPublicDisk(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = trim((string) $path);

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
            return $path;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('#^(public/|storage/|uploads/)#', '', $path);

        $base = config('filesystems.disks.public.url') ?: rtrim(env('ASSET_URL', env('APP_URL')), '/') . '/uploads';

        return rtrim($base, '/') . '/' . $path;
    }
}
