<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Builds absolute URLs to the bundled image assets from a stored filename.
 * The base URL is environment-driven (CDN in production, APP_URL/assets locally).
 */
final class IconUrl
{
    public static function make(string $category, ?string $filename): ?string
    {
        if (blank($filename)) {
            return null;
        }

        return config('mh4u.asset_base_url')."/{$category}/{$filename}";
    }

    public static function monster(?string $filename): ?string
    {
        return self::make('monsters', $filename);
    }

    public static function item(?string $filename): ?string
    {
        return self::make('items', $filename);
    }

    public static function weapon(?string $filename): ?string
    {
        return self::make('weapons', $filename);
    }

    public static function armor(?string $filename): ?string
    {
        return self::make('armor', $filename);
    }

    public static function location(?string $filename): ?string
    {
        return self::make('locations', $filename);
    }
}
