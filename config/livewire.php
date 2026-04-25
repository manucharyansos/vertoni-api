<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Filament uses Livewire for admin file uploads. Livewire validates files
    | before Filament stores them. The default limit is 12MB, which is too small
    | for homepage/banner videos. Keep this in sync with FileUpload::maxSize().
    |
    */
    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TEMP_DISK', 'public'),
        'rules' => ['required', 'file', 'max:51200'],
        'directory' => 'livewire-tmp',
        'middleware' => 'throttle:120,1',
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'jpg', 'jpeg', 'webp',
            'mp4', 'webm', 'ogg', 'mov', 'avi', 'wmv',
            'wav', 'mp3', 'm4a', 'mpga', 'wma',
        ],
        'max_upload_time' => 120,
        'cleanup' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    */
    'render_on_redirect' => false,
];
