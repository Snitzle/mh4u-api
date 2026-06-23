<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Quest reward slot. Slot A is guaranteed; B and Sub are secondary.
 */
enum RewardSlot: string
{
    use HasValues;

    case A = 'A';
    case B = 'B';
    case Sub = 'Sub';
}
