<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => '/auth/github/callback',
],
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => 'https://api.appspivot.com/api/login/sns/callback'
],

'kakao' => [
    'client_id' => env('KAKAO_CLIENT_ID'),
    'client_secret' => env('KAKAO_CLIENT_SECRET'),
    'redirect' => 'https://api.appspivot.com/api/login/sns/callback',
],

'line' => [    
    'client_id' => env('LINE_CLIENT_ID'),  
    'client_secret' => env('LINE_CLIENT_SECRET'),  
    'redirect' => 'https://api.appspivot.com/api/login/sns/callback',
  ],

  'instagram' => [    
    'client_id' => env('INSTAGRAM_CLIENT_ID'),  
    'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),  
    'redirect' => 'https://api.appspivot.com/api/login/sns/callback',
  ],

  'apple' => [
    'client_id' => env('APPLE_CLIENT_ID'),
    'client_secret' => env('APPLE_CLIENT_SECRET'),
    'redirect' => 'https://api.appspivot.com/api/login/sns/callback',
  ],


];
