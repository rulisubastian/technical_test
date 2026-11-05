<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function summary(DashboardService $dashboard): JsonResponse
    {
        return ApiResponse::success($dashboard->summary());
    }
}
