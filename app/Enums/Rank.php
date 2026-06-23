<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Hunting/gathering rank. Drives `?rank=` filters on rewards and gathering.
 */
enum Rank: string
{
    use HasValues;

    case LowRank = 'LR';
    case HighRank = 'HR';
    case GRank = 'G';

    public function label(): string
    {
        return match ($this) {
            self::LowRank => 'Low Rank',
            self::HighRank => 'High Rank',
            self::GRank => 'G Rank',
        };
    }
}
