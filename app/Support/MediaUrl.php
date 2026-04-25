<?php

namespace App\Support;

class MediaUrl
{
    public static function fromPublicDisk(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = ltrim((string) $path, '/');

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
            return $path;
        }

        $baseUrl = rtrim((string) config('filesystems.disks.public.url'), '/');

        if ($baseUrl === '') {
            $baseUrl = rtrim((string) config('app.url'), '/') . '/uploads';
        }

        return $baseUrl . '/' . $path;
    }
}
