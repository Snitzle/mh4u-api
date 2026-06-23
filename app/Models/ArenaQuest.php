<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArenaQuest extends BaseModel
{
    protected $table = 'arena_quests';

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return HasMany<ArenaReward, $this> */
    public function rewards(): HasMany
    {
        return $this->hasMany(ArenaReward::class, 'arena_id');
    }
}
