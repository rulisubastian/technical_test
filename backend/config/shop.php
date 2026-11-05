<?php

declare(strict_types=1);

return [
    // Idempotency: masa berlaku key & batas waktu "sedang diproses" sebelum boleh diambil alih.
    'idempotency_ttl_minutes' => (int) env('IDEMPOTENCY_TTL_MINUTES', 1440),
    'idempotency_processing_seconds' => (int) env('IDEMPOTENCY_PROCESSING_SECONDS', 60),
    'dashboard_cache_ttl' => (int) env('DASHBOARD_CACHE_TTL', 60),
    'rate_limit_login' => (int) env('RATE_LIMIT_LOGIN', 5),
    'rate_limit_orders' => (int) env('RATE_LIMIT_ORDERS', 30),
];
