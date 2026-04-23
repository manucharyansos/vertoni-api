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
        'disk' => null,
        'rules' => ['required', 'file', 'max:204800'], // 200MB, value is in KB
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4', 'mov', 'avi', 'wmv',
            'mp3', 'm4a', 'jpg', 'jpeg', 'mpga', 'webp', 'wma', 'webm', 'ogg',
        ],
        'max_upload_time' => 10,
        'cleanup' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    */
    'render_on_redirect' => false,
];
