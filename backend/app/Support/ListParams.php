<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

/** Parsing sort/pagination dengan whitelist kolom (mencegah SQL injection via order by). */
final class ListParams
{
    /**
     * @param  list<string>  $allowedSorts
     * @return array{sort: string, direction: string, per_page: int, search: string}
     */
    public static function from(Request $request, array $allowedSorts, string $defaultSort = 'id', string $defaultDir = 'desc'): array
    {
        $sort = (string) $request->query('sort', $defaultSort);
        $dir = strtolower((string) $request->query('direction', $defaultDir));

        return [
            'sort' => in_array($sort, $allowedSorts, true) ? $sort : $defaultSort,
            'direction' => $dir === 'asc' ? 'asc' : 'desc',
            'per_page' => max(1, min(100, (int) $request->query('per_page', 10))),
            'search' => trim((string) $request->query('search', '')),
        ];
    }

    public static function like(string $term): string
    {
        return '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_strtolower($term)).'%';
    }
}
