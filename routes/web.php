<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/uploads/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);

    $disk = Storage::disk('public');

    abort_unless($disk->exists($path), 404);

    return response($disk->get($path), 200)
        ->header('Content-Type', $disk->mimeType($path) ?: 'application/octet-stream')
        ->header('Cache-Control', 'public, max-age=31536000, immutable');
})->where('path', '.*');

Route::get('/', fn () => response()->json([
    'status' => 'ok',
    'app' => config('app.name'),
]));
