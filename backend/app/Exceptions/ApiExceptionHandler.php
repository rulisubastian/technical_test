<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class ApiExceptionHandler
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $e): bool => true);

        $exceptions->render(function (Throwable $e, Request $request): JsonResponse {
            return match (true) {
                $e instanceof ValidationException => ApiResponse::error('Validasi gagal', $e->errors(), 422),
                $e instanceof AuthenticationException => ApiResponse::error('Tidak terautentikasi', null, 401),
                $e instanceof AuthorizationException, $e instanceof AccessDeniedHttpException => ApiResponse::error('Anda tidak memiliki akses untuk aksi ini', null, 403),
                $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => ApiResponse::error('Data tidak ditemukan', null, 404),
                $e instanceof InvalidOrderTransition => ApiResponse::error($e->getMessage(), null, 409),
                $e instanceof InsufficientStock => ApiResponse::error($e->getMessage(), $e->errors, 422),
                $e instanceof HttpExceptionInterface => ApiResponse::error($e->getMessage() !== '' ? $e->getMessage() : 'Request tidak valid', null, $e->getStatusCode()),
                // 500: jangan bocorkan detail/stack trace di production.
                default => ApiResponse::error(
                    config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server',
                    null,
                    500,
                ),
            };
        });
    }
}
