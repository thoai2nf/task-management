<?php
// config/broadcasting.php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "ably", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusherapp.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',

                // Timeout settings cho localhost
                'timeout' => 120, // Tăng lên 2 phút

                'curl_options' => [
                    // SSL settings
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_CAINFO => false,
                    CURLOPT_CAPATH => false,

                    // Timeout settings
                    CURLOPT_TIMEOUT => 120,           // Total timeout: 2 minutes
                    CURLOPT_CONNECTTIMEOUT => 60,     // Connection timeout: 1 minute

                    // Network settings
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_USERAGENT => 'Laravel-Broadcasting/1.0',

                    // DNS và connection settings
                    CURLOPT_DNS_CACHE_TIMEOUT => 300,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,

                    // Bypass proxy nếu có
                    CURLOPT_NOPROXY => 'localhost,127.0.0.1,::1',

                    // HTTP settings
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json',
                        'Content-Type: application/json',
                    ],

                    // Debug (chỉ trong development)
                    CURLOPT_VERBOSE => env('APP_DEBUG', false),
                ],
            ],
            'client_options' => [
                // Guzzle HTTP client options
                'timeout' => 120,              // Request timeout
                'connect_timeout' => 60,       // Connection timeout
                'verify' => false,             // Disable SSL verification
                'http_errors' => false,        // Don't throw on HTTP errors
                'allow_redirects' => [
                    'max' => 5,
                    'strict' => false,
                    'referer' => true,
                ],
                'headers' => [
                    'User-Agent' => 'Laravel-Broadcasting/1.0',
                    'Accept' => 'application/json',
                ],

                // Stream context (cho những network khó khăn)
                'stream_context' => [
                    'http' => [
                        'timeout' => 120,
                        'ignore_errors' => true,
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ],
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
