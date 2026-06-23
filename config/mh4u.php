<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Asset Base URL
    |--------------------------------------------------------------------------
    |
    | Absolute base URL prepended to every image filename to build the
    | `icon_url` / `map_url` fields returned by the API. In production this
    | points at the CDN; locally it points at `APP_URL/assets`.
    |
    */

    'asset_base_url' => rtrim((string) env('MH4U_ASSET_BASE_URL', env('APP_URL', 'http://localhost').'/assets'), '/'),

    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | The translation suffixes present in the source database. `en` is the
    | base (untyped) column and the fallback for every other language.
    |
    */

    'default_lang' => 'en',

    'supported_langs' => ['en', 'de', 'fr', 'es', 'it', 'jp'],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | `cache_version` is baked into every cache key so a data re-import can
    | invalidate the entire cache by bumping the token, without a manual flush.
    | `cache_ttl` is the default TTL (seconds) for cached responses.
    |
    */

    'cache_version' => env('MH4U_CACHE_VERSION', 'v1'),

    'cache_ttl' => (int) env('MH4U_CACHE_TTL', 86400),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit
    |--------------------------------------------------------------------------
    |
    | Requests per minute per IP for the public API.
    |
    */

    'rate_limit' => (int) env('MH4U_RATE_LIMIT', 60),

    /*
    |--------------------------------------------------------------------------
    | Kiranico Data Directory
    |--------------------------------------------------------------------------
    |
    | Directory the KiranicoImportSeeder reads the scraped JSON from. It is
    | gitignored (never committed); the seeder skips gracefully when absent.
    |
    */

    'kiranico_data' => env('MH4U_KIRANICO_DATA', database_path('source/kiranico')),

];
