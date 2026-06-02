<?php

return [
    'mail_enabled' => env('NEWSLETTER_MAIL_ENABLED', true),
    'welcome_email_enabled' => env('NEWSLETTER_WELCOME_EMAIL_ENABLED', true),
    'new_product_email_enabled' => env('NEWSLETTER_NEW_PRODUCT_EMAIL_ENABLED', true),
    'frontend_url' => env('FRONTEND_URL', env('APP_URL', 'https://verthoni.com')),
    'batch_size' => (int) env('NEWSLETTER_EMAIL_BATCH_SIZE', 50),
];
