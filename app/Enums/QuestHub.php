<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Where a quest is taken: the solo Caravan, multiplayer Guild, or Event quests.
 */
enum QuestHub: string
{
    use HasValues;

    case Caravan = 'Caravan';
    case Guild = 'Guild';
    case Event = 'Event';
}
