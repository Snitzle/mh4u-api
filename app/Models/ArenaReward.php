<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArenaReward extends BaseModel
{
    protected $table = 'arena_rewards';

    /** @return BelongsTo<ArenaQuest, $this> */
    public function arenaQuest(): BelongsTo
    {
        return $this->belongsTo(ArenaQuest::class, 'arena_id');
    }

    /** @return BelongsTo<Item, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
