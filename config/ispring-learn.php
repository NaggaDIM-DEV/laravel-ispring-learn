<?php

return [
    'account_url'   => env('ISPRING_LEARN_ACCOUNT_URL'),
    /*
     * Authentication method
     * Available values: api-key (experimental), login
     *
     * if value is api-key then fields client_id and client_secret required
     * if value is login then fields username and password required
     */
    'auth_type'     => env('ISPRING_LEARN_AUTH_TYPE', 'login'),
    'client_id'     => env('ISPRING_LEARN_CLIENT_ID'),
    'client_secret' => env('ISPRING_LEARN_CLIENT_SECRET'),
    'username'      => env('ISPRING_LEARN_USERNAME'),
    'password'      => env('ISPRING_LEARN_PASSWORD'),

    'logging'      => [
        'enabled' => env('ISPRING_LEARN_LOGGING', false),
        'channel' => [
            'driver'    => 'daily',
            'path'      => storage_path('logs/ispring-learn/api.log'),
            'level'     => env('ISPRING_LEARN_LOG_LEVEL', 'info'),
            'days'      => env('ISPRING_LEARN_LOGGING_DAYS', 14),
            'replace_placeholders' => true,
        ],
    ],
];