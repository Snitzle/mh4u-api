<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// This is a headless JSON API. The root returns service metadata; browse the
// interactive docs at /docs and the data under /api/v1.
Route::get('/', fn () => response()->json([
    'name' => config('app.name'),
    'description' => 'Public REST API for the Monster Hunter 4 Ultimate game database.',
    'version' => 'v1',
    'documentation' => url('/docs'),
    'api' => url('/api/v1'),
    'repository' => 'https://github.com/Snitzle/mh4u-api',
]));
