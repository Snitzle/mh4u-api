<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonsterHabitat extends BaseModel
{
    protected $table = 'monster_habitats';

    /** @return BelongsTo<Monster, $this> */
    public function monster(): BelongsTo
    {
        return $this->belongsTo(Monster::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
