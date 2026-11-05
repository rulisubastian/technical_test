<?php

declare(strict_types=1);

return [
    'stateful' => [],
    'guard' => [],
    'migration' => false,
    'expiration' => (int) env('SANCTUM_EXPIRATION_MINUTES', 480),
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),
    'middleware' => [],
];
