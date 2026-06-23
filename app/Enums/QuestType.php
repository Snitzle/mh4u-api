<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Quest progression type: key quests unlock urgents, urgents advance rank.
 */
enum QuestType: string
{
    use HasValues;

    case Normal = 'Normal';
    case Key = 'Key';
    case Urgent = 'Urgent';
}
