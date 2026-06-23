<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Whether a piece of armor is for Blademasters, Gunners, or both. The empty
 * string in the source data is modelled as {@see HunterType::Unspecified}.
 */
enum HunterType: string
{
    use HasValues;

    case Unspecified = '';
    case Blade = 'Blade';
    case Gunner = 'Gunner';
    case Both = 'Both';
}
