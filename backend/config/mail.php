<?php

declare(strict_types=1);

return [
    'default' => env('MAIL_MAILER', 'smtp'),
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 1025),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => 10,
        ],
        'array' => ['transport' => 'array'],
        'log' => ['transport' => 'log', 'channel' => null],
    ],
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'no-reply@azatech.test'),
        'name' => env('MAIL_FROM_NAME', 'Azatech Shop'),
    ],
];
