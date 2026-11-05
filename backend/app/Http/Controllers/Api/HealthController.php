<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = ['database' => $this->probe(fn () => DB::select('select 1')), 'redis' => $this->probe(fn () => Redis::connection()->ping())];
        $healthy = ! in_array('down', $checks, true);

        return ApiResponse::success(['status' => $healthy ? 'ok' : 'degraded', 'checks' => $checks], $healthy ? 'OK' : 'Layanan bermasalah', $healthy ? 200 : 503);
    }

    private function probe(callable $fn): string
    {
        try {
            $fn();

            return 'up';
        } catch (Throwable) {
            return 'down';
        }
    }
}
