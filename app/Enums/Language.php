<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * The languages present in the source database. English is the base column
 * (no suffix) and the fallback for every other language.
 */
enum Language: string
{
    use HasValues;

    case English = 'en';
    case German = 'de';
    case French = 'fr';
    case Spanish = 'es';
    case Italian = 'it';
    case Japanese = 'jp';

    /**
     * The column suffix for this language, e.g. German => "_de", English => "".
     * Used to resolve translated columns such as `name_de` / `description_fr`.
     */
    public function columnSuffix(): string
    {
        return $this === self::English ? '' : '_'.$this->value;
    }

    public function isBase(): bool
    {
        return $this === self::English;
    }
}
