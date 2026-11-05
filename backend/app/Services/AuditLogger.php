<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Context;

final class AuditLogger
{
    /** @param array<string, mixed> $meta */
    public function log(?User $user, string $action, string $subjectType, string|int $subjectId, array $meta = []): void
    {
        AuditLog::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => (string) $subjectId,
            'meta' => $meta,
            'ip' => request()->ip(),
            'request_id' => Context::get('request_id'),
        ]);
    }
}
