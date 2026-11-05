<?php

declare(strict_types=1);

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;

return [
    'default' => env('LOG_CHANNEL', 'stderr_json'),
    'deprecations' => ['channel' => 'null', 'trace' => false],
    'channels' => [
        // Structured logging (JSON) ke stderr; request_id ditambahkan via Log::withContext (RequestId middleware).
        'stderr_json' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'info'),
            'handler' => StreamHandler::class,
            'formatter' => JsonFormatter::class,
            'with' => ['stream' => 'php://stderr'],
            'processors' => [Monolog\Processor\PsrLogMessageProcessor::class],
        ],
        'null' => ['driver' => 'monolog', 'handler' => NullHandler::class],
    ],
];
