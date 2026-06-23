<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonsterAilment extends BaseModel
{
    protected $table = 'monster_ailments';

    /** @return BelongsTo<Monster, $this> */
    public function monster(): BelongsTo
    {
        return $this->belongsTo(Monster::class);
    }
}
