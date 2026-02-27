<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'د مجالسو د تعقیب سیستم'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale (RTL: use ps, ar, fa, etc.)
    |--------------------------------------------------------------------------
    */

    'locale' => env('APP_LOCALE', 'ps'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Default User Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the default user credentials that will be
    | used in local development environments. It is particularly useful
    | for quickly logging into the application without needing
    | to create a user manually.
    |
    */

    'default_user' => [
        'name' => env('DEFAULT_USER_NAME', 'Admin'),
        'email' => env('DEFAULT_USER_EMAIL', 'admin@example.com'),
        'password' => env('DEFAULT_USER_PASSWORD', 'password'),
    ],
];
