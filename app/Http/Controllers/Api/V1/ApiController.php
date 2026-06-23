<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{
    /**
     * Per-page size for paginated index endpoints, clamped to a sane range.
     */
    protected function perPage(): int
    {
        return max(1, min(request()->integer('per_page', 30), 100));
    }
}
