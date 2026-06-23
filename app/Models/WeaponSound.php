<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeaponSound extends BaseModel
{
    protected $table = 'weapon_sounds';

    /** @return BelongsTo<Weapon, $this> */
    public function weapon(): BelongsTo
    {
        return $this->belongsTo(Weapon::class);
    }
}
