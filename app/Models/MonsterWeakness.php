<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonsterWeakness extends BaseModel
{
    protected $table = 'monster_weaknesses';

    /** @return BelongsTo<Monster, $this> */
    public function monster(): BelongsTo
    {
        return $this->belongsTo(Monster::class);
    }
}
